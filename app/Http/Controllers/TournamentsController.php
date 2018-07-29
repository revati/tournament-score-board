<?php namespace App\Http\Controllers;

use App\Tournament;
use App\Services\TeamGenerator;
use App\Services\TournamentLifecycle;

class TournamentsController extends Controller {

    public function index()
    {
        return view('tournaments.index')
            ->with('tournaments', Tournament::all());
    }

    public function create(TeamGenerator $generator, TournamentLifecycle $tournamentLifecycle)
    {
        $data = $this->validate(request(), [
            'title' => 'required'
        ]);

        $tournament = $tournamentLifecycle->make($data['title'], $generator->fetch(16));

        return redirect()->route('tournaments.view', $tournament->id);
    }

    public function view($id)
    {
        return view('tournaments.view')
            ->with('tournament', Tournament::findOrFail($id));
    }
}
