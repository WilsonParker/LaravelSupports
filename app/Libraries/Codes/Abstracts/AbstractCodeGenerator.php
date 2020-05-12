<?php


namespace LaravelSupports\Libraries\Codes\Abstracts;


use LaravelSupports\Models\Members\PlusMemberModel;
use LaravelSupports\Libraries\Codes\Exceptions\CodeGenerateException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use phpseclib\System\SSH\Agent;

abstract class AbstractCodeGenerator
{

    /**
     * Maximum number of retries when duplicate code is generated
     *
     * @var int
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    protected int $replayCount = 10;

    /**
     * Size of code
     *
     * @var int
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    protected int $codeLength = 0;

    /**
     * Characters to bo applied to the code
     *
     * @var string
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    protected string $characters = '0123456789';

    /**
     * Size of $characters
     * It it automatically assigned
     *
     * @var int
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    protected int $charactersLength = 0;

    public function __construct()
    {
        // generate $charactersLength
        $this->charactersLength = Str::length($this->characters) - 1;
    }


    /**
     * Change the code of the  model
     *
     * @param Model $model
     * @param string $code
     * @return Model
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    abstract protected function bindCode(Model $model, string $code): Model;

    /**
     * Generate code and retries $replayCount if the code is duplicated
     *
     * @param Model $model
     * @param bool $isNeedException
     * @return Model
     * @throws \Throwable
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    public function generateCode(Model $model, bool $isNeedException = false): Model
    {
        $code = $this->createCode();
        for ($i = 0; $i < $this->replayCount; $i++) {
            $result = $this->bindCode($model, $code);
            if (is_null($result)) {
                continue;
            } else {
                return $result;
            }
        }

        // Throw exception when number of retries exceeded and $isNeedException is true
        throw_if($isNeedException, CodeGenerateException::class, "Number of retries exceeded");
        return null;
    }

    /**
     * create code
     *
     * @return string
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    public function createCode(): string
    {
        $code = "";
        for ($i = 0; $i < $this->codeLength; $i++) {
            $code .= $this->createChar();
        }
        return $code;
    }

    /**
     * create character in code without $exceptionChars
     *
     * @param string $exceptionChars
     * regular expression
     * ex) "/(0|1|2|3|4|5)/"
     * @return string
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    public function createChar($exceptionChars = ""): string
    {
        if (is_null($exceptionChars) || $exceptionChars == "") {
            return Str::substr($this->characters, rand(0, $this->charactersLength), 1);
        } else {
            $replaced = preg_replace_array($exceptionChars, [""], $this->characters);
            return Str::substr($replaced, rand(0, Str::length($replaced) - 1), 1);
        }
    }

}
