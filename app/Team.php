<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model {
    protected $fillable = ['title'];

    public static function make($title)
    {
        return new static(['title' => $title]);
    }

    public function divisions()
    {
        return $this->belongsToMany(Division::class)->withPivot(['score']);
    }

    public function equals(Team $team)
    {
        return $this->id === $team->id;
    }
}
