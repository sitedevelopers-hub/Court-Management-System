@php
    $navItems = [
        ['route' => 'dashboard',                    'label' => 'Dashboard',    'icon' => '⌂',  'color' => '#16a34a'],
        ['route' => 'cases.index',                  'label' => 'Cases',        'icon' => '📁', 'color' => '#2563eb'],
        ['route' => 'participants.index',           'label' => 'Participants', 'icon' => '👤', 'color' => '#9333ea'],
        ['route' => 'hearings.index',               'label' => 'Hearings',     'icon' => '⚖', 'color' => '#d97706'],
        ['route' => 'courtrooms.index',             'label' => 'Courtrooms',   'icon' => '🏛', 'color' => '#0891b2'],
        ['route' => 'court-events.index',           'label' => 'Events',       'icon' => '📅', 'color' => '#e11d48'],
        ['route' => 'case-documents.index',         'label' => 'Documents',    'icon' => '📄', 'color' => '#ca8a04'],
        ['route' => 'case-fees.index',              'label' => 'Fees',         'icon' => '💰', 'color' => '#059669'],
        ['route' => 'payments.index',               'label' => 'Payments',     'icon' => '🧾', 'color' => '#4f46e5'],
        ['route' => 'bail-bonds.index',             'label' => 'Bail',         'icon' => '🔗', 'color' => '#b45309'],
        ['route' => 'financial-adjustments.index',  'label' => 'Adjustments',  'icon' => '📊', 'color' => '#0d9488'],
        ['route' => 'financial-reports.index',      'label' => 'Reports',      'icon' => '📈', 'color' => '#7c3aed'],
        ['route' => 'work-templates.index',         'label' => 'Templates',    'icon' => '📝', 'color' => '#0d9488'],
    ];

    // User Management lives on the admin page — only admins see it.
    if (auth()->check() && auth()->user()->roles()->where('name', 'admin')->exists()) {
        $navItems[] = ['route' => 'user-management.index', 'label' => 'Admin · Users', 'icon' => '🛡', 'color' => '#dc2626'];
    }

    $activeUser = auth()->user();
@endphp

<aside class="cms-sidebar">
    <div class="cms-brand">
        <img class="cms-logo-img" src="{{ asset('images/logo.png') }}" alt="Court MS logo">
        <span class="cms-brand-text">Court<b>MS</b></span>
        <span class="cms-brand-sub">Court Records & Scheduling</span>
    </div>

    <nav class="cms-menu">
        @foreach ($navItems as $item)
            <a href="{{ route($item['route']) }}"
               @class(['cms-link' => true, 'active' => request()->routeIs($item['route'] . '*')])>
                <span class="cms-icon" style="background: {{ $item['color'] }}18; color: {{ $item['color'] }};">{{ $item['icon'] }}</span>
                <span class="cms-label">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="cms-sidebar-footer">
        <div class="cms-profile">
            <span class="cms-avatar">{{ strtoupper(substr($activeUser?->name ?? 'U', 0, 1)) }}</span>
            <span class="cms-profile-name">
                {{ $activeUser?->name ?? 'Guest' }}
                <small>{{ $activeUser?->roles->firstWhere('name','admin') ? 'Administrator' : 'User' }}</small>
            </span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="cms-logout">⎋ Logout</button>
        </form>
    </div>
</aside>

<style>
    /* ===== Sidebar layout (light, p2-style) ===== */
    body {
        padding-left: 248px;
    }

    .cms-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 258px;
        color: #e6f4ea;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        z-index: 200;
        box-shadow: 6px 0 26px rgba(10, 46, 28, .28);
        background: linear-gradient(180deg, #0b3d2e 0%, #14532d 45%, #1d7a46 100%);
        border-right: 1px solid rgba(255, 255, 255, .08);
    }

    /* ===== Brand: bigger logo + stacked title ===== */
    .cms-brand {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        padding: 26px 18px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, .12);
        background: rgba(255, 255, 255, .03);
    }

    .cms-logo-img {
        width: 84px;
        height: 84px;
        border-radius: 20px;
        object-fit: cover;
        box-shadow: 0 8px 22px rgba(4, 26, 16, .4), 0 0 0 3px rgba(255, 255, 255, .14);
    }

    /* Logo stays completely still — no hover movement. */

    .cms-brand-text {
        font-size: 21px;
        color: #ffffff;
        letter-spacing: .4px;
        font-weight: 600;
        text-align: center;
    }

    .cms-brand-text b { color: #fde68a; font-weight: 800; }

    .cms-brand-sub {
        font-size: 11.5px;
        color: #a7d9bf;
        letter-spacing: .6px;
        text-transform: uppercase;
        font-weight: 600;
        white-space: nowrap;
        text-align: center;
        width: 100%;
        line-height: 1.2;
    }

    .cms-menu {
        flex: 1;
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .cms-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 13px;
        border-radius: 12px;
        color: #ffffff;
        text-decoration: none;
        font-size: 15px;
        font-weight: 600;
        letter-spacing: .2px;
        background: linear-gradient(90deg, rgba(255,255,255,.10), rgba(255,255,255,.04));
        border: 1px solid rgba(255,255,255,.12);
        transition: background .18s ease, color .18s ease, transform .14s ease, box-shadow .18s ease;
    }

    .cms-link:hover {
        background: linear-gradient(90deg, rgba(255,255,255,.20), rgba(255,255,255,.08));
        color: #ffffff;
        transform: translateX(3px);
        box-shadow: 0 4px 14px rgba(0,0,0,.18);
    }

    .cms-icon {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 17px;
        flex-shrink: 0;
        background: rgba(255, 255, 255, .18);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.18);
    }

    .cms-link.active {
        background: linear-gradient(90deg, rgba(253,230,138,.38), rgba(253,230,138,.10));
        color: #ffffff;
        font-weight: 800;
        box-shadow: inset 0 0 0 1px rgba(253,230,138,.55), 0 4px 12px rgba(0,0,0,.15);
    }

    .cms-sidebar-footer {
        padding: 14px 14px 18px;
        border-top: 1px solid rgba(255, 255, 255, .12);
    }

    .cms-profile {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px 12px;
    }

    .cms-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .16);
        border: 2px solid rgba(253, 230, 138, .45);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        flex-shrink: 0;
    }

    .cms-profile-name {
        font-size: 13.5px;
        font-weight: 600;
        color: #ffffff;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .cms-profile-name small {
        display: block;
        font-size: 11.5px;
        font-weight: 500;
        color: #a7d9bf;
    }

    .cms-logout {
        width: 100%;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid rgba(253, 230, 138, .55);
        background: linear-gradient(90deg, #fde68a, #fcd34d);
        color: #4a3200;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: filter .2s ease, transform .14s ease, box-shadow .2s ease;
    }

    .cms-logout:hover {
        filter: brightness(1.08);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(252, 211, 77, .35);
    }

    /* ===== Mobile: sidebar becomes a top bar ===== */
    @media (max-width: 900px) {
        body { padding-left: 0; padding-top: 56px; }

        .cms-sidebar {
            width: 100%;
            height: 56px;
            bottom: auto;
            flex-direction: row;
            align-items: center;
            overflow-x: auto;
            overflow-y: hidden;
        }

        .cms-brand { padding: 0 12px; flex-direction: row; }
        .cms-logo-img { width: 32px; height: 32px; border-radius: 8px; }
        .cms-brand-text, .cms-brand-sub { display: none; }

        .cms-menu {
            flex-direction: row;
            padding: 0 6px;
            gap: 2px;
        }

        .cms-link { padding: 7px 10px; white-space: nowrap; font-size: 12.5px; border-radius: 8px; }
        .cms-icon { width: 24px; height: 24px; font-size: 12px; }
        .cms-profile { display: none; }

        .cms-sidebar-footer {
            padding: 0 10px;
            border-top: 0;
            border-left: 1px solid rgba(255, 255, 255, .12);
            margin-left: auto;
        }

        .cms-logout { padding: 7px 10px; font-size: 12px; }
    }

    /* ===== GLOBAL THEME — applies to every page that includes this partial ===== */
    body {
        font-family: 'Segoe UI', 'Inter', system-ui, sans-serif;
        font-size: 15.5px;
        color: #1f2937;
        background:
            radial-gradient(1100px 600px at 10% -6%, rgba(29, 157, 80, .22), transparent 55%),
            radial-gradient(900px 560px at 96% 10%, rgba(13, 84, 60, .16), transparent 58%),
            radial-gradient(800px 520px at 50% 112%, rgba(63, 178, 118, .18), transparent 60%),
            linear-gradient(155deg, #dff2e6 0%, #cfe9db 40%, #d8efe2 68%, #e6f5ec 100%);
        background-attachment: fixed;
    }

    main {
        max-width: 1240px;
        margin: 0 auto;
        padding: 40px 36px 64px;
    }

    h1, h2, h3 { color: #14532d; letter-spacing: .1px; }

    /* Cards and panels get a clearly visible green gradient */
    .panel, .card, .module, .stat {
        background: linear-gradient(150deg, #d7f0e2 0%, #eefaf3 40%, #c9e8d8 100%);
        border: 1px solid #b7ddc9;
        border-radius: 16px;
        box-shadow: 0 8px 22px rgba(20, 83, 45, .12);
    }

    .panel:hover, .card:hover {
        box-shadow: 0 12px 30px rgba(20, 83, 45, .18);
        transition: box-shadow .18s ease;
    }

    table { border-collapse: collapse; }

    th {
        background: #f0f8f3;
        color: #14532d;
        text-transform: uppercase;
        font-size: 12.5px;
        letter-spacing: .4px;
    }

    td { border-bottom: 1px solid #e8f2ec; font-size: 14.5px; }

    tr:hover td { background: #f4faf6; }

    a.btn, button.btn, .btn {
        border-radius: 10px;
        font-weight: 600;
        transition: transform .13s ease, box-shadow .18s ease, background .18s ease;
    }

    a.btn:hover, button.btn:hover, .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(20, 83, 45, .16);
    }

    .delete-btn, .danger {
        background: #c73a2b;
        color: #fff;
    }

    .delete-btn:hover, .danger:hover {
        background: #a32c20;
    }

    .back-btn {
        display: inline-block;
        padding: 10px 18px;
        background: rgba(255, 255, 255, .9);
        border: 1px solid #cfe8d9;
        color: #14532d;
        text-decoration: none;
    }

    .back-btn:hover {
        background: #14532d;
        color: #fff;
    }

    input, select, textarea {
        border-radius: 9px;
        border: 1px solid #cfe3d8;
        font-family: inherit;
        font-size: 14.5px;
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: #1d9d50;
        box-shadow: 0 0 0 3px rgba(29, 157, 80, .13);
    }

    .pagination a, .pagination span {
        border-radius: 8px;
        margin: 0 2px;
    }

    .pagination .current {
        background: #14532d;
        color: #fff;
        border-color: #14532d;
    }
</style>
