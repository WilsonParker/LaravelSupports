<?php

namespace LaravelSupports\Views\Components\Inputs;

class DataTableSearchComponent extends TableSearchComponent
{
    const KEY_PAGINATE_LENGTH = 'length';
    const KEY_SEARCH = 'search';
    const KEY_SORT = 'sort';
    const KEY_KEYWORD = 'keyword';

    protected string $view = 'table.data_table_search_component';

}
