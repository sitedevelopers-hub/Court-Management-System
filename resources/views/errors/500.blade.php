<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: linear-gradient(160deg, #f2f7f3 0%, #e4efe7 100%);
            color: #1e293b;
        }
        .card {
            background: white;
            padding: 48px 40px;
            border-radius: 14px;
            box-shadow: 0 6px 24px rgba(15, 23, 42, .08);
            text-align: center;
            max-width: 460px;
            width: 92%;
        }
        .code { font-size: 72px; font-weight: bold; color: #b91c1c; margin: 0; line-height: 1; }
        h1 { margin: 14px 0 8px; font-size: 22px; }
        p { color: #64748b; line-height: 1.6; margin: 0 0 24px; }
        a.btn {
            display: inline-block;
            background: #1f6f43;
            color: white;
            padding: 12px 22px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }
        a.btn:hover { background: #1e40af; }
    </style>
</head>
<body>
    <div class="card">
        <p class="code">500</p>
        <h1>Server Error</h1>
        <p>Something went wrong on our end. Please try again, or go back to the dashboard.</p>
        <a class="btn" href="{{ url('/dashboard') }}">← Back to Dashboard</a>
    </div>
</body>
</html>
