<?php

namespace LaravelSupports\Views\Components;

use Illuminate\View\Component;

class SelectionComponent extends Component
{
    public string $title;
    public string $content;
    public string $onItemClick;
    public $items;

    /**
     * SelectionComponent constructor.
     *
     * @param string $title
     * @param string $content
     * @param array $items
     * @param string $onItemClick
     */
    public function __construct(string $title, string $content, $items = [], $onItemClick = '')
    {
        $this->title = $title;
        $this->content = $content;
        $this->items = $items;
        $this->onItemClick = $onItemClick;
    }

    /**
     * Create a new component instance.
     *
     * @return void
     */


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('front.components.selection');
    }
}
