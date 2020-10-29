<?php


namespace SubscribeHR\Graph;

use \PHPUnit\Framework\TestCase;
use SubscribeHR\Parser\CSVParser;

class GraphTest extends TestCase
{
    const TEST_FILES_DIR = './tests/resources/test_files/';

    public function testIfSourceOrDestinationIsIncorrect(){
        $filename = self::TEST_FILES_DIR.'test_graph_correct_values.csv';
        $csvParser = new CSVParser();
        $isValid = $csvParser->isValidCSVFile($filename);
        $this->assertTrue($isValid);
        if($isValid){
            list($verticesMap, $vertices) = $csvParser->extractVerticesAndMap($filename);
            $this->assertEquals(array(
                'A' => 1,
                'B' => 1,
                'C' => 1,
                'D' => 1,
                'E' => 1,
                'F' => 1,
            ), $vertices);

            $this->assertEquals(array(
                'A' => array(
                    'B' => 10,
                    'C' => 20
                ),
                'B' => array(
                    'D' => 100,
                ),
                'C' => array(
                    'D' => 30
                ),
                'D' => array(
                    'E' => 10,
                ),
                'E' => array(
                    'F' => 1000
                )
            ), $verticesMap);

            $graph = new Graph($verticesMap, $vertices);
            $isReachable = $graph->isReachable('A','G');
            $this->assertFalse($isReachable);
        }
    }

    public function testIfSourceOrDestinationAreSame()
    {
        $filename = self::TEST_FILES_DIR . 'test_graph_correct_values.csv';
        $csvParser = new CSVParser();
        $isValid = $csvParser->isValidCSVFile($filename);
        $this->assertTrue($isValid);
        if ($isValid) {
            list($verticesMap, $vertices) = $csvParser->extractVerticesAndMap($filename);
            $graph = new Graph($verticesMap, $vertices);
            $isReachable = $graph->isReachable('A', 'A');
            $this->assertFalse($isReachable);
        }
    }

    public function testPathNotFound(){
        $filename = self::TEST_FILES_DIR.'test_graph_correct_values.csv';
        $csvParser = new CSVParser();
        $isValid = $csvParser->isValidCSVFile($filename);
        $this->assertTrue($isValid);
        if($isValid){
            list($verticesMap, $vertices) = $csvParser->extractVerticesAndMap($filename);
            $graph = new Graph($verticesMap, $vertices);
            $isReachable = $graph->isReachable('D','A');
            $this->assertFalse($isReachable);
        }
    }

    public function testPathFound(){
        $filename = self::TEST_FILES_DIR.'test_graph_correct_values.csv';
        $csvParser = new CSVParser();
        $isValid = $csvParser->isValidCSVFile($filename);
        $this->assertTrue($isValid);
        if($isValid){
            list($verticesMap, $vertices) = $csvParser->extractVerticesAndMap($filename);
            $graph = new Graph($verticesMap, $vertices);
            $this->assertTrue($graph->isReachable('A','F'));
            $this->assertTrue($graph->isReachable('B','E'));
        }
    }

    public function testPathFoundWithWeightLimit(){
        $filename = self::TEST_FILES_DIR.'test_graph_correct_values.csv';
        $csvParser = new CSVParser();
        $isValid = $csvParser->isValidCSVFile($filename);
        $this->assertTrue($isValid);
        if($isValid){
            list($verticesMap, $vertices) = $csvParser->extractVerticesAndMap($filename);
            $graph = new Graph($verticesMap, $vertices);

            $weightLimit = 1200;
            $this->assertTrue($graph->isReachable('A','F'));
            $weights = $graph->getWeights();
            $this->assertNotEmpty($weights);
            $this->assertNotEquals(0, $weights['F']);
            $this->assertLessThanOrEqual($weightLimit, $weights['F']);

            $requiredPathArr = array(
                'A' => array('B','C'),
                'B' => array('D'),
                'D' => array('E'),
                'E' => array('F')
            );
            $this->assertEquals($requiredPathArr, $graph->getPath());
        }
    }
}