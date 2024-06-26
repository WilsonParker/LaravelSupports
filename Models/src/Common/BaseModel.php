<?php

namespace LaravelSupports\Models\Common;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LaravelSupports\Models\Relationship\Traits\SaveRelationshipTrait;

abstract class BaseModel extends Model
{
    use SaveRelationshipTrait;

    // use SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const KEY_SEARCH_TYPE = "search_type";
    const KEY_KEYWORD = "keyword";
    protected static array $bootedWithRelationships;
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

    protected array $selectScope;
    // 이미지, 파일을 저장하는 suffix 경로 입니다
    // protected string $path = '';
    // 이미지, 파일을 저장하는 prefix 경로 입니다
    protected string $uploadPath = '';
    // 이미지, 파일을 저장하는 table 의 type 입니다
    protected string $tableType = '';

    /**
     * Select geometrical attributes as text from database.
     *
     * @var bool
     */
    protected bool $geometryAsText = false;


    /**
     * value of pagination limit
     *
     * @author  WilsonParker
     * @added   2020/05/11
     * @updated 2020/05/11
     * @var    int
     * @var    int
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
        $this->initScope();
        $this->init();
    }

    protected function initScope()
    {
        $this->buildSelectScope();
        $this->addSelectScope();
    }

    /**
     * build array $selectScope
     *
     * @return void
     * @author  WilsonParker
     * @added   2020/05/14
     * @updated 2020/05/14
     * @updated 2020/05/27
     */
    protected function buildSelectScope(): void {}

    /**
     * add global select scope
     *
     * @return void
     * @author  WilsonParker
     * @added   2020/05/14
     * @updated 2020/05/14
     */
    protected function addSelectScope(): void
    {
        static::addGlobalScope('selectScope', function (Builder $builder) {
            if (isset($this->selectScope)) {
                $builder->select($this->selectScope);
            }
            $this->buildOrderScope($builder);
            $this->buildWhereScope($builder);
            $this->buildWithScope($builder);
        });

        /**
         * relationship 과 함께 boot 합니다
         *
         * @return void
         * @author  WilsonParker
         * @added   2020/12/11
         * @updated 2020/12/11
         */
        static::addGlobalScope('relationshipScope', function (Builder $builder) {
            if (isset(static::$bootedWithRelationships)) {
                $builder->with(static::$bootedWithRelationships);
            }
        });
    }

    /**
     * set global scope's order
     *
     * @param Builder $builder
     * @return void
     * @author  WilsonParker
     * @added   2020/05/20
     * @updated 2020/05/20
     */
    protected function buildOrderScope(Builder $builder): void {}

    /**
     * set global scope's where clause
     *
     * @param Builder $builder
     * @return void
     * @author  WilsonParker
     * @added   2020/05/20
     * @updated 2020/05/21
     */
    protected function buildWhereScope(Builder $builder): void {}

    /**
     * set global scope's with clause
     *
     * @param Builder $builder
     * @return void
     * @author  WilsonParker
     * @added   2020/05/26
     * @updated 2020/05/26
     */
    protected function buildWithScope(Builder $builder): void {}

    /**
     * __construct 를 대신 할 초기 설정 함수
     *
     * @author  WilsonParker
     * @added   2019-08-23
     * @updated 2020-05-20
     */
    protected function init() {}

    /**
     * create an instance using the connection
     *
     * @param string $connection
     * @return Model
     * @author  WilsonParker
     * @added   2020/05/27
     * @updated 2020/05/27
     */
    public static function createInstanceWithConnection(string $connection): Model
    {
        $model = new static();
        $model->setConnection($connection);
        return $model;
    }

    public static function getModel($id)
    {
        return self::findOrFail($id);
    }

    public static function getList()
    {
        return self::get();
    }

    public static function getModelWhereIn(array $idList, $prop = 'id')
    {
        return self::whereIn($prop, $idList)->get();
    }

    public static function whereBetweenDate($startDate, $endDate, $column = 'created_at')
    {
        return self::whereBetween($column, [$startDate, $endDate]);
    }

    /**
     * Add a relationship exists condition (BelongsTo).
     *
     * @param Builder $query
     * @param string| $relation Relation string name or you can try pass directly model and method will try guess relationship
     * @param mixed   $models
     * @return Builder|static
     */
    public static function scopeWhereInRelated(
        Builder                                        $query,
        string                                         $relation,
        \Illuminate\Database\Eloquent\Collection|Model $models = null,
    ): Builder|static {
        if ($models instanceof Model) {
            $primaryKey = $models->getKeyName();
            $keys = collect([$models->$primaryKey]);
        } else {
            $primaryKey = $models->first()->getKeyName();
            $keys = $models->pluck($primaryKey);
        }
        return $query->whereHas($relation, static function (Builder $query) use ($models, $primaryKey, $keys) {
            return $query->whereIn($primaryKey, $keys);
        });
    }

    /**
     * return a collection of $selectScope data
     *
     * @return Collection
     * @author  WilsonParker
     * @added   2020/05/14
     * @updated 2020/05/14
     */
    public function getSelectData(): Collection
    {
        $data = collect();
        foreach ($this->selectScope as $select) {
            $data->put($select, $this->{$select});
        }
        return $data;
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
    public function bindData(array $data): void
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

    public function bindDataWithSetter(array $data)
    {
        // $data 의 값 중 table columns 에 해당하는 값들을 filter 합니다
        $hasProperties = collect($this->getColumns())->filter(function ($item) use ($data) {
            return Arr::has($data, $item);
        });

        // $hasProperties 의 값을 Model 의 속성에 적용합니
        foreach ($hasProperties as $property) {
            // setter method 이름을 생성합니다
            $methodName = 'set' . Str::ucfirst(Str::camel($property));
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($data[$property]);
            } else {
                $this->{$property} = $data[$property];
            }
        }
    }

    /**
     * 순서가 변경된 $list 값을 이용하여 $modelClass 의 데이터를 변경합니다
     * ex)
     * $this->sortList($sortResults, BrandCategoryModel::class, $successCallback, $errorCallback);
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
     * @param array    $list
     * 순서가 변경된 데이터의 list
     * @param callback $successCallback
     * 성공할 경우 실행할 callback
     * @param callback $failCallback
     * 실패할 경우 실행할 callback
     * @param string   $primaryKey
     * table 의 primary key
     * default : ix
     * @param string   $newSortKey
     * 변경된 순서의 key
     * default : new_sort
     * @return  array
     * @author  WilsonParker
     * @added   2019-08-12
     * @updated 2019-08-12
     */
    public function applySort(
        $list,
        $successCallback,
        $failCallback,
        string $primaryKey = "ix",
        string $newSortKey = "new_sort",
    ) {
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

    public function paginationWithSearch(
        Request $request,
                $limit = 0,
                $attributes = [],
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
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
                ],
            ];
        }
        $attributes = array_merge($attributes, $searchQuery);
        return $this->pagination($limit, $this->buildQuery($attributes));
    }

    protected function isLikeSearchType($searchType): bool
    {
        return collect($this->likeQuery)->contains($searchType);
    }

    public function pagination($page = 0, Builder $query = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return isset($query) ? $query->paginate($page == 0 ? $this->limit : $page) : $this->paginate($page == 0 ? $this->limit : $page);
    }

    /**
     * build query
     *
     * @param array $attributes
     * @return Builder|BaseModel
     * @author  WilsonParker
     * @added   2020/04/29
     * @updated 2020/04/29
     */
    public function buildQuery(array $attributes): Builder|BaseModel
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
     * @author  WilsonParker
     * @added   2020/04/29
     * @updated 2020/04/29
     */
    protected function buildWhereQuery($query, $where): Builder
    {
        $builder = function ($query, $where) {
            if (is_callable($where)) {
                return $query->where($where);
            } else {
                $value = $where['value'];
                if (empty($value)) {
                    return $query;
                }
                $operator = $where['operator'] ?? '=';
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
     * @author  WilsonParker
     * @added   2020/04/29
     * @updated 2020/04/29
     */
    protected function buildWithQuery($query, $with): Builder
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
     * @author  WilsonParker
     * @added   2020/04/29
     * @updated 2020/04/29
     */
    protected function buildOrderQuery($query, $order): Builder
    {
        $builder = function ($query, $order) {
            if (is_callable($order)) {
                return $query->orderBy($order);
            } else {
                $value = $order['value'] ?? 'desc';
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

    /**
     * create an instance corresponding to the class
     * using the connection of the called model
     *
     * @param string $cls
     * @return mixed
     * @author  WilsonParker
     * @added   2020/05/27
     * @updated 2020/05/27
     */
    public function createInstanceWithConnectionFromClass(string $cls): Model
    {
        $model = new $cls();
        $model->setConnection($this->connection);
        return $model;
    }

    public function getPrimaryValue()
    {
        return $this->{$this->primaryKey};
    }

    /**
     * Get a new query builder for the model's table.
     * Manipulate in case we need to convert geometrical fields to text.
     *
     * @param bool $excludeDeleted
     * @return Builder
     */
    public function newQuery(bool $excludeDeleted = true): Builder
    {
        if (!empty($this->geometry) && $this->geometryAsText === true) {
            $raw = '';
            foreach ($this->geometry as $column) {
                $raw .= 'AsText(`' . $this->table . '`.`' . $column . '`) as `' . $column . '`, ';
            }
            $raw = substr($raw, 0, -2);

            return parent::newQuery()->addSelect('*', DB::raw($raw));
        }

        return parent::newQuery();
    }
}
