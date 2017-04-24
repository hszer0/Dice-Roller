<?php
require_once dirname(__FILE__) . '/../DiceRoller.php';
use PHPUnit\Framework\TestCase;

class DiceRollerTest extends TestCase
{
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

    function testRollingAd6ReturnsCorrectValues(){
        $results = $this->diceroller->roll('1d6');
        foreach($results as $result) {
            $this->assertThat(
                $result,
                $this->logicalAnd(
                    $this->greaterThan(0),
                    $this->lessThan(7)
                )
            );
        }
    }

    function testMultipleDiceReturnMultipleResults(){
        $this->assertEquals(2, count($this->diceroller->roll('2d6')));
    }
}
