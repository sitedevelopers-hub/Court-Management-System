<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Document | Court Management System</title>
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
        .hint { color: #64748b; font-size: 13px; margin-top: 6px; }
        .error { color: #b91c1c; font-size: 14px; margin-top: 5px; }
        .notice {
            padding: 13px;
            margin-bottom: 22px;
            border-radius: 8px;
            background: #eff6ff;
            color: #1e40af;
            font-size: 14px;
            line-height: 1.5;
        }
        .actions { display: flex; gap: 12px; margin-top: 10px; }
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
    </style>
</head>
<body>
<div class="container">
    <h1>Upload Court Document</h1>
    <p class="subtitle">Attach a document to a case and classify its confidentiality.</p>

    <div class="notice">
        All file types are accepted — PDF, Word, images, spreadsheets and more.
        Choose the correct confidentiality level according to your court's policies.
    </div>

    @if ($errors->any())
        <div class="error" style="margin-bottom: 18px;">
            Please correct the errors below and try again.
        </div>
    @endif

    <form action="{{ route('case-documents.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid">
            <div class="field full">
                <label for="document_title">Document Title *</label>
                <input id="document_title" name="document_title" type="text"
                       value="{{ old('document_title') }}" required maxlength="255"
                       placeholder="e.g. Witness Statement">
                @error('document_title') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field full">
                <label for="case_id">Linked Case (optional)</label>
                <select id="case_id" name="case_id">
                    <option value="">No case linked</option>
                    @foreach ($cases as $case)
                        <option value="{{ $case->id }}"
                            @selected(old('case_id') == $case->id)>
                            {{ $case->case_number }} — {{ $case->case_title }}
                        </option>
                    @endforeach
                </select>
                @error('case_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="category">Document Category *</label>
                <select id="category" name="category" required onchange="toggleOther()">
                    <option value="">Select category</option>
                    <option value="pleading" @selected(old('category') === 'pleading')>Pleading</option>
                    <option value="judgment" @selected(old('category') === 'judgment')>Judgment</option>
                    <option value="photograph" @selected(old('category') === 'photograph')>Photograph</option>
                    <option value="witness_statement" @selected(old('category') === 'witness_statement')>Witness Statement</option>
                    <option value="evidence" @selected(old('category') === 'evidence')>Evidence</option>
                    <option value="correspondence" @selected(old('category') === 'correspondence')>Correspondence</option>
                    <option value="other" @selected(old('category') === 'other')>Other (describe below)</option>
                </select>
                @error('category') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field" id="otherCategoryWrap" style="display:none;">
                <label for="category_other">Describe the category *</label>
                <input id="category_other" name="category_other" type="text"
                       value="{{ old('category_other') }}" maxlength="255"
                       placeholder="e.g. Affidavit, Charter, Internal memo...">
                @error('category_other') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="confidentiality">Confidentiality *</label>
                <select id="confidentiality" name="confidentiality" required>
                    <option value="restricted" @selected(old('confidentiality', 'restricted') === 'restricted')>Restricted</option>
                    <option value="confidential" @selected(old('confidentiality') === 'confidential')>Confidential</option>
                    <option value="internal" @selected(old('confidentiality') === 'internal')>Internal</option>
                </select>
                @error('confidentiality') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field full">
                <label for="document">Choose File *</label>
                <input id="document" name="document" type="file" required>
                <p class="hint">Any file type is accepted. Do not upload files you are not authorized to store.</p>
                @error('document') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field full">
                <label for="description">Description / Notes</label>
                <textarea id="description" name="description"
                          maxlength="5000"
                          placeholder="Add relevant details about this document...">{{ old('description') }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="actions">
            <button class="btn primary" type="submit">Upload Document</button>
            <a class="btn secondary" href="{{ route('case-documents.index') }}">Cancel</a>
        </div>
    </form>
</div>

<script>
    function toggleOther() {
        const wrap = document.getElementById('otherCategoryWrap');
        const input = document.getElementById('category_other');
        const show = document.getElementById('category').value === 'other';
        wrap.style.display = show ? 'block' : 'none';
        input.required = show;
        if (!show) input.value = '';
    }
    // Restore visibility on back-navigation
    document.addEventListener('DOMContentLoaded', toggleOther);
</script>
</body>
</html>
