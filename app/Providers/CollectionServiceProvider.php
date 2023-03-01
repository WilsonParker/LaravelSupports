<?php

namespace LaravelSupports\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Collection;

class CollectionServiceProvider extends ServiceProvider
{
    public function register()
    {
        /**
         * Model 으로 이루어진 collection 에서 $needle 과 곂치는 부분이 있는지 확인 합니다
         * collection 일 경우 $prop 또는 primaryKey 에 적합한 값이 있는지 확인 합니다.
         * array 일 경우 collection 으로 변환 후 재실행 합니다.
         * callable 일 경우 $needle callback 에 적합한 값이 있는지 확인 합니다.
         * Model 또는 string 일 경우 primaryKey 또는 $prop 에 적합한 값이 있는지 확인 합니다.
         *
         * @param Collection | array | Model $needle
         * @param string                     $pros
         * @return bool
         * @author  WilsonParker
         * @added   2020/11/11
         * @updated 2021/10/12
         */
        Collection::macro('exists', function (
            Collection|array|callable|Model|string $needle,
            string                                 $prop = null
        ): bool {
            $getPropValue = function (Model $item) use ($prop) {
                return isset($prop) ? $item->{$prop} : $item->getPrimaryValue();
            };
            $modelCallback = function (Model $item, $needle) use ($getPropValue, $prop) {
                if ($needle instanceof Model) {
                    return $getPropValue($item) == $getPropValue($needle);
                } else {
                    return $getPropValue($item) == $needle;
                }
            };

            if ($needle instanceof Collection) {
                foreach ($this as $item) {
                    foreach ($needle as $needleItem) {
                        if ($modelCallback($item, $needleItem)) {
                            return true;
                        }
                    }
                }
            } else if (is_array($needle)) {
                return $this->exists(collect($needle), $prop);
            } else if (is_callable($needle)) {
                foreach ($this as $item) {
                    if ($needle($item)) {
                        return true;
                    }
                }
            } else {
                foreach ($this as $item) {
                    if ($modelCallback($item, $needle)) {
                        return true;
                    }
                }
            }

            return false;
        });

        /**
         * collection 에 $keys 중 하나라도 일치하는 key 값이 있는지 확인 합니다
         *
         * @param Collection | array $keys
         * @return bool
         * @author  WilsonParker
         * @added   2020/12/08
         * @updated 2020/12/08
         */
        Collection::macro('existKey', function ($keys): bool {
            if ($keys instanceof Collection || is_array($keys)) {
                foreach ($keys as $key) {
                    if ($this->has($key)) {
                        return true;
                    }
                }
            } else {
                return $this->has($keys);
            }
            return false;
        });

        /**
         * $callback 에 해당하는 내용이 존재하는지 확인 합니다
         *
         * @param Collection | array | Model $needle
         * @param                            $callback
         * function($item, $needle)
         * @return bool
         * @author  WilsonParker
         * @added   2021/03/04
         * @updated 2021/03/04
         */
        Collection::macro('existsCallback', function ($needle, $callback): bool {
            if ($needle instanceof Collection || is_array($needle)) {
                foreach ($this as $item) {
                    foreach ($needle as $needleItem) {
                        if ($callback($item, $needleItem)) {
                            return true;
                        }
                    }
                }
            } else {
                foreach ($this as $item) {
                    if ($callback($item, $needle)) {
                        return true;
                    }
                }
            }

            return false;
        });

        /**
         * $prop 으로 비교하여 $collection 에 해당하는 item 들을 포함한 Collection 을 제공 합니다
         * $collection 에 단일 item 을 넘길 수 있습니다
         *
         * @param Collection | array | Model $collection
         * @param string                     $prop
         * @return Collection
         * @author  WilsonParker
         * @added   2020/11/12
         * @updated 2020/11/12
         */
        Collection::macro('include', function ($collection, string $prop = 'id'): Collection {
            if ($collection instanceof Collection) {
                return $this->filter(function ($item) use ($collection, $prop) {
                    return $collection->exists($item, $prop);
                });
            } else if (is_array($collection)) {
                return $this->filter(function ($item) use ($collection, $prop) {
                    return in_array($item->{$prop}, $collection);
                });
            } else {
                return $collection->exists($collection, $prop) ? collect([$collection]) : collect([]);
            }
        });

        /**
         * key 값이 $arr 에 포함되는 데이터를 제공 합니다
         *
         * @param array $arr
         * @return Collection
         * @author  WilsonParker
         * @added   2020/12/09
         * @updated 2020/12/09
         */
        Collection::macro('includeKeys', function (array $arr): Collection {
            return $this->filter(function ($item, $key) use ($arr) {
                return in_array($key, $arr);
            });
        });

        /**
         * $prop 으로 비교하여 $collection 에 해당하는 item 들을 제외시킨 Collection 을 제공 합니다
         *
         * @param Collection|array $collection
         * @param string           $prop
         * @return Collection
         * @author  WilsonParker
         * @added   2020/11/11
         * @updated 2020/11/11
         * @updated 2023/03/02
         */
        Collection::macro('exclude', function (Collection|array $collection, string $prop = 'id'): Collection {
            $collection = collect($collection);
            return $this->filter(function ($item) use ($collection, $prop) {
                return !$collection->exists($item, is_object($item) ? $prop : null);
            });
        });

        /**
         * key 값이 $arr 에 포함되지 않는 데이터를 제공 합니다
         *
         * @param Collection|array $data
         * @return Collection
         * @author  WilsonParker
         * @added   2020/12/08
         * @updated 2020/12/08
         * @updated 2023/03/02
         */
        Collection::macro('excludeKeys', function (Collection|array $collection): Collection {
            return $this->filter(function ($item, $key) use ($collection) {
                return !in_array($key, $collection);
            });
        });

        /**
         * filter 를 위한 $callback 을 이용하여
         * true, false 결과에 맞게 분할 합니다
         *
         * @param $callback
         * @return Collection
         * @author  WilsonParker
         * @added   2020/11/13
         * @updated 2020/11/13
         */
        Collection::macro('divide', function ($callback, $isKeys = false): Collection {
            $true = $this->filter(function ($item) use ($callback) {
                return $callback($item);
            });
            $false = $isKeys ? $this->diffKeys($true) : $this->diff($true);

            return collect([
                'true' => $true,
                'false' => $false,
            ]);
        });

        /**
         * Model 으로 이루어진 collection 에서 $needle 에 해당하는 값을 제공합니다
         * collection 일 경우 $prop 또는 primaryKey 에 적합한 값이 있는지 확인 합니다.
         * array 일 경우 collection 으로 변환 후 재실행 합니다.
         * callable 일 경우 $needle callback 에 적합한 값이 있는지 확인 합니다.
         * Model 또는 string 일 경우 primaryKey 또는 $prop 에 적합한 값이 있는지 확인 합니다.
         *
         * @param Collection | array | Model $needle
         * @param string                     $pros
         * @return Model
         * @author  WilsonParker
         * @added   2021/11/18
         * @updated 2021/11/18
         */
        Collection::macro('getFirst', function (
            Collection|array|callable|Model|string $needle,
            string                                 $prop = null
        ): ?Model {
            $getPropValue = function (Model $item) use ($prop) {
                return isset($prop) ? $item->{$prop} : $item->getPrimaryValue();
            };
            $modelCallback = function (Model $item, $needle) use ($getPropValue, $prop) {
                if ($needle instanceof Model) {
                    return $getPropValue($item) == $getPropValue($needle);
                } else {
                    return $getPropValue($item) == $needle;
                }
            };

            if ($needle instanceof Collection) {
                foreach ($this as $item) {
                    foreach ($needle as $needleItem) {
                        if ($modelCallback($item, $needleItem)) {
                            return $item;
                        }
                    }
                }
            } else if (is_array($needle)) {
                return $this->exists(collect($needle), $prop);
            } else if (is_callable($needle)) {
                foreach ($this as $item) {
                    if ($needle($item)) {
                        return $item;
                    }
                }
            } else {
                foreach ($this as $item) {
                    if ($modelCallback($item, $needle)) {
                        return $item;
                    }
                }
            }

            return null;
        });
    }
}
