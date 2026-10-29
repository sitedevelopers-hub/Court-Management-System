<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details | Court Management System</title>

    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
        }

        header {
            background: #172554;
            color: white;
            padding: 25px 5%;
        }

        header h1 { margin: 0 0 8px; }
        header p { margin: 0; color: #cbd5e1; }

        main {
            width: 90%;
            max-width: 900px;
            margin: 30px auto;
        }

        .panel {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .06);
            margin-bottom: 20px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 18px;
            padding-bottom: 22px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }

        .avatar {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: #dbeafe;
            color: #1f6f43;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 25px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .profile h2 { margin: 0 0 7px; }
        .profile p { margin: 0; color: #64748b; overflow-wrap: anywhere; }

        .details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 22px;
        }

        .detail label {
            display: block;
            color: #64748b;
            font-size: 12px;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .detail p {
            margin: 0;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .roles {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .role {
            padding: 7px 11px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 20px;
            font-size: 13px;
        }

        .muted { color: #64748b; }

        .notice {
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            border: 0;
            border-radius: 7px;
            padding: 11px 16px;
            background: #1f6f43;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn.gray { background: #475569; }
        .btn.red { background: #b91c1c; }

        @media (max-width: 600px) {
            main { width: 94%; }
            .panel { padding: 18px; }
        }
    </style>
</head>
<body>

@include('partials.nav')

<main>

    @if (session('success'))
        <div class="notice success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="notice error">{{ session('error') }}</div>
    @endif

    <section class="panel">
        <div class="profile">
            <div class="avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <div>
                <h2>{{ $user->name }}</h2>
                <p>{{ $user->email }}</p>
            </div>
        </div>

        <div class="details">
            <div class="detail">
                <label>User ID</label>
                <p>{{ $user->id }}</p>
            </div>

            <div class="detail">
                <label>Full Name</label>
                <p>{{ $user->name }}</p>
            </div>

            <div class="detail">
                <label>Email Address</label>
                <p>{{ $user->email }}</p>
            </div>

            <div class="detail">
                <label>Account Created</label>
                <p>{{ $user->created_at?->format('d M Y, H:i') ?? 'N/A' }}</p>
            </div>

            <div class="detail">
                <label>Last Updated</label>
                <p>{{ $user->updated_at?->format('d M Y, H:i') ?? 'N/A' }}</p>
            </div>

            <div class="detail">
                <label>Email Verification</label>
                <p>
                    @if ($user->email_verified_at)
                        <span class="role">Verified</span>
                    @else
                        <span class="muted">Not verified</span>
                    @endif
                </p>
            </div>
        </div>
    </section>

    <section class="panel">
        <h2>Assigned Roles</h2>

        <div class="roles">
            @forelse ($user->roles as $role)
                <span class="role">{{ $role->display_name }}</span>
            @empty
                <span class="muted">No roles assigned to this account.</span>
            @endforelse
        </div>
    </section>

    <div class="actions">
        <a href="{{ route('user-management.index') }}" class="btn gray">
            ← All Users
        </a>

        <a href="{{ route('user-management.edit', $user) }}" class="btn">
            Edit User
        </a>

        @if (auth()->id() !== $user->id && !$user->roles->contains('name', 'admin'))
            <form action="{{ route('user-management.destroy', $user) }}"
                  method="POST"
                  onsubmit="return confirm('Permanently delete this user account?');">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn red">Delete User</button>
            </form>
        @endif
    </div>

</main>

</body>
</html>
