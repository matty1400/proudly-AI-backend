<!-- resources/views/emails/welcome.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Email</title>
</head>
<body>
    <p>Hello, {{ $data['name'] }}!</p>
    <p>Welcome to YourAppName. Your activation code is: {{ $data['activation_code'] }}</p>
    <!-- Add more content as needed -->
</body>
</html>

