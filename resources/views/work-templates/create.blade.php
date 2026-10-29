<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>New Work Template — Court MS</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #eef2f0; min-height: 100vh; }

    /* ===== Word-like title bar ===== */
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
    }

    .word-bar .doc-title {
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        min-width: 0;
    }

    .word-bar .doc-title span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .word-bar input[type="text"] {
        border: 0;
        background: rgba(255, 255, 255, .14);
        color: #fff;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        min-width: 220px;
        outline: none;
        flex: 1;
    }

    .word-bar input[type="text"]::placeholder { color: rgba(255, 255, 255, .55); font-weight: 500; }

    .word-bar .bar-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

    .word-bar select {
        border: 1px solid rgba(255, 255, 255, .3);
        background: rgba(255, 255, 255, .14);
        color: #fff;
        padding: 7px 10px;
        border-radius: 8px;
        font-size: 13px;
        cursor: pointer;
        outline: none;
    }

    .word-bar select option { color: #14532d; }

    .btn-light {
        background: rgba(255, 255, 255, .16);
        border: 1px solid rgba(255, 255, 255, .28);
        color: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-light:hover { background: rgba(255, 255, 255, .28); }

    .btn-save {
        background: linear-gradient(90deg, #fde68a, #fcd34d);
        border: 1px solid rgba(252, 211, 77, .8);
        color: #4a3200;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: filter .15s ease, transform .12s ease;
    }

    .btn-save:hover { filter: brightness(1.07); transform: translateY(-1px); }

    /* ===== Formatting toolbar (Word ribbon style) ===== */
    .ribbon {
        background: #fff;
        border-bottom: 1px solid #dde6e1;
        padding: 8px 18px;
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        position: sticky;
        top: 54px;
        z-index: 15;
        box-shadow: 0 2px 8px rgba(15, 43, 32, .06);
    }

    .ribbon button, .ribbon select {
        border: 1px solid #d7e3dc;
        background: #f8fbf9;
        color: #2d4a3e;
        padding: 6px 11px;
        border-radius: 7px;
        font-size: 13px;
        cursor: pointer;
        transition: background .12s ease, border-color .12s ease;
    }

    .ribbon button:hover, .ribbon select:hover { background: #e9f5ee; border-color: #1d9d50; }

    .ribbon .sep { width: 1px; height: 24px; background: #dde6e1; }

    .ribbon .font-size { width: 62px; }

    /* ===== Document page ===== */
    .page-wrap {
        display: flex;
        justify-content: center;
        padding: 30px 16px 60px;
    }

    .doc-page {
        background: #fff;
        width: 100%;
        max-width: 850px;
        min-height: 1000px;
        padding: 70px 80px;
        border-radius: 4px;
        box-shadow: 0 10px 36px rgba(15, 43, 32, .16);
        border-top: 6px solid #14532d;
    }

    .doc-title-input {
        width: 100%;
        border: 0;
        outline: none;
        font-size: 26px;
        font-weight: 700;
        color: #14532d;
        margin-bottom: 24px;
        padding: 0 0 12px;
        border-bottom: 2px solid #e3ede7;
        font-family: Georgia, serif;
    }

    #content {
        width: 100%;
        min-height: 760px;
        border: 0;
        outline: none;
        resize: none;
        font-size: 15px;
        line-height: 1.85;
        color: #26323a;
        font-family: 'Times New Roman', Georgia, serif;
    }

    /* Status / hints */
    .hint {
        max-width: 850px;
        margin: 0 auto;
        padding: 0 4px 24px;
        font-size: 13px;
        color: #6b8a7c;
        text-align: center;
    }

    .saved-toast {
        position: fixed;
        bottom: 26px;
        left: 50%;
        transform: translateX(-50%) translateY(80px);
        background: #14532d;
        color: #d9f7e5;
        padding: 11px 22px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 10px 28px rgba(10, 40, 24, .35);
        opacity: 0;
        transition: opacity .3s ease, transform .3s ease;
        z-index: 50;
    }

    .saved-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

    /* Mobile */
    @media (max-width: 640px) {
        .doc-page { padding: 34px 22px; min-height: 640px; }
        .word-bar { flex-wrap: wrap; gap: 8px; }
        .word-bar input[type="text"] { min-width: 140px; }
        .ribbon { position: static; }
    }
</style>
</head>
<body>

<form method="POST" action="{{ route('work-templates.store') }}">
    @csrf

    <!-- Word-like top bar -->
    <div class="word-bar">
        <a class="btn-light" href="{{ route('dashboard') }}" title="Back to Dashboard">← Dashboard</a>

        <div class="doc-title">📝 <span>Untitled Work Template</span></div>

        <input type="text" id="titleInput" name="title" placeholder="Document title…" autofocus>

        <div class="bar-actions">
            <select name="file_format" title="Save file as" required>
                <option value="docx">Word Document (.docx)</option>
                <option value="doc">Word 97-2003 (.doc)</option>
                <option value="pdf">PDF (.pdf)</option>
                <option value="html">Web Page (.html)</option>
                <option value="txt">Plain Text (.txt)</option>
            </select>
            <a class="btn-light" href="{{ route('work-templates.index') }}">✕ Close</a>
            <button type="submit" class="btn-save">💾 Save Document</button>
        </div>
    </div>
    <!-- Formatting ribbon -->
    <div class="ribbon">
        @if ($errors->any())
            <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:7px;font-size:13px;font-weight:600;">
                ⚠ {{ $errors->first() }}
            </span>
        @endif
        <button type="button" data-cmd="bold" title="Bold (Ctrl+B)"><b>B</b></button>
        <button type="button" data-cmd="italic" title="Italic (Ctrl+I)"><i>I</i></button>
        <button type="button" data-cmd="underline" title="Underline (Ctrl+U)"><u>U</u></button>
        <span class="sep"></span>
        <select data-cmd="fontName" title="Font">
            <option value="Times New Roman">Times New Roman</option>
            <option value="Arial">Arial</option>
            <option value="Calibri">Calibri</option>
            <option value="Georgia">Georgia</option>
            <option value="Courier New">Courier New</option>
        </select>
        <select data-cmd="fontSize" class="font-size" title="Font size">
            <option value="2">Small</option>
            <option value="3" selected>Normal</option>
            <option value="4">Large</option>
            <option value="5">Larger</option>
            <option value="6">Huge</option>
        </select>
        <span class="sep"></span>
        <button type="button" data-cmd="justifyLeft" title="Align left">⬅</button>
        <button type="button" data-cmd="justifyCenter" title="Center">↔</button>
        <button type="button" data-cmd="justifyRight" title="Align right">➡</button>
        <span class="sep"></span>
        <button type="button" data-cmd="insertUnorderedList" title="Bullet list">• List</button>
        <button type="button" data-cmd="insertOrderedList" title="Numbered list">1. List</button>
    </div>

    <!-- The "paper" -->
    <div class="page-wrap">
        <div class="doc-page">
            <input class="doc-title-input" type="text" id="pageTitle" placeholder="Document title…" aria-label="Document title">

            <div id="content" contenteditable="true" data-placeholder="Start typing your document…"></div>
            <!-- The contenteditable div above is synced into this hidden field on submit,
                 because contenteditable elements are not submitted with forms. -->
            <input type="hidden" name="content" id="contentField">
        </div>
    </div>

    <p class="hint">Formatting applies to selected text · Ctrl+B/I/U shortcuts work · Saved as your chosen format and downloadable from the templates list.</p>
</form>

<div class="saved-toast" id="toast">💾 Saving…</div>

<script>
    const content = document.getElementById('content');
    const titleInput = document.getElementById('titleInput');
    const pageTitle = document.getElementById('pageTitle');
    const docTitleLabel = document.querySelector('.doc-title span');
    const toast = document.getElementById('toast');

    // Keep the sidebar label, hidden input and page heading in sync.
    function syncTitle(src) {
        const v = src.value.trim();
        titleInput.value = v;
        pageTitle.value = v;
        docTitleLabel.textContent = v || 'Untitled Work Template';
    }
    titleInput.addEventListener('input', () => syncTitle(titleInput));
    pageTitle.addEventListener('input', () => syncTitle(pageTitle));

    // Placeholder for contenteditable
    const style = document.createElement('style');
    style.textContent = '[contenteditable][data-placeholder]:empty:before { content: attr(data-placeholder); color: #9fb5aa; font-style: italic; }';
    document.head.appendChild(style);

    // Word-ribbon commands
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

    // Show a toast while the form posts (feedback before redirect).
    document.querySelector('form').addEventListener('submit', () => {
        // Copy the rich-text content into the hidden field so it is actually
        // submitted — without this the save fails validation and the
        // document never appears in the Saved Templates list.
        document.getElementById('contentField').value = content.innerHTML;
        toast.classList.add('show');
    });
</script>

</body>
</html>
