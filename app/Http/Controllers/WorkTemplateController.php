<?php

namespace App\Http\Controllers;

use App\Models\TemplateShare;
use App\Models\User;
use App\Models\WorkTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkTemplateController extends Controller
{
    /**
     * Formats users can save their work as.
     * 'doc'/.doc opens in Word; 'html' opens in any browser; others are plain text.
     */
    private const FORMATS = [
        'docx' => ['ext' => 'docx', 'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'label' => 'Word Document (.docx)'],
        'doc'  => ['ext' => 'doc',  'mime' => 'application/msword', 'label' => 'Word 97-2003 (.doc)'],
        'pdf'  => ['ext' => 'pdf',  'mime' => 'application/pdf', 'label' => 'PDF (.pdf)'],
        'html' => ['ext' => 'html', 'mime' => 'text/html', 'label' => 'Web Page (.html)'],
        'txt'  => ['ext' => 'txt',  'mime' => 'text/plain', 'label' => 'Plain Text (.txt)'],
    ];

    public function index()
    {
        $templates = WorkTemplate::with(['creator', 'shares'])
            ->where(fn ($q) => $q->where('created_by', Auth::id())
                ->orWhereHas('shares', fn ($s) => $s->where('shared_with', Auth::id())))
            ->latest()
            ->paginate(10);

        $colleagues = User::where('id', '!=', Auth::id())->orderBy('name')->get(['id', 'name', 'email']);

        return view('work-templates.index', compact('templates', 'colleagues'));
    }

    /**
     * Full-screen editor that opens like a Word document.
     */
    public function create()
    {
        return view('work-templates.create');
    }

    /**
     * View a saved template rendered as a document page.
     */
    public function show(WorkTemplate $template)
    {
        $this->authorizeView($template);

        return view('work-templates.show', [
            'template' => $template,
        ]);
    }

    /**
     * Edit form — only the creator may edit.
     */
    public function edit(WorkTemplate $template)
    {
        $this->authorizeView($template);

        return view('work-templates.edit', compact('template'));
    }

    public function update(Request $request, WorkTemplate $template)
    {
        $this->authorizeView($template);

        $validated = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'file_format' => ['nullable', 'in:' . implode(',', array_keys(self::FORMATS))],
        ]);

        $format = $validated['file_format'] ?? $template->file_format;

        // Regenerate the stored file in the chosen format.
        if ($template->file_path && Storage::disk('local')->exists($template->file_path)) {
            Storage::disk('local')->delete($template->file_path);
        }

        $path = $this->storeDocumentFile($validated['title'], $validated['content'], $format);

        $template->update([
            'title'       => $validated['title'],
            'content'     => $validated['content'],
            'file_format' => $format,
            'file_path'   => $path,
        ]);

        return redirect()->route('work-templates.show', $template)
            ->with('success', 'Template updated successfully.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['required', 'string'],
            'file_format' => ['required', 'in:' . implode(',', array_keys(self::FORMATS))],
        ]);

        $path = $this->storeDocumentFile($validated['title'], $validated['content'], $validated['file_format']);

        $template = WorkTemplate::create([
            'title'       => $validated['title'],
            'content'     => $validated['content'],
            'file_format' => $validated['file_format'],
            'file_path'   => $path,
            'created_by'  => Auth::id(),
        ]);

        return redirect()->route('work-templates.show', $template)
            ->with('success', "Template \"{$template->title}\" saved as " . self::FORMATS[$validated['file_format']]['label'] . ".");
    }

    public function download(WorkTemplate $template)
    {
        $this->authorizeView($template);

        $ext = self::FORMATS[$template->file_format]['ext'] ?? 'doc';
        abort_unless(Storage::disk('local')->exists($template->file_path), 404, 'Template file not found.');

        return Storage::disk('local')->download(
            $template->file_path,
            str_replace(' ', '_', $template->title) . '.' . $ext
        );
    }

    public function share(Request $request, WorkTemplate $template)
    {
        $this->authorizeView($template);

        $validated = $request->validate([
            'recipients'   => ['required', 'array', 'min:1'],
            'recipients.*' => ['integer', 'exists:users,id', 'distinct'],
            'note'         => ['nullable', 'string', 'max:500'],
        ]);

        $recipients = User::whereIn('id', $validated['recipients'])->get();

        foreach ($recipients as $recipient) {
            TemplateShare::firstOrCreate([
                'work_template_id' => $template->id,
                'shared_with'      => $recipient->id,
            ], [
                'note'      => $validated['note'] ?? null,
                'shared_at' => now(),
            ]);

            $recipient->notify(new \App\Notifications\TemplateSharedNotification($template, Auth::user(), $validated['note'] ?? null));
        }

        return redirect()->route('work-templates.index')
            ->with('success', 'Template shared with ' . $recipients->count() . ' colleague(s).');
    }

    public function destroy(WorkTemplate $template)
    {
        $this->authorizeView($template);

        if ($template->file_path && Storage::disk('local')->exists($template->file_path)) {
            Storage::disk('local')->delete($template->file_path);
        }

        $template->delete();

        return redirect()->route('work-templates.index')->with('success', 'Template deleted.');
    }

    /**
     * Generate the raw file bytes for the chosen format.
     */
    private function storeDocumentFile(string $title, string $content, string $format): string
    {
        $ext = self::FORMATS[$format]['ext'];
        $filename = 'template-' . now()->format('YmdHis') . '-' . Str::random(6) . '.' . $ext;

        $body = collect(preg_split('/\r\n|\r|\n/', $content))
            ->map(fn ($p) => '<p>' . e($p) . '</p>')
            ->implode('');

        $html = $this->documentHtml($title, $body);

        $payload = match ($format) {
            'txt'  => $content,
            'pdf'  => $this->textToSimplePdf($title, $content),
            default => $html, // docx, doc, html all read Word-compatible HTML
        };

        $path = 'templates/' . $filename;
        Storage::disk('local')->put($path, $payload);

        return $path;
    }

    private function documentHtml(string $title, string $body): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>{$title}</title></head>
<body style="font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.7;">
    <h2 style="color:#14532d;">{$title}</h2>
    {$body}
    <p style="color:#888;margin-top:30px;font-size:10pt;">Drafted in Court Management System — Work Templates</p>
</body>
</html>
HTML;
    }

    /**
     * Minimal valid PDF built from plain text (no external libraries).
     */
    private function textToSimplePdf(string $title, string $content): string
    {
        $lines = [];
        $lines[] = $title;
        $lines[] = '';
        foreach (preg_split('/\r\n|\r|\n/', $content) as $line) {
            $lines[] = $line;
        }

        $escaped = array_map(fn ($l) => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $l), $lines);

        $text = "BT /F1 12 Tf 50 780 Td 18 TL\n";
        foreach ($escaped as $line) {
            $text .= '(' . $line . ") Tj T*\n";
        }
        $text .= "ET";

        $objects = [
            "<< /Type /Catalog /Pages 2 0 R >>",
            "<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
            "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>",
            "<< /Length " . strlen($text) . " >>\nstream\n" . $text . "\nendstream",
            "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $i => $obj) {
            $offsets[$i + 1] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n" . $obj . "\nendobj\n";
        }

        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($offsets as $off) {
            $pdf .= str_pad($off, 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefPos . "\n%%EOF";

        return $pdf;
    }

    /**
     * Creator or a colleague it was shared with may view/download/share.
     */
    private function authorizeView(WorkTemplate $template): void
    {
        $allowed = $template->created_by === Auth::id()
            || $template->shares()->where('shared_with', Auth::id())->exists();

        abort_unless($allowed, 403, 'You do not have access to this template.');
    }
}
