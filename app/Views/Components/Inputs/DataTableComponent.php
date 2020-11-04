<?php

namespace App\Library\LaravelSupports\app\Views\Components\Inputs;

use App\View\Components\BaseComponent;

class DataTableComponent extends BaseComponent
{
    protected string $view = 'table.data_table_component';

    public string $title;
    public string $header;

    /**
     * Create a new component instance.
     *
     * @param string $title
     * @param string $header
     */
    public function __construct(string $title = '', string $header = '')
    {
        $this->title = $title;
        $this->header = $header;
    }

}
