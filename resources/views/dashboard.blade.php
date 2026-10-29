<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Court Management System | Dashboard</title>
    <style>
        * { box-sizing: border-box; }

        main {
            max-width: 1280px;
            margin: 0 auto;
            padding: 34px 34px 64px;
        }

        /* ===== Top row: welcome banner + right column ===== */
        .dash-top {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            margin-bottom: 28px;
            align-items: stretch;
        }

        /* Welcome banner (p2 style — soft light panel) */
        .hero {
            background: linear-gradient(120deg, #cdeedb 0%, #e6f7ee 55%, #d3efe1 100%);
            border: 1px solid #b7ddc9;
            border-radius: 20px;
            padding: 34px 38px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .hero::before {
            content: "";
            position: absolute;
            right: -60px;
            top: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(29, 157, 80, .18), transparent 70%);
        }

        .hero h1 {
            margin: 0 0 10px;
            font-size: 28px;
            font-weight: 700;
            color: #14532d;
        }

        .hero p {
            margin: 0 0 16px;
            font-size: 15.5px;
            color: #5b7a6b;
            max-width: 560px;
            line-height: 1.6;
        }

        .hero-date {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 15px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #d9ede3;
            font-size: 13.5px;
            color: #14532d;
            font-weight: 600;
            align-self: flex-start;
        }

        /* Templates quick-action button (fadish yellow) */
        .hero .template-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            align-self: flex-start;
            margin-top: 14px;
            padding: 10px 20px;
            border-radius: 999px;
            background: linear-gradient(90deg, #fde68a, #fcd34d);
            border: 1px solid rgba(252, 211, 77, .7);
            color: #4a3200;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(252, 211, 77, .3);
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
        }

        .hero .template-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.06);
            box-shadow: 0 8px 20px rgba(252, 211, 77, .45);
        }

        /* Scroll-in animation for cards */
        .reveal {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity .55s ease, transform .55s ease;
        }

        .reveal.shown {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            .reveal { opacity: 1; transform: none; transition: none; }
        }

        /* ===== Right column: profile + reminders ===== */
        .side-col {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .profile-card {
            background: linear-gradient(135deg, #14532d, #1d7a46);
            color: #fff;
            border-radius: 20px;
            padding: 22px;
            box-shadow: 0 10px 26px rgba(11, 61, 46, .22);
        }

        .profile-card .p-row {
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .profile-card .p-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .16);
            border: 2px solid rgba(255, 255, 255, .35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 19px;
            flex-shrink: 0;
        }

        .profile-card .p-name {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .profile-card .p-role {
            font-size: 12.5px;
            color: #c4ecd5;
        }

        .profile-card .p-divider {
            height: 1px;
            background: rgba(255, 255, 255, .16);
            margin: 15px 0;
        }

        .profile-card .p-stats {
            display: flex;
            gap: 10px;
        }

        .profile-card .p-stat {
            flex: 1;
            background: rgba(255, 255, 255, .10);
            border-radius: 12px;
            padding: 9px 6px;
            text-align: center;
        }

        .profile-card .p-stat strong {
            display: block;
            font-size: 19px;
        }

        .profile-card .p-stat small {
            font-size: 11px;
            color: #c4ecd5;
        }

        /* Reminder card with pulsing dot */
        .reminders {
            background: #ffffff;
            border: 1px solid #e2efe8;
            border-radius: 20px;
            padding: 18px 20px;
            box-shadow: 0 8px 22px rgba(20, 83, 45, .07);
            max-height: 300px;
            overflow-y: auto;
            flex: 1;
        }

        .reminders h3 {
            margin: 0 0 12px;
            font-size: 14.5px;
            color: #14532d;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .reminders .dot {
            width: 10px;
            height: 10px;
            background: #ef4444;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, .5);
            animation: pulse 1.8s infinite;
            flex-shrink: 0;
        }

        @keyframes pulse {
            0%   { box-shadow: 0 0 0 0 rgba(239, 68, 68, .55); }
            70%  { box-shadow: 0 0 0 9px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .reminders ul { margin: 0; padding: 0; list-style: none; }

        .reminders li {
            padding: 9px 0;
            border-bottom: 1px solid #eef5f1;
            font-size: 13.5px;
            color: #334155;
            line-height: 1.55;
        }

        .reminders li:last-child { border-bottom: 0; }

        .reminders .r-type {
            display: inline-block;
            background: #e7f7ee;
            color: #14532d;
            padding: 2px 9px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 600;
            margin-right: 6px;
        }

        .reminders .r-court {
            color: #64748b;
            font-size: 12.5px;
            display: block;
        }

        .reminders .empty-rem {
            color: #64748b;
            font-size: 13.5px;
            margin: 0;
        }

        /* Recent activity list styling */
        .module .r-item {
            display: block;
            padding: 4px 0;
            font-size: 13.5px;
            color: #334155;
            border-bottom: 1px dashed #eef5f1;
        }

        .module .r-item:last-child { border-bottom: 0; }

        .module .r-empty {
            display: block;
            font-size: 13.5px;
            color: #94a3b8;
        }

        /* ===== Stat cards — soft tinted, p2-style ===== */
        .section-title {
            font-size: 19px;
            font-weight: 700;
            color: #14532d;
            margin: 0 0 16px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 34px;
        }

        .stat {
            border-radius: 16px;
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            border: 1px solid transparent;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 26px rgba(20, 83, 45, .12);
        }

        .stat.s-green   { background: linear-gradient(135deg, #c9efdc, #eafaf1); border-color: #a8dcbd; }
        .stat.s-blue    { background: linear-gradient(135deg, #cfe2fa, #eef4fe); border-color: #b0c8ef; }
        .stat.s-purple  { background: linear-gradient(135deg, #e6d8f7, #f6effd); border-color: #cfb8e8; }
        .stat.s-amber   { background: linear-gradient(135deg, #fbe6c8, #fdf4e6); border-color: #ecd2a6; }

        .stat .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .stat.s-green  .stat-icon { background: #16a34a22; }
        .stat.s-blue   .stat-icon { background: #2563eb22; }
        .stat.s-purple .stat-icon { background: #9333ea22; }
        .stat.s-amber  .stat-icon { background: #d9770622; }

        .stat h3 {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: #5b7a6b;
            text-transform: uppercase;
            letter-spacing: .7px;
        }

        .stat strong {
            font-size: 34px;
            font-weight: 700;
            color: #14532d;
            line-height: 1;
        }

        /* ===== Modules ===== */
        .modules {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 18px;
        }

        .module {
            background: linear-gradient(150deg, #d7f0e2 0%, #eefaf3 40%, #c9e8d8 100%);
            border: 1px solid #b7ddc9;
            border-radius: 16px;
            padding: 24px 26px;
            box-shadow: 0 6px 18px rgba(20, 83, 45, .10);
            display: flex;
            flex-direction: column;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .module:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(20, 83, 45, .12);
            border-color: #bfe3cf;
        }

        .module-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 10px;
        }

        .module h3 {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
            color: #14532d;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .module-arrows { display: flex; gap: 6px; }

        .nav-arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #f0f8f3;
            color: #14532d;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            border: 1px solid #d9ede3;
            transition: background .15s ease, color .15s ease, transform .15s ease;
            flex-shrink: 0;
        }

        .nav-arrow:hover {
            background: #14532d;
            color: #d9f7e5;
            transform: scale(1.12);
        }

        .module p {
            margin: 0 0 18px;
            color: #5b7a6b;
            line-height: 1.6;
            font-size: 14.5px;
            flex: 1;
        }

        a.link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            align-self: flex-start;
            background: linear-gradient(90deg, #15803d, #1d9d50);
            color: #fff;
            padding: 10px 18px;
            font-size: 14.5px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 10px;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        a.link .link-arrow {
            opacity: 0;
            transform: translateX(-4px);
            transition: opacity .2s ease, transform .2s ease;
        }

        a.link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(21, 128, 61, .3);
        }

        a.link:hover .link-arrow { opacity: 1; transform: translateX(0); }

        footer {
            text-align: center;
            padding: 30px 20px 44px;
            color: #6b8a7c;
            font-size: 14px;
        }

        @media (max-width: 1000px) {
            .dash-top { grid-template-columns: 1fr; }
            main { padding: 24px 20px 48px; }
            .hero h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
    @include('partials.nav')

<main>
    @php
        // Time-aware greeting: morning / afternoon / evening (server timezone)
        $now = now();
        $hour = (int) $now->format('G');
        $minute = (int) $now->format('i');

        if ($hour < 12 || ($hour === 12 && $minute === 0)) {
            $greeting = 'Good morning';
        } elseif ($hour < 17) {
            $greeting = 'Good afternoon';
        } else {
            $greeting = 'Good evening';
        }
        $firstName = explode(' ', trim(auth()->user()->name ?? 'User'))[0];
        $isAdmin = auth()->user()->roles->contains(fn ($r) => $r->name === 'admin');

        // Upcoming scheduled hearings (next 14 days) for the reminder dot
        $upcomingHearings = \App\Models\Hearing::with(['courtCase', 'courtroom'])
            ->where('status', 'scheduled')
            ->whereBetween('scheduled_start', [now()->startOfDay(), now()->addDays(14)->endOfDay()])
            ->orderBy('scheduled_start')
            ->limit(6)
            ->get();
    @endphp

    <div class="dash-top">
        <section class="hero">
            <h1>{{ $greeting }}, {{ $firstName }} 👋</h1>
            <p>Welcome back to the Court Management System. Manage cases, hearings, participants, documents and finances — all from one place.</p>
            <div class="hero-meta">
                <span class="hero-date">📅 {{ date('l, j F Y') }}</span>
                <a class="template-btn" href="{{ route('work-templates.index') }}">📝 Open Work Templates</a>
            </div>
        </section>

        <div class="side-col">
            <div class="profile-card">
                <div class="p-row">
                    <span class="p-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                    <div>
                        <div class="p-name">{{ auth()->user()->name }}</div>
                        <div class="p-role">{{ $isAdmin ? 'Administrator' : 'Registered User' }}</div>
                    </div>
                </div>
                <div class="p-divider"></div>
                <div class="p-stats">
                    <div class="p-stat">
                        <strong>{{ \App\Models\CourtCase::count() }}</strong>
                        <small>Cases</small>
                    </div>
                    <div class="p-stat">
                        <strong>{{ \App\Models\Hearing::where('status', 'scheduled')->count() }}</strong>
                        <small>Hearings</small>
                    </div>
                    <div class="p-stat">
                        <strong>{{ $upcomingHearings->count() }}</strong>
                        <small>Upcoming</small>
                    </div>
                    <div class="p-stat">
                        <strong>{{ \App\Models\Participant::count() }}</strong>
                        <small>People</small>
                    </div>
                </div>
            </div>

            <div class="reminders" id="hearing-reminders">
                <h3><span class="dot"></span> Hearing Reminders ({{ $upcomingHearings->count() }})</h3>
                @if ($upcomingHearings->isNotEmpty())
                    <ul>
                        @foreach ($upcomingHearings as $h)
                            <li>
                                <span class="r-type">{{ ucwords(str_replace('_', ' ', $h->hearing_type)) }}</span>
                                {{ $h->scheduled_start->format('d M Y, H:i') }}
                                <span class="r-court">
                                    {{ $h->courtroom?->room_name ?? $h->courtroom?->room_number ?? 'Courtroom TBA' }}
                                    · {{ $h->courtCase?->case_number ?? 'Case TBA' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="empty-rem">No hearings scheduled in the next 14 days.</p>
                @endif
            </div>
        </div>
    </div>

    <h2 class="section-title">Overview</h2>

    <section class="stats">
        <div class="stat s-green">
            <span class="stat-icon">📁</span>
            <h3>Total Cases</h3>
            <strong>{{ \App\Models\CourtCase::count() }}</strong>
        </div>

        <div class="stat s-blue">
            <span class="stat-icon">⚖</span>
            <h3>Active Cases</h3>
            <strong>{{ \App\Models\CourtCase::where('filing_status', 'active')->count() }}</strong>
        </div>

        <div class="stat s-purple">
            <span class="stat-icon">👤</span>
            <h3>Participants</h3>
            <strong>{{ \App\Models\Participant::count() }}</strong>
        </div>

        <div class="stat s-amber">
            <span class="stat-icon">📅</span>
            <h3>Scheduled Hearings</h3>
            <strong>{{ \App\Models\Hearing::where('status', 'scheduled')->count() }}</strong>
        </div>
    </section>

    <h2 class="section-title">Recent Activity</h2>

    <section class="modules">
        <div class="module">
            <div class="module-top">
                <h3>📁 Recently Registered Cases</h3>
            </div>
            <p>
                @forelse (\App\Models\CourtCase::latest()->take(4)->get() as $recent)
                    <span class="r-item">{{ $recent->case_number }} — {{ $recent->case_title }}</span>
                @empty
                    <span class="r-empty">No cases registered yet.</span>
                @endforelse
            </p>
            <a class="link" href="{{ route('cases.index') }}">View all cases <span class="link-arrow">→</span></a>
        </div>

        <div class="module">
            <div class="module-top">
                <h3>📄 Recently Uploaded Documents</h3>
            </div>
            <p>
                @forelse (\App\Models\CaseDocument::latest()->take(4)->get() as $recent)
                    <span class="r-item">{{ $recent->document_title }} — {{ $recent->courtCase?->case_number ?? 'No case' }}</span>
                @empty
                    <span class="r-empty">No documents uploaded yet.</span>
                @endforelse
            </p>
            <a class="link" href="{{ route('case-documents.index') }}">View all documents <span class="link-arrow">→</span></a>
        </div>

        <div class="module">
            <div class="module-top">
                <h3>✏️ Recently Updated Records</h3>
            </div>
            <p>
                @forelse (\App\Models\Participant::latest('updated_at')->take(4)->get() as $recent)
                    <span class="r-item">{{ $recent->full_name }} — updated {{ $recent->updated_at?->diffForHumans() }}</span>
                @empty
                    <span class="r-empty">No participant updates yet.</span>
                @endforelse
            </p>
            <a class="link" href="{{ route('participants.index') }}">View all participants <span class="link-arrow">→</span></a>
        </div>
    </section>

    <h2 class="section-title">System Modules</h2>

    @php
        $modules = [
            ['route' => 'cases.index',                 'icon' => '📁',  'title' => 'Case Management',       'desc' => 'Register, view, update and delete court cases.', 'label' => 'Manage Cases'],
            ['route' => 'participants.index',          'icon' => '👤',  'title' => 'Participants',          'desc' => 'Manage judges, advocates, clerks, parties and witnesses.', 'label' => 'Manage Participants'],
            ['route' => 'hearings.index',              'icon' => '⚖',  'title' => 'Hearings & Calendar',   'desc' => 'Schedule hearings and manage court dates.', 'label' => 'Manage Hearings'],
            ['route' => 'courtrooms.index',            'icon' => '🏛',  'title' => 'Courtrooms',            'desc' => 'Manage courtroom details and assignments.', 'label' => 'Manage Courtrooms'],
            ['route' => 'court-events.index',          'icon' => '📅',  'title' => 'Court Events',          'desc' => 'Track hearings, events and the court calendar.', 'label' => 'Manage Events'],
            ['route' => 'case-documents.index',        'icon' => '📄',  'title' => 'Documents & Evidence',  'desc' => 'Organize case documents and evidence securely.', 'label' => 'Manage Documents'],
            ['route' => 'case-fees.index',             'icon' => '💰',  'title' => 'Court Fees',            'desc' => 'Track fees charged for court services.', 'label' => 'Manage Fees'],
            ['route' => 'payments.index',              'icon' => '🧾',  'title' => 'Payments',              'desc' => 'Review payment transactions and receipt records.', 'label' => 'Manage Payments'],
            ['route' => 'bail-bonds.index',            'icon' => '🔗',  'title' => 'Bail & Bonds',          'desc' => 'Track bail and bond records and their statuses.', 'label' => 'Manage Bail'],
            ['route' => 'financial-adjustments.index', 'icon' => '📊',  'title' => 'Financial Adjustments', 'desc' => 'Adjust fee amounts and payment records.', 'label' => 'Manage Adjustments'],
            ['route' => 'financial-reports.index',     'icon' => '📈',  'title' => 'Financial Reports',     'desc' => 'View financial overview and transaction reports.', 'label' => 'View Reports'],
            ['route' => 'reports.index',               'icon' => '📋',  'title' => 'Report Generator',      'desc' => 'Generate, download and share reports with colleagues.', 'label' => 'Generate Reports'],
            ['route' => 'work-templates.index',        'icon' => '📝',  'title' => 'Work Templates',        'desc' => 'Work remotely: type, save as Word documents and share.', 'label' => 'Open Templates'],
        ];

        // Admins get an extra card for user management.
        if ($isAdmin) {
            $modules[] = ['route' => 'user-management.index', 'icon' => '🛡', 'title' => 'Admin · User Management', 'desc' => 'Manage system accounts and assigned roles.', 'label' => 'Manage Users'];
        }
    @endphp

    <section class="modules">
        @foreach ($modules as $i => $module)
            <div class="module reveal" style="transition-delay: {{ ($i % 4) * 70 }}ms;">
                <div class="module-top">
                    <h3>{{ $module['icon'] }} {{ $module['title'] }}</h3>
                    <div class="module-arrows">
                        @if ($i > 0)
                            <a class="nav-arrow" href="{{ route($modules[$i - 1]['route']) }}" title="Previous module">&#8592;</a>
                        @endif
                        @if ($i < count($modules) - 1)
                            <a class="nav-arrow" href="{{ route($modules[$i + 1]['route']) }}" title="Next module">&#8594;</a>
                        @endif
                    </div>
                </div>
                <p>{{ $module['desc'] }}</p>
                <a class="link" href="{{ route($module['route']) }}">{{ $module['label'] }} <span class="link-arrow">→</span></a>
            </div>
        @endforeach
    </section>
</main>

<footer>
    Court Management System &copy; {{ date('Y') }}
</footer>

<script>
    // Cards slide in as the page scrolls down.
    (function () {
        const cards = document.querySelectorAll('.reveal');
        if (!('IntersectionObserver' in window)) {
            cards.forEach(c => c.classList.add('shown'));
            return;
        }
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('shown');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        cards.forEach(card => observer.observe(card));
    })();
</script>
</body>
</html>
