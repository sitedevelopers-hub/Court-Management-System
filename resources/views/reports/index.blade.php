<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(160deg, #eef7f1 0%, #e3f0e8 50%, #dcebe1 100%);
            color: #1e293b;
            min-height: 100vh;
        }

        header {
            background: linear-gradient(120deg, #0b3d2c, #14532d);
            color: white;
            padding: 22px 5%;
        }
        header h1 { margin: 0 0 6px; font-size: 24px; }
        header p { margin: 0; color: #d1fae5; }

        main { width: 90%; max-width: 1200px; margin: 30px auto; padding-bottom: 50px; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .topbar h2 { margin: 0; }
        .topbar p { margin: 4px 0 0; color: #475569; }

        .btn {
            display: inline-block;
            padding: 11px 16px;
            border: 0;
            border-radius: 7px;
            background: #14532d;
            color: white;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }
        .btn.secondary { background: #e2e8f0; color: #334155; }

        .alert {
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 18px;
        }
        .alert.success { background: #dcfce7; color: #166534; }

        .panel {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, .07);
            margin-bottom: 26px;
        }
        .panel h3 { margin: 0 0 14px; }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }
        label { font-weight: bold; font-size: 13px; display: block; margin-bottom: 6px; }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font: inherit;
        }

        table { width: 100%; border-collapse: collapse; min-width: 720px; }
        th, td { text-align: left; padding: 13px 12px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f0fdf4; color: #14532d; font-size: 12px; text-transform: uppercase; }

        .actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .actions form { display: inline; }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            background: #dcfce7;
            color: #14532d;
            font-size: 12px;
            font-weight: bold;
        }

        .empty { text-align: center; padding: 30px; color: #64748b; }
        .pagination { margin-top: 18px; }

        dialog::backdrop { background: rgba(4, 40, 24, .45); }
        dialog {
            border: 0;
            border-radius: 12px;
            padding: 26px;
            max-width: 520px;
            width: 92%;
        }
        dialog h3 { margin: 0 0 6px; }
        dialog .recipients { max-height: 180px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 7px; padding: 10px; margin: 14px 0; }
        dialog .recipients label { font-weight: normal; display: flex; align-items: center; gap: 8px; padding: 4px 0; font-size: 14px; }

        @media (max-width: 600px) {
            main { width: 94%; }
            .panel { padding: 16px; }
        }
    </style>
</head>
<body>
    <header>
        <h1>Court Management System</h1>
        <p>Generated Reports — create, download and share</p>
    </header>

    <main>
        <div class="topbar">
            <div>
                <h2>Reports</h2>
                <p>Generate reports for a chosen period, download them, and share via email with colleagues.</p>
            </div>
            <a class="btn secondary" href="{{ route('dashboard') }}">← Back to Dashboard</a>
        </div>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <section class="panel">
            <h3>Generate a New Report</h3>
            <form action="{{ route('reports.generate') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div>
                        <label for="title">Report Title *</label>
                        <input id="title" name="title" required maxlength="255" placeholder="e.g. Monthly Court Revenue Report">
                    </div>
                    <div>
                        <label for="report_type">Report Type *</label>
                        <select id="report_type" name="report_type" required>
                            <option value="">Select type</option>
                            @foreach (['caseload', 'financial', 'hearings', 'bail', 'full'] as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date">
                    </div>
                    <div>
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date">
                    </div>
                </div>
                <div style="margin-top: 18px;">
                    <button class="btn" type="submit">Generate Report</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <h3>Saved Reports</h3>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Period</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td><span class="badge">{{ $report->title }}</span></td>
                            <td>{{ ucfirst($report->report_type) }}</td>
                            <td>{{ $report->start_date }} → {{ $report->end_date }}</td>
                            <td>{{ $report->creator->name ?? '—' }}</td>
                            <td>{{ $report->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn" href="{{ route('reports.download', $report) }}">Download</a>
                                    <button class="btn" type="button" style="background:#0f766e;"
                                            onclick="openShare({{ $report->id }}, '{{ addslashes($report->title) }}')">Share</button>
                                    <form method="POST" action="{{ route('reports.destroy', $report) }}"
                                          onsubmit="return confirm('Delete this report and its file?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn" type="submit" style="background:#b91c1c;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">No reports generated yet. Use the form above to create your first report.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination">{{ $reports->links() }}</div>
        </section>
    </main>

    <dialog id="shareDialog">
        <h3 id="shareTitle">Share Report</h3>
        <p style="color:#64748b;font-size:13px;margin:0;">Select colleagues to receive this report by email.</p>
        <form id="shareForm" method="POST">
            @csrf
            <div class="recipients">
                @foreach ($colleagues as $colleague)
                    <label>
                        <input type="checkbox" name="recipients[]" value="{{ $colleague->id }}">
                        {{ $colleague->name }} ({{ $colleague->email }})
                    </label>
                @endforeach
                @if ($colleagues->isEmpty())
                    <p style="color:#64748b;">No other users registered yet.</p>
                @endif
            </div>
            <label for="note">Optional note</label>
            <textarea id="note" name="note" rows="2" maxlength="500" placeholder="Add a short message..."></textarea>
            <div style="display:flex;gap:10px;margin-top:16px;">
                <button class="btn" type="submit">Send Share</button>
                <button class="btn secondary" type="button" onclick="document.getElementById('shareDialog').close()">Cancel</button>
            </div>
        </form>
    </dialog>

    <script>
        function openShare(reportId, title) {
            const form = document.getElementById('shareForm');
            form.action = '/reports/' + reportId + '/share';
            document.getElementById('shareTitle').textContent = 'Share: ' + title;
            document.getElementById('shareDialog').showModal();
        }
    </script>
</body>
</html>
