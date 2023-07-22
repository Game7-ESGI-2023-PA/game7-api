<?php

namespace App\Service;

use Exception;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class QueryGameDispatcher
{

    public function __construct(private HttpClientInterface $client){}

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function queryGameEngine(
        array $initArgs,
        string $gameDir,
        string $gameExecutable,
        array $instructions
    ): ?array
    {
        $body = [
            "initArgs" => $initArgs,
            "gameDir" => $gameDir,
            "gameExecutable" => $gameExecutable,
            "instructions" => $instructions,
        ];

        // dd($body);
        $response = $this->client->request(
            'POST',
            'http://game-dispatcher:3000/execute-game/python',
            [
                "json" => $body
            ]
        );

        if($response->getStatusCode() !== 200) { return null;}

        return $response->toArray();
    }
}
