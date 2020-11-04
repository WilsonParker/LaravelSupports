<?php


namespace LaravelSupports\Libraries\Supports\Date;

use Carbon\Carbon;
use DateTime;

/**
 * 날짜 관련 Helper 클래스 입니다
 *
 * @author  dew9163
 * @added   2020/03/16
 * @updated 2020/03/16
 */
class DateHelper
{
    private $date;
    const DEF_FORMAT = 'Y-m-d H:i:s';
    const YEARS = 'years';
    const MONTHS = 'months';
    const DAYS = 'days';
    const DAY_OF_DAYS = 1;
    const DAY_OF_MONTHS = 30;
    const DAY_OF_YEARS = 365;

    const SIMPLE_DAY_OF_MONTHS = 30;
    const SIMPLE_DAY_OF_YEARS = 360;

    public function __construct()
    {
        $this->date = new DateTime();
    }

    public function getCurrentTime($format = self::DEF_FORMAT)
    {
        return date($format);
    }

    public function getCurrentMonth()
    {
        return date("n");
    }

    public function getLastDayOfAMonth()
    {
        return $this->date->format('t');
    }

    public function getLastDayOfDate($date)
    {
        return $date->format('t');
    }

    public function getLastDayOfNextMonth()
    {
        return date("t", strtotime("+1 month"));
    }

    public function getMonthAndLastDayOfNextMonth()
    {
        return date("n월 t일", strtotime("+1 month"));
    }

    public function getNowMonthAndDayOfMonth()
    {
        return date("n월 d일");
    }

    public function getDateTheMonthAdded($month = 0, $format = self::DEF_FORMAT)
    {
        return date($format, strtotime("+$month month"));
    }

    public function formatDate($date, $format = self::DEF_FORMAT)
    {
        return Carbon::parse($date)->format($format);
    }

    /**
     * $date 에 $unit 단위로 $value 만큼 add 합니다
     *
     * @param Carbon $date
     * @param $unit
     * @param $value
     * @param bool $overflow
     * @return Carbon|null
     * @author  dew9163
     * @added   2020/06/18
     * @updated 2020/06/18
     * @updated 2020/07/06
     * add weeks
     * @updated 2020/08/31
     * add $overflow
     * when $overflow is true, adding the date moves to the next date
     * (apply in months, years)
     */
    public function addDate(Carbon $date, $unit, $value, $overflow = false)
    {
        switch ($unit) {
            case 'hours':
                $date->addHours($value);
                break;
            case 'days':
                $date->addDays($value);
                break;
            case 'weeks':
                $date->addWeeks($value);
                break;
            case 'months':
                if ($overflow) {
                    $date->addMonths($value);
                } else {
                    $currentDay = $date->day;
                    $dayOfNextMonth = $date->day(1)->addMonth($value)->daysInMonth;
                    $day = $currentDay < $dayOfNextMonth ? $currentDay : $dayOfNextMonth;
                    $date->day($day);
                }
                break;
            case 'years':
                $date->addYears($value);
                break;
        }
        return $date;
    }

    /**
     * $fromUnit 의 $value 를
     * $toUnit 의 값으로 변환하여 제공 합니다
     *
     * @param $fromUnit
     * @param $value
     * @param $toUnit
     * @return null
     * @author  dew9163
     * @added   2020/06/24
     * @updated 2020/06/24
     */
    public function convertDateInteger($fromUnit, $value, $toUnit)
    {
        switch ($fromUnit) {
            case self::DAYS:
                $days = $value * self::DAY_OF_DAYS;
                break;
            case self::MONTHS:
                $days = $value * self::DAY_OF_MONTHS;
                break;
            case self::YEARS:
                $days = $value * self::DAY_OF_YEARS;
                break;
            default :
                return 0;
        }

        switch ($toUnit) {
            case self::DAYS:
                $result = $days / self::DAY_OF_DAYS;
                break;
            case self::MONTHS:
                $result = $days / self::DAY_OF_MONTHS;
                break;
            case self::YEARS:
                $result = $days / self::DAY_OF_YEARS;
                break;
            default :
                return 0;
        }

        return $result;
    }

    public function convertSimpleDateInteger($fromUnit, $value, $toUnit)
    {
        switch ($fromUnit) {
            case self::DAYS:
                $days = $value * self::DAY_OF_DAYS;
                break;
            case self::MONTHS:
                $days = $value * self::SIMPLE_DAY_OF_MONTHS;
                break;
            case self::YEARS:
                $days = $value * self::SIMPLE_DAY_OF_YEARS;
                break;
            default :
                return 0;
        }

        switch ($toUnit) {
            case self::DAYS:
                $result = $days / self::DAY_OF_DAYS;
                break;
            case self::MONTHS:
                $result = $days / self::SIMPLE_DAY_OF_MONTHS;
                break;
            case self::YEARS:
                $result = $days / self::SIMPLE_DAY_OF_YEARS;
                break;
            default :
                return 0;
        }

        return $result;
    }

    /**
     * $from 과 $to 를 비교 합니다
     * $greaterThan 이 true 일 경우
     * $from 이 더 클 경우 true 를 제공 합니다
     *
     * @param Carbon $to
     * @param Carbon $from
     * @param
     * @return void
     * @author  dew9163
     * @added   2020/06/24
     * @updated 2020/06/24
     */
    public function compareDate(Carbon $from, Carbon $to, $greaterThan)
    {

    }
}
