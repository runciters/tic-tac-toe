<?php

namespace App\Tests\Unit;

use App\Entity\Game;
use App\Enum\Player;
use App\Enum\PositionCoordinate;
use App\Exception\GameNotFound;
use App\GameHandler;
use App\Repository\InMemoryGameRepository;
use PHPUnit\Framework\TestCase;

class GameHandlerTest extends TestCase
{

    public function testPlay()
    {
        $game = Game::create();
        $repository = new InMemoryGameRepository();
        $repository->save($game);
        $handler = new GameHandler($repository);

        $result = $handler->play(
            (string) $game->getId(),
            Player::One->value,
            PositionCoordinate::One->value,
            PositionCoordinate::One->value
        );

        $this->assertSame([
            'gameId' => (string) $game->getId(),
            'state' => [
                [null, null, null],
                [null, 1, null],
                [null, null, null],
            ],
            'isWon' => false,
            'lastMoveBy' => 'Player One'
        ], $result->toArray());
    }

    public function testPlayShouldThrowGameNotFound()
    {
        $game = Game::create();
        $repository = new InMemoryGameRepository();
        $handler = new GameHandler($repository);

        $this->expectException(GameNotFound::class);

        $handler->play(
            (string) $game->getId(),
            Player::One->value,
            PositionCoordinate::One->value,
            PositionCoordinate::One->value
        );
    }

    public function testNew()
    {
        $repository = new InMemoryGameRepository();
        $handler = new GameHandler($repository);

        $result = $handler->new();

        $this->assertIsString($result);
        $this->assertInstanceOf(Game::class, $repository->findOrFail($result));
    }
}
