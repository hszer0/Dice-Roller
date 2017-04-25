<?php

require __DIR__ . '/../vendor/autoload.php';
require_once dirname(__FILE__) . '/../DiceRoller.php';

use Eris\Generator;
use PHPUnit\Framework\TestCase;

class DiceRollerTest extends TestCase
{
    use Eris\TestTrait;

    private $diceroller;

    function setUp()
    {
        $this->diceroller = new DiceRoller;
    }

    function testInvalidRollInputReturnsEmptyArray()
    {
        $this->assertEquals([], $this->diceroller->rollFromSubcommand());
        $this->assertEquals([], $this->diceroller->rollFromSubcommand('invalid'));
        $this->assertEquals([], $this->diceroller->rollFromSubcommand('d3'));
        $this->assertEquals([], $this->diceroller->rollFromSubcommand('3d'));
    }

    function testRollReturnsValueHigherThanZero()
    {
        $this->assertGreaterThan(0, $this->diceroller->rollFromSubcommand('1d6')[0]);
    }

    function testPropertyRollingADieReturnsValuesWithinLimits()
    {
        $this->forAll(
            Generator\pos()
        )
            ->then(function ($n) {
                $this->assertThat(
                    $this->diceroller->rollFromSubcommand("1d{$n}")[0],
                    $this->logicalAnd(
                        $this->greaterThanOrEqual(1),
                        $this->lessThanOrEqual($n)
                    )
                );
            });
    }

    function testPropertyNDiceReturnNResults()
    {
        $this->forAll(
            Generator\pos()
        )
            ->then(function ($n) {
                $this->assertEquals($n, count($this->diceroller->rollFromSubcommand("{$n}d6")));
            });
    }

    function testInvalidSumInputReturnsZero()
    {
        $this->assertEquals(0, $this->diceroller->rollDiceAndCalculateSum());
        $this->assertEquals(0, $this->diceroller->rollDiceAndCalculateSum('d3'));
        $this->assertEquals(0, $this->diceroller->rollDiceAndCalculateSum('3d'));
    }

    function testSumNumbers()
    {
        $this->assertEquals(4, $this->diceroller->rollDiceAndCalculateSum('1+3'));
        $this->assertEquals(6, $this->diceroller->rollDiceAndCalculateSum('1+3+2'));
        $this->assertEquals(8, $this->diceroller->rollDiceAndCalculateSum('6-1+3'));
    }

    function testSumDice()
    {
        $commandResultsBetween = function ($command, $min, $max) {
            for ($i = 0; $i < 100; $i++) {
                $this->assertThat(
                    $this->diceroller->rollDiceAndCalculateSum($command),
                    $this->logicalAnd(
                        $this->greaterThanOrEqual($min),
                        $this->lessThanOrEqual($max)
                    )
                );
            }
        };

        $commandResultsBetween('1d6+3', 1, 9);
        $commandResultsBetween('1d3+3+1d2', 5, 8);
        $commandResultsBetween('6d4-1', 0, 35);
    }

    function testPropertySumIsCommutative()
    {
        $this->forAll(
            Generator\int(),
            Generator\int()
        )
            ->then(function ($first, $second) {
                $this->assertEquals(
                    $this->diceroller->rollDiceAndCalculateSum("{$first}+{$second}"),
                    $this->diceroller->rollDiceAndCalculateSum("{$second}+{$first}")
                );
            });
    }
}

