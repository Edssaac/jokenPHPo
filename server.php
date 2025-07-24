<?php

use Swoole\WebSocket\Server;

$server = new Server("0.0.0.0", 8081);
$players = [];

const MAX_PLAYERS = 2;

$playerIDs = [
    1 => null,
    2 => null
];

$server->on("open", function (Server $server, $req) use (&$players, &$playerIDs) {
    if (count($players) >= MAX_PLAYERS) {
        $server->push($req->fd, json_encode(['fullRoom' => true]));
        $server->close($req->fd);

        return;
    }

    foreach ($playerIDs as $id => $value) {
        if ($value === null) {
            $playerIDs[$id] = $req->fd;

            $players[$req->fd] = ['number' => $id, 'choice' => null];

            echo "Novo jogador conectado: Jogador {$id}\n";

            $server->push($req->fd, json_encode(['playerNumber' => $id]));

            return;
        }
    }
});

$server->on("message", function (Server $server, $frame) use (&$players) {
    $data = json_decode($frame->data, true);

    $fd = $frame->fd;

    if (!isset($data['action']) || $data['action'] !== 'play') {
        return;
    }

    if (isset($data['choice']) && in_array($data['choice'], ['rock', 'paper', 'scissor'])) {
        $players[$fd]['choice'] = $data['choice'];

        echo "Jogador {$players[$fd]['number']} escolheu: {$data['choice']}\n";

        if (count($players) === MAX_PLAYERS && !in_array(null, array_column($players, 'choice'))) {
            $result = determineWinner($players);

            foreach ($players as $playerFd => $player) {
                $server->push($playerFd, json_encode(['result' => $result]));

                $players[$playerFd]['choice'] = null;
            }
        }
    }
});

$server->on("close", function (Server $server, $fd) use (&$players, &$playerIDs) {
    if (isset($players[$fd])) {
        $playerNumber = $players[$fd]['number'];

        echo "Jogador {$playerNumber} desconectado\n";

        unset($players[$fd]);

        $playerIDs[$playerNumber] = null;
    }
});

function determineWinner($players)
{
    $choices = array_column($players, 'choice');

    $winningCombinations = [
        'rock' => 'scissor',
        'paper' => 'rock',
        'scissor' => 'paper'
    ];

    if ($choices[0] === $choices[1]) {
        $winner = 'draw';
    } else if (isset($winningCombinations[$choices[0]]) && $winningCombinations[$choices[0]] === $choices[1]) {
        $winner = 'P1';
    } else {
        $winner = 'P2';
    }

    return [
        'winner' => $winner,
        'P1' => $choices[0],
        'P2' => $choices[1]
    ];
}

$server->start();
