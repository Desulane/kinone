<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/films.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Выбор фильма</title>
</head>

<body>
<div class="mobile">
    <button class="close" onclick="returnToSession()"></button>
    <div class="content" id="movies-container">

    </div>
</div>
<script>
    let movies = []; // массив для хранения фильмов
    let currentMovieIndex = 0; // индекс текущего фильма

    // Загрузка фильмов
    async function loadMovies() {
        // Определение значения collection
        const collection = "{{ $session->collection }}"; // Получаем значение collection из PHP
        // Формируем путь к файлу с данными в зависимости от значения collection
        const moviesFilePath = `/${collection}.json`;
        // Используем fetch для загрузки файла movies.json
        const resp = await fetch(moviesFilePath);
        const respData = await resp.json();
        movies = respData.movies;
        showCurrentMovie();
    }

    // Показать текущий фильм
    function showCurrentMovie() {
        const movieContainer = document.getElementById('movies-container');
        const currentMovie = movies[currentMovieIndex];
        const imageQuality = "{{ auth()->user()->image_quality }}";
        let nonYoutubeTrailers
        let allTrailers
        if (currentMovie.trailers){
            nonYoutubeTrailers = currentMovie.trailers.filter(trailer => trailer.site !== 'YOUTUBE');
            allTrailers = currentMovie.trailers.filter(trailer => trailer.name === 'Трейлер')
        }


        if (currentMovie) {
            // Проверяем, есть ли запись о текущем фильме для текущего пользователя и сессии
            fetch(`{{ route('movie.check', ['session' => $session]) }}?kinopoiskId=${currentMovie.kinopoiskId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                        if (!data.movieExists) {
                            // Если фильм еще не оценен, отображаем его
                            movieContainer.innerHTML = `
                            <div class="popup" id="myModal">
                                <span class="close" onclick="document.getElementById('myModal').style.display='none'">&times;</span>
                                <p id="popupText">${currentMovie.description}</p>
                            </div>
                            <div class="image-container">
                                <img class="movie-poster" src="${imageQuality === 'preview' ? currentMovie.posterUrlPreview : currentMovie.posterUrl}" alt="${currentMovie.nameRu}" onclick="openPopup()">
                                <div class="info-button" onclick="openPopup()">i</div>
                            </div>

                            <h2>${currentMovie.nameRu}</h2>
                            <div class="container">
                                ${nonYoutubeTrailers ?
                                `<button class="abc" onclick="window.location.href = '${nonYoutubeTrailers[0].url}'">Трейлер</button>` :
                                `<button class="abc disabled">Трейлер отсутствует</button>`
                            }
                            </div>
                            <div class="rating">
                            <p>${currentMovie.year}</p>
                            </div>
                            <div class="rating">
                            <img src="{{ asset('images/kp.png') }}" alt="Рейтинг на Кинопоиске" class="icon">
                            ${currentMovie.ratingKinopoisk}
                            </div>
                            <h3>${currentMovie.genres.map(genre => genre.genre).join(', ')}</h3>
                            <div class="session">
                                <button class="dislike" onclick="dislikeMovie(${currentMovie.kinopoiskId})">Не смотрим</button>
                                <button class="button" onclick="likeMovie(${currentMovie.kinopoiskId})">Смотрим</button>
                            </div>
                            `;
                        } else {
                            // Если фильм уже оценен, переходим к следующему
                            nextMovie();
                        }
                    }
                )
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        } else {
            movieContainer.innerHTML = '<p>Фильмы закончились</p>';
        }
    }

    async function dislikeMovie(kinopoiskId) {
        await reactMovie(kinopoiskId, 'dislike');
        nextMovie();
    }

    async function likeMovie(kinopoiskId) {
        try {
            await reactMovie(kinopoiskId, 'like');
            const response = await checkIfMovieLikedByAll(kinopoiskId);
            if (response.isLikedByAll) {
                showNotification('Фильм понравился всем!');
            }
            nextMovie();
        } catch (error) {
            console.error('Error during likeMovie:', error);
            // Добавьте обработку ошибки, если необходимо
        }
    }

    // Функция для проверки, понравился ли фильм всем пользователям
    async function checkIfMovieLikedByAll(kinopoiskId) {
        const token = document.querySelector('meta[name="csrf-token"]').content;

        try {
            const response = await fetch(
                `{{ route('movie.check', ['session' => $session]) }}?kinopoiskId=${kinopoiskId}`);

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error during checkIfMovieLikedByAll:', error);
            throw error; // Перебрасываем ошибку для дальнейшей обработки
        }
    }


    // Функция для отображения уведомлений
    function showNotification(message) {
        // Ваш код для отображения уведомлений, может потребоваться использование сторонних библиотек
        alert(message);
    }

    async function reactMovie(kinopoiskId, reaction) {
        const token = document.querySelector('meta[name="csrf-token"]').content;

        await fetch(`{{ route('movie.react', ['session' => $session]) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({
                kinopoiskId: kinopoiskId,
                reaction: reaction,
            }),
        });
    }


    function returnToSession() {
        window.location.href = "{{ route('session.show', ['session' => $session]) }}";
    }

    // Переход к следующему фильму
    function nextMovie() {
        currentMovieIndex++;
        showCurrentMovie();
    }

    window.addEventListener('load', () => {
        loadMovies();
    });

    function openPopup() {
        document.getElementById('myModal').style.display = "block";
    }

    function closePopup() {
        document.getElementById('myModal').style.display = "none";
    }

    // Обработчик события клика на весь документ
    window.addEventListener('click', function (event) {
        var modal = document.getElementById('myModal');
        // Если клик произошел вне модального окна, закрываем его
        if (event.target === modal) {
            closePopup();
        }
    });
</script>
</body>

</html>
