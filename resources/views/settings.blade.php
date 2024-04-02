<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/films.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Настройки</title>
</head>

<body>
    <div class="mobile">
        <button class="close" onclick="window.location.href = '{{ url('/') }}'"></button>
        <form method="POST" action="{{ route('update-settings') }}">
            @csrf
            <label for="image_quality">Выберите качество изображений:</label>
            <select name="image_quality" id="image_quality">
                <option value="default" {{ Auth::user()->image_quality === 'default' ? 'selected' : '' }}>Обычное
                </option>
                <option value="preview" {{ Auth::user()->image_quality === 'preview' ? 'selected' : '' }}>Низкое
                </option>
            </select>
            <div class="buttons-container">
                <button type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</body>

</html>
