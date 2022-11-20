<?php

namespace App\Repository;

use App\Entity\Game;
use App\Exception\GameNotFound;

interface GameRepositoryInterface
{
    public function save(Game $game): void;

    /**
     * @throws GameNotFound
     */
    public function findOrFail(string $gameId): Game;
}