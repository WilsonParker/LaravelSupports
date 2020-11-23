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
        return [BaseComponent::KEY_SEARCH, BaseComponent::KEY_SORT, BaseComponent::KEY_PAGINATE_LENGTH, BaseComponent::KEY_KEYWORD, BaseComponent::KEY_SUB_KEYWORD, BaseComponent::KEY_SUB_SEARCH, BaseComponent::KEY_SEARCH_OPERATOR];
    }

    /**
     * build search query
     *
     * @param Request $request
     * @param $query
     * @return Builder
     * @author  dew9163
     * @added   2020/09/20
     * @updated 2020/11/04
     */
    protected function buildQuery(Request $request, $query): Builder
    {
        $query = clone $query;
        $this->searchData = $request->only($this->getSearchKeys());
        if ($request->has([BaseComponent::KEY_SEARCH, BaseComponent::KEY_KEYWORD])) {
            $search = $this->searchData[BaseComponent::KEY_SEARCH] ?? '';
            $keyword = $this->searchData[BaseComponent::KEY_KEYWORD] ?? '';
            $query = $this->buildSearchQuery($query, $search, $keyword);
        }

        if ($request->has([BaseComponent::KEY_SORT])) {
            $sort = $this->searchData[BaseComponent::KEY_SORT];
            if (isset($sort)) {
                $query = $this->buildSortQuery($query, $sort);
            }
        }

        if ($request->has([BaseComponent::KEY_SUB_SEARCH, BaseComponent::KEY_SUB_KEYWORD])) {
            $subSearch = $this->searchData[BaseComponent::KEY_SUB_SEARCH];
            $subKeyword = $this->searchData[BaseComponent::KEY_SUB_KEYWORD] ?? '';
            $operator = $this->searchData[BaseComponent::KEY_SEARCH_OPERATOR];
            $query = $this->buildSubSearchQuery($query, $subSearch, $subKeyword, $operator);
        }
        $query = $this->buildAdditionalSearchQuery($request, $query);

        return $query;
    }

    /**
     * build search query using paginate
     *
     * @param Request $request
     * @param Builder $query
     * @return Paginator
     * @author  dew9163
     * @added   2020/09/20
     * @updated 2020/09/20
     */
    protected function buildSearchQueryPagination(Request $request, Builder $query): Paginator
    {
        return $this->buildQuery($request, clone $query)->paginate($this->getLength($request));
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

    protected function getInputWithDefaultIfEmpty($request, $key, $default = '')
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

    protected function backWithConfig($prefix, bool $isSuccess = true)
    {
        $message = $isSuccess ? config($prefix . '.success.message') : config($prefix . '.fail.message');
        return $this->backWithMessage($message);
    }

    protected function backWithMessage(string $message)
    {
        return redirect()->back()->with([
            'message' => $message
        ]);
    }

    protected function backWithErrors(Throwable $e)
    {
        return redirect()->back()->withErrors($e->getMessage());
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
            Cache::add($key, $result, $expired);
        }
        return $result;
    }
}
