<?php

namespace LaravelSupports\Views\Components\Inputs;


class GridCheckBoxListComponent extends CheckBoxListComponent
{
    protected string $view = 'input.grid_checkbox_list_component';
    public string $rowClass = '';
    public string $colClass = '';
    public int $rows = 1;

    public function __construct(array $items = [], string $rowClass = '', string $colClass = '', string $inputClass = '', string $labelClass = '', string $othersClass = 'form-control others-input', int $rows = 1, int $maxSelectableSize = 0)
    {
        $this->items = $items;
        $this->rowClass = $rowClass;
        $this->colClass = $colClass;
        $this->inputClass = $inputClass;
        $this->labelClass = $labelClass;
        $this->othersClass = $othersClass;
        $this->rows = $rows;
        $this->maxSelectableSize = $maxSelectableSize;
    }
}
