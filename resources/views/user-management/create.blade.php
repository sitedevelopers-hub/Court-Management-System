<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User | Court Management System</title>

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
            max-width: 850px;
            margin: 30px auto;
        }

        .panel {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .06);
        }

        .field { margin-bottom: 20px; }

        label {
            display: block;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 15px;
        }

        .help {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: #64748b;
        }

        .roles {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .role-option {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 13px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #eef5ef;
        }

        .role-option input { margin-top: 3px; }

        .role-option label {
            margin: 0;
            cursor: pointer;
        }

        .role-option small {
            display: block;
            margin-top: 5px;
            font-weight: normal;
            color: #64748b;
        }

        .notice {
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .error { background: #fee2e2; color: #991b1b; }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            border: 0;
            border-radius: 7px;
            padding: 12px 17px;
            background: #1f6f43;
            color: white;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .btn.gray { background: #475569; }

        @media (max-width: 600px) {
            main { width: 94%; }
            .panel { padding: 18px; }
        }
    </style>
</head>
<body>

@include('partials.nav')

<main>
    <section class="panel">

        @if ($errors->any())
            <div class="notice error">
                <strong>Please correct the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('user-management.store') }}" method="POST">
            @csrf

            <div class="field">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name"
                       value="{{ old('name') }}"
                       maxlength="255" required>
            </div>

            <div class="field">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       maxlength="255" required>
            </div>

            <div class="field">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password"
                       minlength="8" autocomplete="new-password" required>
                <span class="help">Use at least 8 characters. Never share passwords with other users.</span>
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm Password *</label>
                <input type="password" id="password_confirmation"
                       name="password_confirmation"
                       minlength="8" autocomplete="new-password" required>
            </div>

            <div class="field">
                <label>Assign Roles</label>
                <span class="help">Select only the roles needed for this person's duties.</span>

                <div class="roles">
                    @forelse ($roles as $role)
                        <div class="role-option">
                            <input type="checkbox"
                                   id="role_{{ $role->id }}"
                                   name="roles[]"
                                   value="{{ $role->id }}"
                                   @checked(in_array($role->id, old('roles', [])))>

                            <label for="role_{{ $role->id }}">
                                {{ $role->display_name }}
                                @if ($role->description)
                                    <small>{{ $role->description }}</small>
                                @endif
                            </label>
                        </div>
                    @empty
                        <p>No roles have been created yet. Roles must be configured before assigning access.</p>
                    @endforelse
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn">Create User</button>

                <a href="{{ route('user-management.index') }}"
                   class="btn gray">Cancel</a>
            </div>
        </form>
    </section>
</main>

</body>
</html>
