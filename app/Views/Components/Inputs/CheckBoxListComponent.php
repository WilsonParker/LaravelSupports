<?php

namespace LaravelSupports\Views\Components\Inputs;


use LaravelSupports\Views\Components\BaseComponent;

class CheckBoxListComponent extends BaseComponent
{
    const KEY_KEY = 'key';
    const KEY_VALUE = 'value';
    const KEY_TEXT = 'text';
    const KEY_NAME = 'name';
    const KEY_CHECKED = 'checked';
    const KEY_IS_OTHER = 'is_other';
    const KEY_OTHER_VALUE = 'other_value';

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
     * @param string|null $value
     * @param string $name
     * @param bool $isChecked
     * @param bool $isOther
     * @param string $otherValue
     * @return array
     * @author  dew9163
     * @added   2020/11/05
     * @updated 2020/11/18
     */
    public static function buildDataWithArray(array &$arr, string $key, string $text, string $value = null, string $name = '', bool $isChecked = false, bool $isOther = false, string $otherValue = ''): array
    {
        $result = self::buildData($key, $text, $value, $name, $isChecked, $isOther, $otherValue);
        if (isset($arr)) {
            array_push($arr, $result);
        }
        return $result;
    }

    public static function buildData(string $key, string $text, string $value = null, string $name = '', bool $isChecked = false, bool $isOther = false, string $otherValue = ''): array
    {
        $val = isset($value) ? $value : $key;
        return [
            self::KEY_KEY => $key,
            self::KEY_TEXT => $text,
            self::KEY_NAME => $name,
            self::KEY_VALUE => $val,
            self::KEY_CHECKED => $isChecked,
            self::KEY_IS_OTHER => $isOther,
            self::KEY_OTHER_VALUE => $otherValue,
        ];
    }
}
