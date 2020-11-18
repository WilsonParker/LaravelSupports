<?php

namespace LaravelSupports\Views\Components\Tables;


use LaravelSupports\Views\Components\BaseComponent;

class DataTableComponent extends BaseComponent
{
    protected string $view = 'table.data_table_component';

    public string $title;
    public string $header;
    public array $sort;

    /**
     * Create a new component instance.
     *
     * @param string $title
     * @param string $header
     * @param array $sort
     */
    public function __construct(string $title = '', string $header = '', array $sort = [0, 'desc'])
    {
        $this->title = $title;
        $this->header = $header;
        $this->sort = $sort;
    }

}
