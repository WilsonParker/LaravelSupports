<?php

namespace LaravelSupports\Views\Components\Tables;


use LaravelSupports\Views\Components\BaseComponent;

class TableComponent extends BaseComponent
{
    protected string $view = 'table.table_component';

    public array $length;
    public array $search;
    public array $subSearch;
    public array $sort;
    public array $searchData;
    public $link;
    public string $url;
    public string $title;
    public string $header;
    public string $tableRoot;
    public string $id;

    /**
     * Create a new component instance.
     *
     * @param string $title
     * @param string $header
     * @param string $tableRoot
     * @param int[] $length
     * @param string[] $search
     * @param array $subSearch
     * @param array $sort
     * @param string $link
     * @param array $searchData
     * @param string $url
     */
    public function __construct(string $id = 'table_component', string $title = '', string $header = '', string $tableRoot = '', $length = [10, 25, 50, 100], $search = ['name' => '이름'], $subSearch = [], $sort = [], $link = '', $searchData = [], $url = '')
    {
        $this->id = $id;
        $this->title = $title;
        $this->header = $header;
        $this->tableRoot = $tableRoot;
        $this->length = $length;
        $this->search = $search;
        $this->subSearch = $subSearch;
        $this->sort = $sort;
        $this->link = $link;
        $this->searchData = $searchData;
        $this->url = $url;
    }
}
