<?php

declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Uid\Ulid;

final class GameId
{
    public function __construct(
        private readonly Ulid $ulid
    )
    {}

    public function __toString(): string
    {
        return $this->ulid->toRfc4122();
    }
}