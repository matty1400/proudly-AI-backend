<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome Email</title>
</head>
<body>
    <h1>Welcome to The Proudly Platform!</h1>
    <p>Dear {{ $data['name'] }},</p>
    <p>Welcome to StoryStreak, your code is {{ $data['activation'] }} We are excited to have you on board.</p>
    <p>Thank you for joining us.</p>
    <p>Regards,<br>
    The StoryStreak Team</p>
</body>
</html>

