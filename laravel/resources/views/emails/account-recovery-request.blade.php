<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Recovery Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #00d4ff, #8a2be2);
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
            text-align: center;
        }
        .content {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .info-box {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #00d4ff;
            border-radius: 4px;
        }
        .message-box {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            white-space: pre-wrap;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            color: #666;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #00d4ff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Account Recovery Request</h1>
    </div>
    
    <div class="content">
        <p>Hello Administrator,</p>
        
        <p>A user has submitted an account recovery request:</p>
        
        <div class="info-box">
            <strong>User Information:</strong><br>
            <strong>Name:</strong> {{ $user->full_name }}<br>
            <strong>Email:</strong> {{ $user->email }}<br>
            <strong>User ID:</strong> {{ $user->id }}<br>
            <strong>Account Status:</strong> {{ ucfirst($user->status) }}<br>
            @if($user->ban_reason)
                <strong>Ban Reason:</strong> {{ $user->ban_reason }}
            @endif
        </div>
        
        <div class="message-box">
            <strong>User's Message:</strong><br><br>
            {{ $message }}
        </div>
        
        <p>Please review this request and take appropriate action.</p>
        
        <p>
            <a href="{{ route('admin.users.detail', $user->id) }}" class="button">View User Profile</a>
        </p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from Music Locker.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>
</html>





