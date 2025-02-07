<?php

class DateConverter
{
    /**
     * Convert Farsi (Persian) digits to English digits.
     *
     * @param string $farsiDigits
     * @return string
     */
    public static function convertFarsiToEnglish($farsiDigits)
    {
        $farsiToEnglish = [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        ];

        return strtr($farsiDigits, $farsiToEnglish);
    }

    /**
     * Convert Jalali (Persian) date to Miladi (Gregorian) date.
     *
     * @param string $jalaliDate
     * @return string
     */
    public static function convertJalaliToMiladi($jalaliDate)
    {
        // Using Morilog\Jalali for Jalali to Gregorian conversion
        return JalaliDateTime::fromFormat('Y-m-d', $jalaliDate)->toCarbon()->toDateString();
    }

    /**
     * Convert Jalali (Persian) date to Unix timestamp.
     *
     * @param string $jalaliDate
     * @return int
     */
    public static function convertJalaliToTimestamp($jalaliDate)
    {
        // Using Morilog\Jalali to convert to timestamp
        return JalaliDateTime::fromFormat('Y-m-d', $jalaliDate)->toCarbon()->timestamp;
    }

    /**
     * Convert Miladi (Gregorian) date to Unix timestamp.
     *
     * @param string $miladiDate
     * @return int
     */
    public static function convertMiladiToTimestamp($miladiDate)
    {
        // Using Carbon to handle Gregorian date
        return \Carbon\Carbon::parse($miladiDate)->timestamp;
    }

    /**
     * Convert Unix timestamp to both Jalali and Miladi date formats.
     *
     * @param int $timestamp
     * @return array
     */
    public static function convertTimestampToDates($timestamp)
    {
        // Convert to Miladi date (using Carbon)
        $miladiDate = \Carbon\Carbon::createFromTimestamp($timestamp)->toDateString();

        // Convert to Jalali date (using Morilog\Jalali)
        $jalaliDate = JalaliDateTime::fromCarbon(\Carbon\Carbon::createFromTimestamp($timestamp))->format('Y-m-d');

        return [
            'miladi' => $miladiDate,
            'jalali' => $jalaliDate,
        ];
    }
}