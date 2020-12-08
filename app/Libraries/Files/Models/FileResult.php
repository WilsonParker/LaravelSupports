<?php


namespace App\Library\LaravelSupports\app\Libraries\Files\Models;


class FileResult
{
    private string $path;
    private string $name;
    private string $originName;

    /**
     * FileResult constructor.
     *
     * @param string $path
     * @param string $name
     * @param string $originName
     */
    public function __construct(string $path, string $name, string $originName = '')
    {
        $this->path = $path;
        $this->name = $name;
        $this->originName = $originName;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getOriginName(): string
    {
        return $this->originName;
    }

    /**
     * @param string $originName
     */
    public function setOriginName(string $originName): void
    {
        $this->originName = $originName;
    }

}
