<?php


namespace LaravelSupports\Libraries\Codes\Abstracts;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LaravelSupports\Libraries\Codes\Contracts\GenerateCode;
use LaravelSupports\Libraries\Codes\Exceptions\CodeGenerateException;
use LaravelSupports\Libraries\Supports\Databases\Traits\TransactionTrait;
use Throwable;

abstract class AbstractCodeGenerator
{
    use TransactionTrait;

    public const DEFAULT_CODE_LENGTH = 8;

    /**
     * Maximum number of retries when duplicate code is generated
     *
     * @var int
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    protected int $replayCount = 100;

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

    public function __construct($codeLength = self::DEFAULT_CODE_LENGTH)
    {
        $this->codeLength = $codeLength;
        // generate $charactersLength
        $this->initLength();
    }

    /**
     * If the $charactersLength doesn't exist, initialize it
     *
     * @return void
     * @author  dew9163
     * @added   2020/06/11
     * @updated 2020/06/11
     */
    protected function initLength()
    {
        if ($this->charactersLength == 0) {
            $this->charactersLength = Str::length($this->characters) - 1;
        }
    }

    //abstract protected function bindCode(CodeGeneratable $model, string $code): Model;

    /**
     * Change the code of the  model
     *
     * @param GenerateCode $model
     * @param string $code
     * @return Model
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     * @updated 2020/06/11
     */
    protected function bindCode(GenerateCode $model, string $code): Model
    {
        $callback = function () use ($model, $code) {
            $model->setCode($code);
            return $model;
        };
        $errorCallback = function ($e) {
            return null;
        };
        return $this->runTransaction($callback, $errorCallback);
    }

    /**
     * Check if code exists
     *
     * @param GenerateCode $model
     * @param string $code
     * @return bool
     * @author  dew9163
     * @added   2020/06/11
     * @updated 2020/06/11
     */
    // abstract protected function isExistCode(Model $model, string $code): bool;
    protected function isExistCode(GenerateCode $model, string $code): bool
    {
        return $model->isExists($code);
    }

    /**
     * Generate code and retries $replayCount if the code is duplicated
     *
     * @param GenerateCode $model
     * @param bool $isNeedException
     * @return Model
     * @throws Throwable
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    public function generateCode(GenerateCode $model, bool $isNeedException = false): ?Model
    {
        $code = $this->createUniqueCode($model, $isNeedException);
        return $this->bindCode($model, $code);
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
     * create not exists code
     *
     * @param GenerateCode $model
     * @param bool $isNeedException
     * @return string
     * @throws Throwable
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    public function createUniqueCode(GenerateCode $model, bool $isNeedException = false): ?string
    {
        for ($i = 0; $i < $this->replayCount; $i++) {
            $code = $this->createCode();
            if ($this->isExistCode($model, $code)) {
                continue;
            } else {
                return $code;
            }
        }

        // Throw exception when number of retries exceeded and $isNeedException is true
        throw_if($isNeedException, CodeGenerateException::class, "Number of retries exceeded");
        return null;
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
