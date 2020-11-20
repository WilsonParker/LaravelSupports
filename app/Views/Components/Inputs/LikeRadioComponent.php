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

    protected string $view = 'input.like_radio_component';

    public string $name;
    public array $items;

    /**
     * RadioComponent constructor.
     *
     * @param string $name
     * @param array $items
     */
    public function __construct(string $name = '', array $items = [])
    {
        $this->name = $name;
        $this->items = $items;
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
