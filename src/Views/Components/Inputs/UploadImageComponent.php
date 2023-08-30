<?php

namespace LaravelSupports\Views\Components\Inputs;


use LaravelSupports\Views\Components\BaseComponent;

class UploadImageComponent extends BaseComponent
{
    protected string $view = 'input.upload_image_component';

    public string $imgClass;
    public string $id;
    public string $rootDivAttr;
    public string $divAttr;
    public string $inputAttr;
    public string $name;
    public string $text;
    public string $src;
    public bool $needPreview;

    /**
     * UploadImageComponent constructor.
     *
     * @param string $id
     * @param string $name
     * @param string $src
     * @param string $imgClass
     * @param string $text
     * @param string $divAttr
     * @param string $inputAttr
     * @param string $rootDivAttr
     * @param bool $needPreview
     */
    public function __construct(string $id = '', string $name = '', string $src = '...', string $imgClass = '', string $text = 'upload', string $divAttr = '', string $inputAttr = '', string $rootDivAttr = '', bool $needPreview = false)
    {
        $this->id = $id;
        $this->name = $name == '' ? $id : $name;
        $this->text = $text;
        $this->imgClass = $imgClass;
        $this->src = $src;
        $this->needPreview = $needPreview;
        $this->divAttr = $divAttr;
        $this->inputAttr = $inputAttr;
        $this->rootDivAttr = $rootDivAttr;
    }


}
