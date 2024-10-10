<?php

namespace App\Repositories;

use App\Models\League;
use App\Repositories\Contracts\LeagueRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class LeagueRepository implements LeagueRepositoryInterface
{

    public function __construct(private League $model)
    {
    }

    public function create(array $data): array
    {
        return $this->model->create($data)->toArray();
    }

    public function exists(string $sessionId): bool
    {
        return $this->model->where('ulid', $sessionId)->exists();
    }

    public function getWithClubs(string $sessionId): mixed
    {
        return $this->model->where('ulid', $sessionId)
        ->with('clubs')
        ->firstOrFail()->toArray();
    }
}