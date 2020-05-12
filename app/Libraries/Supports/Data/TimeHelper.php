<?php


namespace LaravelSupports\Libraries\Supports\Data;


class TimeHelper
{


    /**
     * 현재 시간 초 를 제공합니다
     *
     * @return  float
     * @author  dew9163
     * @added   2020/02/27
     * @updated 2020/02/27
     */
    public static function getTime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * BookLib 를 테스트하기 위한 함수
     *
     * @param   $callback
     * @return  void
     * @author  dew9163
     * @added   2020/02/27
     * @updated 2020/02/27
     */
    public static function execute($callback)
    {
        $root = "/home/vagrant/FlybookLaravel/Files/svc/addon/flybook";
        include_once $root . "/ini/common.ini.php";
        include_once $root . "/lib/pplane.net/php/loadLibrary.php";
        $request = [];
        $request['dbh'] = new PDO (PDO_DSN, PDO_USER, PDO_PASS);
        $book = new RenewalBook();

        $startDate = date('Y-m-d H:i:s');
        $start = getTime();

        $output = $callback($book, $request);

        $endDate = date('Y-m-d H:i:s');
        $end = getTime();

        $output["startDate"] = $startDate;
        $output["endDate"] = $endDate;
        echo json_encode($output);

        $time = $end - $start;
        $time_arr[] = $time;
        echo '<br/>수행시간: ' . number_format($time, 4) . '초';

        $request["dbh"] = null;
    }
}
