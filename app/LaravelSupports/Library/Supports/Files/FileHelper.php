<?php


namespace App\Library;


use Illuminate\Support\Facades\Storage;

class FileHelper
{

    /**
     * @brief    첨부파일 업로드 처리
     * @param    Request $request, String $dir, String $filename
     * @return   array
     * @author   WilsonParker
     * @date     20190408
     * @updated  20190618
     * @bug      파일이름을 파라미터로 넘겨주지 않으면 램덤으로 설정합니다 20190408 정태현
     * @bug      파일 이름에 확장자를 추가하여 $result 에 url을 설정합니다 20190409 정태현
     * @todo
     * @see
     **/
    public static function store($file, $dir = 'img', $filename = null)
    {
        if($filename == null){
            $filename = uniqid('img_', true);
        }
        $ext = substr(strrchr($file->getClientOriginalName(), '.'), 1);
        $filename .= ".".$ext;

        $result = [
            'is_saved'  => false,
            'message'   => '파일 업로드에 실패했습니다.',
            'path'      => '',
        ];

        // 기존 구조에 맞춰 upload 폴더 하위에 저장되도록 고정(파일시스템은 upload driver 사용)
        // \File::makeDirectory("/upload/{$dir}", $mode = 0777, true, true);
        \File::makeDirectory($dir, $mode = 0777, true, true);
        if ($filename) {
            $result["is_saved"] = true;
            $result["message"] = "OK";
            // $result["path"] = "/upload\/" . $file->storeAs($dir, $filename, 'upload');
            $result["path"] = $file->storeAs($dir, $filename, 'public');
            $result["filename"] = $filename;
        } else {
            $result["is_saved"] = true;
            $result["message"] = "OK";
            // $result["path"] = "/upload\/" . $file->store($dir, 'upload');
            $result["path"] = $file->store($dir, 'public');
            $result["filename"] = $file->hashName();
        }
        $result["url"] = self::url("$dir/$filename");
        return $result;
    }

    /**
     * @brief    파일 URL 조회
     * @param    String $filePath
     * @return   File
     * @author   WilsonParker
     * @date     20190408
     * @bug
     * @todo
     * @see
     **/
    public static function url($filePath)
    {
        return Storage::disk('public')->url($filePath);
    }
}
