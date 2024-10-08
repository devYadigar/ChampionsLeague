<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeagueClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('league_clubs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_id');
            $table->unsignedBigInteger('league_id');
            $table->unsignedTinyInteger('played')->default(0);
            $table->unsignedTinyInteger('won')->default(0);
            $table->unsignedTinyInteger('drawn')->default(0);
            $table->unsignedTinyInteger('lost')->default(0);
            $table->unsignedTinyInteger('gf')->default(0);
            $table->unsignedTinyInteger('ga')->default(0);
            $table->timestamps();
            
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->foreign('league_id')->references('id')->on('league')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('league_clubs');
    }
}
