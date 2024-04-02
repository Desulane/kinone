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
    <div class ="mobile">
        <button class="close" onclick="window.location.href = '{{ url('/') }}'"></button>

        <form method="POST" action="{{ route('session.create') }}">
            @csrf
            <label for="session_name">Название сессии:</label>
            <input type="text" id="session_name" name="session_name">
            <label for="collection">Выберите коллекцию:</label>
            <select name="collection" id="collection">
                <option value="CATASTROPHE_THEME">Фильмы-катастрофы</option>
                <option value="CLOSES_RELEASES">Ближайшие релизы</option>
                <option value="COMICS_THEME">Фильмы по комиксам</option>
                <option value="FAMILY">Для всей семьи</option>
                <option value="KIDS_ANIMATION_THEME">Мультфильмы для детей</option>
                <option value="LOVE_THEME">Романтические фильмы</option>
                <option value="OSKAR_WINNERS_2021">Оскар 2021</option>
                <option value="TOP_250_MOVIES">ТОП 250 фильмов</option>
                <option value="TOP_250_TV_SHOWS">ТОП 250 сериалов</option>
                <option value="TOP_POPULAR_ALL">ТОП популярного</option>
                <option value="TOP_POPULAR_MOVIES">ТОП популярных фильмов</option>
                <option value="VAMPIRE_THEME">Фильмы про вампиров</option>
                <option value="ZOMBIE_THEME">Фильмы про зомби</option>
            </select>
            <div class="buttons-container">
                <button type="submit">Продолжить</button>
            </div>
        </form>

    </div>
</body>

</html>
