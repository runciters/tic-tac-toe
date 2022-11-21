<?php

declare(strict_types=1);

namespace App;

use App\Entity\Game;
use App\Repository\GameRepositoryInterface;
use App\ValueObject\GameResult;

class GameHandler
{
    public function __construct(
        private readonly GameRepositoryInterface $repository
    )
    {}

    public function new(): string
    {
        $game = Game::create();
        $this->repository->save($game);

        return (string) $game->getId();
    }

    public function play(string $gameId, int $player, int $position): GameResult
    {
        $game = $this->repository->findOrFail($gameId);
        $game->play($player, $position);
        $this->repository->save($game);

        return new GameResult($game);
    }
}