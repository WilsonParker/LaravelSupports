<?php

namespace LaravelSupports\ViewModels;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Spatie\ViewModels\ViewModel;

class BaseViewModel extends ViewModel
{
    public const DATE_FORMAT = 'Y-m';
    public const KEY_SEARCH_LABEL = 'search_label';
    public const KEY_SEARCH_VALUES = 'search_values';
    public const KEY_SEARCH_TYPE = 'search_type';
    public const KEY_SEARCH_TYPE_MULTIPLE = 'search_type_multiple';
    public const KEY_SEARCH_KEYWORD_TYPE = 'search_keyword_type';
    public const KEY_SEARCH_KEYWORD_TYPE_MULTIPLE = 'search_keyword_type_multiple';
    public const KEY_SORT_LABEL = 'sort_label';
    public const KEY_SORT_VALUES = 'sort_values';

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

    /**
     * Component name in $view
     *
     * @var array
     * @author  dew9163
     * @added   2020/09/16
     * @updated 2020/09/16
     */
    public array $components = [];

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

    protected function buildSearch(string $label, string $key, $value, string $searchType = '')
    {
        $this->search = $this->buildSearchArray($this->search, $label, $key, $value, $searchType);
    }

    protected function buildSubSearch(string $label, string $key, $value, string $searchType = '')
    {
        $this->subSearch = $this->buildSearchArray($this->subSearch, $label, $key, $value, $searchType);
    }

    protected function buildSearchArray(array $array, string $label, string $key, $value, string $searchType = '')
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

    public function formatDate(string $date, string $format)
    {
        return isset($date) ? Carbon::parse($date)->format($format) : '';
    }

    public function formatDefaultDate($date)
    {
        return $this->formatDate($date, $this->getDateFormat());
    }

    public function getDateFormat()
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
}
