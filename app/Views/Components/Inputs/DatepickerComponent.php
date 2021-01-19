<?php

namespace LaravelSupports\Views\Components\Inputs;


use LaravelSupports\Views\Components\BaseComponent;

class DatepickerComponent extends BaseComponent
{
    protected string $view = 'input.datepicker';

    public string $startDate;
    public string $endDate;

    public string $divClass;
    public string $dateAll;

    public string $options = "format: 'yyyy-mm-dd',
                autoclose: true,";

    /**
     * CheckBoxListComponent constructor.
     *
     * @param string $startDate
     * @param string $endDate
     */
    public function __construct(string $startDate = '', string $endDate = '', string $divClass = '', string $dateAll = 'N', string $options = '')
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->divClass = $divClass;
        $this->dateAll = $dateAll;
        $this->options = $options != '' ? $options : $this->options;
    }
}
