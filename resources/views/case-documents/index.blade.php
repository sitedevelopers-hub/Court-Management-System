<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents & Evidence | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
            font-family: Arial, sans-serif;
        }
        .container { max-width: 1250px; margin: auto; }
        .backbar { margin-bottom: 14px; }
        .backtop {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 10px;
            background: linear-gradient(90deg, #f8fdfa, #e3f4ea);
            border: 1px solid #b7ddc9;
            color: #14532d;
            font-weight: 700;
            font-size: 13.5px;
            text-decoration: none;
            box-shadow: 0 3px 10px rgba(20, 83, 45, .10);
            transition: background .18s ease, color .18s ease, transform .13s ease;
        }
        .backtop:hover {
            background: linear-gradient(90deg, #14532d, #1d7a46);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(20, 83, 45, .25);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        h1 { margin: 0 0 8px; color: #0f172a; }
        .subtitle { margin: 0; color: #64748b; }
        .btn {
            display: inline-block;
            padding: 11px 16px;
            border-radius: 9px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: transform .13s ease, box-shadow .15s ease;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(20, 83, 45, .18); }
        .primary { background: linear-gradient(90deg, #14532d, #1d7a46); color: white; }
        .secondary { background: #ffffff; color: #14532d; border: 1px solid #b7ddc9; }
        .danger { background: #fee2e2; color: #991b1b; }
        .success {
            background: #dcfce7;
            color: #166534;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 18px;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 18px;
        }
        .panel {
            background: linear-gradient(150deg, #d7f0e2 0%, #eefaf3 40%, #c9e8d8 100%);
            border: 1px solid #b7ddc9;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 8px 22px rgba(20, 83, 45, .12);
        }
        .search {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search input {
            flex: 1;
            min-width: 0;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
        }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 850px; }
        th, td {
            text-align: left;
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        th {
            color: #14532d;
            font-size: 12px;
            text-transform: uppercase;
            background: #d9f0e3;
        }
        .muted { color: #64748b; font-size: 13px; }
        .tag {
            display: inline-block;
            padding: 5px 9px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .actions { display: flex; flex-wrap: wrap; gap: 7px; }
        .empty { text-align: center; padding: 35px; color: #64748b; }
        .pagination { margin-top: 20px; }
        @media (max-width: 600px) {
            body { padding: 14px; }
            .panel { padding: 14px; }
            .search { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="backbar">
        <a href="{{ route('dashboard') }}" class="backtop">← Back to Dashboard</a>
    </div>
    <div class="header">
        <div>
            <h1>Documents & Evidence</h1>
            <p class="subtitle">Securely manage documents linked to court cases.</p>
        </div>
        <a href="{{ route('case-documents.create') }}" class="btn primary">
            + Upload Document
        </a>
    </div>

    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <div class="panel">
        <form method="GET" action="{{ route('case-documents.index') }}" class="search">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Search title, filename, category or case number...">
            <button type="submit" class="btn primary">Search</button>
            @if ($search !== '')
                <a href="{{ route('case-documents.index') }}" class="btn secondary">Clear</a>
            @endif
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Document</th>
                    <th>Case</th>
                    <th>Category</th>
                    <th>Confidentiality</th>
                    <th>Uploaded By</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($documents as $document)
                    <tr>
                        <td>
                            <strong>{{ $document->document_title }}</strong>
                            <div class="muted">{{ $document->original_filename }}</div>
                        </td>
                        <td>
                            {{ $document->courtCase?->case_number ?? 'Case unavailable' }}
                            <div class="muted">
                                {{ $document->courtCase?->case_title ?? '' }}
                            </div>
                        </td>
                        <td>
                            <span class="tag">
                                {{ ucwords(str_replace('_', ' ', $document->category)) }}
                            </span>
                        </td>
                        <td>{{ ucfirst($document->confidentiality) }}</td>
                        <td>{{ $document->uploader?->name ?? 'System' }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn secondary"
                                   href="{{ route('case-documents.show', $document) }}">View</a>
                                <a class="btn secondary"
                                   href="{{ route('case-documents.edit', $document) }}">Edit</a>
                                <a class="btn primary"
                                   href="{{ route('case-documents.download', $document) }}">Download</a>
                                <form action="{{ route('case-documents.destroy', $document) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this document record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">
                            No documents found. Upload a document to get started.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $documents->links() }}
        </div>
    </div>
</div>
</body>
</html>
