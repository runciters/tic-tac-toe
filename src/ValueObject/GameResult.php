<?php

namespace App\ValueObject;

use App\Entity\Game;

final class GameResult implements \JsonSerializable
{
    public function     __construct(
        private readonly Game $game
    )
    {}

    public function toArray(): array
    {
        $result = [
            'gameId' => (string) $this->game->getId(),
            'state' => $this->game->getState(),
            'isWon' => $this->game->getWonBy() !== null,
            'lastMoveBy' => 'Player ' . $this->game->getLastMoveBy()->name
        ];

        if ($this->game->getWonBy() !== null) {
            $result['wonBy'] = $this->game->getWonBy();
        }

        return $result;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}