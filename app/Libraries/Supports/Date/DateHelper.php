<?php


namespace LaravelSupports\Libraries\Supports\Date;

use Carbon\Carbon;

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

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * 이번 달 정보를 제공 합니다
     *
     * @return  string
     * @author  dew9163
     * @added   2020/03/16
     * @updated 2020/03/16
     */
    public function getCurrentMonth(): string
    {
        return date("n");
    }

    /**
     * 이번 달의 마지막 일 정보를 제공 합니다
     *
     * @return  string
     * @author  dew9163
     * @added   2020/03/16
     * @updated 2020/03/16
     */
    public function getLastDayOfAMonth(): string
    {
        return $this->date->format('t');
    }

    /**
     * $date 에 해당하는 날짜의 마지막 일 정보를 제공 합니다
     *
     * @param
     * @return
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public function getLastDayOfDate($date)
    {
        return $date->format('t');
    }

    /**
     * 다음 달의 마지막 일 정보를 제공 합니다
     *
     * @return  string
     * @author  dew9163
     * @added   2020/03/16
     * @updated 2020/03/16
     */
    public function getLastDayOfNextMonth(): string
    {
        return date("t", strtotime("+1 month"));
    }

    /**
     * $month 만큼 지난 달의 월과 일 정보를 제공 합니다
     *
     * @param int $month
     * @return  string
     * @author  dew9163
     * @added   2020/03/16
     * @updated 2020/03/16
     */
    public function getMonthAndLastDayOfMonth(int $month = 0): string
    {
        return date("n월 t일", strtotime("+$month month"));
    }

    /**
     * 현재 월과 일 정보를 제공 합니다
     *
     * @return  string
     * @author  dew9163
     * @added   2020/04/23
     * @updated 2020/04/23
     */
    public function getNowMonthAndDayOfMonth(): string
    {
        return date("n월 d일");
    }

    /**
     * $month 를 더한 날짜 정보를 제공 합니다
     *
     * @param int $month
     * @param string $format
     * @return false|string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public function getDateTheMonthAdded($month = 0, $format = 'Y-m-d H:i:s')
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
     * @return Carbon|null
     * @author  dew9163
     * @added   2020/06/18
     * @updated 2020/06/18
     */
    public function addDate(Carbon $date, $unit, $value)
    {
        switch ($unit) {
            case 'hours':
                return $date->addHours($value);
            case 'days':
                return $date->addDays($value);
            case 'months':
                return $date->addMonths($value);
            case 'years':
                return $date->addYears($value);
            default :
                return null;
        }
    }
}
