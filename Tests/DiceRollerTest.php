<?php

require __DIR__ . '/../vendor/autoload.php';
require_once dirname(__FILE__) . '/../DiceRoller.php';

use Eris\Generator;
use PHPUnit\Framework\TestCase;

class DiceRollerTest extends TestCase
{
    use Eris\TestTrait;

    private $diceroller;

    function setUp(){
        $this->diceroller = new DiceRoller;
    }

    function testInvalidRollInputReturnsEmptyArray(){
        $this->assertEquals([], $this->diceroller->rollToArray());
        $this->assertEquals([], $this->diceroller->rollToArray('invalid'));
        $this->assertEquals([], $this->diceroller->rollToArray('d3'));
        $this->assertEquals([], $this->diceroller->rollToArray('3d'));
    }

    function testRollReturnsValueHigherThanZero(){
        $this->assertGreaterThan(0, $this->diceroller->rollToArray('1d6')[0]);
    }

    function testPropertyRollingADieReturnsValuesWithinLimits(){
        $this->forAll(
            Generator\pos()
        )
            ->then(function ($n) {
                $this->assertThat(
                    $this->diceroller->rollToArray("1d{$n}")[0],
                    $this->logicalAnd(
                        $this->greaterThanOrEqual(1),
                        $this->lessThanOrEqual($n)
                    )
                );
            });
    }

    function testPropertyNDiceReturnNResults(){
        $this->forAll(
            Generator\pos()
        )
            ->then(function ($n) {
                $this->assertEquals($n, count($this->diceroller->rollToArray("{$n}d6")));
            });
    }

    function testInvalidTotalInputReturnsZero(){
        $this->assertEquals(0, $this->diceroller->total());
        $this->assertEquals(0, $this->diceroller->total('d3'));
        $this->assertEquals(0, $this->diceroller->total('3d'));
    }

    function testTotalSumNumbers(){
        $this->assertEquals(4, $this->diceroller->total('1+3'));
        $this->assertEquals(6, $this->diceroller->total('1+3+2'));
        $this->assertEquals(8, $this->diceroller->total('6-1+3'));
    }

    function testTotalSumDice(){
        $commandResultsBetween = function ($command, $min, $max)
        {
            for ($i = 0; $i < 100; $i++) {
                $this->assertThat(
                    $this->diceroller->total($command),
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

    function testPropertyTotalIsCommutative(){
        $this->forAll(
            Generator\int(),
            Generator\int()
        )
            ->then(function ($first, $second) {
                $this->assertEquals(
                    $this->diceroller->total("{$first}+{$second}"),
                    $this->diceroller->total("{$second}+{$first}")
                    );
            });
    }
}

