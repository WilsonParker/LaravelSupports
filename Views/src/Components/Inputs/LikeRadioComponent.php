<?php

namespace LaravelSupports\Views\Components\Inputs;


use LaravelSupports\Views\Components\BaseComponent;

class LikeRadioComponent extends BaseComponent
{
    const KEY_TEXT = 'text';
    const KEY_VALUE = 'value';
    const KEY_IS_CHECKED = 'is_checked';
    const KEY_CHECKED_ICON = 'checked_icon';
    const KEY_NON_CHECKED_ICON = 'non_checked_icon';
    const KEY_CHECKED_LABEL = 'checked_label';
    const KEY_NON_CHECKED_LABEL = 'non_checked_label';
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
     * @param string $checkedIcon
     * @param string $nonCheckedIcon
     * @param string $checkedLabel
     * @param string $nonCheckedLabel
     * @param bool $isChecked
     * @return array
     * @author  WilsonParker
     * @added   2020/11/05
     * @updated 2020/11/05
     */
    public static function buildDataWithArray(array &$arr, string $text, string $value, string $checkedIcon = '', string $nonCheckedIcon = '', string $checkedLabel = '', string $nonCheckedLabel = '', bool $isChecked = false): array
    {
        $result = self::buildData($text, $value, $checkedIcon, $nonCheckedIcon, $checkedLabel, $nonCheckedLabel, $isChecked);
        if (isset($arr)) {
            array_push($arr, $result);
        }
        return $result;
    }

    public static function buildData(string $text, string $value, string $checkedIcon = '', string $nonCheckedIcon = '', string $checkedLabel = '', string $nonCheckedLabel = '', bool $isChecked = false): array
    {
        return [
            self::KEY_TEXT => $text,
            self::KEY_VALUE => $value,
            self::KEY_CHECKED_ICON => $checkedIcon,
            self::KEY_NON_CHECKED_ICON => $nonCheckedIcon,
            self::KEY_CHECKED_LABEL => $checkedLabel,
            self::KEY_NON_CHECKED_LABEL => $nonCheckedLabel,
            self::KEY_IS_CHECKED => $isChecked,
        ];
    }
}
