<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Court Management System</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: 'Segoe UI', Arial, sans-serif;
            background:
                radial-gradient(900px 500px at 80% -10%, rgba(29, 157, 80, .25), transparent 60%),
                radial-gradient(700px 400px at 0% 100%, rgba(11, 61, 46, .35), transparent 60%),
                linear-gradient(160deg, #0b3d2e 0%, #14532d 55%, #1d7a46 100%);
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 35px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.25);
        }

        .logo {
            width: 65px;
            height: 65px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #14532d, #1d9d50);
            color: #fff;
            font-size: 27px;
            font-weight: bold;
        }

        h1 {
            margin: 0 0 8px;
            color: #14532d;
            text-align: center;
            font-size: 25px;
        }

        .subtitle {
            margin: 0 0 28px;
            color: #687787;
            text-align: center;
            font-size: 14px;
        }

        label {
            display: block;
            margin: 16px 0 7px;
            color: #243647;
            font-size: 14px;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #ccd5df;
            border-radius: 8px;
            outline: none;
            font-size: 15px;
        }

        input:focus {
            border-color: #1d9d50;
            box-shadow: 0 0 0 3px rgba(29, 157, 80, .14);
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 16px 0 22px;
            color: #526273;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(90deg, #15803d, #1d9d50);
            color: #fff;
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

        .error {
            margin-top: 7px;
            color: #b42318;
            font-size: 13px;
        }

        .alert {
            margin-bottom: 15px;
            padding: 11px;
            border-radius: 7px;
            background: #fff0ef;
            color: #b42318;
            font-size: 14px;
        }

        .footer {
            margin-top: 23px;
            color: #7b8794;
            text-align: center;
            font-size: 13px;
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

        .success-msg {
            margin-bottom: 15px;
            padding: 11px;
            border-radius: 7px;
            background: #dcfce7;
            color: #166534;
            font-size: 14px;
        }

        .admin-access {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px dashed #cfe8d9;
            text-align: center;
        }

        .admin-access-title {
            margin: 0 0 10px;
            font-size: 13px;
            font-weight: 700;
            color: #000000;
        }

        .admin-access-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .admin-btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 9px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            color: #111827;
            font-weight: 600;
            font-size: 13.5px;
            text-decoration: none;
            transition: transform .12s ease, box-shadow .15s ease;
        }

        .admin-btn:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }

        .admin-btn.alt {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            color: #111827;
        }
    </style>
</head>
<body>
    <main class="login-card">
        <div class="logo">CMS</div>

        <h1>Court Management System</h1>
        <p class="subtitle">Sign in to access your court workspace</p>

        @if ($errors->any())
            <div class="alert">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="success-msg">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <label for="email">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Enter your email"
                autocomplete="username"
                required
                autofocus
            >

            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
            >

            <label class="remember">
                <input type="checkbox" name="remember" value="1">
                Remember me
            </label>

            <button type="submit">Sign In</button>
        </form>

        <div class="footer">
            No account yet? <a href="{{ route('register') }}">Sign up here</a> · Authorised personnel only
        </div>

        @if (!auth()->check())
            <div class="admin-access">
                <p class="admin-access-title">Admin</p>
                <div class="admin-access-buttons">
                    <a href="{{ route('register') }}" class="admin-btn">Sign Up as Admin</a>
                    <a href="{{ route('login') }}" class="admin-btn alt">Log In</a>
                </div>
            </div>
        @endif
    </main>
</body>
</html>
