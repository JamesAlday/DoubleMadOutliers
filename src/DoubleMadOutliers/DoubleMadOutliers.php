<?php
/**
 * Double Median Average Deviation (MAD) Outlier Test
 *
 * Uses 'Double MAD' method to find outliers in asymmetric data. It splits the data into 2 'legs'
 * around the median and does a MAD test to check for outliers in each leg.
 * This Prevents high/low outliers to cancel each other out or hide smaller outliers within
 * widely varied data.
 *
 * Usage:
 *  $data = [30, 10, 4, 7, 4, 5, 5, 7, 8, 1, 16, 4, 5, 5];
 *  $cufoff = 3;
 *  $MadOutliers = new DoubleMadOutlierTest($data, $cutoff);
 *  $outliers = $MadOutliers->findOutliers();
 *  Returns: [9 => 1, 10 => 16, 0 => 30]
 *
 * This is an adaptation of the algorithm/R script written by Peter Rosenmai on Eureka Statistics
 * @link https://eurekastatistics.com/using-the-median-absolute-deviation-to-find-outliers/ Sauce of algo.
 */

namespace DoubleMadOutliers;

class DoubleMadOutliers
{
    protected $data;
    private $median;
    private $cutoff;

    /**
     * MadOutlierTest
     *
     * @param array $data Associative array of data to examine for outliers, ['label' => <data>]
     * @param int $cutoff Number of MADs away from median to count as an outlier (Default: 4)
     * @return array      Returns the portion of $data array that are considered outliers
     */
    public function __construct($data, $cutoff = 4)
    {
        $this->data = $data;

        // Make sure array is sorted by value, maintain keys
        asort($this->data, SORT_NUMERIC);

        $this->cutoff = $cutoff;

        // Calculate the median for the dataset
        $this->median = $this->calculateMedian($this->data);
    }

    /**
     * Get MAD for values to the left and right of median value, 'Double MAD'
     *
     * @return array Array containing MAD values for each 'leg' of data, ['left' => 0, 'right' => 0]
     */
    public function doubleMad()
    {
        $left = $right = [];

        // Find absolute deviation from median for each value
        foreach ($this->data as $value) {
            $absdev = abs($value - $this->median);
            // Split deviations into left/right tails, exact median values go in both
            if ($value <= $this->median) {
                $left[] = $absdev;
            }

            if ($value >= $this->median) {
                $right[] = $absdev;
            }
        }

        // Find Median Absolute Deviation for each tail
        $mad = [
            'left' => $this->calculateMedian($left),
            'right' => $this->calculateMedian($right),
        ];

        // MAD=0 === ERROR
        if ($mad['left'] == 0 || $mad['right'] == 0) {
            throw new Exception("MAD is 0");
        }

        return $mad;
    }

    /**
     * Find Outliers.
     *
     * Uses doubleMad method to compare data points to MAD and determine their distance from the median.
     *
     * @return array    Associative array of outliers (uses keys of original data array)
     */
    public function findOutliers()
    {
        $outliers = [];
        $mad_distance = [];

        // Get MAD for lower/upper halves of data
        $dmad = $this->doubleMad();

        foreach ($this->data as $key => $value) {
            // Determine which MAD value to use based on whether value is left/right of median
            $mad = ($value <= $this->median) ? $dmad['left'] : $dmad['right'];

            if ($value === $this->median) {
                $mad_distance[$key] = 0;
            } else {
                // Record distance from MAD for data
                $mad_distance[$key] = abs($value - $this->median) / $mad;
            }
        }

        foreach ($mad_distance as $key => $dist) {
            // Compare distances to cutoff, consider outlier if it's > cutoff
            if ($dist > $this->cutoff) {
                $outliers[$key] = $this->data[$key];
            }
        }

        return $outliers;
    }

    /**
     * Calculate the median value of an array of data
     *
     * @param type $arr Array of data values (keys stripped for calculation)
     * @return integer|float  Output type will depend on type of data entered
     */
    public function calculateMedian($arr)
    {
        $arr = array_values($arr); // make sure we have a non-assoc. array for this step

        $count = count($arr); //total numbers in array
        $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value

        if ($count % 2) {
            // odd number, middle is the median
            $median = $arr[$middleval];
        } else {
            // even number, calculate avg of 2 medians
            $low = $arr[$middleval];
            $high = $arr[$middleval+1];
            $median = (($low+$high)/2);
        }

        return $median;
    }

    /**
     * Return median returned by calculateMedian()
     *
     * @return integer|float
     */
    public function getMedian()
    {
        return $this->median;
    }
}
