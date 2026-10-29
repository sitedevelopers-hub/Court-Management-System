<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Details | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
            font-family: Arial, sans-serif;
        }
        .container { max-width: 950px; margin: auto; }
        .card {
            background: white;
            padding: 28px;
            border-radius: 14px;
            box-shadow: 0 5px 20px #0f172a12;
        }
        h1 { margin-top: 0; overflow-wrap: anywhere; }
        .subtitle { color: #64748b; margin-bottom: 24px; }
        .notice, .success {
            padding: 13px;
            margin-bottom: 18px;
            border-radius: 8px;
            background: #eff6ff;
            color: #1e40af;
        }
        .success { background: #dcfce7; color: #166534; }
        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }
        .item {
            padding: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            min-width: 0;
        }
        .label {
            display: block;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .value { overflow-wrap: anywhere; line-height: 1.5; }
        .full { grid-column: 1 / -1; }
        .actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 25px; }
        .btn {
            display: inline-block;
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }
        .primary { background: #1f6f43; color: white; }
        .secondary { background: #e2e8f0; color: #1e293b; }
        @media (max-width: 600px) {
            .details { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
            .card { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>{{ $document->document_title }}</h1>
        <p class="subtitle">Document information and linked case details.</p>

        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div class="notice">
            This page displays document metadata. The file itself is stored privately
            and should only be accessed by authorized users.
        </div>

        <div class="details">
            <div class="item">
                <span class="label">Document Title</span>
                <div class="value">{{ $document->document_title }}</div>
            </div>

            <div class="item">
                <span class="label">Category</span>
                <div class="value">
                    {{ ucwords(str_replace('_', ' ', $document->category)) }}
                </div>
            </div>

            <div class="item">
                <span class="label">Case Number</span>
                <div class="value">
                    {{ $document->courtCase?->case_number ?? 'Case unavailable' }}
                </div>
            </div>

            <div class="item">
                <span class="label">Case Title</span>
                <div class="value">
                    {{ $document->courtCase?->case_title ?? 'Not available' }}
                </div>
            </div>

            <div class="item">
                <span class="label">Original Filename</span>
                <div class="value">{{ $document->original_filename }}</div>
            </div>

            <div class="item">
                <span class="label">File Size</span>
                <div class="value">
                    {{ $document->file_size ? number_format($document->file_size / 1024, 2) . ' KB' : 'Unknown' }}
                </div>
            </div>

            <div class="item">
                <span class="label">Confidentiality</span>
                <div class="value">{{ ucfirst($document->confidentiality) }}</div>
            </div>

            <div class="item">
                <span class="label">Uploaded By</span>
                <div class="value">{{ $document->uploader?->name ?? 'System' }}</div>
            </div>

            <div class="item">
                <span class="label">Uploaded At</span>
                <div class="value">{{ $document->created_at?->format('d M Y, h:i A') }}</div>
            </div>

            <div class="item">
                <span class="label">Last Updated</span>
                <div class="value">{{ $document->updated_at?->format('d M Y, h:i A') }}</div>
            </div>

            <div class="item full">
                <span class="label">Description / Notes</span>
                <div class="value">
                    {{ $document->description ?: 'No description provided.' }}
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('case-documents.download', $document) }}"
               class="btn primary">Download File</a>

            <a href="{{ route('case-documents.edit', $document) }}"
               class="btn secondary">Edit Details</a>

            <a href="{{ route('case-documents.index') }}"
               class="btn secondary">Back to Documents</a>
        </div>
    </div>
</div>
</body>
</html>
