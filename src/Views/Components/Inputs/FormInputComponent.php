<?php

namespace LaravelSupports\Views\Components\Inputs;


use LaravelSupports\Views\Components\BaseComponent;

class FormInputComponent extends BaseComponent
{
    protected string $view = 'input.form_input_component';

    public string $divClass;
    public string $labelClass;
    public string $inputClass;
    public string $helpClass;
    public string $divAttr;
    public string $inputAttr;
    public string $id;
    public string $name;
    public string $label;
    public string $help;
    public bool $hasHelp;
    public bool $isTextArea;
    public $value;

    /**
     * InputComponent constructor.
     *
     * @param string $divClass
     * @param string $labelClass
     * @param string $inputClass
     * @param string $helpClass
     * @param string $id
     * @param string $name
     * @param string $label
     * @param null $value
     * @param string $help
     * @param string $divAttr
     * @param string $inputAttr
     * @param bool $hasHelp
     * @param bool $isTextArea
     */
    public function __construct(string $divClass = 'form-group', string $labelClass = '', string $inputClass = 'form-control', string $helpClass = 'form-text text-muted', string $id = '', string $name = '', string $label = '', $value = null, string $help = '', string $divAttr = '', string $inputAttr = '', bool $hasHelp = false, bool $isTextArea = false)
    {
        $this->divClass = $divClass;
        $this->labelClass = $labelClass;
        $this->inputClass = $inputClass;
        $this->helpClass = $helpClass;
        $this->id = $id;
        $this->name = $name == '' ? $id : $name;
        $this->label = $label;
        $this->help = $help;
        $this->hasHelp = $hasHelp;
        $this->isTextArea = $isTextArea;
        $this->value = $value;
        $this->divAttr = $divAttr;
        $this->inputAttr = $inputAttr;
    }

}
