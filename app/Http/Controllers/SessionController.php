<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    public function showCreateForm()
    {
        return view('sessions.create');
    }

    public function create(Request $request)
    {
        $session = Session::create([
            'invitation_code' => Str::random(6),
            'user_id' => auth()->user()->id,
            'session_name' => $request->input('session_name'),
            'collection' => $request->input('collection'),
        ]);

        $session->users()->attach(auth()->user());

        return redirect()->route('session.show', $session);
    }

    public function join()
    {
        return view('sessions.join');
    }

    public function store(Request $request)
    {

        $session = Session::where('invitation_code', $request->input('invitation_code'))->first();

        if (!$session) {
            abort(404, 'Session not found');
        }

        // Присоединение пользователя к сессии
        auth()->user()->sessions()->syncWithoutDetaching($session);

        return redirect()->route('session.show', $session);
    }

    public function show(Session $session)
    {
        $user = auth()->user();
        $isCreator = $session->user_id === $user->id;

        $likedMoviesWithData = $this->getLikedMoviesInSession($session);
        return view('sessions.show', compact('session', 'likedMoviesWithData', 'user', 'isCreator'));
    }

    public function destroy(Session $session)
    {
        // Добавьте логику удаления сессии и редиректа
        $session->delete();

        return redirect()->route('home')->with('success', 'Session deleted successfully');
    }

    public function leave(Session $session)
    {
        // Добавьте логику выхода пользователя из сессии
        auth()->user()->sessions()->detach($session);

        return redirect()->route('home')->with('success', 'You left the session');
    }

    public function getUsers(Session $session)
    {
        $users = $session->users;

        return response()->json(['users' => $users]);
    }

    public function getLikedMoviesInSession(Session $session)
    {
        $likedMovies = DB::table('movie_user')
            ->select('movie_user.movie_id')
            ->join('session_user', 'session_user.user_id', '=', 'movie_user.user_id')
            ->where('movie_user.session_id', '=', $session->id)
            ->where('movie_user.reaction', '=', 'like')
            ->groupBy('movie_user.movie_id')
            ->havingRaw('COUNT(DISTINCT movie_user.user_id) = ?', [count($session->users)])
            ->get();

        // Получаем содержимое movies.json
        $moviesJson = json_decode(file_get_contents(public_path('movies.json')), true);

        // Теперь для каждого movie_id найдем соответствующий объект фильма в movies.json
        $likedMoviesWithData = collect($likedMovies)->map(function ($movie) use ($moviesJson) {
            $kinopoiskId = $movie->movie_id;
            $foundMovie = collect($moviesJson['movies'])->firstWhere('kinopoiskId', $kinopoiskId);

            return $foundMovie;
        })->filter(); // Фильтруем, чтобы удалить пустые значения

        return $likedMoviesWithData;
    }
}
