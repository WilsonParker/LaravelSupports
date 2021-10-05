<?php

namespace LaravelSupports\Views\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class BaseComponent extends Component
{
    public const KEY_KEY = 'key';
    public const KEY_VALUE = 'value';
    public const KEY_TEXT = 'text';
    public const KEY_NAME = 'name';
    public const KEY_LABEL = 'label';
    public const KEY_VALUES = 'values';
    public const KEY_TYPE = 'type';
    public const KEY_KEYWORD = 'keyword';
    public const KEY_OPERATOR = 'operator';

    public const KEY_SEARCH = 'search';
    public const KEY_SEARCH_LABEL = self::KEY_SEARCH . '_' . self::KEY_LABEL;
    public const KEY_SEARCH_VALUES = self::KEY_SEARCH . '_' . self::KEY_VALUES;
    public const KEY_SEARCH_TYPE = self::KEY_SEARCH . '_' . self::KEY_TYPE;
    public const KEY_SEARCH_TYPE_MULTIPLE = self::KEY_SEARCH_TYPE . '_multiple';
    public const KEY_SEARCH_KEYWORD_TYPE = self::KEY_SEARCH . '_' . self::KEY_KEYWORD . '_' . self::KEY_TYPE;
    public const KEY_SEARCH_KEYWORD_TYPE_MULTIPLE = self::KEY_SEARCH_KEYWORD_TYPE . '_multiple';
    public const KEY_SUB_SEARCH = 'sub_' . self::KEY_SEARCH;
    public const KEY_SEARCH_OPERATOR = self::KEY_SEARCH . '_' . self::KEY_OPERATOR;
    public const KEY_SEARCH_OPERATOR_AND = self::KEY_SEARCH_OPERATOR . '_and';
    public const KEY_SEARCH_OPERATOR_OR = self::KEY_SEARCH_OPERATOR . '_or';

    public const KEY_SORT = 'sort';
    public const KEY_FILTER = 'filter';
    public const KEY_SORT_LABEL = self::KEY_SORT . '_' . self::KEY_LABEL;
    public const KEY_SORT_VALUES = self::KEY_SORT . '_' . self::KEY_VALUES;
    public const KEY_SUB_KEYWORD = 'sub_' . self::KEY_KEYWORD;
    public const KEY_PAGINATE_LENGTH = 'length';

    protected string $view = '';
    protected string $prefix = 'layouts.components.';

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        return view($this->prefix . $this->view);
    }

    public function renderWithData()
    {
        return $this->render()->with($this->data());
    }
}
