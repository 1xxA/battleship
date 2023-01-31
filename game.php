<?php
require_once('classes.php');

$player1 = new Player("Danko", new Board(), new Board());
$player2 = new Player("Zarko", new Board(), new Board());

$game = new Game($player1, $player2);

$game->initialize_players();
echo "\n\n\n=======GAME START=======\n";
while (!$game->is_over()) {
    $game->make_move();
}

