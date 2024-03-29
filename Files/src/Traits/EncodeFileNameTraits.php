<?php


namespace LaravelSupports\Files\Traits;


use Illuminate\Support\Str;

trait EncodeFileNameTraits
{
    public static function getStaticFileNameExtension(string $name): string
    {
        return Str::afterLast($name, '.');
    }

    public function encodeFileNameWithPath(string $path, string $name = '', bool $hash = true): string
    {
        return Str::finish($path, '/') . $this->encodeFileName($name, $hash);
    }

    public function encodeFileName(string $name = '', bool $hash = true): string
    {
        $encodedName = $hash ? (string)Str::uuid() : $name;
        return $encodedName . '.' . $this->getFileNameExtension($name);
    }

    public function getFileNameExtension(string $name): string
    {
        return Str::afterLast($name, '.');
    }

    public function getFileExtension($file): string
    {
        return $file->getClientOriginalExtension();
    }

    public function getImageFileExtension($file): string
    {
        $extension = '';
        switch (exif_imagetype($file)) {
            case 1 :
                $extension = "gif";
                break;
            case 2 :
                $extension = "jpg";
                break;
            case 3 :
                $extension = "png";
                break;
        }
        return $extension;
    }
}
