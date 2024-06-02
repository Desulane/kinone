<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/films.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Текущий сеанс</title>
</head>
<body>
<div class="mobile">
    <button class="close" onclick="window.location.href = '{{ url('/') }}'"></button>
    <div class="content">
        <div>
            <h1>{{ $session->session_name }}</h1>
            @if($isCreator)
                <div class="session">
                    <input class="invite-code" type="text" value="{{ $session->invitation_code }}" readonly>
                    <button class="invite-button" onclick="copyInviteCode()">Скопировать</button>
                </div>
            @endif
            <div class="players">
                <h2>Участники</h2>
                <ul id="users-list" class="user-list">
                    @foreach($session->users as $user)
                        <li class="{{ $user->id === $session->user_id ? 'creator' : '' }}">
                            {{ $user->name }}
                            @if($user->id === $session->user_id)
                                (Создатель)
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <h2>Понравившиеся фильмы:</h2>
        @if($likedMoviesWithData->isNotEmpty())
            <ul id="liked-movies-list">
                @foreach($likedMoviesWithData as $movie)
                    <li> <!-- Добавьте этот стиль для выравнивания текста слева -->
                        <a href="https://www.kinopoisk.ru/film/{{ $movie['kinopoiskId'] }}" target="_blank">
                            {{ $movie['nameRu'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p>Нет фильмов, которые понравились всем</p>
        @endif

        <div class="buttons-container">
            <form action="{{ route('movie.index', ['session' => $session]) }}" method="get">
                @csrf
                <button type="submit" class="button">К выбору</button>
            </form>

            @if($isCreator)
                <form action="{{ route('session.destroy', ['session' => $session]) }}" method="post">
                    @csrf
                    @method('delete')
                    <button type="submit" class="button">Удалить сессию</button>
                </form>
            @else
                <form action="{{ route('session.leave', ['session' => $session]) }}" method="post">
                    @csrf
                    <button type="submit" class="button">Покинуть сессию</button>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
    function copyInviteCode() {
        const inviteCodeInput = document.querySelector('.invite-code');
        inviteCodeInput.select();
        document.execCommand('copy');
        window.getSelection().removeAllRanges();
    }

    function updateUsers() {
        fetch("{{ route('session.users', ['session' => $session]) }}")
            .then(response => response.json())
            .then(data => {
                const usersList = document.getElementById('users-list');
                // Очищаем текущий список участников
                usersList.innerHTML = "";

                // Добавляем обновленные данные
                data.users.forEach(user => {
                    const listItem = document.createElement('li');
                    listItem.textContent = user.name + (user.id === {{ $session->user_id }} ? ' (Создатель)' : '');
                    usersList.appendChild(listItem);
                });
            })
            .catch(error => console.error('Error updating users:', error));
    }

    let likedMoviesList = document.getElementById('liked-movies-list');

    // Обновляем список каждые 3 секунды
    setInterval(() =>
        updateUsers(), 3000);
</script>
