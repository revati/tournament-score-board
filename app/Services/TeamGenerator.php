<?php namespace App\Services;

use App\Team;
use Faker\Factory;

class TeamGenerator {

    private $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory->create();
    }

    public function fetch($count)
    {
        $actualCount = Team::limit($count)->count();

        if($actualCount !== $count) {
            $this->generate($count - $actualCount);
        }

        return Team::limit($count)->get();
    }

    private function generate($count)
    {
        for($i = 0; $i < $count; $i++) {
            Team::make($this->factory->company)->save();
        }
    }
}
