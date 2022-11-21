<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\GameNotFound;
use App\Exception\GameOver;
use App\Exception\InvalidMove;
use App\Exception\InvalidPlayer;
use App\GameHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game', name: 'new_game', methods: ['POST'], format: "json", stateless: true)]
    public function newAction(GameHandler $gameHandler): JsonResponse
    {
        $result = $gameHandler->new();

        return $this->json(['gameId' => $result]);
    }

    #[Route('/game/{gameId}', name: 'play_game', methods: ['PATCH'], format: "json", stateless: true)]
    public function playAction(string $gameId, Request $request, GameHandler $gameHandler): JsonResponse
    {
        $player = $request->request->get('player');
        $position = $request->request->get('position');

        if (null === $player | null === $position) {
            throw new BadRequestHttpException("Required parameters: player, position");
        }

        try {
            $result = $gameHandler->play(
                $gameId,
                (int) $player,
                (int) $position
            );
        } catch (GameNotFound $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        } catch (InvalidMove|InvalidPlayer|GameOver $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        return $this->json($result);
    }
}
