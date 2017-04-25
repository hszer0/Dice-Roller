<?php

class DiceRoller {
    public $resultSet;

    private function generateDieResults($amount = 1, $eyes = 1) {
        for ($i = 1; $i <=$amount; $i++) {
            yield rand(1, $eyes);
        }
    }

    private function splitOnOperators($command){
        $this->resultSet = explode("+", str_replace("-", "+-", $command));
        return $this;
    }

    private function calculateDieRolls(){
        $rollAndSum = function ($command) {
            return array_sum($this->rollToArray($command));
        };

        $this->resultSet = array_map($rollAndSum, $this->resultSet);
        return $this;
    }

    function rollToArray($command = ""){
        if (!$command) {
            return [];
        } elseif (is_numeric($command)) {
            return [(int)$command];
        } else {
            $commandArray = explode("d", $command);
            return iterator_to_array($this->generateDieResults($commandArray[0], $commandArray[1])) ;
        }
    }

    function total($command = ""){
        $this->splitOnOperators($command)->calculateDieRolls();
        return array_sum($this->resultSet);
    }
}
