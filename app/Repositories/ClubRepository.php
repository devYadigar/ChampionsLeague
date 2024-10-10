<?php

namespace App\Repositories;

use App\Models\Club;
use App\Repositories\Contracts\ClubRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ClubRepository implements ClubRepositoryInterface
{
    public function __construct(private Club $model)
    {
    }

    public function pluck(string $param): array
    {
        return $this->model->pluck($param)->toArray();
    }
}