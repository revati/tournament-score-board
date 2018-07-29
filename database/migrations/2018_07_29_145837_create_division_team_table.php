<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDivisionTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('division_team', function (Blueprint $table) {
            $table->unsignedInteger('division_id');
            $table->unsignedInteger('team_id');
            $table->unsignedInteger('score')->default(0);

            $table->primary(['division_id', 'team_id']);
            $table->foreign('division_id')->references('id')->on('divisions');
            $table->foreign('team_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('division_team');
    }
}
