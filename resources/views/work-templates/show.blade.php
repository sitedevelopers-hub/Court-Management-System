<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $template->title }} — Court MS</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #eef2f0; min-height: 100vh; }

    .word-bar {
        background: #14532d;
        color: #fff;
        padding: 10px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 10px rgba(10, 40, 24, .3);
        position: sticky;
        top: 0;
        z-index: 20;
        flex-wrap: wrap;
    }

    .word-bar .doc-title { font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px; flex: 1; min-width: 200px; }
    .word-bar .doc-title span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .btn-light {
        background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.28);
        color: #fff; padding: 8px 16px; border-radius: 8px; font-size: 13.5px;
        font-weight: 600; cursor: pointer; transition: background .15s ease;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-light:hover { background: rgba(255,255,255,.28); }

    .btn-save {
        background: linear-gradient(90deg, #fde68a, #fcd34d);
        border: 1px solid rgba(252,211,77,.8); color: #4a3200;
        padding: 8px 20px; border-radius: 8px; font-size: 13.5px; font-weight: 700;
        cursor: pointer; transition: filter .15s ease, transform .12s ease;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-save:hover { filter: brightness(1.07); transform: translateY(-1px); }

    .page-wrap { display: flex; justify-content: center; padding: 30px 16px 60px; }
    .doc-page {
        background: #fff; width: 100%; max-width: 850px;
        padding: 70px 80px; border-radius: 4px;
        box-shadow: 0 10px 36px rgba(15,43,32,.16); border-top: 6px solid #14532d;
    }
    .doc-page .doc-heading {
        font-size: 26px; font-weight: 700; color: #14532d;
        margin-bottom: 24px; padding-bottom: 12px;
        border-bottom: 2px solid #e3ede7; font-family: Georgia, serif;
    }
    .doc-body {
        font-size: 15px; line-height: 1.9; color: #26323a;
        font-family: 'Times New Roman', Georgia, serif;
        white-space: pre-wrap;
    }
    .doc-meta {
        margin-top: 36px; padding-top: 16px; border-top: 1px solid #eef3f0;
        font-size: 12.5px; color: #8aa79a;
    }

    .meta-strip { max-width: 850px; margin: 0 auto 18px; display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
    .meta-chip {
        background: #fff; border: 1px solid #dfe9e3; border-radius: 999px;
        padding: 6px 14px; font-size: 12.5px; color: #4b6b5c; font-weight: 600;
    }
</style>
</head>
<body>

<div class="word-bar">
    <div class="doc-title">📄 <span>{{ $template->title }}</span></div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        @if ($template->created_by === auth()->id())
            <a class="btn-light" href="{{ route('work-templates.edit', $template) }}">✏️ Edit</a>
        @endif
        <a class="btn-save" href="{{ route('work-templates.download', $template) }}">⬇ Download</a>
        <a class="btn-light" href="{{ route('work-templates.index') }}">✕ Close</a>
    </div>
</div>

<div class="meta-strip">
    <span class="meta-chip">Format: {{ strtoupper($template->file_format) }}</span>
    <span class="meta-chip">By: {{ $template->creator->name ?? '—' }}</span>
    <span class="meta-chip">Created: {{ $template->created_at->format('d M Y, H:i') }}</span>
</div>

<div class="page-wrap">
    <div class="doc-page">
        <div class="doc-heading">{{ $template->title }}</div>
        <div class="doc-body">{{ $template->content }}</div>
        <div class="doc-meta">Saved in Court Management System — Work Templates</div>
    </div>
</div>

</body>
</html>
