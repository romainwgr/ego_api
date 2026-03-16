<!DOCTYPE html>
<html>
<head>
    <title>New membership request</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">

    <h2>New membership request</h2>
    <p>A new user has completed their profile and is waiting for validation.</p>

    <table style="border-collapse: collapse; width: 100%; max-width: 600px;">
        <tr>
            <td style="padding: 8px; font-weight: bold; width: 180px;">Name</td>
            <td style="padding: 8px;">{{ $user->first_name }} {{ $user->last_name }}</td>
        </tr>
        <tr style="background-color: #f5f5f5;">
            <td style="padding: 8px; font-weight: bold;">Username</td>
            <td style="padding: 8px;">{{ $user->username }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">Email</td>
            <td style="padding: 8px;">{{ $user->email }}</td>
        </tr>
        <tr style="background-color: #f5f5f5;">
            <td style="padding: 8px; font-weight: bold;">Professional email</td>
            <td style="padding: 8px;">{{ $user->professional_email }}</td>
        </tr>
        @if($user->orcid)
        <tr>
            <td style="padding: 8px; font-weight: bold;">ORCID</td>
            <td style="padding: 8px;">{{ $user->orcid }}</td>
        </tr>
        @endif
        <tr style="background-color: #f5f5f5;">
            <td style="padding: 8px; font-weight: bold;">Organisation</td>
            <td style="padding: 8px;">
                @if($user->egoMember)
                    {{ $user->egoMember->name }}
                @else
                    <em>Unknown</em>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">Motivation</td>
            <td style="padding: 8px;">{{ $user->motivation }}</td>
        </tr>
    </table>

    <br>
    <p>
        <a href="{{ rtrim(env('APP_FRONTEND_URL'), '/') }}/admin/pending-requests"
           style="background-color: #1a73e8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
            Review request
        </a>
    </p>

    <br>
    <p>EGC Team</p>

</body>
</html>
