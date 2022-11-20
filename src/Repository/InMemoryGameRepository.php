<?php

namespace App\Repository;

use App\Entity\Game;
use App\Exception\GameNotFound;

class InMemoryGameRepository implements GameRepositoryInterface
{
    private array $games = [];

    public function save(Game $game): void
    {
        $this->games[(string) $game->getId()] = $game;
    }

    /**
     * @inheritDoc
     */
    public function findOrFail(string $gameId): Game
    {
        return $this->games[$gameId] ?? throw new GameNotFound();
    }
}