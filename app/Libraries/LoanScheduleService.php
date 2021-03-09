<?php


namespace LaravelSupports\Libraries\Book;


class LoanScheduleService
{
    public function getScheduledDeliveryDate($date = null, $format = 'm/d', $useDay = true)
    {
        return $this->convertScheduledDeliveryDate($this->calcScheduledDeliveryTime($date), $format, $useDay);
    }

    public function calcScheduledDeliveryTime($date = null)
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

    public function convertScheduledDeliveryDate($date, $format = 'm/d', $useDay = true)
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

    public function bindOverDueDate($scheduledReturnDate, $targetDate = null)
    {
        $scheduledDate = $scheduledReturnDate->format('Y-m-d');
        $targetDate = is_null($targetDate) ? date('Y-m-d') : $targetDate;

        $diff = (new \DateTime($targetDate))->diff(new \DateTime($scheduledDate))->days;
        return $targetDate > $scheduledDate ? $diff : null;
    }
}
