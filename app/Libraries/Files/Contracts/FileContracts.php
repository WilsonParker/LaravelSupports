<?php


namespace LaravelSupports\Libraries\Files\Contracts;


interface FileContracts
{
    public function deleteFile(string $path, string $name): string;

    public function saveFile($file, string $path): string;

    public function saveImageUsingUrl(string $url, string $path): string;

}
