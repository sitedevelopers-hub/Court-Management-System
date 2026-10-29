<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Protected | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
            font-family: Arial, sans-serif;
        }
        .card {
            max-width: 550px;
            width: 100%;
            background: white;
            padding: 35px;
            border-radius: 14px;
            text-align: center;
            box-shadow: 0 5px 20px #0f172a12;
        }
        .icon {
            width: 65px;
            height: 65px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #dbeafe;
            color: #1f6f43;
            font-size: 30px;
        }
        h1 { font-size: 24px; }
        p { color: #64748b; line-height: 1.7; }
        .reference {
            padding: 13px;
            border-radius: 8px;
            background: #eef5ef;
            color: #1e293b;
            font-weight: 700;
            overflow-wrap: anywhere;
        }
        .btn {
            display: inline-block;
            margin: 8px 4px 0;
            padding: 12px 17px;
            border-radius: 8px;
            background: #1f6f43;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        .secondary { background: #e2e8f0; color: #1e293b; }
    
        .delete-btn { background: #dc2626; color: #fff; border: none; padding: 10px 16px; border-radius: 7px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .delete-btn:hover { background: #b91c1c; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">ðŸ”’</div>
        <h1>Payment Record Protected</h1>
        <p>
            Completed payments cannot be edited directly because they form part
            of the financial audit trail. Corrections should be handled through
            an authorized financial adjustment process.
        </p>

        <div class="reference">
            Receipt: {{ $payment->receipt_number }}
        </div>

        <p>
            Amount recorded:
            <strong>KES {{ number_format((float) $payment->amount, 2) }}</strong>
        </p>

        <a href="{{ route('payments.show', $payment) }}" class="btn">
            View Receipt
        </a>

        <a href="{{ route('payments.index') }}" class="btn secondary">
            Back to Payments
        </a>
    </div>
</body>
</html>
