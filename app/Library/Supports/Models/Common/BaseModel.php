<?php

namespace App\Library\Supports\Models\Common;

use App\Library\Supports\Requests\Contracts\RequestValueCastContract;
use App\Library\Supports\Requests\RequestBinder;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BaseModel extends Model
{
//    use SoftDeletes;

    // 둘 다 사용하지 않을 경우 timestamps만 꺼주면 된다. (기본값 true)
    public bool $timestamps = true;
//    PK 값을 변경한다. (기본값 id)
//    protected string $primaryKey = "ix";
//    테이블 명시
//    protected string $table = "";
    // 임의의 생성 필드가 있다면 설정해준다. (기본값 created_at)
    // const CREATED_AT = 'created_at';
    // 사용하지 않을 경우 Null로 초기화해주자. (기본값 updated_at)
    // const UPDATED_AT = updated_at;
    // PK가 auto increment가 아닐경우 false로 바꿔준다. (기본값 true)
    public bool $incrementing = true;

    /*protected $guarded = [
        'ix'
    ];*/
    // 이미지, 파일을 저장하는 suffix 경로 입니다
    protected string $path;
    // 이미지, 파일을 저장하는 prefix 경로 입니다
    protected string $uploadPath;
    // 이미지, 파일을 저장하는 table 의 type 입니다
    protected string $tableType;
    protected int $paginate = 20;

    /**
     * BaseModel 을 extends 할 경우 __construct 대신 init 을 override 해야됩니다
     *
     * @param array $attributes
     * @author  WilsonParker
     * @added   2019-08-23
     * @updated 2019-08-23
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->paginate = config("constants.pageLimit");
        $this->init();
    }

    /**
     * __construct 를 대신 할 초기 설정 함수
     *
     * @author  WilsonParker
     * @added   2019-08-23
     * @updated 2019-08-23
     */
    protected function init()
    {

    }

    /**
     * array $data 값을 Model 에 적용합니다
     * $data 의 key 값이 Model 의 table columns 와 일치할 경우 값을 적용합니다
     *
     * @param array $data
     * @return  void
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    public function bindData(array $data)
    {
        // $data 의 값 중 table columns 에 해당하는 값들을 filter 합니다
        $hasProperties = collect($this->getColumns())->filter(function ($item) use ($data) {
            return Arr::has($data, $item);
        });

        // $hasProperties 의 값을 Model 의 속성에 적용합니
        foreach ($hasProperties as $property) {
            $this->{$property} = $data[$property];
        }
    }

    /**
     * Model 의 table columns 데이터를 return 합니다
     *
     * @return  array
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    public function getColumns(): array
    {
        return array_values($this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()));
    }

    /**
     * 순서가 변경된 $list 값을 이용하여 $modelClass 의 데이터를 변경합니다
     *
     * ex)
     * $this->sortList($sortResults, BrandCategoryModel::class, $successCallback, $errorCallback);
     *
     * $list 는
     * [
     *      {
     *          "ix" : 1,
     *          "new_sort" : 2,
     *      },
     *      ...
     * ]
     * 같은 형식으로 넘어옵니다
     *
     * @param array $list
     * 순서가 변경된 데이터의 list
     * @param callback $successCallback
     * 성공할 경우 실행할 callback
     * @param callback $failCallback
     * 실패할 경우 실행할 callback
     * @param string $primaryKey
     * table 의 primary key
     * default : ix
     * @param string $newSortKey
     * 변경된 순서의 key
     * default : new_sort
     * @return  array
     * @author  WilsonParker
     * @added   2019-08-12
     * @updated 2019-08-12
     */
    public function applySort($list, $successCallback, $failCallback, $primaryKey = "ix", $newSortKey = "new_sort")
    {
        foreach ($list as $item) {
            // 고유키로 Model 을 select 합니다
            $model = $this::find($item[$primaryKey]);
            // 가져온 Model 정보에 바뀐 sort 값을 적용합니다
            $model->sort = $item[$newSortKey];
            $result = $model->save();
            // 데이터 설정 중 문제 발생 시 $failCallback 실행
            if (!$result)
                return $failCallback();
        }
        // 모두 성공 시 $successCallback 실행
        return $successCallback();
    }

}
