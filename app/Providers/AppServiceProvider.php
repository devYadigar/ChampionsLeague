<?php

namespace App\Providers;

use App\Repositories\ClubRepository;
use App\Repositories\Contracts\ClubRepositoryInterface;
use App\Repositories\Contracts\LeagueClubRepositoryInterface;
use App\Repositories\Contracts\LeagueRepositoryInterface;
use App\Repositories\LeagueClubRepository;
use App\Repositories\LeagueRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LeagueRepositoryInterface::class, LeagueRepository::class);
        $this->app->bind(ClubRepositoryInterface::class, ClubRepository::class);
        $this->app->bind(LeagueClubRepositoryInterface::class, LeagueClubRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
