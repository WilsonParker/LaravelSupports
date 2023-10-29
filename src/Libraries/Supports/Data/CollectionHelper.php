<?php

/**
 * @class   CollectionHelper.php
 * @author  WilsonParker
 * @brief   Collection 관련 function 을 제공해줍니다

 * @create  20200310
 * @update  20200310
 **/

namespace LaravelSupports\Libraries\Supports\Data;

use Illuminate\Support\Collection;

class CollectionHelper
{

    /**
     * 두 개의 Collection 을 비교 해서 하나라도 포함하면
     * true 를 return 합니다
     *
     * @param   Collection $from
     * @param   Collection $to
     * @return  bool
     * @author  WilsonParker
     * @added   2020/03/10
     * @updated 2020/03/10
     */
    public static function contains(Collection $from, Collection $to)
    {
        return $from->contains(function ($item) use ($to) {
            return $to->contains($item);
        });
    }

}
