<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit: {{ $template->title }} — Court MS</title>
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

    .word-bar .doc-title { font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .word-bar input[type="text"] {
        border: 0; background: rgba(255,255,255,.14); color: #fff;
        padding: 8px 12px; border-radius: 8px; font-size: 14px; font-weight: 600;
        min-width: 220px; outline: none; flex: 1;
    }
    .word-bar .bar-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .word-bar select {
        border: 1px solid rgba(255,255,255,.3); background: rgba(255,255,255,.14);
        color: #fff; padding: 7px 10px; border-radius: 8px; font-size: 13px; cursor: pointer; outline: none;
    }
    .word-bar select option { color: #14532d; }

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
    }
    .btn-save:hover { filter: brightness(1.07); transform: translateY(-1px); }

    .ribbon {
        background: #fff; border-bottom: 1px solid #dde6e1; padding: 8px 18px;
        display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
        position: sticky; top: 54px; z-index: 15; box-shadow: 0 2px 8px rgba(15,43,32,.06);
    }
    .ribbon button, .ribbon select {
        border: 1px solid #d7e3dc; background: #f8fbf9; color: #2d4a3e;
        padding: 6px 11px; border-radius: 7px; font-size: 13px; cursor: pointer;
        transition: background .12s ease, border-color .12s ease;
    }
    .ribbon button:hover, .ribbon select:hover { background: #e9f5ee; border-color: #1d9d50; }
    .ribbon .sep { width: 1px; height: 24px; background: #dde6e1; }
    .ribbon .font-size { width: 62px; }

    .page-wrap { display: flex; justify-content: center; padding: 30px 16px 60px; }
    .doc-page {
        background: #fff; width: 100%; max-width: 850px; min-height: 1000px;
        padding: 70px 80px; border-radius: 4px;
        box-shadow: 0 10px 36px rgba(15,43,32,.16); border-top: 6px solid #14532d;
    }
    .doc-title-input {
        width: 100%; border: 0; outline: none; font-size: 26px; font-weight: 700;
        color: #14532d; margin-bottom: 24px; padding: 0 0 12px;
        border-bottom: 2px solid #e3ede7; font-family: Georgia, serif;
    }
    #content {
        width: 100%; min-height: 760px; border: 0; outline: none; resize: none;
        font-size: 15px; line-height: 1.85; color: #26323a; font-family: 'Times New Roman', Georgia, serif;
    }
    .hint { max-width: 850px; margin: 0 auto; padding: 0 4px 24px; font-size: 13px; color: #6b8a7c; text-align: center; }

    @media (max-width: 640px) {
        .doc-page { padding: 34px 22px; min-height: 640px; }
        .ribbon { position: static; }
    }
</style>
</head>
<body>

<form method="POST" action="{{ route('work-templates.update', $template) }}">
    @csrf
    @method('PUT')

    <div class="word-bar">
        <a class="btn-light" href="{{ route('dashboard') }}" title="Back to Dashboard">← Dashboard</a>
        <div class="doc-title">✏️ <span>Editing: {{ $template->title }}</span></div>
        <input type="text" id="titleInput" name="title" value="{{ old('title', $template->title) }}">
        <div class="bar-actions">
            <select name="file_format" title="Save file as">
                @foreach (['docx' => 'Word Document (.docx)', 'doc' => 'Word 97-2003 (.doc)', 'pdf' => 'PDF (.pdf)', 'html' => 'Web Page (.html)', 'txt' => 'Plain Text (.txt)'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('file_format', $template->file_format) === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <a class="btn-light" href="{{ route('work-templates.show', $template) }}">✕ Close</a>
            <button type="submit" class="btn-save">💾 Update Document</button>
        </div>
    </div>

    <div class="ribbon">
        <button type="button" data-cmd="bold"><b>B</b></button>
        <button type="button" data-cmd="italic"><i>I</i></button>
        <button type="button" data-cmd="underline"><u>U</u></button>
        <span class="sep"></span>
        <select data-cmd="fontName">
            @foreach (['Times New Roman', 'Arial', 'Calibri', 'Georgia', 'Courier New'] as $font)
                <option value="{{ $font }}">{{ $font }}</option>
            @endforeach
        </select>
        <select data-cmd="fontSize" class="font-size">
            @foreach ([2 => 'Small', 3 => 'Normal', 4 => 'Large', 5 => 'Larger', 6 => 'Huge'] as $val => $label)
                <option value="{{ $val }}" @selected($val == 3)>{{ $label }}</option>
            @endforeach
        </select>
        <span class="sep"></span>
        <button type="button" data-cmd="justifyLeft">⬅</button>
        <button type="button" data-cmd="justifyCenter">↔</button>
        <button type="button" data-cmd="justifyRight">➡</button>
        <span class="sep"></span>
        <button type="button" data-cmd="insertUnorderedList">• List</button>
        <button type="button" data-cmd="insertOrderedList">1. List</button>
    </div>

    <div class="page-wrap">
        <div class="doc-page">
            <input class="doc-title-input" type="text" id="pageTitle" value="{{ old('title', $template->title) }}">

            <div id="content" contenteditable="true">{{ old('content', $template->content) }}</div>
            <input type="hidden" name="content" id="contentField">
        </div>
    </div>

    <p class="hint">Existing content is loaded — edit freely and save. The stored file is regenerated in the chosen format.</p>
</form>

<script>
    const content = document.getElementById('content');
    const titleInput = document.getElementById('titleInput');
    const pageTitle = document.getElementById('pageTitle');

    function syncTitle(src) {
        const v = src.value.trim();
        titleInput.value = v;
        pageTitle.value = v;
    }
    titleInput.addEventListener('input', () => syncTitle(titleInput));
    pageTitle.addEventListener('input', () => syncTitle(pageTitle));

    document.querySelectorAll('.ribbon [data-cmd]').forEach(el => {
        const cmd = el.dataset.cmd;
        if (el.tagName === 'SELECT') {
            el.addEventListener('change', () => {
                document.execCommand(cmd, false, el.value);
                content.focus();
            });
        } else {
            el.addEventListener('mousedown', e => e.preventDefault());
            el.addEventListener('click', () => {
                document.execCommand(cmd, false, null);
                content.focus();
            });
        }
    });

    // Copy the rich-text content into the hidden field on submit —
    // contenteditable elements are not submitted with forms on their own.
    document.querySelector('form').addEventListener('submit', () => {
        document.getElementById('contentField').value = content.innerHTML;
    });
</script>

</body>
</html>
