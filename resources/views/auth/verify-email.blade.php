<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | Court Management System</title>

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
            background: #f1f5f9;
            font-family: Arial, sans-serif;
            color: #1e293b;
        }

        .verification-card {
            width: 100%;
            max-width: 460px;
            padding: 35px;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 10px 35px rgba(15, 23, 42, 0.10);
            text-align: center;
        }

        .icon {
            width: 65px;
            height: 65px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e0e7ff;
            border-radius: 50%;
            color: #1e3a8a;
            font-size: 30px;
        }

        h1 {
            margin-bottom: 12px;
            font-size: 25px;
        }

        p {
            color: #64748b;
            line-height: 1.6;
            font-size: 14px;
        }

        .message {
            margin: 15px 0;
            padding: 12px;
            border-radius: 7px;
            background: #ecfdf5;
            color: #166534;
            font-size: 14px;
        }

        .warning {
            background: #fffbeb;
            color: #92400e;
        }

        button {
            width: 100%;
            margin-top: 15px;
            padding: 13px;
            border: none;
            border-radius: 7px;
            background: #1e3a8a;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #172554;
        }

        .logout-button {
            background: #e2e8f0;
            color: #334155;
        }

        .logout-button:hover {
            background: #cbd5e1;
        }
    </style>
</head>

<body>
    <div class="verification-card">
        <div class="icon">&#9993;</div>

        <h1>Verify Your Email</h1>

        <p>
            Thank you for registering with the Court Management System.
            Please open the verification link sent to your email address
            to activate your account.
        </p>

        @if (session('success'))
            <div class="message">{{ session('success') }}</div>
        @endif

        @if (session('warning'))
            <div class="message warning">{{ session('warning') }}</div>
        @endif

        @if ($errors->any())
            <div class="message warning">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit">Resend Verification Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-button">Log Out</button>
        </form>
    </div>
</body>
</html>
