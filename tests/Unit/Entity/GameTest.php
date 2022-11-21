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
        $game->play(1, 1);

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
        $game->play(1, 5);
        $game->play(2, 3);

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

        $game->play(3, 1);
    }

    public function testPlayWithMoveOffBoardShouldThrowException()
    {
        $game = Game::create();

        $this->expectException(InvalidMove::class);
        $this->expectExceptionMessage('Cannot play off the board');

        $game->play(1, 10);
    }

    public function testPlayASecondMoveFromSamePlayerShouldThrowException()
    {
        $game = Game::create();
        $game->play(1, 1);

        $this->expectException(InvalidMove::class);
        $this->expectExceptionMessage('Please wait for your turn to play');

        $game->play(1, 1);
    }

    public function testPlayAgainSameMoveShouldThrowException()
    {
        $game = Game::create();
        $game->play(1, 1);
        $game->play(2, 5);

        $this->expectException(InvalidMove::class);
        $this->expectExceptionMessage('Invalid move: this is cheating :)');

        $game->play(1, 1);
    }

    public function testPlayPlayerOneWinsHorizontally()
    {
        $game = Game::create();
        $game->play(1, 1);
        $game->play(2, 5);
        $game->play(1, 2);
        $game->play(2, 6);
        $game->play(1, 3);

        $this->assertTrue($game->isCompleted());
        $this->assertSame(Player::One, $game->getWonBy());
    }

    public function testPlayPlayerOneWinsVertically()
    {
        $game = Game::create();
        $game->play(1, 1);
        $game->play(2, 2);
        $game->play(1, 4);
        $game->play(2, 6);
        $game->play(1, 7);

        $this->assertTrue($game->isCompleted());
        $this->assertSame(Player::One, $game->getWonBy());
    }

    public function testPlayPlayerOneWinsDiagonally()
    {
        $game = Game::create();
        $game->play(1, 1);
        $game->play(2, 2);
        $game->play(1, 5);
        $game->play(2, 6);
        $game->play(1, 9);

        $this->assertTrue($game->isCompleted());
        $this->assertSame(Player::One, $game->getWonBy());
    }

    public function testPlayNoOneWins()
    {
        $game = Game::create();
        $game->play(1, 1);
        $game->play(2, 5);
        $game->play(1, 2);
        $game->play(2, 3);
        $game->play(1, 7);
        $game->play(2, 4);
        $game->play(1, 6);
        $game->play(2, 8);
        $game->play(1, 9);

        $this->assertTrue($game->isCompleted());
        $this->assertNull($game->getWonBy());
    }
}
