<?php


namespace LaravelSupports\Files\Services;


use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as Image;
use LaravelSupports\Files\Contracts\FileModelSaveContracts;
use LaravelSupports\Files\Models\FileResult;
use LaravelSupports\Files\Traits\EncodeFileNameTraits;

class FileService
{
    use EncodeFileNameTraits;

    const TEMP_PATH = 'files/temp/';

    public function saveImageUsingUrl(string $url, string $path, string $originName = ''): FileResult
    {
        $encodedName = self::TEMP_PATH . $this->encodeFileName();
        $img = Image::make($url);
        $img->save($encodedName, 70);
        return $this->saveFile(new File($encodedName), $path, $originName);
    }

    public function saveFile($file, string $path, string $originName = ''): FileResult
    {
        $name = Str::afterLast(Storage::putFile($path, $file), '/');
        return new FileResult($path, $name, $originName);
    }

    public function deleteFileWithModel(FileModelSaveContracts $model): FileResult
    {
        return $this->deleteFile($model->getPath(), $model->getName());
    }

    public function deleteFile(string $path, string $name = '', string $originName = ''): FileResult
    {
        $pathStr = Str::finish($path, '/');
        Storage::delete($pathStr . $name);
        return new FileResult($path, $name, $originName);
    }

}
