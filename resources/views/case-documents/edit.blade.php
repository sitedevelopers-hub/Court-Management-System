<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Document | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 850px;
            margin: auto;
            padding: 30px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 5px 20px #0f172a12;
        }
        h1 { margin-top: 0; color: #0f172a; }
        .subtitle { color: #64748b; margin-bottom: 25px; }
        .notice {
            padding: 13px;
            margin-bottom: 22px;
            border-radius: 8px;
            background: #eff6ff;
            color: #1e40af;
            line-height: 1.5;
            font-size: 14px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }
        .field { margin-bottom: 18px; }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font: inherit;
        }
        input:focus, select:focus, textarea:focus {
            outline: 2px solid #93c5fd;
            border-color: #2563eb;
        }
        textarea { min-height: 100px; resize: vertical; }
        .full { grid-column: 1 / -1; }
        .error { color: #b91c1c; font-size: 14px; margin-top: 5px; }
        .actions { display: flex; gap: 12px; margin-top: 10px; flex-wrap: wrap; }
        .btn {
            display: inline-block;
            padding: 12px 18px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
        }
        .primary { background: #1f6f43; color: white; }
        .secondary { background: #e2e8f0; color: #1e293b; }
        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
            .container { padding: 20px; }
        }
    
        .delete-btn { background: #dc2626; color: #fff; border: none; padding: 10px 16px; border-radius: 7px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .delete-btn:hover { background: #b91c1c; }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit Document Details</h1>
    <p class="subtitle">Update the document metadata and classification.</p>

    <div class="notice">
        Editing these details will not replace the stored file. To replace the
        actual document, a separate secure replacement workflow is needed.
    </div>

    @if ($errors->any())
        <div class="error" style="margin-bottom: 18px;">
            Please correct the errors below and try again.
        </div>
    @endif

    <form action="{{ route('case-documents.update', $document) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid">
            <div class="field full">
                <label for="document_title">Document Title *</label>
                <input id="document_title" name="document_title" type="text"
                       value="{{ old('document_title', $document->document_title) }}"
                       maxlength="255" required>
                @error('document_title') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field full">
                <label for="case_id">Linked Case *</label>
                <select id="case_id" name="case_id" required>
                    <option value="">Select a case</option>
                    @foreach ($cases as $case)
                        <option value="{{ $case->id }}"
                            @selected(old('case_id', $document->case_id) == $case->id)>
                            {{ $case->case_number }} — {{ $case->case_title }}
                        </option>
                    @endforeach
                </select>
                @error('case_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="category">Document Category *</label>
                <select id="category" name="category" required>
                    @foreach ([
                        'pleading' => 'Pleading',
                        'judgment' => 'Judgment',
                        'photograph' => 'Photograph',
                        'witness_statement' => 'Witness Statement',
                        'evidence' => 'Evidence',
                        'correspondence' => 'Correspondence',
                        'other' => 'Other'
                    ] as $value => $label)
                        <option value="{{ $value }}"
                            @selected(old('category', $document->category) === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('category') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="confidentiality">Confidentiality *</label>
                <select id="confidentiality" name="confidentiality" required>
                    @foreach ([
                        'restricted' => 'Restricted',
                        'confidential' => 'Confidential',
                        'internal' => 'Internal'
                    ] as $value => $label)
                        <option value="{{ $value }}"
                            @selected(old('confidentiality', $document->confidentiality) === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('confidentiality') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field full">
                <label>Current File</label>
                <input type="text" value="{{ $document->original_filename }}" disabled>
                <p class="subtitle">The current file remains unchanged.</p>
            </div>

            <div class="field full">
                <label for="description">Description / Notes</label>
                <textarea id="description" name="description"
                          maxlength="5000">{{ old('description', $document->description) }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="actions">
            <button class="btn primary" type="submit">Save Changes</button>
            <a class="btn secondary"
               href="{{ route('case-documents.show', $document) }}">Cancel</a>
            <form action="{{ route('case-documents.destroy', $document) }}" method="POST"
                  onsubmit="return confirm('Delete this document record? This action cannot be undone.')"
                  style="display:inline">
                @csrf
                @method('DELETE')
                <button class="delete-btn" type="submit">Delete Document</button>
            </form>
        </div>
    </form>
</div>
</body>
</html>
