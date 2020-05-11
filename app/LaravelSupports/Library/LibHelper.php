<?php


namespace App\Library;
use Illuminate\Pagination\LengthAwarePaginator;

class LibHelper
{

    /**
     * @param  $data
     * @param  $limit
     * @param  $currentPage
     * @param  $options
     * @brief   커스텀 페이지네이션
     * @return   LengthAwarePaginator $paginator
     * @throws
     */
    public static function customPaginator($data = array(), $limit = 10, $currentPage = 1, $options = array("path" => "/", "query" => array()))
    {
        if ($limit < 1) {
            $limit = 10;
        }

        if ($currentPage < 1) {
            $currentPage = 1;
        }

        if (is_object($data)) {
            if (method_exists($data, "toArray")) {
                $data = $data->toArray();
            } else {
                $data = collect($data)->toArray();
            }
        } else {
            if (!is_array($data)) {
                return false;
            }
        }
        $paginator = new LengthAwarePaginator(array_slice($data, ($currentPage - 1) * $limit, $limit), count($data), $limit, $currentPage, $options);
        return $paginator;
    }

}
