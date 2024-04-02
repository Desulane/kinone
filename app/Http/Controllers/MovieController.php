<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Movie;
use App\Models\User;
use App\Models\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Builder;

class MovieController extends Controller
{
    public function index(Session $session)
    {
        $this->authorize('join', $session);
        $imageQuality = auth()->user()->image_quality;

        return view('movies.index', compact('session', 'imageQuality'));
    }

    public function getMoviesJson()
    {
        $path = storage_path('app/movies.json');

        if (!file_exists($path)) {
            abort(404);
        }

        $content = file_get_contents($path);

        return response($content)->header('Content-Type', 'application/json');
    }

    public function like($kinopoiskId)
    {
        $this->react($kinopoiskId, 'like');
        return redirect()->back();
    }

    public function dislike($kinopoiskId)
    {
        $this->react($kinopoiskId, 'dislike');
        return redirect()->back();
    }


    public function react(Request $request, Session $session)
    {
        $this->authorize('join', $session);

        $user = Auth::user();

        $kinopoiskId = $request->input('kinopoiskId');
        $reaction = $request->input('reaction');

        DB::table('movie_user')->insert([
            'movie_id' => $kinopoiskId,
            'user_id' => $user->id,
            'reaction' => $reaction,
            'session_id' => $session->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $isLikedByAll = $this->checkIfMovieLikedByAll($session, $request->kinopoiskId);
        return response()->json(['success' => true, 'isLikedByAll' => $isLikedByAll]);
    }

    protected function checkIfMovieLikedByAll(Session $session, $kinopoiskId)
    {
        $likedUsersCount = DB::table('movie_user')
            ->where('session_id', $session->id)
            ->where('movie_id', $kinopoiskId)
            ->where('reaction', 'like')
            ->distinct()
            ->count('user_id');

        return $likedUsersCount === count($session->users);
    }


    public function check(Request $request, Session $session)
    {
        $this->authorize('join', $session);

        $user = Auth::user();
        $kinopoiskId = $request->input('kinopoiskId');

        $movieExists = DB::table('movie_user')
            ->where('session_id', $session->id)
            ->where('user_id', $user->id)
            ->where('movie_id', $kinopoiskId)
            ->exists();

        return response()->json(['movieExists' => $movieExists]);
    }
}
