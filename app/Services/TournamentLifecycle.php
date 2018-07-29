<?php namespace App\Services;

use App\Game;
use App\Tournament;
use App\Division;
use \DomainException;

class TournamentLifecycle {

    public function make($title, $teams)
    {
        $count = $teams->count();

        switch($count) {
            case 16:
                return $this->makePreliminaryTournament($title, $teams);
            default:
                throw new DomainException("Tournamnets with [$count] teams isnt supported");
        }
    }

    public function finishGame(Game $game, $score)
    {
        $game->finish($score)->save();

        if($game->isTied()) {
            $this->updateTeamScores($game, $game->team_a_id, 1);
            $this->updateTeamScores($game, $game->team_b_id, 1);
        } else {
            $this->updateTeamScores($game, $game->winning_team_id, 3);
        }

        $this->finishDivision($game->division);

        return $game;
    }

    private function updateTeamScores(Game $game, $teamId, $score)
    {
        $teams = $game->division->teams();

        $newScore = $teams->findOrFail($teamId)->pivot->score + $score;
        $teams->updateExistingPivot($teamId, ['score' => $newScore]);
    }

    private function makePreliminaryTournament($title, $teams)
    {
        $tournament = Tournament::makePreliminary($title);
        $tournament->save();

        $divisionNames = ['Division A', 'Division B'];

        $divisions = $teams
            ->shuffle()
            ->split(2)
            ->map(function($teams, $key) use ($tournament, $divisionNames) {
                $teams = $teams->values();
                $division = Division::makePreliminary($divisionNames[$key]);
                $tournament->divisions()->save($division);
                $division->teams()->saveMany($teams);
                $this->makeGames($tournament, $division, $teams);

                return $division;
            });

        return $tournament;
    }

    private function makeGames($tournament, $division, $teams)
    {
        if($division->isPlayoff()) {
            return $this->makePlayoffGames($tournament, $division, $teams);
        }

        return $this->makePreliminaryGames($tournament, $division, $teams);
    }

    private function makePreliminaryGames($tournament, $division, $teams)
    {
        $teams
            ->each(function($team, $index) use ($tournament, $division, $teams) {
                $restTeams = $teams->slice($index + 1);

                $restTeams
                    ->each(function($oponent) use ($tournament, $division, $team) {
                        return Game::make($tournament, $division, $team, $oponent)->save();
                    });
            });
    }

    private function finishDivision(Division $division)
    {
        $unfinishedGamesCount = $division
            ->games()
            ->isPending()
            ->count();

        if($unfinishedGamesCount) return;

        $division->finish()->save();

        $this->proceedTournament($division->tournament);
    }

    private function proceedTournament(Tournament $tournament)
    {
        $unfinishedDivisionsCount = $tournament
            ->divisions()
            ->isPending()
            ->count();

        if($unfinishedDivisionsCount) return;

        switch($tournament->status) {
            case Tournament::STATUS_PRELIMINARY:
                dd($tournament);
                break;

        }
    }
}
