<?php


namespace LaravelSupports\Libraries\Supports\Data\Traits;


use LaravelSupports\Libraries\FileUpload;
use LaravelSupports\Models\ImagesModel;

trait FileSaveTrait
{
    /**
     * 이미지와 image 모델을 저장을 하는 기본 함수 입니다
     * $this->uploadPath 가 설정되어야 합니다
     *
     * @param   $file
     * file data
     * @param   $ix
     * 저장하는 model 의 ix
     * @return  ImagesModel
     * @throws  \Exception
     * @author  WilsonParker
     * @added   2019-08-26
     * @updated 2019-08-26
     */
    public function defaultSaveImageWithModel($file, $ix)
    {
        // 파일저장
        $fileResult = $this->defaultSaveImageFile($file);
        return $this->defaultBindImageModel($fileResult, $ix);
    }

    /**
     * 이미지를 저장하는 기본 함수 입니다
     *
     * @param   $file
     * @return \LaravelSupports\Libraries\Exception|array
     * ("oriFileName" => $oriFileName, "newFileName" => $newFileName, "hash" => $fileHash);
     * @throws \Exception
     * @author  WilsonParker
     * @added   2019-09-06
     * @updated 2019-09-06
     */
    public function defaultSaveImageFile($file)
    {
        $fileManager = new FileUpload();
        // 파일저장
        $fileResult = $fileManager->imgUploadWithDefaultFileName($file, $this->uploadPath);
        return $fileResult;
    }

    /**
     * $fileResult 를 이용하여 이미지 모델을 저장하는 기본 함수 입니다
     *
     * @param   $fileResult
     * @param   $ix
     * @return  ImagesModel
     * @author  WilsonParker
     * @added   2019-09-06
     * @updated 2019-09-06
     */
    public function defaultBindImageModel($fileResult, $ix) {
        // 이미지 테이블 저장
        $imgModel = new ImagesModel();
        $imgModel->table_type = $this->tableType;
        $imgModel->ref_ix = $ix;
        $imgModel->origin_name = $fileResult["oriFileName"];
        $imgModel->save_name = $fileResult["newFileName"];
        $imgModel->save();
        return $imgModel;
    }

    /**
     * $fileResult 를 이용하여 url 을 생성합니다
     * $this->path 가 설정되어야 합니다
     *
     * @param   $fileResult
     * @return  string
     * @author  WilsonParker
     * @added   2019-09-06
     * @updated 2019-09-06
     */
    public function getImageUrl($fileResult) {
        $viewPath = config("image.viewPath");
        return "/".$viewPath."/".$this->path."/".$fileResult['newFileName'];
    }

}
