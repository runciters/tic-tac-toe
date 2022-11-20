<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GameApiTest extends WebTestCase
{
    public function testCreateANewGame(): void
    {
        $client = static::createClient();
        $client->request('POST', '/game');
        $content = $this->getJsonContent($client);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('gameId', $content);
        $this->assertIsString($content['gameId']);
    }

    public function testPlayGame(): void
    {
        $client = static::createClient();
        $client->request('POST', '/game');
        $newGameContent = $this->getJsonContent($client);
        $gameId = $newGameContent['gameId'];

        $client->request('PATCH', "/game/$gameId", ['player' => 1, 'x' => 1, 'y' => 1]);
        $content = $this->getJsonContent($client);

        $this->assertResponseIsSuccessful();
        $this->assertSame($gameId, $content['gameId']);
        $this->assertSame([[null, null, null], [null, 1, null], [null, null, null]], $content['state']);
        $this->assertFalse($content['isWon']);
        $this->assertSame('Player One', $content['lastMoveBy']);
    }

    public function testPlayGameNotFound(): void
    {
        $client = static::createClient();

        $client->request('PATCH', "/game/1234", ['player' => 1, 'x' => 1, 'y' => 1]);
        $content = $this->getJsonContent($client);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertSame('Game not found', $content['error']);
    }

    public function testPlayGameWithoutRequiredParameters(): void
    {
        $client = static::createClient();
        $client->request('POST', '/game');
        $newGameContent = $this->getJsonContent($client);
        $gameId = $newGameContent['gameId'];

        $client->request('PATCH', "/game/$gameId");
        $content = $this->getJsonContent($client);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertSame('Required parameters: player, x, y', $content['error']);
    }

    public function testPlayGameWithInvalidMove(): void
    {
        $client = static::createClient();
        $client->request('POST', '/game');
        $newGameContent = $this->getJsonContent($client);
        $gameId = $newGameContent['gameId'];

        $client->request('PATCH', "/game/$gameId", ['player' => 3, 'x' => 1, 'y' => 1]);
        $content = $this->getJsonContent($client);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertSame('We have player 1 and 2 only :(', $content['error']);
    }

    private function getJsonContent(KernelBrowser $client): mixed
    {
        $response = $client->getResponse();

        return \json_decode($response->getContent(), true);
    }
}