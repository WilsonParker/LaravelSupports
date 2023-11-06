<?php


namespace LaravelSupports\Models\Common;


use LaravelSupports\FileUpload;

class FilesModel extends BaseModel
{
    public $timestamps = true;
    protected $table = "files";
    protected $primaryKey = "ix";
    protected array $guarded = ["ix"];

    /**
     * 파일 URL을 획득
     * @return  string
     * @author
     * @added   2019-09-05
     * @updated 2019-09-05
     */
    public function getUrl(string $fileConfigKey)
    {
        return '/' . config("file.viewPath") . config("file.{$fileConfigKey}.path") . $this->save_name;
    }
}
