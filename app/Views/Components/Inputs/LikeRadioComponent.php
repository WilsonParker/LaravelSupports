<?php

namespace LaravelSupports\Views\Components\Inputs;


use LaravelSupports\Views\Components\BaseComponent;

class LikeRadioComponent extends BaseComponent
{
    const KEY_TEXT = 'text';
    const KEY_VALUE = 'value';
    const KEY_CHECKED = 'checked';
    const KEY_NON_CHECKED = 'non_checked';
    const KEY_IS_CHECKED = 'is_checked';
    const KEY_DIV_CLASS = 'div_class';
    const KEY_INPUT_CLASS = 'input_class';
    const KEY_LABEL_CLASS = 'label_class';

    protected string $view = 'input.like_radio_component';

    public array $items;
    public string $name;
    public string $divClass;
    public string $inputClass;
    public string $labelClass;

    /**
     * RadioComponent constructor.
     *
     * @param string $name
     * @param array $items
     * @param string $divClass
     * @param string $inputClass
     * @param string $labelClass
     */
    public function __construct(string $name = '', array $items = [], string $divClass = 'form-check form-check-inline', string $inputClass = 'form-check-input input_radio', string $labelClass = 'form-check-label')
    {
        $this->name = $name;
        $this->items = $items;
        $this->divClass = $divClass;
        $this->inputClass = $inputClass;
        $this->labelClass = $labelClass;
    }

    /**
     * view 를 꾸미기 위한 data 생성
     *
     * @param array $arr
     * @param string $text
     * @param string $value
     * @param string $checked
     * @param string $nonChecked
     * @param bool $isChecked
     * @return array
     * @author  dew9163
     * @added   2020/11/05
     * @updated 2020/11/05
     */
    public static function buildDataWithArray(array &$arr, string $text, string $value, string $checked, string $nonChecked, bool $isChecked = false): array
    {
        $result = self::buildData($text, $value, $checked, $nonChecked, $isChecked);
        if (isset($arr)) {
            array_push($arr, $result);
        }
        return $result;
    }

    public static function buildData(string $text, string $value, string $checked, string $nonChecked, bool $isChecked = false): array
    {
        return [
            self::KEY_TEXT => $text,
            self::KEY_VALUE => $value,
            self::KEY_CHECKED => $checked,
            self::KEY_NON_CHECKED => $nonChecked,
            self::KEY_IS_CHECKED => $isChecked,
        ];
    }
}
