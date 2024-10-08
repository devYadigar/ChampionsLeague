<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('home_club_id');
            $table->unsignedBigInteger('away_club_id');
            $table->unsignedBigInteger('league_id');
            $table->unsignedTinyInteger('week');
            $table->unsignedTinyInteger('home_goals')->default( 0);
            $table->unsignedTinyInteger('away_goals')->default(0);
            $table->enum('status', ['scheduled', 'completed'])->default('scheduled');
            $table->timestamps();

            $table->foreign('league_id')->references('id')->on('league')
              ->onDelete('cascade');
            $table->foreign('home_club_id')->references('id')->on('clubs')
              ->onDelete('cascade');
            $table->foreign('away_club_id')->references('id')->on('clubs')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
}
