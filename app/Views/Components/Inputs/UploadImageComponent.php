<?php

namespace LaravelSupports\Views\Components\Inputs;


use LaravelSupports\Views\Components\BaseComponent;

class UploadImageComponent extends BaseComponent
{
    protected string $view = 'input.upload_image_component';

    public string $imgClass;
    public string $id;
    public string $text;
    public string $src;
    public bool $needPreview;

    /**
     * UploadImageComponent constructor.
     *
     * @param string $id
     * @param string $src
     * @param string $imgClass
     * @param string $text
     * @param bool $needPreview
     */
    public function __construct(string $id = '', string $src = '...', string $imgClass = '', string $text = 'upload', bool $needPreview = false)
    {
        $this->id = $id;
        $this->text = $text;
        $this->imgClass = $imgClass;
        $this->src = $src;
        $this->needPreview = $needPreview;
    }


}
