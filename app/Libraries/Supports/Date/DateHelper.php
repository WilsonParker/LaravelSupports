<?php


namespace LaravelSupports\Libraries\Supports\Date;

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
     * 이번 달 정보를 return 합니다
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
     * 이번 달의 마지막 일 정보를 return 합니다
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
     * 다음 달의 마지막 일 정보를 return 합니다
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
     * $month 만큼 지난 달의 월과 일 정보를 return 합니다
     *
     * @param   int $month
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
     * 현재 월과 일 정보를 return 합니다
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

}
