<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details | Court Management System</title>

    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #243247;
        }

        .container { max-width: 900px; margin: auto; }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,.05);
            margin-bottom: 22px;
        }

        h1 { color: #17365d; margin-top: 0; }

        .details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .detail {
            background: #eef5ef;
            padding: 15px;
            border-radius: 7px;
            overflow-wrap: anywhere;
        }

        .detail strong {
            display: block;
            color: #526176;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .full { grid-column: 1 / -1; }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 11px 16px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .primary { background: #17365d; color: white; }
        .secondary { background: #e5eaf1; color: #243247; }

        .alert {
            background: #e7f7ed;
            color: #176b38;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
        }

        @media (max-width: 600px) {
            body { padding: 14px; }
            .card { padding: 17px; }
            .full { grid-column: auto; }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="card">
        <h1>Event Details</h1>
        <p>Review the information for this court event.</p>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <div class="details">
            <div class="detail">
                <strong>Event Title</strong>
                {{ $event->title }}
            </div>

            <div class="detail">
                <strong>Event Type</strong>
                {{ ucfirst($event->event_type) }}
            </div>

            <div class="detail">
                <strong>Related Case</strong>
                @if($event->courtCase)
                    {{ $event->courtCase->case_number }} — {{ $event->courtCase->case_title }}
                @else
                    No case linked
                @endif
            </div>

            <div class="detail">
                <strong>Status</strong>
                {{ ucfirst($event->status) }}
            </div>

            <div class="detail">
                <strong>Start Date and Time</strong>
                {{ $event->starts_at?->format('d M Y, H:i') ?? '—' }}
            </div>

            <div class="detail">
                <strong>End Date and Time</strong>
                {{ $event->ends_at?->format('d M Y, H:i') ?? 'Not specified' }}
            </div>

            <div class="detail">
                <strong>Reminder Date and Time</strong>
                {{ $event->remind_at?->format('d M Y, H:i') ?? 'No reminder set' }}
            </div>

            <div class="detail">
                <strong>Created By</strong>
                {{ $event->creator?->name ?? 'System / unavailable' }}
            </div>

            <div class="detail full">
                <strong>Description</strong>
                {{ $event->description ?: 'No description provided.' }}
            </div>

            <div class="detail">
                <strong>Created On</strong>
                {{ $event->created_at?->format('d M Y, H:i') ?? '—' }}
            </div>

            <div class="detail">
                <strong>Last Updated</strong>
                {{ $event->updated_at?->format('d M Y, H:i') ?? '—' }}
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('court-events.index') }}" class="btn secondary">
                Back to Events
            </a>

            <a href="{{ route('court-events.edit', $event) }}" class="btn primary">
                Edit Event
            </a>
        </div>
    </div>

</div>
</body>
</html>
