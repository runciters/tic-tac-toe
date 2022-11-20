<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Game;
use App\Exception\GameNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class GameRepository implements GameRepositoryInterface
{
    private EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Game::class);
    }

    public function save(Game $game): void
    {
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }

    /**
     * @throws GameNotFound
     */
    public function findOrFail(string $gameId): Game
    {
        try {
            $result = $this->repository->find($gameId);
        } catch (\Throwable) {
            throw new GameNotFound();
        }

        if ($result instanceof Game) {
            return $result;
        }

        throw new GameNotFound();
    }
}
