<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ClubRepositoryInterface
{
    public function pluck(string $param): array;
}