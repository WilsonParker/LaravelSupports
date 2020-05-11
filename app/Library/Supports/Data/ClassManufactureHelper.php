<?php


namespace App\Library\Supports\Data;


use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionProperty;

class ClassManufactureHelper
{
    /**
     * route.php 에 사용될 함수 입니다
     * 파라미터로 $cls 를 념겨주면 route 에 사용되는 값을 만들어서 return 해줍니다
     *
     * @param   class $cls
     * @return  mixed
     * @author  WilsonParker
     * @added   2019-08-27
     * @updated 2019-08-27
     */
    public static function routePath($cls) {
        $prefix = "App\Http\Controllers\\";
        return str_replace($prefix, "", $cls);
    }

    /**
     * $obj 에 $data 를 binding 합니다
     * $data 의 key 는 $obj 의 변수로 설정하며 해당 변수의 값을 $data 의 value 로 설정합니다
     *
     * ex)
     * $data = [
     *  "ix" = 1
     * ];
     *
     * $data->ix = 1;
     *
     * @param   $obj
     * 데이터를 binding 할 객체
     * @param   array $data
     * binding 할 데이터
     * @param   bool $isPublic
     * public 변수에만 binding 할지 설정
     * @return  void
     * @throws  \ReflectionException
     * @author  WilsonParker
     * @added   2019-08-27
     * @updated 2019-08-27
     */
    public static function bindData($obj, array $data, bool $isPublic= false)
    {
        if($isPublic) {
            $class = new ReflectionClass($obj);
            $hasProperties = collect($class->getProperties(ReflectionProperty::IS_PUBLIC))->filter(function ($item) use ($data) {
                return Arr::has($data, $item->name);
            })->map(function ($item) {
                return $item->name;
            });
            foreach($hasProperties as $property) {
                $obj->{$property} = $data[$property];
            }
        } else {
            foreach($data as $key => $value) {
                $obj->{$key} = $value;
            }
        }
    }
}
