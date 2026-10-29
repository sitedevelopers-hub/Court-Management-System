<?php

namespace App\Http\Controllers;

use App\Models\CaseDocument;
use App\Models\CourtCase;
use App\Models\DocumentAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CaseDocumentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $documents = CaseDocument::with(['courtCase', 'uploader'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('document_title', 'like', "%{$search}%")
                        ->orWhere('original_filename', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhereHas('courtCase', function ($caseQuery) use ($search) {
                            $caseQuery->where('case_number', 'like', "%{$search}%")
                                ->orWhere('case_title', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('case-documents.index', compact('documents', 'search'));
    }

    public function create()
    {
        $cases = CourtCase::orderBy('case_number')->get();

        return view('case-documents.create', compact('cases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => ['nullable', 'exists:cases,id'],
            'document_title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in([
                'pleading',
                'judgment',
                'photograph',
                'witness_statement',
                'evidence',
                'correspondence',
                'other',
            ])],
            'category_other' => ['nullable', 'string', 'max:255', 'required_if:category,other'],
            'document' => [
                'required',
                'file',
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'confidentiality' => ['required', Rule::in([
                'restricted',
                'confidential',
                'internal',
            ])],
        ]);

        // Free-text detail for "Other" is appended to the description so the
        // user's own wording is always captured and searchable.
        if (($validated['category'] ?? null) === 'other' && !empty($validated['category_other'])) {
            $validated['description'] = trim('[' . $validated['category_other'] . '] ' . ($validated['description'] ?? ''));
        }
        unset($validated['category_other']);

        $file = $request->file('document');
        $storedFilename = Str::uuid() . '.' . $file->extension();
        $directory = 'case-documents/' . ($validated['case_id'] ?? 'unlinked');

        $path = $file->storeAs($directory, $storedFilename, 'local');

        if (!$path) {
            return back()->withInput()->withErrors([
                'document' => 'The file could not be saved. Please try again.',
            ]);
        }

        try {
            $document = CaseDocument::create([
                'case_id' => $validated['case_id'] ?? null,
                'uploaded_by' => auth()->id(),
                'document_title' => $validated['document_title'],
                'category' => $validated['category'],
                'original_filename' => $file->getClientOriginalName(),
                'stored_filename' => $storedFilename,
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'description' => $validated['description'] ?? null,
                'confidentiality' => $validated['confidentiality'],
            ]);

            $this->logAccess($document, 'uploaded');
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        return redirect()
            ->route('case-documents.show', $document)
            ->with('success', 'Document uploaded securely.');
    }

    public function show(CaseDocument $case_document)
    {
        $case_document->load(['courtCase', 'uploader']);

        $this->logAccess($case_document, 'viewed');

        return view('case-documents.show', [
            'document' => $case_document,
        ]);
    }

    public function edit(CaseDocument $case_document)
    {
        $cases = CourtCase::orderBy('case_number')->get();

        return view('case-documents.edit', [
            'document' => $case_document,
            'cases' => $cases,
        ]);
    }

    public function update(Request $request, CaseDocument $case_document)
    {
        $validated = $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'document_title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in([
                'pleading',
                'judgment',
                'photograph',
                'witness_statement',
                'evidence',
                'correspondence',
                'other',
            ])],
            'description' => ['nullable', 'string', 'max:5000'],
            'confidentiality' => ['required', Rule::in([
                'restricted',
                'confidential',
                'internal',
            ])],
        ]);

        $case_document->update($validated);

        $this->logAccess($case_document, 'updated');

        return redirect()
            ->route('case-documents.show', $case_document)
            ->with('success', 'Document details updated successfully.');
    }

    public function destroy(CaseDocument $case_document)
    {
        // Remove the physical file from storage if it exists.
        if ($case_document->storage_path && Storage::disk('local')->exists($case_document->storage_path)) {
            Storage::disk('local')->delete($case_document->storage_path);
        }

        $case_document->delete();

        $this->logAccess($case_document, 'deleted');

        return redirect()
            ->route('case-documents.index')
            ->with('success', 'Document and its file deleted permanently.');
    }

    public function download(CaseDocument $case_document)
    {
        $path = $case_document->storage_path;

        abort_unless(Storage::disk('local')->exists($path), 404, 'Document file not found.');

        $this->logAccess($case_document, 'downloaded');

        return Storage::disk('local')->download(
            $path,
            $case_document->original_filename
        );
    }

    private function logAccess(CaseDocument $document, string $action): void
    {
        DocumentAccessLog::create([
            'case_document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'ip_address' => request()->ip(),
            'accessed_at' => now(),
        ]);
    }
}
