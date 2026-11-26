<?php

namespace App\Services;

class WhoGrowthStandardsService
{
    /**
     * WHO Weight-for-Age (BB/U) Standard Table (0 - 60 Months)
     * Values: [-3SD, -2SD, -1SD, Median, +1SD, +2SD, +3SD]
     */
    protected static array $bbUBoys = [
        0  => [2.1, 2.5, 2.9, 3.3, 3.9, 4.4, 5.0],
        1  => [2.9, 3.4, 3.9, 4.5, 5.1, 5.8, 6.6],
        2  => [3.8, 4.3, 4.9, 5.6, 6.3, 7.1, 8.0],
        3  => [4.4, 5.0, 5.7, 6.4, 7.2, 8.0, 9.0],
        4  => [4.9, 5.6, 6.2, 7.0, 7.8, 8.7, 9.7],
        5  => [5.3, 6.0, 6.7, 7.5, 8.4, 9.3, 10.4],
        6  => [5.7, 6.4, 7.1, 7.9, 8.8, 9.8, 10.9],
        7  => [5.9, 6.7, 7.4, 8.3, 9.2, 10.3, 11.4],
        8  => [6.2, 6.9, 7.7, 8.6, 9.6, 10.7, 11.9],
        9  => [6.4, 7.1, 8.0, 8.9, 9.9, 11.0, 12.3],
        10 => [6.6, 7.4, 8.2, 9.2, 10.2, 11.4, 12.7],
        11 => [6.8, 7.6, 8.4, 9.4, 10.5, 11.7, 13.0],
        12 => [6.9, 7.7, 8.6, 9.6, 10.8, 12.0, 13.3],
        15 => [7.4, 8.3, 9.2, 10.3, 11.5, 12.8, 14.3],
        18 => [7.8, 8.8, 9.8, 10.9, 12.2, 13.7, 15.3],
        21 => [8.2, 9.2, 10.3, 11.5, 12.9, 14.5, 16.2],
        24 => [8.6, 9.7, 10.8, 12.2, 13.6, 15.3, 17.1],
        30 => [9.4, 10.5, 11.8, 13.3, 15.0, 16.9, 19.0],
        36 => [10.0, 11.3, 12.7, 14.3, 16.2, 18.3, 20.7],
        42 => [10.7, 12.0, 13.6, 15.3, 17.4, 19.7, 22.4],
        48 => [11.3, 12.7, 14.4, 16.3, 18.6, 21.2, 24.2],
        54 => [11.9, 13.4, 15.2, 17.3, 19.8, 22.7, 26.0],
        60 => [12.4, 14.1, 16.0, 18.3, 21.0, 24.2, 27.9],
    ];

    protected static array $bbUGirls = [
        0  => [2.0, 2.4, 2.8, 3.2, 3.7, 4.2, 4.8],
        1  => [2.7, 3.2, 3.6, 4.2, 4.8, 5.5, 6.2],
        2  => [3.4, 3.9, 4.5, 5.1, 5.8, 6.6, 7.5],
        3  => [4.0, 4.5, 5.2, 5.8, 6.6, 7.5, 8.5],
        4  => [4.4, 5.0, 5.7, 6.4, 7.3, 8.2, 9.3],
        5  => [4.8, 5.4, 6.1, 6.9, 7.8, 8.8, 10.0],
        6  => [5.1, 5.7, 6.5, 7.3, 8.2, 9.3, 10.6],
        7  => [5.3, 6.0, 6.8, 7.6, 8.6, 9.8, 11.1],
        8  => [5.6, 6.3, 7.0, 7.9, 9.0, 10.2, 11.6],
        9  => [5.8, 6.5, 7.3, 8.2, 9.3, 10.5, 12.0],
        10 => [5.9, 6.7, 7.5, 8.5, 9.6, 10.9, 12.4],
        11 => [6.1, 6.9, 7.7, 8.7, 9.9, 11.2, 12.8],
        12 => [6.3, 7.0, 7.9, 8.9, 10.1, 11.5, 13.1],
        15 => [6.7, 7.6, 8.5, 9.6, 10.9, 12.4, 14.1],
        18 => [7.2, 8.1, 9.1, 10.2, 11.6, 13.2, 15.0],
        21 => [7.6, 8.6, 9.6, 10.9, 12.3, 14.0, 16.0],
        24 => [8.1, 9.0, 10.2, 11.5, 13.0, 14.8, 17.0],
        30 => [8.8, 10.0, 11.2, 12.7, 14.4, 16.5, 18.9],
        36 => [9.6, 10.8, 12.2, 13.9, 15.8, 18.1, 20.8],
        42 => [10.2, 11.6, 13.1, 15.0, 17.2, 19.8, 22.8],
        48 => [10.9, 12.3, 14.0, 16.1, 18.5, 21.5, 24.8],
        54 => [11.5, 13.0, 14.9, 17.2, 19.9, 23.2, 27.0],
        60 => [12.1, 13.7, 15.8, 18.2, 21.2, 24.9, 29.1],
    ];

    /**
     * WHO Height/Length-for-Age (TB/U atau PB/U) Standard Table (0 - 60 Months)
     * Values in cm: [-3SD, -2SD, -1SD, Median, +1SD, +2SD, +3SD]
     */
    protected static array $tbUBoys = [
        0  => [44.2, 46.1, 48.0, 49.9, 51.8, 53.7, 55.6],
        1  => [48.9, 50.8, 52.8, 54.7, 56.7, 58.6, 60.6],
        2  => [52.4, 54.4, 56.4, 58.4, 60.4, 62.4, 64.4],
        3  => [55.3, 57.3, 59.4, 61.4, 63.5, 65.5, 67.6],
        4  => [57.6, 59.7, 61.8, 63.9, 66.0, 68.0, 70.1],
        5  => [59.6, 61.7, 63.8, 65.9, 68.0, 70.1, 72.2],
        6  => [61.2, 63.3, 65.5, 67.6, 69.8, 71.9, 74.0],
        7  => [62.7, 64.8, 67.0, 69.2, 71.3, 73.5, 75.7],
        8  => [64.0, 66.2, 68.4, 70.6, 72.8, 75.0, 77.2],
        9  => [65.2, 67.5, 69.7, 72.0, 74.2, 76.5, 78.7],
        10 => [66.4, 68.7, 71.0, 73.3, 75.6, 77.9, 80.1],
        11 => [67.6, 69.9, 72.2, 74.5, 76.9, 79.2, 81.5],
        12 => [68.6, 71.0, 73.4, 75.7, 78.1, 80.5, 82.9],
        15 => [71.6, 74.1, 76.6, 79.1, 81.7, 84.2, 86.7],
        18 => [74.2, 76.9, 79.6, 82.3, 85.0, 87.7, 90.4],
        21 => [76.5, 79.4, 82.3, 85.1, 88.0, 90.9, 93.8],
        24 => [78.7, 81.7, 84.8, 87.8, 90.9, 93.9, 97.0],
        30 => [82.7, 86.0, 89.2, 92.5, 95.7, 99.0, 102.3],
        36 => [86.1, 89.6, 93.1, 96.6, 100.1, 103.6, 107.1],
        42 => [89.2, 93.0, 96.7, 100.5, 104.2, 108.0, 111.7],
        48 => [92.0, 96.0, 100.0, 104.0, 108.0, 112.0, 116.0],
        54 => [94.7, 98.9, 103.2, 107.4, 111.6, 115.8, 120.0],
        60 => [97.1, 101.6, 106.1, 110.5, 115.0, 119.5, 123.9],
    ];

    protected static array $tbUGirls = [
        0  => [43.6, 45.4, 47.3, 49.1, 51.0, 52.9, 54.7],
        1  => [47.8, 49.8, 51.7, 53.7, 55.6, 57.6, 59.5],
        2  => [51.0, 53.0, 55.0, 57.1, 59.1, 61.1, 63.2],
        3  => [53.5, 55.6, 57.7, 59.8, 61.9, 64.0, 66.1],
        4  => [55.6, 57.8, 59.9, 62.1, 64.3, 66.4, 68.6],
        5  => [57.4, 59.6, 61.8, 64.0, 66.2, 68.5, 70.7],
        6  => [58.9, 61.2, 63.5, 65.7, 68.0, 70.3, 72.5],
        7  => [60.3, 62.7, 65.0, 67.3, 69.6, 71.9, 74.2],
        8  => [61.7, 64.0, 66.4, 68.7, 71.1, 73.5, 75.8],
        9  => [62.9, 65.3, 67.7, 70.1, 72.6, 75.0, 77.4],
        10 => [64.1, 66.5, 69.0, 71.5, 73.9, 76.4, 78.9],
        11 => [65.2, 67.7, 70.3, 72.8, 75.3, 77.8, 80.3],
        12 => [66.3, 68.9, 71.4, 74.0, 76.6, 79.2, 81.7],
        15 => [69.5, 72.2, 74.8, 77.5, 80.2, 83.0, 85.7],
        18 => [72.3, 75.1, 78.0, 80.8, 83.7, 86.6, 89.5],
        21 => [74.9, 77.9, 80.9, 83.9, 86.9, 89.9, 92.9],
        22 => [75.8, 78.8, 81.9, 84.9, 87.9, 90.9, 94.0],
        24 => [78.0, 81.0, 84.1, 87.1, 90.2, 93.2, 96.3],
        30 => [82.0, 85.3, 88.6, 91.9, 95.2, 98.5, 101.8],
        36 => [85.4, 88.9, 92.4, 96.0, 99.5, 103.1, 106.6],
        42 => [88.5, 92.2, 96.0, 99.8, 103.6, 107.4, 111.2],
        48 => [91.3, 95.2, 99.2, 103.2, 107.2, 111.2, 115.2],
        54 => [93.9, 98.1, 102.3, 106.5, 110.7, 114.9, 119.1],
        60 => [96.3, 100.7, 105.1, 109.5, 113.9, 118.4, 122.8],
    ];

    /**
     * WHO BMI-for-Age (IMT/U) Standard Table (0 - 60 Months)
     * Values: [-3SD, -2SD, -1SD, Median, +1SD, +2SD, +3SD]
     */
    protected static array $imtUBoys = [
        0  => [10.9, 12.0, 13.0, 14.1, 15.3, 16.5, 17.8],
        6  => [13.2, 14.2, 15.2, 16.3, 17.5, 18.7, 20.0],
        12 => [13.7, 14.6, 15.6, 16.6, 17.7, 18.9, 20.2],
        24 => [13.0, 13.8, 14.7, 15.7, 16.8, 17.9, 19.1],
        36 => [12.6, 13.3, 14.2, 15.1, 16.1, 17.2, 18.4],
        48 => [12.3, 13.0, 13.8, 14.7, 15.7, 16.8, 18.0],
        60 => [12.1, 12.7, 13.5, 14.4, 15.4, 16.6, 17.9],
    ];

    protected static array $imtUGirls = [
        0  => [10.8, 11.8, 12.8, 13.9, 15.1, 16.3, 17.6],
        6  => [12.7, 13.7, 14.8, 15.9, 17.1, 18.4, 19.8],
        12 => [13.2, 14.1, 15.1, 16.2, 17.4, 18.7, 20.0],
        24 => [12.7, 13.5, 14.4, 15.4, 16.5, 17.7, 19.0],
        36 => [12.3, 13.0, 13.9, 14.8, 15.8, 16.9, 18.2],
        48 => [12.0, 12.7, 13.5, 14.4, 15.4, 16.6, 17.9],
        60 => [11.8, 12.4, 13.2, 14.1, 15.2, 16.4, 17.8],
    ];

    /**
     * Get lookup table for Weight-for-Age (BB/U).
     */
    public static function getBbUTable(string $gender): array
    {
        return strtoupper($gender) === 'P' ? self::$bbUGirls : self::$bbUBoys;
    }

    /**
     * Get lookup table for Height-for-Age (TB/U).
     */
    public static function getTbUTable(string $gender): array
    {
        return strtoupper($gender) === 'P' ? self::$tbUGirls : self::$tbUBoys;
    }

    /**
     * Get lookup table for BMI-for-Age (IMT/U).
     */
    public static function getImtUTable(string $gender): array
    {
        return strtoupper($gender) === 'P' ? self::$imtUGirls : self::$imtUBoys;
    }

    /**
     * Linear interpolation between closest reference points in standard tables.
     */
    public static function interpolatePoints(array $table, float $x): array
    {
        $keys = array_keys($table);
        sort($keys);

        if ($x <= $keys[0]) {
            return $table[$keys[0]];
        }
        if ($x >= end($keys)) {
            return $table[end($keys)];
        }

        $prev = $keys[0];
        $next = $keys[0];
        foreach ($keys as $k) {
            if ($k <= $x) {
                $prev = $k;
            }
            if ($k >= $x) {
                $next = $k;
                break;
            }
        }

        if ($prev === $next) {
            return $table[$prev];
        }

        $factor = ($x - $prev) / ($next - $prev);
        $res = [];
        for ($i = 0; $i < 7; $i++) {
            $res[$i] = round($table[$prev][$i] + $factor * ($table[$next][$i] - $table[$prev][$i]), 2);
        }

        return $res;
    }

    /**
     * Estimate Z-Score from reference 7-point array [-3SD, -2SD, -1SD, 0SD, +1SD, +2SD, +3SD].
     */
    public static function estimateZScore(float $value, array $ref): float
    {
        $m = $ref[3]; // Median
        if ($value == $m) {
            return 0.0;
        }

        if ($value < $m) {
            // Below median
            $sd = $m - $ref[1]; // Distance from 0 to -2SD divided by 2
            $oneSd = $sd > 0 ? $sd / 2 : 1.0;
            return round(($value - $m) / $oneSd, 2);
        } else {
            // Above median
            $sd = $ref[5] - $m; // Distance from 0 to +2SD divided by 2
            $oneSd = $sd > 0 ? $sd / 2 : 1.0;
            return round(($value - $m) / $oneSd, 2);
        }
    }

    /**
     * Classify BB/U (Weight-for-Age)
     * Standard Kemenkes RI / WHO:
     * < -3 SD       : Berat Badan Sangat Kurang (Severely Underweight)
     * -3 SD s/d <-2 : Berat Badan Kurang (Underweight)
     * -2 SD s/d +1  : Berat Badan Normal (Normal)
     * > +1 SD       : Risiko Berat Badan Lebih
     */
    public static function classifyBbU(string $gender, float $ageMonths, float $weight): array
    {
        $table = self::getBbUTable($gender);
        $ref = self::interpolatePoints($table, $ageMonths);
        $z = self::estimateZScore($weight, $ref);

        if ($z < -3.0) {
            $status = 'Berat Badan Sangat Kurang';
            $badge = ['bg' => 'var(--nx-soft-red)', 'color' => 'var(--nx-danger)', 'icon' => 'fa-triangle-exclamation'];
        } elseif ($z < -2.0) {
            $status = 'Berat Badan Kurang';
            $badge = ['bg' => 'var(--nx-warning-soft)', 'color' => '#B45309', 'icon' => 'fa-circle-exclamation'];
        } elseif ($z <= 1.0) {
            $status = 'Berat Badan Normal';
            $badge = ['bg' => 'var(--nx-mint-soft)', 'color' => 'var(--nx-forest)', 'icon' => 'fa-circle-check'];
        } else {
            $status = 'Risiko Berat Badan Lebih';
            $badge = ['bg' => 'var(--nx-powder)', 'color' => '#1F517F', 'icon' => 'fa-info-circle'];
        }

        return [
            'zscore' => $z,
            'status' => $status,
            'badge' => $badge,
            'median' => $ref[3],
            'min_normal' => $ref[1],
            'max_normal' => $ref[4],
        ];
    }

    /**
     * Classify TB/U (Length/Height-for-Age)
     * Standard Kemenkes RI / WHO:
     * < -3 SD       : Sangat Pendek (Severely Stunted)
     * -3 SD s/d <-2 : Pendek (Stunted)
     * -2 SD s/d +3  : Normal
     * > +3 SD       : Tinggi
     */
    public static function classifyTbU(string $gender, float $ageMonths, float $height): array
    {
        $table = self::getTbUTable($gender);
        $ref = self::interpolatePoints($table, $ageMonths);
        $z = self::estimateZScore($height, $ref);

        if ($z < -3.0) {
            $status = 'Sangat Pendek (Severely Stunted)';
            $badge = ['bg' => 'var(--nx-soft-red)', 'color' => 'var(--nx-danger)', 'icon' => 'fa-triangle-exclamation'];
        } elseif ($z < -2.0) {
            $status = 'Pendek (Stunted)';
            $badge = ['bg' => 'var(--nx-warning-soft)', 'color' => '#B45309', 'icon' => 'fa-circle-exclamation'];
        } elseif ($z <= 3.0) {
            $status = 'Normal';
            $badge = ['bg' => 'var(--nx-mint-soft)', 'color' => 'var(--nx-forest)', 'icon' => 'fa-circle-check'];
        } else {
            $status = 'Tinggi';
            $badge = ['bg' => 'var(--nx-powder)', 'color' => '#1F517F', 'icon' => 'fa-arrow-up'];
        }

        return [
            'zscore' => $z,
            'status' => $status,
            'badge' => $badge,
            'median' => $ref[3],
            'min_normal' => $ref[1],
            'max_normal' => $ref[5],
        ];
    }

    /**
     * Classify BB/TB (Weight-for-Height / Wasting)
     */
    public static function classifyBbTb(string $gender, float $height, float $weight): array
    {
        // Expected weight for height ratio median approximation (approx 0.13 * height^1.5 or simplified WHO table median)
        // Ratio = weight / (height/100)^2
        $heightM = $height / 100.0;
        $bmi = $heightM > 0 ? ($weight / ($heightM * $heightM)) : 16.0;

        return self::classifyImtU($gender, 24, $bmi);
    }

    /**
     * Classify IMT/U (BMI-for-Age)
     * Standard Kemenkes RI / WHO:
     * < -3 SD       : Gizi Buruk (Severely Wasted)
     * -3 SD s/d <-2 : Gizi Kurang (Wasted)
     * -2 SD s/d +1  : Gizi Baik (Normal)
     * > +1 SD s/d +2: Berisiko Gizi Lebih (Risk of Overweight)
     * > +2 SD s/d +3: Gizi Lebih (Overweight)
     * > +3 SD       : Obesitas (Obese)
     */
    public static function classifyImtU(string $gender, float $ageMonths, float $bmi): array
    {
        $table = self::getImtUTable($gender);
        $ref = self::interpolatePoints($table, $ageMonths);
        $z = self::estimateZScore($bmi, $ref);

        if ($z < -3.0) {
            $status = 'Gizi Buruk (Severely Wasted)';
            $badge = ['bg' => 'var(--nx-soft-red)', 'color' => 'var(--nx-danger)', 'icon' => 'fa-triangle-exclamation'];
        } elseif ($z < -2.0) {
            $status = 'Gizi Kurang (Wasted)';
            $badge = ['bg' => 'var(--nx-warning-soft)', 'color' => '#B45309', 'icon' => 'fa-circle-exclamation'];
        } elseif ($z <= 1.0) {
            $status = 'Gizi Baik (Normal)';
            $badge = ['bg' => 'var(--nx-mint-soft)', 'color' => 'var(--nx-forest)', 'icon' => 'fa-circle-check'];
        } elseif ($z <= 2.0) {
            $status = 'Berisiko Gizi Lebih';
            $badge = ['bg' => 'var(--nx-powder)', 'color' => '#1F517F', 'icon' => 'fa-info-circle'];
        } elseif ($z <= 3.0) {
            $status = 'Gizi Lebih (Overweight)';
            $badge = ['bg' => 'var(--nx-warning-soft)', 'color' => '#D97706', 'icon' => 'fa-triangle-exclamation'];
        } else {
            $status = 'Obesitas';
            $badge = ['bg' => 'var(--nx-soft-red)', 'color' => 'var(--nx-danger)', 'icon' => 'fa-circle-xmark'];
        }

        return [
            'zscore' => $z,
            'status' => $status,
            'badge' => $badge,
            'median' => $ref[3],
        ];
    }

    /**
     * Get chart dataset configuration for Chart.js
     * Returns: labels (ages), curves (-3SD, -2SD, Median, +2SD, +3SD), and colors.
     */
    public static function getChartJsReference(string $gender, string $type = 'bb_u'): array
    {
        $table = match ($type) {
            'tb_u'  => self::getTbUTable($gender),
            'imt_u' => self::getImtUTable($gender),
            default => self::getBbUTable($gender),
        };

        $ages = array_keys($table);
        sort($ages);

        $minus3 = [];
        $minus2 = [];
        $median = [];
        $plus2  = [];
        $plus3  = [];

        foreach ($ages as $age) {
            $vals = $table[$age];
            $minus3[] = ['x' => $age, 'y' => $vals[0]];
            $minus2[] = ['x' => $age, 'y' => $vals[1]];
            $median[] = ['x' => $age, 'y' => $vals[3]];
            $plus2[]  = ['x' => $age, 'y' => $vals[5]];
            $plus3[]  = ['x' => $age, 'y' => $vals[6]];
        }

        return [
            'type'   => $type,
            'gender' => strtoupper($gender),
            'ages'   => $ages,
            'minus3' => $minus3,
            'minus2' => $minus2,
            'median' => $median,
            'plus2'  => $plus2,
            'plus3'  => $plus3,
        ];
    }
}
