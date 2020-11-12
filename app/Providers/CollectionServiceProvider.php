<?php

namespace LaravelSupports\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Collection;

class CollectionServiceProvider extends ServiceProvider
{
    public function register()
    {
        /**
         * $value 으로 이루어진 collection 에서 같은 $prop 값을 가진 item 이 있는지 체크 합니다
         *
         * @author  dew9163
         * @added   2020/11/11
         * @updated 2020/11/11
         */
        Collection::macro('exists', function ($value, string $prop = 'id'): bool {
            foreach ($this as $item) {
                if ($item->{$prop} == $value->{$prop}) {
                    return true;
                }
            }
            return false;
        });

        /**
         * $prop 으로 비교하여 $collection 에 해당하는 item 들을 포함한 Collection 을 제공 합니다
         * $collection 에 단일 item 을 넘길 수 있습니다
         *
         * @author  dew9163
         * @added   2020/11/12
         * @updated 2020/11/12
         */
        Collection::macro('include', function ($collection, string $prop = 'id'): Collection {
            if ($collection instanceof Collection) {
                return $this->filter(function ($item) use ($collection, $prop) {
                    return $collection->exists($item, $prop);
                });
            } else {
                return $collection->exists($collection, $prop) ? collect([$collection]) : collect([]);
            }
        });

        /**
         * $prop 으로 비교하여 $collection 에 해당하는 item 들을 제외시킨 Collection 을 제공 합니다
         *
         * @author  dew9163
         * @added   2020/11/11
         * @updated 2020/11/11
         */
        Collection::macro('exclude', function (Collection $collection, string $prop = 'id'): Collection {
            return $this->filter(function ($item) use ($collection, $prop) {
                return !$collection->exists($item, $prop);
            });
        });
    }
}
