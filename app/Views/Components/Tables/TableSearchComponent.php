<?php

namespace LaravelSupports\Views\Components\Tables;


use LaravelSupports\Views\Components\BaseComponent;

class TableSearchComponent extends BaseComponent
{
    protected string $view = 'table.table_search_component';

    public array $length;
    public array $search;
    public array $subSearch;
    public array $sort;
    public array $searchData;
    public $link;
    public string $url;
    public string $id;
    public string $title;
    public string $header;
    public string $tableRoot;

    /**
     * Create a new component instance.
     *
     * @param string $id
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
    public function __construct(string $id = 'table_search_component', string $title = '', string $header = '', string $tableRoot = '', $length = [10, 25, 50, 100], $search = ['name' => '이름'], $subSearch = [], $sort = [], $link = '', $searchData = [], $url = '')
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

    /**
     * build search form html
     *
     * @param
     * @return string
     * @author  dew9163
     * @added   2020/11/04
     * @updated 2020/11/04
     */
    function buildSearchHtml($search)
    {
        $result = '';
        foreach ($search as $key => $values) {
            $result .= '<label>' . $values[self::KEY_SEARCH_LABEL] . '&nbsp;';
            $result .= '<select class="custom-select custom-select-sm form-control form-control-sm" name="';
            if ($values[self::KEY_SEARCH_TYPE] == self::KEY_SEARCH_TYPE_MULTIPLE) {
                $result .= $key . '[]';
            } else {
                $result .= $key;
            }
            $result .= '" ';
            if ($values[self::KEY_SEARCH_TYPE] == self::KEY_SEARCH_TYPE_MULTIPLE) {
                $result .= 'multiple';
            }
            $result .= '>';

            foreach ($values[self::KEY_SEARCH_VALUES] as $itemKey => $itemValue) {
                $result .= '<option value = "' . $itemKey . '"';
                if (isset($this->searchData[$key]) && $this->searchData[$key] == $itemKey || ($values[self::KEY_SEARCH_TYPE] == self::KEY_SEARCH_TYPE_MULTIPLE && in_array($itemKey, $this->searchData[$key]))) {
                    $result .= 'selected';
                }
                $result .= '>' . $itemValue . '</option>';
            }
        }
        $result .= '</select></label>&nbsp';
        return $result;
    }
}
