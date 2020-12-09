<?php

namespace LaravelSupports\Views\Components\Inputs;


use LaravelSupports\Views\Components\BaseComponent;

class FormSelectComponent extends BaseComponent
{
    protected string $view = 'input.form_select_component';

    public string $divClass;
    public string $labelClass;
    public string $selectClass;
    public string $divAttr;
    public string $selectAttr;
    public string $label;
    public string $id;
    public string $name;
    public string $selectedValue;
    public $items;

    /**
     * FormSelectComponent constructor.
     *
     * @param string $divClass
     * @param string $labelClass
     * @param string $selectClass
     * @param string $label
     * @param string $id
     * @param string $name
     * @param string $selectedValue
     * @param string $divAttr
     * @param string $selectAttr
     * @param array $items
     */
    public function __construct(string $divClass = 'form-group', string $labelClass = '', string $selectClass = 'custom-select form-control', string $label = '', string $id = '', string $name = '', string $selectedValue = '', string $divAttr = '', string $selectAttr = '', $items = [])
    {
        $this->divClass = $divClass;
        $this->labelClass = $labelClass;
        $this->selectClass = $selectClass;
        $this->label = $label;
        $this->id = $id;
        $this->name = $name == '' ? $id : $name;
        $this->selectedValue = $selectedValue;
        $this->items = $items;
        $this->divAttr = $divAttr;
        $this->selectAttr = $selectAttr;
    }


    public static function buildItemData(string $text, string $value): array
    {
        return [
            $text => $value
        ];
    }
}
