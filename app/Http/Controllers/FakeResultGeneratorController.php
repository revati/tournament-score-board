<?php namespace App\Http\Controllers;

use App\Tournament;
use App\Services\GameResultsGenerator;
use App\Services\TournamentLifecycle;

class FakeResultGeneratorController
{
    public function nextStep($id, TournamentLifecycle $tournamentLifecycle, GameResultsGenerator $gameResultsGenerator)
    {
        $tournament = Tournament::findOrFail($id);

        $tournament
            ->divisions()
            ->isPending()
            ->firstOrFail()
            ->games()
            ->isPending()
            ->get()
            ->map(function($game) use ($tournamentLifecycle, $gameResultsGenerator) {
                $score = $gameResultsGenerator->generateResults($game);
                $tournamentLifecycle->finishGame($game, $score);
            });

        return redirect()->route('tournaments.view', $tournament->id);
    }
}
