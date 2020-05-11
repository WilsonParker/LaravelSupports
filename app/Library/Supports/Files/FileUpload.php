<?php

namespace App\Library;
use File;
use Image;

/**
 * 파일업로드 라이브러리
 * @author  오세현
 * @added   2019-07-19
 * @updated 2019-07-19
 */

class FileUpload
{
    /**
     * 이미지 파일 업로드 하는 함수
     * http://image.intervention.io 참조
     * @param  $file
     *  파일 객체
     * @param $newFileName
     *  신규 파일명
     * @param $basePath
     *  저장 기본 경로
     * @return  array | Exception
     * @throws \Exception
     * @author  오세현
     * @added   2019-07-29
     * @updated 2019-07-29
     */
    public function imgUpload($file, $newFileName, $basePath) {
        try {
            $originalFile = Image::make($file->getRealPath());
            $oriFileName = $file->getClientOriginalName();
            $fileHash = md5_file($file);//업로드파일 md5 해쉬
            // 이미 파일의 디렉토리가 존재한다면 디렉토리를 추가적으로 생성하지 않는다.
            if(!File::isDirectory($basePath)){
                File::makeDirectory($basePath, $mode = 0755, true, true);
            }

            $originalFile->save($basePath."/".$newFileName);

            return array("oriFileName" => $oriFileName, "newFileName" => $newFileName, "hash" => $fileHash);
        } catch (\Exception $e) {
            dd($e);
            throw new \Exception(" 파일 업로드중 오류가 발생하였습니다.");
        }
    }

    /**
     * 파일 업로드 하는 함수
     * @param  $file
     *  파일 객체
     * @param $newFileName
     *  신규 파일명
     * @param $basePath
     *  저장 기본 경로
     * @return  array | Exception
     * @throws \Exception
     * @author  오세현
     * @added   2019-07-29
     * @updated 2019-07-29
     */
    public function fileUpload($file, $newFileName, $basePath)
    {
        try {

            $oriFileName = $file->getClientOriginalName();
            $fileHash = md5_file($file);//업로드파일 md5 해쉬
            // 이미 파일의 디렉토리가 존재한다면 디렉토리를 추가적으로 생성하지 않는다.
            if(!File::isDirectory($basePath)){
                File::makeDirectory($basePath, $mode = 0755, true, true);
            }
            // 파일 이동
            $file->move($basePath, $newFileName);

            return array("oriFileName" => $oriFileName, "newFileName" => $newFileName, "hash" => $fileHash);
        } catch (\Exception $e) {
            throw new \Exception(" 파일 업로드중 오류가 발생하였습니다.");
        }
    }

    /**
     * 파일 삭제하는 함수
     * @param   $file
     * @return
     * @author  오세현
     * @added   2019-08-29
     * @updated 2019-08-29
     */
    public function remove($file)
    {
        if(File::exists($file)) { // 파일이 존재하는 경우에만 삭제
            return File::delete($file);
        } else {
            return true;
        }

    }

    /**
     * 기본적으로 $newFileName 을 생성해줍니다
     *
     * @param   $file
     * @param   $basePath
     * @return  Exception|array
     * @throws  \Exception
     * @author  WilsonParker
     * @added   2019-08-26
     * @updated 2019-08-26
     */
    public function imgUploadWithDefaultFileName($file, $basePath) {
        $newFileName = time() . rand(100, 999) . "." . $file->getClientOriginalExtension();
        return $this->imgUpload($file, $newFileName, $basePath);
    }

}
