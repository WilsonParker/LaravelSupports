<?php


namespace App\LaravelSupports\Library\Supports\Models\Common;


use App\LaravelSupports\Library\FileUpload;
use App\LaravelSupports\Models\Common\BaseModel;

class FilesModel extends BaseModel
{
    protected string $table = "files";
    protected string $primaryKey = "ix";

    public bool $timestamps = true;

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
