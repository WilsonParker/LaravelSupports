<?php

namespace LaravelSupports\Views\Components\Inputs;


use LaravelSupports\Views\Components\BaseComponent;

class CheckBoxListComponent extends BaseComponent
{
    const KEY_KEY = 'key';
    const KEY_TEXT = 'text';
    const KEY_IS_OTHER = 'is_other';

    protected string $view = 'input.checkbox_list_component';

    public array $items;
    public string $divClass;
    public string $inputClass;
    public string $labelClass;

    /**
     * CheckBoxListComponent constructor.
     *
     * @param array $items
     * @param string $divClass
     * @param string $inputClass
     * @param string $labelClass
     */
    public function __construct(array $items = [], string $divClass = '', string $inputClass = '', string $labelClass = '')
    {
        $this->items = $items;
        $this->divClass = $divClass;
        $this->inputClass = $inputClass;
        $this->labelClass = $labelClass;
    }


    /**
     * view 를 꾸미기 위한 data 생성
     *
     * @param array $arr
     * @param string $key
     * @param string $text
     * @param bool $isOther
     * @return void
     * @author  dew9163
     * @added   2020/11/05
     * @updated 2020/11/05
     */
    public static function buildData(array &$arr, string $key, string $text, bool $isOther = false)
    {
        array_push($arr, [
            self::KEY_KEY => $key,
            self::KEY_TEXT => $text,
            self::KEY_IS_OTHER => $isOther,
        ]);
    }
}
