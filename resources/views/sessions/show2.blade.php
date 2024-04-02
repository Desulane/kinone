<h1>Детали сессии:</h1>

@if($isCreator)
    <p>Код приглашения: {{ $session->invitation_code }}</p>
@endif

<h2>Пользователи:</h2>
<ul id="users-list">
    @foreach($session->users as $user)
        <li>{{ $user->name }} @if($user->id === $session->user_id)
                (Создатель)
            @endif
        </li>
    @endforeach
</ul>

<h2>Понравившиеся фильмы:</h2>
@if($likedMoviesWithData->isNotEmpty())
    <ul>
        @foreach($likedMoviesWithData as $movie)
            <li>
                <a href="https://www.kinopoisk.ru/film/{{ $movie['kinopoiskId'] }}" target="_blank">
                    {{ $movie['nameRu'] }}
                </a>
            </li>
        @endforeach
    </ul>
@else
    <p>Нет фильмов, которые понравились всем</p>
@endif

<form action="{{ route('movie.index', ['session' => $session]) }}" method="get">
    @csrf
    <button type="submit">К выбору</button>
</form>

@if($isCreator)
    <form action="{{ route('session.destroy', ['session' => $session]) }}" method="post">
        @csrf
        @method('delete')
        <button type="submit">Удалить сессию</button>
    </form>
@else
    <form action="{{ route('session.leave', ['session' => $session]) }}" method="post">
        @csrf
        <button type="submit">Покинуть сессию</button>
    </form>
@endif

<script>
    function updateUsers() {
        fetch("{{ route('session.users', ['session' => $session]) }}")
            .then(response => response.json())
            .then(data => {
                // Очищаем текущий список участников
                document.getElementById('users-list').innerHTML = "";

                // Добавляем обновленные данные
                data.users.forEach(user => {
                    const listItem = document.createElement('li');
                    listItem.textContent = user.name + (user.id === {{ $session->user_id }} ? ' (Creator)' : '');
                    document.getElementById('users-list').appendChild(listItem);
                });
            })
            .catch(error => console.error('Error updating users:', error));
    }

    // Обновляем список каждые 3 секунды
    setInterval(updateUsers, 3000);
</script>
