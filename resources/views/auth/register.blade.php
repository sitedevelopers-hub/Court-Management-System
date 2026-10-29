<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Court Management System</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Arial, sans-serif;
            background:
                radial-gradient(900px 500px at 80% -10%, rgba(29, 157, 80, .25), transparent 60%),
                radial-gradient(700px 400px at 0% 100%, rgba(11, 61, 46, .35), transparent 60%),
                linear-gradient(160deg, #0b3d2e 0%, #14532d 55%, #1d7a46 100%);
        }

        .card {
            width: 100%;
            max-width: 440px;
            padding: 35px;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 15px 45px rgba(0,0,0,.25);
        }

        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #14532d, #1d9d50);
            color: #fff;
            font-size: 22px;
            font-weight: bold;
        }

        h1 {
            margin: 0 0 8px;
            color: #14532d;
            text-align: center;
            font-size: 25px;
        }

        .subtitle {
            margin: 0 0 24px;
            color: #687787;
            text-align: center;
            font-size: 14px;
            line-height: 1.5;
        }

        label {
            display: block;
            margin: 15px 0 7px;
            color: #243647;
            font-size: 14px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd5df;
            border-radius: 8px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #1d9d50;
            box-shadow: 0 0 0 3px rgba(29, 157, 80, .14);
        }

        .admin-box {
            margin-top: 18px;
            padding: 13px 14px;
            border: 1px dashed #9fd9b8;
            border-radius: 10px;
            background: #f0faf4;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-box input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #1d9d50;
            flex-shrink: 0;
        }

        .admin-box label {
            margin: 0;
            font-size: 13.5px;
            font-weight: 600;
            color: #14532d;
            line-height: 1.45;
        }

        .admin-box .admin-note {
            display: block;
            font-size: 12px;
            font-weight: 400;
            color: #5b7a6b;
        }

        .admin-box.disabled {
            opacity: .55;
            pointer-events: none;
        }

        button {
            width: 100%;
            margin-top: 23px;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(90deg, #15803d, #1d9d50);
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background .2s ease, transform .12s ease, box-shadow .2s ease;
        }

        button:hover {
            background: linear-gradient(90deg, #14532d, #15803d);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(21, 128, 61, .28);
        }

        .errors {
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 8px;
            background: #fff0ef;
            color: #b42318;
            font-size: 13px;
        }

        .footer {
            margin-top: 22px;
            text-align: center;
            color: #687787;
            font-size: 14px;
        }

        .footer a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 22px;
            border-radius: 9px;
            background: #eef7f1;
            border: 1px solid #cfe8d9;
            color: #14532d;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: background .15s ease, transform .12s ease;
        }

        .footer a:hover {
            background: #14532d;
            color: #ffffff;
            transform: translateY(-1px);
            text-decoration: none;
        }

        .notice {
            margin-top: 16px;
            color: #687787;
            font-size: 12px;
            line-height: 1.5;
            text-align: center;
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="logo">CMS</div>

        <h1>Create Your Account</h1>
        <p class="subtitle">
            Register for the Court Management System
        </p>

        @if ($errors->any())
            <div class="errors">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf

            <label for="name">Full name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="Enter your full name"
                autocomplete="name"
                maxlength="255"
                required
            >

            <label for="email">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Enter your email address"
                autocomplete="email"
                maxlength="255"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="At least 8 characters"
                autocomplete="new-password"
                minlength="8"
                required
            >

            <label for="password_confirmation">Confirm password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Enter the password again"
                autocomplete="new-password"
                minlength="8"
                required
            >

            <div class="admin-box {{ $adminSlotsAvailable ? '' : 'disabled' }}">
                <input type="checkbox" id="as_admin" name="as_admin" value="1"
                       @checked(old('as_admin') || (!$adminSlotsAvailable))>
                <label for="as_admin">
                    Register as Administrator
                    <span class="admin-note">
                        @if ($adminSlotsAvailable)
                            Full access to every function of the system.
                            {{ 2 - $adminCount }} of 2 admin slots left.
                        @else
                            All 2 administrator slots are taken — new accounts are standard users.
                        @endif
                    </span>
                </label>
            </div>

            <button type="submit">Create Account</button>
        </form>

        <p class="notice">
            After you create your account you can sign in right away and access
            your dashboard.
        </p>

        <div class="footer">
            Already have an account?
            <a href="{{ route('login') }}">Sign in</a>
        </div>
    </main>
</body>
</html>
