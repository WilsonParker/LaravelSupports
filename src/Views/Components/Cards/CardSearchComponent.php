<?php

namespace LaravelSupports\Views\Components\Cards;


use LaravelSupports\Views\Components\BaseComponent;

class CardSearchComponent extends BaseComponent
{
    protected string $view = 'card.card_search_component';

    public array $length;
    public array $search;
    public array $subSearch;
    public array $sort;
    public array $searchData;
    public $link;
    public string $url;
    public string $title;
    public string $header;

    /**
     * Create a new component instance.
     *
     * @param string $title
     * @param array $search
     * @param array $subSearch
     * @param array $sort
     * @param string $link
     * @param array $searchData
     */
    public function __construct(string $title = '', string $header = '', $length = [16, 32, 64, 128], $search = ['name' => '이름'], $subSearch = [], $sort = [], $link = '', $searchData = [], $url = '')
    {
        $this->title = $title;
        $this->header = $header;
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
     * @author  WilsonParker
     * @added   2020/11/04
     * @updated 2020/11/04
     */
    function buildSearchHtml($search)
    {
        $result = '';
        foreach ($search as $key => $values) {
            $result .= '<label>' . $values[self::KEY_SEARCH_LABEL].'&nbsp;';
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
