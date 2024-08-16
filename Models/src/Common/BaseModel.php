<?php

namespace LaravelSupports\Models\Common;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{

    /**
     * 둘 다 사용하지 않을 경우 false
     *
     * @author  WilsonParker
     * @added   2020/05/11
     * @updated 2020/05/11
     * @var boolean
     * @var boolean
     */
    public $timestamps = true;
    public $incrementing = true;
    protected $guarded = [];
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * Model 의 table columns 데이터를 return 합니다
     *
     * @param array $except
     * @return  array
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     * @updated 2020-11-17
     */
    public function getColumns(array $except = []): array
    {
        $columns = array_values($this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()));
        return array_diff($columns, $except);
    }

}
