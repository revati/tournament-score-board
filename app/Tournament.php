<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model {
    protected $fillable = ['title', 'preliminary'];

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

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }
}
