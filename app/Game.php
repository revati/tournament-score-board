<?php namespace App;

use DomainException;
use Illuminate\Database\Eloquent\Model;

class Game extends Model {
    protected $fillable = ['tournament_id', 'division_id', 'team_a_id', 'team_b_id'];

    const STATUS_PENDING = Division::STATUS_PENDING;
    const STATUS_FINISHED = Division::STATUS_FINISHED;

    public static function make(Tournament $tournament, Division $division, Team $teamA, Team $teamB)
    {
        return new static([
            'tournament_id' => $tournament->id,
            'division_id' => $division->id,
            'team_a_id' => $teamA->id,
            'team_b_id' => $teamB->id,
        ]);
    }

    public function finish($score)
    {
        if(! array_key_exists($this->team_a_id, $score) || ! array_key_exists($this->team_b_id, $score)) {
            throw new DomainException("[{$this->id}] To finish game, must provide score for both teams!");
        }

        $this->team_a_score = $score[$this->team_a_id];
        $this->team_b_score = $score[$this->team_b_id];
        $this->status = static::STATUS_FINISHED;

        return $this;
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function isBetween(Team $teamA, Team $teamB)
    {
        return ! $teamA->equals($teamB) && $this->isTeamPlaying($teamA) && $this->isTeamPlaying($teamB);
    }

    public function isFinished()
    {
        return $this->status === static::STATUS_FINISHED;
    }

    public function isTied()
    {
        return $this->team_a_score === $this->team_b_score;
    }

    public function getWinningTeamIdAttribute()
    {
        return $this->team_a_score > $this->team_b_score
            ? $this->team_a_id
            : $this->team_b_id;
    }

    public function scopeIsPending($query)
    {
        $query->where('status', static::STATUS_PENDING);
    }

    public function isTeamPlaying(Team $teamA)
    {
        return in_array($teamA->id, [$this->team_a_id, $this->team_b_id]);
    }

    public function teamScore(Team $teamA)
    {
        switch($teamA->id) {
            case $this->team_a_id: return $this->team_a_score;
            case $this->team_b_id: return $this->team_b_score;
        }
    }
}
