<?php


namespace LaravelSupports\Files\Contracts;


interface FileModelSaveContracts
{
    public function saveModel(string $path, string $name, string $originName): FileModelSaveContracts;

    public function getPath(): string;

    public function getName(): string;

    public function getOriginName(): string;
}
