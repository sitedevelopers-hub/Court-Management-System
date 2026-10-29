<?php

namespace App\Http\Controllers;

use App\Models\GeneratedReport;
use App\Models\ReportShare;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $reports = GeneratedReport::with(['creator', 'shares'])
            ->latest()
            ->paginate(10);

        $colleagues = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('reports.index', compact('reports', 'colleagues'));
    }

    /**
     * Generate a report file for the chosen date range and save it.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'report_type'=> ['required', 'string', 'max:60'],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $start = $validated['start_date'] ?? now()->startOfMonth()->toDateString();
        $end   = $validated['end_date'] ?? now()->endOfMonth()->toDateString();

        // Collect the summary data for the selected range.
        $summary = $this->buildSummary($start, $end);

        // Build a simple, portable Word-compatible document (HTML .doc).
        $filename = 'report-' . now()->format('YmdHis') . '-' . Str::random(6) . '.doc';
        $html = $this->renderReportHtml($validated['title'], $validated['report_type'], $start, $end, $summary);

        $path = 'reports/' . $filename;
        Storage::disk('local')->put($path, $html);

        $report = GeneratedReport::create([
            'title'       => $validated['title'],
            'report_type' => $validated['report_type'],
            'start_date'  => $start,
            'end_date'    => $end,
            'file_path'   => $path,
            'created_by'  => Auth::id(),
        ]);

        return redirect()
            ->route('reports.index')
            ->with('success', "Report \"{$report->title}\" generated and saved. You can now download or share it.");
    }

    public function download(GeneratedReport $report)
    {
        abort_unless(Storage::disk('local')->exists($report->file_path), 404, 'Report file not found.');

        return Storage::disk('local')->download(
            $report->file_path,
            str_replace(' ', '_', $report->title) . '.doc'
        );
    }

    /**
     * Share a generated report with one or more colleagues (in-system email).
     */
    public function share(Request $request, GeneratedReport $report)
    {
        $validated = $request->validate([
            'recipients'   => ['required', 'array', 'min:1'],
            'recipients.*' => ['integer', 'exists:users,id', 'distinct'],
            'note'         => ['nullable', 'string', 'max:500'],
        ]);

        $recipients = User::whereIn('id', $validated['recipients'])->get();

        foreach ($recipients as $recipient) {
            // Record the share...
            ReportShare::firstOrCreate([
                'generated_report_id' => $report->id,
                'shared_with'         => $recipient->id,
            ], [
                'note'      => $validated['note'] ?? null,
                'shared_at' => now(),
            ]);

            // ...and email it as an in-system notification.
            $recipient->notify(new \App\Notifications\ReportSharedNotification($report, Auth::user(), $validated['note'] ?? null));
        }

        return redirect()
            ->route('reports.index')
            ->with('success', 'Report shared with ' . $recipients->count() . ' colleague(s) via email.');
    }

    public function destroy(GeneratedReport $report)
    {
        // Remove the file, then the record.
        if ($report->file_path && Storage::disk('local')->exists($report->file_path)) {
            Storage::disk('local')->delete($report->file_path);
        }

        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Report deleted.');
    }

    private function buildSummary(string $start, string $end): array
    {
        return DB::transaction(function () use ($start, $end) {
            return [
                'total_cases'       => \App\Models\CourtCase::count(),
                'active_cases'      => \App\Models\CourtCase::where('filing_status', 'active')->count(),
                'total_payments'    => (float) \App\Models\Payment::where('status', 'completed')->whereBetween('paid_at', [$start, $end])->sum('amount'),
                'total_fees'        => (float) \App\Models\CaseFee::sum('amount'),
                'outstanding'       => (float) \App\Models\CaseFee::get()->sum(fn ($f) => max(0, (float)$f->amount - (float)$f->amount_paid)),
                'bonds_ordered'     => (float) \App\Models\BailBond::sum('ordered_amount'),
                'bonds_deposited'   => (float) \App\Models\BailBond::sum('deposited_amount'),
                'hearings_range'    => \App\Models\Hearing::whereBetween('scheduled_start', [$start, $end])->count(),
            ];
        });
    }

    private function renderReportHtml(string $title, string $type, string $start, string $end, array $s): string
    {
        $rows = [
            'Total cases'        => number_format($s['total_cases']),
            'Active cases'       => number_format($s['active_cases']),
            'Hearings in range'  => number_format($s['hearings_range']),
            'Total fees raised'  => 'KES ' . number_format($s['total_fees'], 2),
            'Payments received'  => 'KES ' . number_format($s['total_payments'], 2),
            'Outstanding fees'   => 'KES ' . number_format($s['outstanding'], 2),
            'Bail ordered'       => 'KES ' . number_format($s['bonds_ordered'], 2),
            'Bail deposited'     => 'KES ' . number_format($s['bonds_deposited'], 2),
        ];

        $htmlRows = '';
        foreach ($rows as $label => $value) {
            $htmlRows .= "<tr><td style='padding:8px;border:1px solid #ddd;'>{$label}</td><td style='padding:8px;border:1px solid #ddd;'><b>{$value}</b></td></tr>";
        }

        $generatedBy = Auth::user()?->name ?? 'System';
        $generatedAt = now()->format('d M Y, H:i');
        $typeLabel = ucfirst($type);

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>{$title}</title></head>
<body style="font-family: Arial, sans-serif;">
    <h1 style="color:#14532d;">{$title}</h1>
    <p><b>Type:</b> {$typeLabel}</p>
    <p><b>Period:</b> {$start} to {$end}</p>
    <p><b>Generated:</b> {$generatedAt} by {$generatedBy}</p>
    <table style="border-collapse:collapse;min-width:480px;">
        {$htmlRows}
    </table>
    <p style="color:#777;margin-top:24px;">Court Management System — auto-generated report.</p>
</body>
</html>
HTML;
    }
}
