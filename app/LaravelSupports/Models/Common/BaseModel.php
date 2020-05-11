<?php

namespace App\LaravelSupports\Models\Common;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BaseModel extends Model
{
//    use SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * 둘 다 사용하지 않을 경우 false
     *
     * @var boolean
     * @author  dew9163
     * @added   2020/05/11
     * @updated 2020/05/11
     */
    public $timestamps = true;
    public $incrementing = true;
    protected $primaryKey = "ix";
    protected $table = "";

    const KEY_SEARCH_TYPE = "search_type";
    const KEY_KEYWORD = "keyword";
    // 이미지, 파일을 저장하는 suffix 경로 입니다
    protected string $path;
    // 이미지, 파일을 저장하는 prefix 경로 입니다
    protected string $uploadPath;
    // 이미지, 파일을 저장하는 table 의 type 입니다
    protected string $tableType;

    /**
     * value of pagination limit
     *
     * @var    int
     * @author  dew9163
     * @added   2020/05/11
     * @updated 2020/05/11
     */
    protected int $limit = 10;
    protected array $likeQuery = [
        "title",
        "name",
        "email",
    ];

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

    /**
     * build query
     *
     * @param array $attributes
     * @return Builder
     * @author  dew9163
     * @added   2020/04/29
     * @updated 2020/04/29
     */
    public function buildQuery(array $attributes)
    {
        $query = $this;

        if (Arr::has($attributes, 'where')) {
            $query = $this->buildWhereQuery($query, $attributes['where']);
        }

        if (Arr::has($attributes, 'with')) {
            $query = $this->buildWithQuery($query, $attributes['with']);
        }

        if (Arr::has($attributes, 'order')) {
            $query = $this->buildOrderQuery($query, $attributes['order']);
        }

        return $query;
    }

    /**
     * build where query
     *
     * @param $query
     * @param $where
     * @return Builder
     * @author  dew9163
     * @added   2020/04/29
     * @updated 2020/04/29
     */
    private function buildWhereQuery($query, $where)
    {
        $builder = function ($query, $where) {
            if (is_callable($where)) {
                return $query->where($where);
            } else {
                $value = $where['value'];
                if (empty($value)) {
                    return $query;
                }
                $operator = isset($where['operator']) ? $where['operator'] : '=';
                return $query->where($where['key'], $operator, $where['value']);
            }
        };

        if (is_array($where)) {
            foreach ($where as $item) {
                $query = $builder($query, $item);
            }
        } else {
            $query = $builder($query, $where);
        }
        return $query;
    }

    /**
     * build with query
     *
     * @param $query
     * @param $with
     * @return Builder
     * @author  dew9163
     * @added   2020/04/29
     * @updated 2020/04/29
     */
    private function buildWithQuery($query, $with)
    {
        if (is_array($with)) {
            foreach ($with as $withItem) {
                $query = $query->with($withItem);
            }
        } else {
            $query = $query->with($with);
        }
        return $query;
    }

    /**
     * build order query
     *
     * @param $query
     * @param $order
     * @return Builder
     * @author  dew9163
     * @added   2020/04/29
     * @updated 2020/04/29
     */
    private function buildOrderQuery($query, $order)
    {
        $builder = function ($query, $order) {
            if (is_callable($order)) {
                return $query->orderBy($order);
            } else {
                $value = isset($order['value']) ? $order['value'] : 'desc';
                return $query->orderBy($order['key'], $value);
            }
        };

        if (is_array($order)) {
            foreach ($order as $item) {
                $query = $builder($query, $item);
            }
        } else {
            $query = $builder($query, $order);
        }
        return $query;
    }

    public function pagination($page = 0, $query = null)
    {
        return isset($query) ? $query->paginate($page == 0 ? $this->limit : $page) : $this->paginate($page == 0 ? $this->limit : $page);
    }

    public function paginationWithSearch(Request $request, $limit = 0, $attributes = [])
    {
        $keyword = $request->input(self::KEY_KEYWORD, "");
        $searchType = $request->input(self::KEY_SEARCH_TYPE, "");
        $searchQuery = [];
        if (!empty($keyword)) {
            if ($this->isLikeSearchType($searchType)) {
                $operator = 'like';
                $keyword = "%$keyword%";
            } else {
                $operator = '=';
            }
            $searchQuery = [
                'where' => [
                    [
                        'key' => $searchType,
                        'operator' => $operator,
                        'value' => $keyword,
                    ],
                ]
            ];
        }
        $attributes = array_merge($attributes, $searchQuery);
        return $this->pagination($limit, $this->buildQuery($attributes));
    }

    protected function isLikeSearchType($searchType)
    {
        return collect($this->likeQuery)->contains($searchType);
    }

    public static function getModel($id)
    {
        return self::find($id);
    }

    public static function getList()
    {
        return self::get();
    }

}
