<?php


namespace App\Library\Exceptions\Models;


use Illuminate\Database\Eloquent\Model;

class ExceptionModel extends Model
{
    public const KEY_MESSAGE = "message";
    public const KEY_CODE = "code";
    public const KEY_URL = "url";
    public const KEY_FILE = "file";
    public const KEY_CLASS = "class";
    public const KEY_TRACE = "trace";

    /**
     * Database Table 이름 입니다
     *
     * @see
     */
    protected $table = "exception_logs";

    public function bind(array $data) {
        $this->{self::KEY_CODE} = $data[self::KEY_CODE];
        $this->{self::KEY_MESSAGE} = $data[self::KEY_MESSAGE];
        $this->{self::KEY_URL} = $data[self::KEY_URL];
        $this->{self::KEY_FILE} = $data[self::KEY_FILE];
        $this->{self::KEY_CLASS} = $data[self::KEY_CLASS];
        $this->{self::KEY_TRACE} = $data[self::KEY_TRACE];
    }
}
