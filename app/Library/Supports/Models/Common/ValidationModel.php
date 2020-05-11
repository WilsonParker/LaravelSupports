<?php

/**
 * @class   ValidationModel.php
 * @author  WilsonParker
 * @brief
 * @see
 * @todo
 * @bug
 * @create  20181226
 * @update  20181227
 **/

namespace App\Library\Supports\Models\Common;

use App\Library\Supports\Crypt\CryptHelper;
use App\Library\Supports\Data\StringHelper;
use App\Library\Supports\Requests\RequestBinder;
use App\Library\Supports\Requests\Contracts\RequestValueCastContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

abstract class ValidationModel extends Model implements RequestValueCastContract
{
    use RequestBinder;
    use CryptHelper;

    public const MESSAGE = "message", DEFAULT_MESSAGE = "", REG = "$([\w_])+$"

        /**
         * @author  WilsonParker
         * @brief   Rules 에 적용할 조건 값 입니다
         * C : Create
         * U : Update
         * A : C|U = ALL
         * @var   String
         **/, case = "case", CASE_CREATE = "C", CASE_UPDATE = "U", CASE_ALL = "A", CASE_ALL_WITH_OR = "C|U";

    protected array $cols = [];
    protected array $messages = [];
    protected array $rules = [];

    /**
     * @author  WilsonParker
     * @brief
     * message 의 default 는 $DEFAULT_MESSAGE 입니다
     * case 의 default 는 A 입니다
     *
     * case 의 경우
     * 단일 문자를 검사하므로 CU / C,U /C|U 등 모두 가능합니다
     *
     * bail 처럼 message, case 등 값을 포함하지 않으면 모두
     * default 를 가지게 됩니다
     *
     * @var   array
     **/

    protected array $information = [];

    /*
    Example)
    protected $information = [
         "MB_IDX" => [
             "bail",
             "require" => [
                 "message" => "an message",
                 "case" => "C|U"
             ],
             "date" => [
                 "message" => "an message",
                 "case" => "C"
             ],
             "in:Y,N" => [
                 "message" => "an message"
             ]
         ]
     ];
     */

    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * @return  array
     * @author  WilsonParker
     * @brief
     * $information 의 값을 이용하여
     * message 들을 Property.in => some message 형식으로
     * $messages 에 저장 합니다
     * @see
     * @todo
     * @bug
     * @create  20181227
     * @update  20181227
     **/
    protected function createMessages(): array
    {
        $messages = [];
        foreach ($this->information as $key => $info) {
            foreach ($info as $iKey => $iVal) {
                $message = is_array($iVal) && array_key_exists(self::MESSAGE, $iVal) ? $iVal[self::MESSAGE] : self::DEFAULT_MESSAGE;
                $rKey = is_numeric($iKey) ? $iVal : $iKey;
                $messages[$key . "." . $this->matchesKey($rKey)] = $message;
            }
        }
        return $messages;
    }

    /**
     * @return  array
     * @author  WilsonParker
     * @brief
     * $information 의 값을 이용하여
     * rule 들을 Property => required|sometimes 형식으로
     * $rules 에 저장 합니다
     * @see
     * @todo
     * @bug
     * @create  20181227
     * @update  20181227
     **/
    protected function createRules(): array
    {
        $rules = [];
        foreach (explode("|", self::CASE_ALL_WITH_OR) as $iCase) {
            foreach ($this->information as $key => $info) {
                $i = 0;
                $rule = "";
                foreach ($info as $iKey => $iVal) {
                    if (is_array($iVal) && array_key_exists(self::case, $iVal)) {
                        if (!StringHelper::contains(strtoupper($iVal[self::case]), $iCase) && !(strtoupper($iVal[self::case]) == self::CASE_ALL)) {
                            continue;
                        }
                    }
                    $rKey = is_numeric($iKey) ? $iVal : $iKey;
                    $rule .= $i != 0 ? "|" . $rKey : $rKey;
                    $i++;
                }
                if (!empty($rule)) {
                    $rules[$iCase][$key] = $rule;
                }
            }
        }
        return $rules;
    }

    /**
     * @param   string
     * @return  string
     * @author  WilsonParker
     * @brief
     * $messages 를 생성할 때
     * Check.in:yn 같은 값을 Check.in 으로 바꾸어 줍니다
     * @see
     * @todo
     * @bug
     * @create  20181227
     * @update  20181227
     **/
    protected function matchesKey(string $key): string
    {
        preg_match_all(self::REG, $key, $matches);
        return $matches[0][0];
    }

    public function getRules(string $case): string
    {
        if (empty($this->rules)) {
            $this->rules = $this->createRules();
        }
        return $this->rules[strtoupper($case)];
    }

    public function getMessages(): string
    {
        if (empty($this->messages)) {
            $this->messages = $this->createMessages();
        }
        return $this->messages;
    }

    public function getCols(): array
    {
        if (empty($this->cols)) {
            $this->cols = DB::select("desc " . $this->table);
        }
        return $this->cols;
    }

    /**
     * @param Request $request
     * @param string
     * @return
     * @author  WilsonParker
     * @brief   Validator::make 메소드를 실행하여 return 해줍니다
     * @see
     * @todo
     * @bug
     * @create  20190104
     * @update  20190104
     */
    public function make(Request $request, string $case)
    {
        return \Validator::make($request->all(), $this->getRules($case), $this->getMessages())->validate();
    }

}
