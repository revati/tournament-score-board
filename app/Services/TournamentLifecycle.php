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

    private function makePlayoffGames($tournament, $division, $teams)
    {
        $teams
            ->chunk(2)
            ->map(function($teams) use ($tournament, $division) {
                $teams = $teams->values();
                return Game::make($tournament, $division, $teams[0], $teams[1])->save();
            });
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

    private function updateTeamScores(Game $game, $teamId, $score)
    {
        $teams = $game->division->teams();

        $newScore = $teams->findOrFail($teamId)->pivot->score + $score;
        $teams->updateExistingPivot($teamId, ['score' => $newScore]);
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

    public function proceedTournament(Tournament $tournament)
    {
        $unfinishedDivisionsCount = $tournament
            ->divisions()
            ->isPending()
            ->count();

        if($unfinishedDivisionsCount) return;

        switch($tournament->status) {
            case Tournament::STATUS_PRELIMINARY:
                $this->proceedFromPreliminaryStage($tournament);
                break;
            case Tournament::STATUS_PLAYOFF:
                $this->proceedFromPlayoffStage($tournament);
                break;
        }
    }

    private function proceedFromPreliminaryStage($tournament)
    {
        list($firstTeams, $secondTeams) = $tournament
            ->divisions
            ->map(function($division) {
                return $division->winningTeams()->get();
            });

        $teams = $firstTeams
            ->reverse()
            ->zip($secondTeams)
            ->flatten();

        $tournament->proceedToPlayoff()->save();
        $division = Division::makePlayoff("Play off 1/4");
        $tournament->divisions()->save($division);
        $division->teams()->saveMany($teams);
        $this->makeGames($tournament, $division, $teams);
    }

    private function proceedFromPlayoffStage($tournament)
    {
        // TODO: Use relationship mapper to map hasMany relationship to hasOne (last division)
        $teams = $tournament
            ->divisions()
            ->orderBy('id', 'desc')
            ->firstOrFail()
            ->games
            ->map
            ->winningTeam;

        // On winner, no need to proceeed
        if($teams->count() === 1) {
            $tournament->finish()->save();

            return;
        }

        $number = $teams->count()/2;
        $division = Division::makePlayoff("Play off 1/$number");
        $tournament->divisions()->save($division);
        $division->teams()->saveMany($teams);
        $this->makeGames($tournament, $division, $teams);
    }
}
