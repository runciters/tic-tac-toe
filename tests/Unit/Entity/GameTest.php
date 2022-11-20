<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Game;
use App\Enum\Player;
use App\Exception\InvalidMove;
use App\Exception\InvalidPlayer;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{

    public function testCreate()
    {
        $game = Game::create();

        $this->assertInstanceOf(Game::class, $game);
        $this->assertNotNull($game->getId());
        $this->assertFalse($game->isCompleted());
        $this->assertNull($game->getWonBy());
        $this->assertSame([
            [null, null, null],
            [null, null, null],
            [null, null, null]
        ], $game->getState());
    }

    public function testPlayFirstValidMove()
    {
        $game = Game::create();
        $game->play(1, 0, 0);

        $this->assertFalse($game->isCompleted());
        $this->assertNull($game->getWonBy());
        $this->assertSame([
            [   1, null, null],
            [null, null, null],
            [null, null, null]
        ], $game->getState());
    }

    public function testPlaySecondValidMove()
    {
        $game = Game::create();
        $game->play(1, 1, 1);
        $game->play(2, 2, 0);

        $this->assertFalse($game->isCompleted());
        $this->assertNull($game->getWonBy());
        $this->assertSame([
            [null, null,    2],
            [null,    1, null],
            [null, null, null]
        ], $game->getState());
    }

    public function testPlayWithInvalidPlayerShouldThrowException()
    {
        $game = Game::create();

        $this->expectException(InvalidPlayer::class);

        $game->play(3, 0, 0);
    }

    public function testPlayWithMoveOffBoardShouldThrowException()
    {
        $game = Game::create();

        $this->expectException(InvalidMove::class);
        $this->expectExceptionMessage('Cannot play off the board');

        $game->play(1, 4, 0);
    }

    public function testPlayASecondMoveFromSamePlayerShouldThrowException()
    {
        $game = Game::create();
        $game->play(1, 0, 0);

        $this->expectException(InvalidMove::class);
        $this->expectExceptionMessage('Please wait for your turn to play');

        $game->play(1, 0, 1);
    }

    public function testPlayAgainSameMoveShouldThrowException()
    {
        $game = Game::create();
        $game->play(1, 0, 0);
        $game->play(2, 1, 1);

        $this->expectException(InvalidMove::class);
        $this->expectExceptionMessage('Invalid move: this is cheating :)');

        $game->play(1, 0, 0);
    }

    public function testPlayPlayerOneWinsHorizontally()
    {
        $game = Game::create();
        $game->play(1, 0, 0);
        $game->play(2, 1, 1);
        $game->play(1, 1, 0);
        $game->play(2, 2, 1);
        $game->play(1, 2, 0);

        $this->assertTrue($game->isCompleted());
        $this->assertSame(Player::One, $game->getWonBy());
    }

    public function testPlayPlayerOneWinsVertically()
    {
        $game = Game::create();
        $game->play(1, 0, 0);
        $game->play(2, 1, 0);
        $game->play(1, 0, 1);
        $game->play(2, 2, 1);
        $game->play(1, 0, 2);

        $this->assertTrue($game->isCompleted());
        $this->assertSame(Player::One, $game->getWonBy());
    }

    public function testPlayPlayerOneWinsDiagonally()
    {
        $game = Game::create();
        $game->play(1, 0, 0);
        $game->play(2, 1, 0);
        $game->play(1, 1, 1);
        $game->play(2, 2, 1);
        $game->play(1, 2, 2);

        $this->assertTrue($game->isCompleted());
        $this->assertSame(Player::One, $game->getWonBy());
    }

    public function testPlayNoOneWins()
    {
        $game = Game::create();
        $game->play(1, 0, 0);
        $game->play(2, 1, 1);
        $game->play(1, 1, 0);
        $game->play(2, 2, 0);
        $game->play(1, 0, 2);
        $game->play(2, 0, 1);
        $game->play(1, 2, 1);
        $game->play(2, 1, 2);
        $game->play(1, 2, 2);

        $this->assertTrue($game->isCompleted());
        $this->assertNull($game->getWonBy());
    }
}
