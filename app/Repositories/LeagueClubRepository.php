<?php

namespace App\Repositories;

use App\Models\LeagueClub;
use App\Repositories\Contracts\LeagueClubRepositoryInterface;

class LeagueClubRepository implements LeagueClubRepositoryInterface
{
    public function __construct(private LeagueClub $model)
    {
    }

    public function insert(array $data): bool
    {
        return $this->model->insert($data);
    }
}