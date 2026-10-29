<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Templates | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; color: #1e293b; }

        main { width: 90%; max-width: 1200px; margin: 30px auto; padding-bottom: 50px; }

        .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; flex-wrap: wrap; margin-bottom: 20px; }
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
        .btn.view { background: #0f766e; }
        .btn.edit { background: #1d9d50; }
        .btn.delete { background: #b91c1c; }

        .alert { padding: 12px; border-radius: 7px; margin-bottom: 18px; }
        .alert.success { background: #dcfce7; color: #166534; }

        .panel { background: linear-gradient(150deg, #f7fcf9 0%, #ffffff 45%, #eef8f2 100%); border: 1px solid #e2efe8; padding: 24px; border-radius: 12px; box-shadow: 0 4px 16px rgba(15, 23, 42, .07); margin-bottom: 26px; }
        .panel h3 { margin: 0 0 14px; }

        table { width: 100%; border-collapse: collapse; min-width: 760px; }
        th, td { text-align: left; padding: 13px 12px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f0fdf4; color: #14532d; font-size: 12px; text-transform: uppercase; }

        .actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .actions form { display: inline; }

        .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; background: #dcfce7; color: #14532d; font-size: 12px; font-weight: bold; }
        .format-chip { display: inline-block; padding: 3px 9px; border-radius: 6px; background: #eef2ff; color: #3730a3; font-size: 11px; font-weight: bold; text-transform: uppercase; }

        .empty { text-align: center; padding: 30px; color: #64748b; }

        dialog::backdrop { background: rgba(4, 40, 24, .45); }
        dialog { border: 0; border-radius: 12px; padding: 26px; max-width: 520px; width: 92%; }
        dialog .recipients { max-height: 180px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 7px; padding: 10px; margin: 14px 0; }
        dialog .recipients label { font-weight: normal; display: flex; align-items: center; gap: 8px; padding: 4px 0; font-size: 14px; }
    </style>
</head>
<body>
    @include('partials.nav')

    <main>
        <div class="topbar">
            <div>
                <h2>Work Templates</h2>
                <p>Type documents in the full-screen editor, save as Word / PDF / HTML / Text, view, download, share with colleagues — work from anywhere.</p>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a class="btn" href="{{ route('work-templates.create') }}">📝 New Document</a>
                <a class="btn secondary" href="{{ route('dashboard') }}">← Back to Dashboard</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <section class="panel">
            <h3>Saved Templates</h3>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Format</th>
                        <th>Preview</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $template)
                        <tr>
                            <td><span class="badge">{{ $template->title }}</span></td>
                            <td><span class="format-chip">{{ $template->file_format }}</span></td>
                            <td style="max-width:240px;color:#475569;">{{ Str::limit(strip_tags($template->content), 60) }}</td>
                            <td>{{ $template->creator->name ?? '—' }}</td>
                            <td>{{ $template->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn view" href="{{ route('work-templates.show', $template) }}">View</a>
                                    <a class="btn" href="{{ route('work-templates.download', $template) }}">Download</a>
                                    <a class="btn edit" href="{{ route('work-templates.edit', $template) }}">Edit</a>
                                    <button class="btn" type="button" style="background:#0f766e;"
                                            onclick="openShare({{ $template->id }}, '{{ addslashes($template->title) }}')">Share</button>
                                    <form method="POST" action="{{ route('work-templates.destroy', $template) }}"
                                          onsubmit="return confirm('Delete this template and its file?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn delete" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">No templates yet. Click "New Document" to open the editor.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>

    <dialog id="shareDialog">
        <h3 id="shareTitle">Share Template</h3>
        <p style="color:#64748b;font-size:13px;margin:0;">Select colleagues who should receive this template.</p>
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
        function openShare(templateId, title) {
            const form = document.getElementById('shareForm');
            form.action = '/work-templates/' + templateId + '/share';
            document.getElementById('shareTitle').textContent = 'Share: ' + title;
            document.getElementById('shareDialog').showModal();
        }
    </script>
</body>
</html>
