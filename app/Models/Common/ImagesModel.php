<?php


namespace LaravelSupports\Models\Common;


use LaravelSupports\Libraries\FileUpload;
use LaravelSupports\Models\Common\BaseModel;

class ImagesModel extends BaseModel
{
    protected $table = "images";
    protected $primaryKey = "ix";

    public $timestamps = true;

    private string $viewPath;

    protected array $guarded = ["ix"];

    protected function init()
    {
        $this->viewPath = config("image.viewPath");
    }

    /**
     * @param string $tableType
     * @param int $refIx
     * @return  Collection
     * @author
     * @added   2019-08-02
     *
     * 등록날짜 순으로 정렬 추가
     * @updator WilsonParker
     * @updated 2019-08-27
     */
    public function getDetail(string $tableType, int $refIx)
    {
        return $this->select()->where([
            ["table_type", $tableType],
            ["ref_ix", $refIx]])
            ->orderby("created_at", "desc")
            ->first();
    }

    /**
     * 이미지 테이블의 IX로 데이터 가져오기
     * @param int $ix
     * 이미지 테이블 고유 IX
     * @return
     * @author
     * @added   2019-08-29
     * @updated 2019-08-29
     */
    public function getByIx(int $ix)
    {
        return $this->select()->where("ix", $ix)->first();
    }

    /**
     * table 의 $path 를 받아서 url 을 생성하여 return 해줍니다
     *
     * ex)
     * $showingBanner->getImage()->getUrl($path)
     *
     * @param string
     * @return  string
     * @author  WilsonParker
     * @added   2019-08-26
     * @updated 2019-08-26
     */
    public function getUrl($path): string
    {
        return "/" . $this->viewPath . $path . "/" . $this->save_name;
    }

    /**
     * delete data with files data
     *
     * @param string $path
     * @return bool
     * @throws \Exception
     * @author  WilsonParker
     * @added   2020/03/31
     * @updated 2020/03/31
     */
    public function deleteWithFile(string $path): bool
    {
        $fileManger = new FileUpload();
        $r = $fileManger->remove($path . "/" . $this->save_name);
        return $this->delete();
    }

}
