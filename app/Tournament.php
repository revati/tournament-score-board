<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model {
    protected $fillable = ['title', 'status'];

    const STATUS_PRELIMINARY = 'preliminary';
    const STATUS_PLAYOFF = 'playoff';
    const STATUS_FINISHED = 'finished';

    public static function makePreliminary($title)
    {
        return new static([
            'title' => $title,
            'status' => static::STATUS_PRELIMINARY,
        ]);
    }

    public function proceedToPlayoff()
    {
        $this->status = static::STATUS_PLAYOFF;

        return $this;
    }

    public function finish()
    {
        $this->status = static::STATUS_FINISHED;

        return $this;
    }

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }
}
