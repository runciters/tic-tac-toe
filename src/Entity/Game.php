<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Player;
use App\Enum\Position;
use App\Enum\PositionCoordinate;
use App\Exception\GameOver;
use App\Exception\InvalidMove;
use App\Exception\InvalidPlayer;
use App\ValueObject\GameId;
use Doctrine\ORM\Mapping as ORM;
use SplFixedArray;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity()]
class Game
{
    private const BOARD_SIZE = 3;

    #[ORM\Id]
    #[ORM\Column(type: "ulid", unique: true)]
    private readonly Ulid $id;

    #[ORM\Column(type: "json")]
    private array $state;

    #[ORM\Column(type: "smallint", nullable: true, enumType: Player::class)]
    private ?Player $lastMoveBy = null;

    #[ORM\Column(type: "smallint", nullable: true, enumType: Player::class)]
    private ?Player $wonBy = null;

    #[ORM\Column(type: "boolean")]
    private bool $isCompleted = false;

    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
        $this->id = new Ulid();
        $this->initState();
    }

    public function getId(): GameId
    {
        return new GameId($this->id);
    }

    /**
     *    0 1 2 x
     *  0 _|_|_
     *  1 _|_|_
     *  2  | |
     *  y
     */
    public function getState(): array
    {
        return $this->state;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function getWonBy(): ?Player
    {
        return $this->wonBy;
    }

    public function getLastMoveBy(): ?Player
    {
        return $this->lastMoveBy;
    }

    public function play(int $player, int $position): void
    {
        if ($this->isCompleted) {
            throw new GameOver();
        }

        try {
            $player = Player::from($player);
        } catch (\ValueError) {
            throw new InvalidPlayer();
        }

        if ($this->lastMoveBy === $player) {
            throw new InvalidMove('Please wait for your turn to play');
        }

        try {
            [$x, $y] = Position::from($position)->getCoordinates();
        } catch (\ValueError) {
            throw new InvalidMove('Cannot play off the board');
        }

        $currentState = $this->loadState();
        if (null !== $currentState[$y][$x]) {
            throw new InvalidMove("Invalid move: this is cheating :)");
        }

        $currentState[$y][$x] = $player->value;

        $winner = $this->findAWinner($currentState);

        $this->saveState($currentState);
        $this->lastMoveBy = $player;

        if (null !== $winner) {
            $this->wonBy = $winner;
            $this->isCompleted = true;
        } else {
            $isCompleted = $this->checkIfCompleted($currentState);
            $this->isCompleted = $isCompleted;
        }
    }

    private function checkIfCompleted(SplFixedArray $state): bool
    {
        foreach ($state as $row) {
            foreach ($row as $cell) {
                if (null === $cell) {
                    return false;
                }
            }
        }

        return true;
    }

    private function findAWinner(SplFixedArray $state): ?Player
    {
        // Checking Rows
        for ($row = 0; $row < self::BOARD_SIZE; $row++) {
            if (null !== $state[$row][0] && $state[$row][0] === $state[$row][1] && $state[$row][1] === $state[$row][2]) {
                return Player::from($state[$row][0]);
            }
        }

        // Checking Columns
        for ($column = 0; $column < self::BOARD_SIZE; $column++) {
            if (null !== $state[0][$column] && $state[0][$column] === $state[1][$column] && $state[1][$column] === $state[2][$column]) {
                return Player::from($state[0][$column]);
            }
        }

        // Checking Diagonals
        if (null !== $state[0][0] && $state[0][0] === $state[1][1] && $state[1][1] === $state[2][2]) {
            return Player::from($state[0][0]);
        }

        if (null !== $state[0][2] && $state[0][2] === $state[1][1] && $state[1][1] === $state[2][0]) {
            return Player::from($state[0][2]);
        }

        return null;
    }

    private function loadState(): SplFixedArray
    {
        $currentState = new SplFixedArray(self::BOARD_SIZE);
        foreach ($this->state as $index => $item) {
            $currentState[$index] = SplFixedArray::fromArray($item);
        }

        return $currentState;
    }

    /**
     * @param SplFixedArray<int, SplFixedArray> $state
     */
    private function saveState(SplFixedArray $state): void
    {
        $newState = [];
        foreach ($state as $index => $item) {
            $newState[$index] = $item->toArray();
        }

        $this->state = $newState;
    }

    private function initState(): void
    {
        $this->state = [
            [null, null, null],
            [null, null, null],
            [null, null, null]
        ];
    }
}
