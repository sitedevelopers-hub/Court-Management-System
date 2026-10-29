<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hearing Details | Court Management System</title>
    <style>
        body {
            background: transparent;
margin: 0;
            padding: 30px;
            color: #243247;
        }

        .container {
            max-width: 850px;
            margin: auto;
        }

        .header, .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        }

        h1 { margin-top: 0; color: #17365d; }

        .details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .detail {
            padding: 12px;
            background: #eef5ef;
            border-radius: 6px;
        }

        .detail strong {
            display: block;
            margin-bottom: 8px;
            color: #526176;
            font-size: 13px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
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
        .danger { background: #b42318; color: white; }

        .success {
            padding: 12px;
            background: #e7f7ed;
            color: #176b38;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        @media (max-width: 600px) {
            body { padding: 15px; }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1>Hearing Details</h1>
        <p>View the scheduled hearing and its current status.</p>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div class="actions">
            <a href="{{ route('hearings.index') }}" class="btn secondary">
                Back to Hearings
            </a>

            <a href="{{ route('hearings.edit', $hearing) }}" class="btn primary">
                Edit Hearing
            </a>
        </div>
    </div>

    <div class="card">
        <div class="details">
            <div class="detail">
                <strong>Case Number</strong>
                {{ $hearing->courtCase?->case_number ?? '—' }}
            </div>

            <div class="detail">
                <strong>Case Title</strong>
                {{ $hearing->courtCase?->case_title ?? '—' }}
            </div>

            <div class="detail">
                <strong>Hearing Date and Time</strong>
                {{ $hearing->scheduled_start?->format('d M Y, H:i') ?? '—' }}
            </div>

            <div class="detail">
                <strong>End Date and Time</strong>
                {{ $hearing->scheduled_end?->format('d M Y, H:i') ?? 'Not specified' }}
            </div>

            <div class="detail">
                <strong>Courtroom</strong>
                {{ $hearing->courtroom?->room_number ?? 'Not assigned' }}
            </div>

            <div class="detail">
                <strong>Courtroom Location</strong>
                {{ $hearing->courtroom?->location ?? '—' }}
            </div>

            <div class="detail">
                <strong>Hearing Type</strong>
                {{ ucfirst($hearing->hearing_type) }}
            </div>

            <div class="detail">
                <strong>Status</strong>
                {{ ucfirst($hearing->status) }}
            </div>

            <div class="detail">
                <strong>Outcome</strong>
                {{ $hearing->outcome ?: 'Not recorded' }}
            </div>

            <div class="detail">
                <strong>Adjournment Reason</strong>
                {{ $hearing->adjournment_reason ?: 'Not applicable' }}
            </div>

            <div class="detail">
                <strong>Previously Scheduled</strong>
                {{ $hearing->rescheduled_from?->format('d M Y, H:i') ?? 'No previous schedule recorded' }}
            </div>
        </div>

        <div class="actions">
            <form action="{{ route('hearings.destroy', $hearing) }}"
                  method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this hearing?')">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn danger">
                    Delete Hearing
                </button>
            </form>
        </div>
    </div>

</div>
</body>
</html>
