<?php


namespace LaravelSupports\Controllers;

use LaravelSupports\Controllers\Traits\RedirectTraits;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use LaravelSupports\Libraries\Supports\Data\StringHelper;
use LaravelSupports\Libraries\Supports\Databases\Traits\TransactionTrait;
use LaravelSupports\ViewModels\BaseViewModel;
use LaravelSupports\Views\Components\BaseComponent;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class BaseController extends Controller
{
    use TransactionTrait, RedirectTraits;

    /**
     * view root
     *
     * @var string
     * @author  WilsonParker
     */
    protected string $root = '';

    /**
     * view prefix
     *
     * @var array
     * @author  WilsonParker
     */
    protected array $prefix = [];

    /**
     * view suffix
     *
     * @var array
     * @author  WilsonParker
     */
    protected array $suffix = [];
    protected int $paginate = 10;
    protected BaseViewModel $viewModel;
    protected string $title = '';
    protected string $description = '';
    /**
     * data for search
     * [ search, sort, filters ]
     *
     * @var array
     * @author  WilsonParker
     */
    protected array $searchData = [];
    /**
     * default value for sort
     *
     * @var string
     * @author  WilsonParker
     */
    protected string $defaultSort = '';

    protected string $dateFormat = 'Y-m-d';
    protected string $searchDateAt = 'created_at';
    protected string $strStartDate;
    protected string $strEndDate;
    protected string $startTime;
    protected string $endTime;

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {

    }

    /**
     * append $prefix
     *
     * @param array | string $prefix
     * @return void
     * @author  WilsonParker
     * @added   2020/09/22
     * @updated 2020/09/22
     */
    protected function appendPrefix($prefix): void
    {
        if (is_array($prefix)) {
            $this->prefix = array_merge($this->prefix, $prefix);
        } else {
            $this->prefix = Arr::add($this->prefix, count($this->prefix), $prefix);
        }
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string $view
     * @return View|Factory
     * @author  WilsonParker
     * @added   2020/10/29
     * @updated 2020/10/29
     * @updated 2021/10/19
     * remove set title & description
     */
    protected function buildView(string $view, BaseViewModel $viewModel = null): Factory|View
    {
//        $this->viewModel->setTitle($this->title);
//        $this->viewModel->setDescription($this->description);

        $arr = Arr::prepend($this->prefix, $this->root);
        $arr = array_merge($arr, $this->suffix);
        $arr = Arr::add($arr, count($arr), $view);
        $strView = implode('.', $arr);
        return view($strView, $viewModel ?? $this->viewModel);
    }

    /**
     * build search query
     *
     * @param Builder $query
     * @param string $search
     * @param string $keyword
     * @return Builder
     * @author  WilsonParker
     * @added   2020/09/20
     * @updated 2020/09/20
     */
    protected function buildSearchQuery(Builder $query, string $search, string $keyword): Builder
    {
        return $query;
    }

    /**
     * build sub search query
     *
     * @param Builder $query
     * @param string $search
     * @param string $keyword
     * @param string $operator
     * @return Builder
     * @author  WilsonParker
     * @added   2020/11/04
     * @updated 2020/11/04
     */
    protected function buildSubSearchQuery(Builder $query, string $search, string $keyword, string $operator): Builder
    {
        return $query;
    }

    /**
     * build additional search query
     *
     * @param Request $request
     * @param Builder $query
     * @return Builder
     * @author  WilsonParker
     * @added   2020/11/10
     * @updated 2020/11/10
     * add date
     * @updated 2021/03/31
     */
    protected function buildAdditionalSearchQuery(Request $request, Builder $query): Builder
    {
        return $query;
    }

    /**
     * buildSearchDataQueryList 로 인해 생성된 query list 를 검색에 적용
     *
     * @param Builder $query
     * @return Builder
     * @author  WilsonParker
     * @added   2021/01/14
     * @updated 2021/01/14
     */
    protected function buildSearchDataQuery(Builder $query): Builder
    {
        foreach ($this->buildSearchDataQueryList($query) as $key => $callback) {
            if ($this->isValidSearchData($key, $this->searchData)) {
                $callback($query, $this->searchData[$key]);
            }
        }
        return $query;
    }

    /**
     * $searchData 를 검색하기 위해 필요한 query list
     *
     * @param Builder $query
     * @return array
     * @author  WilsonParker
     * @added   2021/01/14
     * @updated 2021/01/14
     * @example
     * [
     *  'key' => function ($query, $value) {
     *      $query->where('column', $value);
     *  }
     * ]
     */
    protected function buildSearchDataQueryList(Builder $query): array
    {
        return [];
    }

    /**
     * build sort query
     *
     * @param Builder $query
     * @param string $sort
     * @return Builder
     * @author  WilsonParker
     * @added   2020/11/04
     * @updated 2020/11/04
     */
    protected function buildSortQuery(Builder $query, string $sort): Builder
    {
        return $query;
    }

    /**
     * build filter query
     *
     * @param Builder $query
     * @param array $filter
     * @return Builder
     * @author  dev9163
     * @added   2021/10/01
     * @updated 2021/10/01
     */
    protected function buildFilterQuery(Builder $query, array $filters): Builder
    {
        return $query;
    }

    protected function getSearchKeys(): array
    {
        $keys = [BaseComponent::KEY_SEARCH, BaseComponent::KEY_SORT, BaseComponent::KEY_FILTER, BaseComponent::KEY_PAGINATE_LENGTH, BaseComponent::KEY_KEYWORD, BaseComponent::KEY_SUB_KEYWORD, BaseComponent::KEY_SUB_SEARCH, BaseComponent::KEY_SEARCH_OPERATOR];
        return array_merge($keys, $this->appendSearchKeys());
    }

    protected function appendSearchKeys(): array
    {
        return [];
    }

    /**
     * build search query
     *
     * @param Request $request
     * @param $query
     * @param bool $clone
     * @return Builder
     * @author  WilsonParker
     * @added   2020/09/20
     * @updated 2020/11/04
     * @updated 2021/03/03
     * set '' if keyword value does not exists
     */
    protected function buildQuery(Request $request, $query, bool $clone = true): Builder
    {
        $rQuery = $clone ? clone $query : $query;
        $this->buildSearchData($request);

        $search = $this->searchData[BaseComponent::KEY_SEARCH] ?? '';
        $keyword = $this->searchData[BaseComponent::KEY_KEYWORD] ?? '';
        if ($request->has([BaseComponent::KEY_SEARCH]) && $this->isValidKeyword($search, $keyword)) {
            $rQuery = $this->buildSearchQuery($rQuery, $search, $keyword);
        }

        if ($request->has([BaseComponent::KEY_SORT])) {
            $sort = $this->searchData[BaseComponent::KEY_SORT];
        } else if ($this->defaultSort != '') {
            $sort = $this->defaultSort;
            $this->searchData[BaseComponent::KEY_SORT] = $sort;
        }
        if (isset($sort)) {
            $rQuery = $this->buildSortQuery($rQuery, $sort);
        }

        if ($request->has([BaseComponent::KEY_FILTER])) {
            $filters = $this->searchData[BaseComponent::KEY_FILTER];
            if (isset($filters)) {
                $rQuery = $this->buildFilterQuery($rQuery, $filters);
            }
        }

        if ($request->has([BaseComponent::KEY_SUB_SEARCH, BaseComponent::KEY_SUB_KEYWORD])) {
            $subSearch = $this->searchData[BaseComponent::KEY_SUB_SEARCH] ?? '';
            $subKeyword = $this->searchData[BaseComponent::KEY_SUB_KEYWORD] ?? '';
            $operator = $this->searchData[BaseComponent::KEY_SEARCH_OPERATOR] ?? '';
            if ($this->isValidKeyword($subSearch, $subKeyword)) {
                $rQuery = $this->buildSubSearchQuery($rQuery, $subSearch, $subKeyword, $operator);
            }
        }
        $rQuery = $this->buildSearchDataQuery($rQuery);
        $rQuery = $this->buildAdditionalSearchQuery($request, $rQuery);

        $rQuery = $this->buildDateQuery($request, $rQuery);

        return $rQuery;
    }

    protected function buildDateQuery($request, $query)
    {
        if (isset($this->strStartDate)) {
            $data = $this->bindDateData($request->all());

            $this->searchData = array_merge($this->searchData, $data);

            $startDate = $data['start_date'] . $this->startTime;
            $endDate = $data['end_date'] . $this->endTime;

            if (isset($data['date_all']) == false || $data['date_all'] != 'Y') {
                $query->when($startDate, function ($query, $startDate) {
                    $query->where($this->searchDateAt, '>=', $startDate);
                });

                $query->when($endDate, function ($query, $endDate) {
                    $query->where($this->searchDateAt, '<=', $endDate);
                });
            }
        }

        return $query;
    }

    /**
     * build search query using paginate
     *
     * @param Request $request
     * @param Builder $query
     * @param bool $clone
     * @return Paginator
     * @author  WilsonParker
     * @added   2020/09/20
     * @updated 2021/01/14
     */
    protected function buildSearchQueryPagination(Request $request, Builder $query, bool $clone = true): Paginator
    {
        return $this->buildQueryPagination($request, $this->buildQuery($request, $query, $clone), $clone);
    }

    protected function buildQueryPagination(Request $request, Builder $query, bool $clone = true): Paginator
    {
        $cloneQuery = $clone ? clone $query : $query;
        return $cloneQuery->paginate($this->getLength($request));
    }

    /**
     * If paginator items are filtered, paginate again.
     *
     * @param Collection $items
     * @return LengthAwarePaginator
     * @author  WilsonParker
     * @added   2021/03/22
     * @updated 2021/03/22
     */
    protected function buildFilteredPaginate(Collection $items): LengthAwarePaginator
    {
        $pageStart = request('page', 1);
        $offSet = ($pageStart * $this->paginate) - $this->paginate;
        $itemsForCurrentPage = $items->slice($offSet, $this->paginate);

        return new LengthAwarePaginator(
            $itemsForCurrentPage, $items->count(), $this->paginate,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }

    /**
     * build search data
     *
     * @param Request $request
     * @return void
     * @author  WilsonParker
     * @added   2021/03/10
     * @updated 2021/03/10
     */
    protected function buildSearchData(Request $request)
    {
        $this->searchData = $request->only($this->getSearchKeys());
    }

    protected function bindDateData($data)
    {
        $data['start_date'] = isset($data['start_date']) && $data['start_date'] != '' ? $data['start_date'] : $this->getDefaultStartDate();
        $data['end_date'] = isset($data['end_date']) && $data['end_date'] != '' ? $data['end_date'] : $this->getDefaultEndDate();

        $data['date_all'] = $data['date_all'] ?? 'N';

        return $data;
    }

    /**
     * @return string
     */
    public function getStrStartDate(): string
    {
        return $this->strStartDate;
    }

    /**
     * @param string $strStartDate
     */
    public function setStrStartDate(string $strStartDate): void
    {
        $this->strStartDate = $strStartDate;
    }

    /**
     * @return string
     */
    public function getStrEndDate(): string
    {
        return $this->strEndDate;
    }

    /**
     * @param string $strEndDate
     */
    public function setStrEndDate(string $strEndDate): void
    {
        $this->strEndDate = $strEndDate;
    }

    public function getDefaultStartDate(): string
    {
        $strDate = $this->getStrStartDate();
        return $strDate != '' ? date('Y-m-d', strtotime($strDate)) : date('Y-m-d');
    }

    public function getDefaultEndDate(): string
    {
        $strDate = $this->getStrEndDate();
        return $strDate != '' ? date('Y-m-d', strtotime($strDate)) : date('Y-m-d');
    }

    protected function mergeWhere($attributes, $array)
    {
        $where = $attributes['where'] ?? [];
        $where = array_merge($where, $array);
        $attributes['where'] = $where;
        return $attributes;
    }

    protected function cleanUpRequest(Request $request, $cleanUp)
    {
        $excepted = $request->except($cleanUp);
        $request->query = new ParameterBag($excepted);
    }

    protected function getInputWithDefaultIfEmpty($request, $key, $default = ''): string
    {
        $value = $request->input($key, '');
        return isset($value) && !empty($value) ? $value : $default;
    }

    /**
     * get page length (limit)
     *
     * @param Request $request
     * @return int
     * @author  WilsonParker
     * @added   2020/09/20
     * @updated 2020/09/20
     */
    protected function getLength(Request $request): int
    {
        $length = $request->input(BaseComponent::KEY_PAGINATE_LENGTH, $this->paginate);
        $this->searchData[BaseComponent::KEY_PAGINATE_LENGTH] = $length;
        return $length;
    }

    protected function redirectUrlWithConfig(string $prefix, string $url, array $params = [], bool $isSuccess = true, array $replace = []): \Illuminate\Http\RedirectResponse
    {
        return $this->redirectUrlWithMessage($this->getConfigMessage($prefix, $isSuccess, $replace), $url, $params);
    }

    protected function redirectRouteWithConfig(string $prefix, string $route, array $params = [], bool $isSuccess = true, array $replace = []): \Illuminate\Http\RedirectResponse
    {
        return $this->redirectRouteWithMessage($this->getConfigMessage($prefix, $isSuccess, $replace), $route, $params);
    }

    protected function bindConfigMessage(string $prefix, array $replace = [], bool $isSuccess = true): string
    {
        $message = $isSuccess ? config($prefix . '.success.message') : config($prefix . '.fail.message');
        $helper = new StringHelper();
        return $helper->replaceWithCollection($replace, $message);
    }

    protected function getConfigMessage(string $prefix, bool $isSuccess = true, array $replace = []): string
    {
        $message = $isSuccess ? config($prefix . '.success.message') : config($prefix . '.fail.message');
        $helper = new StringHelper();
        return $helper->replaceWithCollection($replace, $message);
    }

    protected function setTitleAndDescription(string $title, string $description)
    {
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * Cache 에 저장 유무를 확인하여 데이터를 불러오거나 저장 합니다
     *
     * @param string $key
     * @param $callback
     * @param Carbon $expired
     * @param bool $notUsedCache
     * cache 사용 여부
     * @return mixed
     * @author  WilsonParker
     * @added   2020/11/04
     * @updated 2020/11/04
     */
    protected function getCacheData(string $key, $callback, Carbon $expired, bool $notUsedCache = false)
    {
        if (Cache::has($key) && !$notUsedCache) {
            $result = Cache::get($key);
        } else {
            $result = $callback();
            Cache::forget($key);
            Cache::put($key, $result, $expired);
        }
        return $result;
    }

    protected function getDate($data): array
    {
        $now = Carbon::now()->format($this->dateFormat);
        $start = $data[BaseViewModel::KEY_START_DATE] ?? $now;
        $end = $data[BaseViewModel::KEY_END_DATE] ?? $now;
        return [
            BaseViewModel::KEY_START_DATE => $start,
            BaseViewModel::KEY_END_DATE => $end,
        ];
    }

    /**
     * 검색어 $keyword 가 유효한 지 확인 합니다
     *
     * @param string $search
     * @param string $keyword
     * @return bool
     * @author  WilsonParker
     * @added   2021/01/14
     * @updated 2021/01/14
     */
    protected function isValidKeyword(string $search, string $keyword): bool
    {
        return true;
    }

    /**
     * $key 에 해당하는 $searchData 가 유효한 지 확인 합니다
     *
     * @param string $key
     * @param array $searchData
     * @return bool
     * @author  WilsonParker
     * @added   2021/01/14
     * @updated 2021/01/14
     */
    protected function isValidSearchData(string $key, array $searchData): bool
    {
        return isset($searchData[$key]);
    }
}
