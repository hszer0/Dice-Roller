<?php

class DiceRoller {
    private function generateResults($amount = 1, $eyes = 1) {
        for ($i = 1; $i <=$amount; $i++) {
            yield rand(1, $eyes);
        }
    }

    function roll($command = ""){
        if (!$command) {
            return [];
        }
        else {
            $commandArray = explode("d", $command);
            return iterator_to_array($this->generateResults($commandArray[0], $commandArray[1])) ;
        }
    }
}