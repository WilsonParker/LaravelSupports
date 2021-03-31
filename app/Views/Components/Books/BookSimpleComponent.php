<?php

namespace LaravelSupports\Views\Components\Books;


use FlyBookModels\Books\BookModel;
use LaravelSupports\Views\Components\BaseComponent;
use phpDocumentor\Reflection\Types\Collection;

class BookSimpleComponent extends BaseComponent
{
    protected string $view = 'book.book_simple_component';

    public BookModel $book;

    /**
     * Create a new component instance.
     *
     * @param BookModel $book
     */
    public function __construct(BookModel $book)
    {
        $this->book = $book;
    }
}
