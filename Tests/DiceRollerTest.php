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

    function testRollReturnsEmptyArray(){
        $this->assertEquals([], $this->diceroller->roll());
    }

    function testRollReturnsValueHigherThan0(){
        $this->assertGreaterThan(0, $this->diceroller->roll('1d6')[0]);
    }

    function testRollingADieReturnsValuesWithinLimits(){
        $this->forAll(
            Generator\pos()
        )
            ->then(function ($n) {
                $this->assertThat(
                    $this->diceroller->roll("1d{$n}")[0],
                    $this->logicalAnd(
                        $this->greaterThanOrEqual(1),
                        $this->lessThanOrEqual($n)
                    )
                );
            });
    }

    function testNDiceReturnNResults(){
        $this->forAll(
            Generator\pos()
        )
            ->then(function ($n) {
                $this->assertEquals($n, count($this->diceroller->roll("{$n}d6")));
            });
    }
}

