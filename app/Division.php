<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Division extends Model {
    protected $fillable = ['title', 'type'];

    const TYPE_PLAYOFF = Tournament::STATUS_PLAYOFF;
    const TYPE_PRELIMINARY = Tournament::STATUS_PRELIMINARY;

    const STATUS_PENDING = 'pending';
    const STATUS_FINISHED = Tournament::STATUS_FINISHED;

    public static function makePreliminary($title)
    {
        return new static([
            'title' => $title,
            'type' => static::TYPE_PRELIMINARY,
            'status' => static::STATUS_PENDING,
        ]);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)
            ->withPivot(['score'])
            ->orderBy('pivot_score', 'desc');
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function scopeIsPending($query)
    {
        $query->where('status', static::STATUS_PENDING);
    }

    public function isPlayoff()
    {
        return $this->type === static::TYPE_PLAYOFF;
    }

    public function findGame($team, $oponent)
    {
        return $this->games
            ->first(function($game) use ($team, $oponent) {
                return $game->isBetween($team, $oponent);
            });
    }

    public function finish()
    {
        $this->status = static::STATUS_FINISHED;

        return $this;
    }
}
