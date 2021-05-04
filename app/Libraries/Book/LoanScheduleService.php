<?php


namespace LaravelSupports\Libraries\Book;


class LoanScheduleService
{
    /**
     * 배송예정일을 제공합니다
     *
     * @param null $date
     * @param string $format
     * @param bool $useDay
     * @return string
     * @author  seul
     * @added   2021/05/04
     * @updated 2021/05/04
     */
    public function getScheduledDeliveryDate($date = null, $format = 'm/d', $useDay = true) : string
    {
        return $this->convertScheduledDeliveryDate($this->calcScheduledDeliveryTime($date), $format, $useDay);
    }

    /**
     * 배송예정일을 계산합니다.
     *
     * @param null $date
     * @return string
     * @author  seul
     * @added   2021/05/04
     * @updated 2021/05/04
     */
    public function calcScheduledDeliveryTime($date = null) : string
    {
        $date = is_null($date) ? now() : $date;
        $day = date('w', strtotime($date));

        if ($day >= 5) {
            $date->next(1);
        } else if ($day == 1 || $day == 3) {
            $date->addDay(2);
        } else {
            $date->addDay(1);
        }

        return $date;
    }

    /**
     * 배송예정일 날짜 문구를 변환합니다
     *
     * @param $date
     * @param string $format
     * @param bool $useDay
     * @return string
     * @author  seul
     * @added   2021/05/04
     * @updated 2021/05/04
     */
    public function convertScheduledDeliveryDate($date, $format = 'm/d', $useDay = true) : string
    {
        $dayKor = ['일', '월', '화', '수', '목', '금', '토'];

        if ($useDay) {
            return $date->format($format) . ' (' . $dayKor[$date->format('w')] . ')';
        } else {
            $dateString = $date->format($format);

            $dateString = str_replace('AM', '오전', $dateString);
            $dateString = str_replace('PM', '오후', $dateString);

            return $dateString;
        }
    }

    /**
     * 연체일을 제공합니다.
     *
     * @param $scheduledReturnDate
     * @param null $targetDate
     * @return int|null
     * @throws \Exception
     * @author  seul
     * @added   2021/05/04
     * @updated 2021/05/04
     */
    public function bindOverDueDate($scheduledReturnDate, $targetDate = null) : ?int
    {
        $scheduledDate = $scheduledReturnDate->format('Y-m-d');
        $targetDate = is_null($targetDate) ? date('Y-m-d') : $targetDate;

        $diff = (new \DateTime($targetDate))->diff(new \DateTime($scheduledDate))->days;
        return $targetDate > $scheduledDate ? $diff : null;
    }
}
