<?php

class Board {
    private $matrix;
    private $m;
    private $n;
    private $o_count;

    public function __construct($m = 10, $n = 10) {

        if ($m < 1 || $n < 1) {
            throw new Exception("Board dimensions must be more than or equal to 1");
        }

        $this->m = $m;
        $this->n = $n;
        $this->o_count = 0;
        for ($j = 1; $j < $n+1; $j++) {
            $pom[$j] = "-";
        }
        
        
        for ($i = 1, $letter = "A"; $i < $m+1; $i++, $letter++) {
            $this->matrix[$letter] = $pom;
        }
    }

    public function print_board() {

        echo "\n\\";
        for ($i = 1; $i < $this->n+1; $i++) {
            echo " $i ";
        }
        echo "\n";

        for ($i = 1, $letter = "A"; $i < $this->m+1; $i++, $letter++) {
            echo $letter;
            for ($j = 1; $j < $this->n+1; $j++) {
                echo " " . $this->matrix[$letter][$j] . " ";
            }
            echo "\n";
        }
    }

    public function get_field($field) {
        list($i, $j) = explode(" ", $field);
        return $this->matrix[$i][$j];
    }

    public function set_field_x($field) {
        list($i, $j) = explode(" ", $field);
        if ($this->matrix[$i][$j] == "O") {
            $this->o_count--;
        }
        $this->matrix[$i][$j] = "X";
    }

    public function set_field_o($field) {
        list($i, $j) = explode(" ", $field);
        if ($this->matrix[$i][$j] != "O") {
            $this->matrix[$i][$j] = "O";
            $this->o_count++;
        }
    }

    public function o_count() {
        return $this->o_count;
    }

    public function in_bounds($field) {
        list($i, $j) = explode(" ", $field);
        return array_key_exists($i, $this->matrix) && array_key_exists($j, $this->matrix["A"]);
    }

}

class Player {
    private $my_board;
    private $opponents_board;
    private $name;

    public function __construct($name, $my_board, $opponents_board) {
        $this->my_board = $my_board;
        $this->opponents_board = $opponents_board;
        $this->name = $name;
    }

    public function attack_field($field) {
        if ($this->my_board->get_field($field) != "O") {
            return false;
        }
        $this->my_board->set_field_x($field);
        return true;
    }

    public function mark_miss($field) {
        $this->opponents_board->set_field_x($field);
    }

    public function mark_hit($field) {
        $this->opponents_board->set_field_o($field);
    }

    public function place_ship($field) {
        $this->my_board->set_field_o($field);
    }

    public function get_move() {
        return readline($this->name . "'s move (enter row and column, separated by a space character ex. A 4): ");
    }

    public function valid_ship_placement($field) {
        return $this->my_board->in_bounds($field) && ($this->my_board->get_field($field) == "-");
    }

    public function valid_move($field) {
        return $this->opponents_board->in_bounds($field) && ($this->opponents_board->get_field($field) == "-");
    }

    public function ship_count() {
        return $this->my_board->o_count();
    }

    public function display() {
        echo "\n{$this->name}'s board: ";
        $this->my_board->print_board();
        echo "\nOponents board: ";
        $this->opponents_board->print_board();
    }

    public function print_my_board() {
        $this->my_board->print_board();
    }

    public function get_name() {
        return $this->name;
    }

}



class Game {
    private $player1;
    private $player2;
    private $current_player;
    private $has_winner;

    public function __construct($p1, $p2) {
        $this->player1 = $p1;
        $this->player2 = $p2;
        $this->current_player = $p1;
        $this->has_winner = false;
    }

    public function make_move() {
        $this->current_player->display();
        $field = $this->current_player->get_move();
        if (!$this->current_player->valid_move($field)) {
            echo "\nInvalid field!\n";
            return;
        }
        $other_player = $this->current_player == $this->player1 ? $this->player2 : $this->player1;
        $attack_successful = $other_player->attack_field($field);

        if (!$attack_successful) {
            $this->current_player->mark_miss($field);
            $this->current_player = $other_player;
            echo "\nMiss!\n";
            return;
        }

        $this->current_player->mark_hit($field);
        echo "\nHit!\n";
        if ($other_player->ship_count() == 0) {
            $this->has_winner = true;
            echo $this->current_player->get_name() . " wins!\n";

        }
    }

    public function is_over() {
        return $this->has_winner;
    }

    public function initialize_players() {
        echo "\n\n\n\n=========================================\n" . $this->player1->get_name() . " is placing ships\n========================================="; 
        $this->initialize_player($this->player1);
        echo "\n\n\n\n=========================================\n" . $this->player2->get_name() . " is placing ships\n=========================================";
        $this->initialize_player($this->player2);
    }

    private function initialize_player($player) {
        $count = 5;
        while ($count > 0) {
            $player->print_my_board();
            echo "\nFields to choose: " . $count . "\n";
            $field = $player->get_move();
            if (!$player->valid_ship_placement($field)) {
                continue;
            }
            $player->place_ship($field);
            $count--;
        }
        $player->print_my_board();
    }
}



