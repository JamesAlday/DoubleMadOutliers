# Double Median Absolute Distribution Outlier Test

This is a small library class that tests asymmetric data for outliers based on the 'Double MAD' method.

The data is split into 2 'legs' around the median, and each leg is tested for values that are larger than the
median absolute deviation for that leg.  This prevents high/low outliers in the data from canceling each other out
or smaller outliers being hidden in widely varied data.

This library is an adaptation of the algorithm/R script written by Peter Rosenmai on Eureka Statistics.  He gives a much more
in-depth analysis of the reasoning behind this method, and it well worth a read if you intend to use this class.

https://eurekastatistics.com/using-the-median-absolute-deviation-to-find-outliers/
