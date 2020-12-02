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
    const KEY_OTHER_HINT = 'other_hint';

    protected string $view = 'input.checkbox_list_component';

    public array $items;
    public string $divClass = '';
    public string $inputClass = '';
    public string $labelClass = '';
    public string $othersClass = '';
    public int $maxSelectableSize = 0;

    /**
     * CheckBoxListComponent constructor.
     *
     * @param array $items
     * @param string $divClass
     * @param string $inputClass
     * @param string $labelClass
     * @param string $othersClass
     * @param int $maxSelectableSize
     */
    public function __construct(array $items = [], string $divClass = '', string $inputClass = '', string $labelClass = '', string $othersClass = 'form-control others-input', int $maxSelectableSize = 0)
    {
        $this->items = $items;
        $this->divClass = $divClass;
        $this->inputClass = $inputClass;
        $this->labelClass = $labelClass;
        $this->othersClass = $othersClass;
        $this->maxSelectableSize = $maxSelectableSize;
    }

    /**
     * 기타 내용을 input text 를 받기 위한 tag 생성
     *
     * @param array $values
     * @return string
     * @author  dew9163
     * @added   2020/12/02
     * @updated 2020/12/02
     */
    public function buildOthersInput(array $values): string
    {
        $result = '';
        if ($values[self::KEY_IS_OTHER]) {
            $otherID = $values[self::KEY_KEY];
            $otherInputID = $otherID . '_input';
            $result .= "
            <div class=\"input-group mb-3\">
                <input type=\"text\" class=\"{$this->othersClass}\" id=\"{$otherInputID}\" name=\"{$otherInputID}\" placeholder=\"{$values[self::KEY_OTHER_HINT]}\"
        ";
            if ($values[self::KEY_CHECKED]) {
                $result .= "value=\"{$values[self::KEY_OTHER_VALUE]}\"";
            }
            $result .= "aria-label=\"{$values[self::KEY_OTHER_HINT]}\"";

            if (!$values[self::KEY_CHECKED]) {
                $result .= "readonly";
            }
            $result .= "></div>";
        }
        return $result;
    }

    /**
     * 기타 내용을 input text 를 받기 위한 script 생성
     *
     * @param array $values
     * @return string
     * @author  dew9163
     * @added   2020/12/02
     * @updated 2020/12/02
     */
    public function buildOthersScript(array $values): string
    {
        $result = "";
        if ($values[self::KEY_IS_OTHER]) {
            $otherID = $values[self::KEY_KEY];
            $otherInputID = $otherID . '_input';
            $result = "
            <script>
                $(function () {
                    $('#{$otherID}').on('click', function () {
                        $('#{$otherInputID}').attr('readonly', !this.checked);
                    });
                });
            </script>
            ";
        }
        return $result;
    }

    /**
     * 최대 선택 갯수 만큼만 선택 할 수 있도록 설정하는 script
     *
     * @return string
     * @author  dew9163
     * @added   2020/12/02
     * @updated 2020/12/02
     */
    public function buildMaxSelectableScript(): string
    {
        return "
        <script>
            $(function () {
                let checkBoxSelector = \"input[name='{$this->items[0][self::KEY_NAME]}']\";
                $(checkBoxSelector).on('click', function () {
                    let selectedSize = $(checkBoxSelector + ':checked').length;
                    let maxSelectableSize = {$this->maxSelectableSize};
                    if (maxSelectableSize > 0 && selectedSize > maxSelectableSize) {
                        this.checked = false;
                        modal.setContent(`최대 {$this->maxSelectableSize}개 까지만 선택 가능 합니다`);
                        modal.show();
                    }
                });
            });
        </script>
        ";
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
     * @param string $otherHint
     * @return array
     * @author  dew9163
     * @added   2020/11/05
     * @updated 2020/11/18
     */
    public static function buildDataWithArray(array &$arr, string $key, string $text, string $value = null, string $name = '', bool $isChecked = false, bool $isOther = false, string $otherValue = '', string $otherHint = ''): array
    {
        $result = self::buildData($key, $text, $value, $name, $isChecked, $isOther, $otherValue, $otherHint);
        if (isset($arr)) {
            array_push($arr, $result);
        }
        return $result;
    }

    public static function buildData(string $key, string $text, string $value = null, string $name = '', bool $isChecked = false, bool $isOther = false, string $otherValue = '', string $otherHint = ''): array
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
            self::KEY_OTHER_HINT => $otherHint,
        ];
    }
}
