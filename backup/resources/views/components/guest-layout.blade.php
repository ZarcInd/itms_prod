<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>
<body>
    <div class="font-sans text-gray-900 antialiased">
        {{ $slot }}
    </div>
</body>
</html>
