<?php

class DiceRoller
{
    public $resultSet;

    private function generateDieResults($amount = 1, $eyes = 1)
    {
        for ($i = 1; $i <= $amount; $i++) {
            yield rand(1, $eyes);
        }
    }

    private function splitOnOperators($command)
    {
        $this->resultSet = explode('+', str_replace('-', '+-', $command));
        return $this;
    }

    private function calculateDieRolls()
    {
        $rollAndSum = function ($command) {
            return array_sum($this->rollFromSubcommand($command));
        };

        $this->resultSet = array_map($rollAndSum, $this->resultSet);
        return $this;
    }

    function isValidInput($command)
    {
        return preg_match('(\d*(([+-]?\d+d\d+)|([+-]\d+(?!d))))', $command);
    }

    function rollToArray($subcommand)
    {
        if ($this->isValidInput($subcommand)) {
            $commandArray = explode('d', $subcommand);
            return iterator_to_array($this->generateDieResults($commandArray[0], $commandArray[1]));
        }

        return [];
    }

    function rollFromSubcommand($subcommand = '')
    {
        if (is_numeric($subcommand)) {
            return [(int)$subcommand];
        }

        return $this->rollToArray($subcommand);
    }

    function rollDiceAndCalculateSum($command = '')
    {
        if ($this->isValidInput($command)) {
            $this->splitOnOperators($command)->calculateDieRolls();
            return array_sum($this->resultSet);
        }

        return 0;
    }
}
