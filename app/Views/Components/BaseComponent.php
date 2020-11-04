<?php

namespace LaravelSupports\Views\Components;

use Illuminate\View\Component;

class BaseComponent extends Component
{
    public const KEY_SEARCH_LABEL = 'search_label';
    public const KEY_SEARCH_VALUES = 'search_values';
    public const KEY_SEARCH_TYPE = 'search_type';
    public const KEY_SEARCH_TYPE_MULTIPLE = 'search_type_multiple';
    public const KEY_SEARCH_KEYWORD_TYPE_MULTIPLE = 'search_keyword_type_multiple';
    public const KEY_SEARCH = 'search';
    public const KEY_SUB_SEARCH = 'sub_search';
    public const KEY_SEARCH_OPERATOR = 'search_operator';
    public const KEY_SEARCH_OPERATOR_AND = 'search_operator_and';
    public const KEY_SEARCH_OPERATOR_OR = 'search_operator_or';

    public const KEY_SORT = 'sort';
    public const KEY_SORT_LABEL = 'sort_label';
    public const KEY_SORT_VALUES = 'sort_values';
    public const KEY_SEARCH_KEYWORD_TYPE = 'search_keyword_type';
    public const KEY_KEYWORD = 'keyword';
    public const KEY_SUB_KEYWORD = 'sub_keyword';
    public const KEY_PAGINATE_LENGTH = 'length';

    protected string $view = '';
    protected string $prefix = 'admin.layouts.components.';

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view($this->prefix . $this->view);
    }
}
