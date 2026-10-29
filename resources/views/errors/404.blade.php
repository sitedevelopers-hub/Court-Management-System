<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found | Court Management System</title>
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
        .code {
            font-size: 72px;
            font-weight: bold;
            color: #0f3d2e;
            margin: 0;
            line-height: 1;
        }
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
        <p class="code">404</p>
        <h1>Page Not Found</h1>
        <p>The page you are looking for doesn't exist or may have been moved.</p>
        <a class="btn" href="{{ url('/dashboard') }}">← Back to Dashboard</a>
    </div>
</body>
</html>
