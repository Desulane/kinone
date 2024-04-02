<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/films.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Главный экран</title>
</head>

<body>
    <div class="mobile">
        <button class="settings" onclick="window.location.href = '{{ route('settings') }}'"></button>
        <div class="content">
            <h1>Добро пожаловать</h1>
            <!-- Форма создания сессии -->
            <form method="GET" action="{{ route('session.createForm') }}">
                @csrf
                <button class="button" type="submit">Создать сессию</button>
            </form>

            <!-- Список созданных сессий, если они существуют -->
            @if (count(auth()->user()->sessions) > 0)
                <p>Ваши сессии:</p>
                <ul>
                    @foreach (auth()->user()->sessions as $createdSession)
                        <li>
                            <a href="{{ route('session.show', $createdSession) }}">
                                {{ $createdSession->session_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            <!-- Форма присоединения к сессии -->
            <form method="GET" action="{{ route('session.join') }}">
                @csrf
                <button class="button" type="submit">Присоединиться к сессии</button>
            </form>

            <!-- Список присоединенных сессий, если они существуют -->
            @if (count(auth()->user()->sessions->where('user_id', '!=', auth()->user()->id)) > 0)
                <p>Присоединенные сессии:</p>
                <ul>
                    @foreach (auth()->user()->sessions->where('user_id', '!=', auth()->user()->id) as $session)
                        <li>
                            <a href="{{ route('session.show', $session) }}">
                                Сессия {{ $session->id }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</body>

</html>
