<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface LeagueRepositoryInterface
{
    public function create(array $data): array;
    public function exists(string $sessionId): bool;
    public function getWithClubs(string $sessionId): mixed;

}