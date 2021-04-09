<?php

namespace LaravelSupports\Views\Components\Books;


use FlyBookModels\Books\BookModel;
use LaravelSupports\Views\Components\BaseComponent;
use phpDocumentor\Reflection\Types\Collection;

class BookSimpleComponent extends BaseComponent
{
    protected string $view = 'book.book_simple_component';

    public BookModel $book;
    public bool $hasInput;
    public string $nameBookID;

    /**
     * Create a new component instance.
     *
     * @param BookModel $book
     * @param bool $hasInput
     * @param string $nameBookID
     */
    public function __construct(BookModel $book, bool $hasInput = false, string $nameBookID = 'ref_book_id')
    {
        $this->book = $book;
        $this->hasInput = $hasInput;
        $this->nameBookID = $nameBookID;
    }
}
