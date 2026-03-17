<!DOCTYPE html>
<html>
<head>
    <title>Welcome to the European Glider Community</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">

    <h2>Welcome to the European Glider Community!</h2>
    <p>Hello {{ $user->first_name }},</p>
    <p>Your membership request has been approved. You can now log in and access the platform.</p>

    <br>
    <p>
        <a href="{{ rtrim(env('APP_FRONTEND_URL'), '/') }}"
           style="background-color: #1a73e8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
            Log in
        </a>
    </p>

    <br>
    <p>Thank you,<br>EGC Team</p>

</body>
</html>