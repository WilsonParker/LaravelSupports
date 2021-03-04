<?php


namespace LaravelSupports\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use LaravelSupports\Libraries\Supports\Databases\Traits\TransactionTrait;
use LaravelSupports\ViewModels\BaseViewModel;
use LaravelSupports\Views\Components\BaseComponent;
use Symfony\Component\HttpFoundation\ParameterBag;
use Throwable;

abstract class BaseController extends Controller
{
    use TransactionTrait;

    /**
     * view root
     *
     * @var string
     * @author  dew9163
     */
    protected string $root = '';

    /**
     * view prefix
     *
     * @var array
     * @author  dew9163
     */
    protected array $prefix = [];

    /**
     * view suffix
     *
     * @var array
     * @author  dew9163
     */
    protected array $suffix = [];
    protected int $paginate = 10;
    protected BaseViewModel $viewModel;
    protected string $title = '';
    protected string $description = '';
    protected array $searchData = [];
    protected string $dateFormat = 'Y-m-d';

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
     * @author  dew9163
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
     * @author  dew9163
     * @added   2020/10/29
     * @updated 2020/10/29
     */
    protected function buildView(string $view)
    {
        $this->viewModel->setTitle($this->title);
        $this->viewModel->setDescription($this->description);

        $arr = Arr::prepend($this->prefix, $this->root);
        $arr = array_merge($arr, $this->suffix);
        $arr = Arr::add($arr, count($arr), $view);
        $strView = implode('.', $arr);
        return view($strView, $this->viewModel);
    }

    /**
     * build search query
     *
     * @param Builder $query
     * @param string $search
     * @param string $keyword
     * @return Builder
     * @author  dew9163
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
     * @author  dew9163
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
     * @author  dew9163
     * @added   2020/11/10
     * @updated 2020/11/10
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
     * @author  dew9163
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
     * @author  dew9163
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
     * @author  dew9163
     * @added   2020/11/04
     * @updated 2020/11/04
     */
    protected function buildSortQuery(Builder $query, string $sort): Builder
    {
        return $query;
    }

    protected function getSearchKeys(): array
    {
        $keys = [BaseComponent::KEY_SEARCH, BaseComponent::KEY_SORT, BaseComponent::KEY_PAGINATE_LENGTH, BaseComponent::KEY_KEYWORD, BaseComponent::KEY_SUB_KEYWORD, BaseComponent::KEY_SUB_SEARCH, BaseComponent::KEY_SEARCH_OPERATOR];
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
     * @author  dew9163
     * @added   2020/09/20
     * @updated 2020/11/04
     * @updated 2021/03/03
     * set '' if keyword value does not exists
     */
    protected function buildQuery(Request $request, $query, bool $clone = true): Builder
    {
        $rQuery = $clone ? clone $query : $query;
        $this->searchData = $request->only($this->getSearchKeys());

        $search = $this->searchData[BaseComponent::KEY_SEARCH] ?? '';
        $keyword = $this->searchData[BaseComponent::KEY_KEYWORD] ?? '';
        if ($request->has([BaseComponent::KEY_SEARCH]) && $this->isValidKeyword($search, $keyword)) {
            $rQuery = $this->buildSearchQuery($rQuery, $search, $keyword);
        }

        if ($request->has([BaseComponent::KEY_SORT])) {
            $sort = $this->searchData[BaseComponent::KEY_SORT];
            if (isset($sort)) {
                $rQuery = $this->buildSortQuery($rQuery, $sort);
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

        return $rQuery;
    }

    /**
     * build search query using paginate
     *
     * @param Request $request
     * @param Builder $query
     * @param bool $clone
     * @return Paginator
     * @author  dew9163
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

    protected function mergeWhere($attributes, $array)
    {
        $where = isset($attributes['where']) ? $attributes['where'] : [];
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
     * @author  dew9163
     * @added   2020/09/20
     * @updated 2020/09/20
     */
    protected function getLength(Request $request): int
    {
        $length = $request->input(BaseComponent::KEY_PAGINATE_LENGTH, $this->paginate);
        $this->searchData[BaseComponent::KEY_PAGINATE_LENGTH] = $length;
        return $length;
    }

    /**
     * config 정보를 포함하여 이전 페이지로 이동 합니다
     *
     * @param string $prefix
     * @param bool $redirect
     * @param bool $isSuccess
     * @return \Illuminate\Http\RedirectResponse
     * @author  dew9163
     * @added   2020/12/08
     * @updated 2020/12/08
     */
    protected function backWithConfig(string $prefix, bool $redirect = true, bool $isSuccess = true): \Illuminate\Http\RedirectResponse
    {
        $message = $isSuccess ? config($prefix . '.success.message') : config($prefix . '.fail.message');
        return $this->backWithMessage($message, $redirect);
    }

    protected function backWithMessage(string $message, bool $redirect = true): \Illuminate\Http\RedirectResponse
    {
        if ($redirect) {
            return redirect()->back()->with([
                'message' => $message
            ]);
        } else {
            return back()->with([
                'message' => $message
            ]);
        }
    }

    protected function backWithErrors(Throwable $e): \Illuminate\Http\RedirectResponse
    {
        return back()->withInput()->withErrors($e->getMessage());
    }

    /**
     * config 정보를 포함하여 $route 로 $params 를 전달하여 이동 합니다
     *
     * @param string $prefix
     * @param string $route
     * @param array $params
     * @param bool $isSuccess
     * @return \Illuminate\Http\RedirectResponse
     * @author  dew9163
     * @added   2020/12/08
     * @updated 2020/12/08
     */
    protected function redirectWithConfig(string $prefix, string $route, array $params, bool $isSuccess = true): \Illuminate\Http\RedirectResponse
    {
        $message = $isSuccess ? config($prefix . '.success.message') : config($prefix . '.fail.message');
        return $this->redirectWithMessage($message, $route, $params);
    }

    protected function redirectWithMessage(string $message, string $route, array $params): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route($route, $params)->with([
            'message' => $message
        ]);
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
     * @author  dew9163
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
     * @author  dew9163
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
     * @author  dew9163
     * @added   2021/01/14
     * @updated 2021/01/14
     */
    protected function isValidSearchData(string $key, array $searchData): bool
    {
        return isset($searchData[$key]);
    }
}
