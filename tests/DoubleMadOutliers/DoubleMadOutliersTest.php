<?php

use PHPUnit\Framework\TestCase;

use DoubleMadOutliers\DoubleMadOutliers;

class DoubleMadOutliersTest extends TestCase
{
    /**
     * @covers DoubleMadOutliers\DoubleMadOutliers::getMedian
     */
    public function testGetMedian()
    {
        $data = [1,2,3,4,5];
        $dmo = new DoubleMadOutliers($data);

        $this->assertEquals(3, $dmo->getMedian());
    }

    /**
     * @covers DoubleMadOutliers\DoubleMadOutliers::calculateMedian
     */
    public function testCalculateMedian()
    {
        // Test single median value
        $data_single = [1,2,3,4,5,6,7,8,9];
        $dmo = new DoubleMadOutliers($data_single);

        $this->assertEquals(5, $dmo->getMedian());

        // Test average median value
        $data_average = [1,2,3,4,5,6,7,8,9,10];
        $dmo = new DoubleMadOutliers($data_average);

        $this->assertEquals(5.5, $dmo->getMedian());
    }

    /**
     * @covers DoubleMadOutliers\DoubleMadOutliers::doubleMad
     */
    public function testDoubleMad()
    {
        // Test average median value
        $dmo = new DoubleMadOutliers([1,2,3,4,5,6,7,8,9,10]);

        $this->assertEquals(['left' => 2.5, 'right' => 2.5], $dmo->doubleMad());

        // Test single median value
        $dmo = new DoubleMadOutliers([1,2,3,4,5,6,7,8,9]);
        $this->assertEquals(['left' => 2, 'right' => 2], $dmo->doubleMad());
    }

    /**
     * @covers DoubleMadOutliers\DoubleMadOutliers::findOutliers
     */
    public function testFindOutliers()
    {
        $data = [30, 10, 4, 7, 4, 5, 5, 7, 8, 1, 16, 4, 5, 5];

        // Test default cutoff
        $dmo = new DoubleMadOutliers($data);
        $this->assertEquals([9 => 1, 10 => 16, 0 => 30], $dmo->findOutliers());

        // Test small cutoff
        $dmo = new DoubleMadOutliers($data, 1);
        $expected = [9 => 1, 2 => 4, 4 => 4, 11 => 4, 8 => 8, 1 => 10, 10 => 16, 0 => 30];
        $this->assertEquals($expected, $dmo->findOutliers());

        // Test large cutoff
        $dmo = new DoubleMadOutliers($data, 10);
        $this->assertEquals([0 => 30], $dmo->findOutliers());
    }
}