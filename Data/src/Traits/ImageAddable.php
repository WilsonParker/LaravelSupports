<?php
/**
 * @class   ImageAddable.php
 * @brief   추가이미지 기록용 트레이트
 *          추가이미지를 사용하는 모델 클래스에 trait를 import해서 이용
 *          모델 클래스에 $addImagesDir 프로퍼티를 설정(추가이미지 파일저장위치, 미지정시 etc/addimages에 저장)
 * @date    20190903
 **/

namespace LaravelSupports\Data\Traits;

use LaravelSupports\FileUpload;
use LaravelSupports\Models\ImagesModel;

trait ImageAddable
{
    /**
     * @brief    폼 리퀘스트 받아서 추가이미지 최신화하는 작업
     * @param array $data
     * @return   array
     * @date     20190903
     **/
    public function syncAddImages($data)
    {
        if (request()->method() == "POST") {
            if ($data["add_images"]) {
                $fileManager = new FileUpload();

                $vieworder = 0;
                $addImagesDir = isset($this->addImagesDir) ? $this->addImagesDir : 'etc/addimages';

                foreach ($data["add_images"] as $addImage) {
                    // 이미지 모델 생성
                    $imgModel = new ImagesModel();

                    // 저장용 파일명 생성
                    $newFileName = time() . rand(100, 999) . "." . $addImage->getClientOriginalExtension();

                    // 파일저장
                    $fileResult = $fileManager->imgUpload($addImage, $newFileName, $addImagesDir);

                    // 이미지 모델 데이터 입력
                    $imgModel->table_type = $this->addImagesTableType;
                    $imgModel->ref_ix = $this->getKey();
                    $imgModel->origin_name = $fileResult["oriFileName"];
                    $imgModel->save_name = $fileResult["newFileName"];
                    $imgModel->sort = $vieworder;
                    $imgModel->save();

                    $vieworder++;
                }
            }
        } elseif (request()->method() == "PUT") {
            $fileMetadataJSON = $data["metadata_files"];
            if (!$fileMetadataJSON) {
                return $this->addImages()->delete();
            }

            // 업로드 된 추가 상세이미지들에 대한 메타데이터 목록
            $fileMetadata = json_decode($fileMetadataJSON);

            if (!isset($fileMetadata->files->add_images)) {
                return $this->addImages()->delete();
            }

            // 클라이언트로부터 실제로 업로드 된 추가 상세이미지 파일들과 추가를 위한 인덱스 넘버링
            $uploadedAddImagesFile = $data["add_images"];
            $newImageIndex = 0;


            // DB상에 기록되어있는 기존 추가 상세이미지들에 대한 배열
            $legacyAddImages = [];
            foreach ($this->addImages as $addImage) {
                $legacyAddImages[$addImage->save_name] = $addImage;
            }

            $fileManager = new FileUpload();

            $addImagesDir = isset($this->addImagesDir) ? $this->addImagesDir : 'etc/addimages';

            // 메타데이터를 반복하며 조건에 따른 처리
            for ($i = 0; $i < count($fileMetadata->files->add_images); $i++) {
                // 순서대로 메타데이터 획득
                $one = $fileMetadata->files->add_images[$i];

                // 기존에 등록되어있던 파일이라면 노출순서만 정정
                if (!$one->isNew && isset($legacyAddImages[basename($one->url)])) {
                    $legacyAddImages[basename($one->url)]->update([
                        'sort' => $i,
                    ]);
                    unset($legacyAddImages[basename($one->url)]);
                    // 신규 등록하는 파일이라면 파일복사 후 DB에 저장
                } else {
                    if (!isset($uploadedAddImagesFile[$newImageIndex])) continue;

                    $addImage = $uploadedAddImagesFile[$newImageIndex];

                    // 이미지 모델 생성
                    $imgModel = new ImagesModel();

                    // 저장용 파일명 생성
                    $newFileName = time() . rand(100, 999) . "." . $addImage->getClientOriginalExtension();

                    // 파일저장
                    $fileResult = $fileManager->imgUpload($addImage, $newFileName, $addImagesDir);

                    // 이미지 모델 데이터 입력
                    $imgModel->table_type = $this->addImagesTableType;
                    $imgModel->ref_ix = $this->getKey();
                    $imgModel->origin_name = $fileResult["oriFileName"];
                    $imgModel->save_name = $fileResult["newFileName"];
                    $imgModel->sort = $i;
                    $imgModel->save();

                    $newImageIndex++;
                }
            }

            // 메타데이터 반복을 마친 후에 남아있는 $legacyAddImages는 삭제된 파일이므로 DB와 스토리지에서 제거해준다.
            foreach ($legacyAddImages as $removedImage) {
                $removedImage->delete();
            }
        }
    }
}
