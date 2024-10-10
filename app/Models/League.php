<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class League extends Model
{
    use HasFactory;

    protected $table = "league";
    protected $guarded = [];
    
    public function clubs(): BelongsToMany 
    {
        return $this->belongsToMany(Club::class, 'league_clubs', 'league_id', 'club_id')
        ->withPivot('played', 'won', 'drawn', 'lost', 'gf', 'ga')
        ->orderByRaw('((league_clubs.won * 3) + league_clubs.drawn) DESC');
    }
}