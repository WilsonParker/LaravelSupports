<?php

namespace LaravelSupports\ViewModels;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LaravelSupports\Libraries\Supports\Objects\HasDataWithDefaultTrait;
use Spatie\ViewModels\ViewModel;

class BaseViewModel extends ViewModel
{
    use HasDataWithDefaultTrait;

    public const DATE_FORMAT = 'Y-m';
    public const KEY_SEARCH_LABEL = 'search_label';
    public const KEY_SEARCH_VALUES = 'search_values';
    public const KEY_SEARCH_TYPE = 'search_type';
    public const KEY_SEARCH_TYPE_MULTIPLE = 'search_type_multiple';
    public const KEY_SEARCH_KEYWORD_TYPE = 'search_keyword_type';
    public const KEY_SEARCH_KEYWORD_TYPE_MULTIPLE = 'search_keyword_type_multiple';
    public const KEY_SORT_LABEL = 'sort_label';
    public const KEY_SORT_VALUES = 'sort_values';
    public const KEY_START_DATE = 'start_date';
    public const KEY_END_DATE = 'end_date';
    public const KEY_KEYWORD = 'keyword';

    protected string $dateFormat = self::DATE_FORMAT;
    protected string $viewPrefix = '';
    protected string $viewSuffix = '';

    public $data;
    public array $searchData = [];
    public array $search = [];
    public array $subSearch = [];
    public array $sort = [];
    public string $title = '';
    public string $description = '';
    public $startDate;
    public $endDate;
    public bool $hasBasicModal = true;
    public bool $hasModal = true;

    /**
     * Component name in $view
     *
     * @var array
     * @author  dew9163
     * @added   2020/09/16
     * @updated 2020/09/16
     */
    public array $components = [];
    protected array $ignoreContains = [];

    public function __construct()
    {
        $this->load();
    }

    protected function load()
    {
        $this->view = $this->viewPrefix . '.' . $this->viewSuffix;
        $this->init();
    }

    protected function init()
    {
    }

    protected function shouldIgnore(string $methodName): bool
    {
        if (Str::startsWith($methodName, '__')) {
            return true;
        }

        $contains = collect($this->ignoreContains)->map(function ($item) {
            return strtoupper($item);
        })->toArray();

        return Str::contains(strtoupper($methodName), $contains) || in_array($methodName, $this->ignoredMethods());
    }

    /**
     * parent::toArray() 를 실행합니다
     *
     * @return  array
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    public function toArray(): array
    {
        $this->onArrayBefore($this);
        return parent::toArray();
    }

    /**
     * toArray() 를 실행하기 전에 실행되는 함수 입니다
     *
     * @param   $viewModel
     * @return  void
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    protected function onArrayBefore($viewModel)
    {

    }

    /**
     * $list 를 json 으로 변환해주는 함수 입니다
     *
     * @param   $list
     * @param   $setKeyCallback
     * function ($item)
     * $list 데이터 각각의 요소인 $item 을 넘겨 받으며
     * array $json 의 key 에 해당하는 값을 return 해주어야 합니다
     * @param   $getItemCallback
     * function ($item)
     * $list 데이터 각각의 요소인 $item 을 넘겨 받으며
     * array $json 의 value 에 해당하는 값을 return 해주어야 합니다
     * @return  false|string
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    protected function convertListToJson($list, $setKeyCallback, $getItemCallback)
    {
        $json = [];
        foreach ($list as $item) {
            $json[$setKeyCallback($item)] = $getItemCallback($item);
        }
        return $this->defaultJsonEncode($json);
    }

    /**
     * Json 으로 encode 하는 기본 함수 입니다
     *
     * @param   $data
     * @return  false|string
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    protected function defaultJsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    protected function buildSearch(string $label, string $key, $value, string $searchType = '')
    {
        $this->search = $this->buildSearchArray($this->search, $label, $key, $value, $searchType);
    }

    protected function buildSubSearch(string $label, string $key, $value, string $searchType = '')
    {
        $this->subSearch = $this->buildSearchArray($this->subSearch, $label, $key, $value, $searchType);
    }

    protected function buildSearchArray(array $array, string $label, string $key, $value, string $searchType = ''): array
    {
        if (Arr::has($array, $key)) {
            Arr::forget($array, $key);
        }

        return Arr::add($array, $key, [
            self::KEY_SEARCH_LABEL => $label,
            self::KEY_SEARCH_VALUES => $value,
            self::KEY_SEARCH_TYPE => $searchType,
        ]);
    }

    protected function buildSort(string $key, $value)
    {
        if (Arr::has($this->sort, $key)) {
            Arr::forget($this->sort, $key);
        }
        $this->sort = Arr::add($this->sort, $key, [
            self::KEY_SORT_VALUES => $value,
        ]);
    }

    public function formatDate(string $date, string $format): string
    {
        return isset($date) ? Carbon::parse($date)->format($format) : '';
    }

    public function formatDefaultDate($date): string
    {
        return $this->formatDate($date, $this->getDateFormat());
    }

    public function imageURL($path, $image): string
    {
        return config('image.images_url') . '/' . $path . '/' . $image;
    }

    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate): void
    {
        $this->endDate = $endDate;
    }

    public function setDate(array $data): void
    {
        $this->startDate = $data[self::KEY_START_DATE];
        $this->endDate = $data[self::KEY_END_DATE];
    }

    /**
     * @param mixed|null $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    public function defaultData($data, $key, $default = ''): string
    {
        if (is_array($data)) {
            $result = $data[$key] ?? $default;
        } else {
            $result = $data->{$key} ?? $default;
        }

        return $result;
    }

    /**
     * keyword 강조 처리
     *
     * @param string $content
     * @return string
     * @author  seul
     * @added   2021/04/01
     * @updated 2021/04/01
     */
    public function highlight(string $content): string
    {
        if (isset($this->searchData[self::KEY_KEYWORD])) {
            $keyword = str_replace(' ', '', $this->searchData[self::KEY_KEYWORD]);
            $keywords = explode(',', $keyword);

            foreach ($keywords as $keyword) {
                $pregKeyword = implode('\s{0,}', mb_str_split($keyword));
                preg_match("/{$pregKeyword}/i", $content, $matches);

                foreach ($matches as $match) {
                    $content = str_replace($match, '<span class="bg-highlight">' . $match . '</span>', $content);
                }
            }
            return $content;
        } else {
            return $content;
        }
    }
}
