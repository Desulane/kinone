<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/films.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Ввести код</title>
</head>
<body>
<div class="mobile">
    <button class="close" onclick="window.location.href = '{{ url('/') }}'"></button>
    <div class="content">
        <form action="{{ route('session.store') }}" method="post">
            @csrf
            <h2>Введите код приглашения в поле и нажмите на кнопку</h2>
            <div class="session">
                <input class="invite-code" type="text" name="invitation_code" id="invitation_code" required>
                <button class="invite-button" type="submit">►</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
