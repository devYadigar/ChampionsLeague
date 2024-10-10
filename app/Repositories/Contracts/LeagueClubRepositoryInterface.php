<?php

namespace App\Repositories\Contracts;

interface LeagueClubRepositoryInterface
{
    public function insert(array $data): bool;
}