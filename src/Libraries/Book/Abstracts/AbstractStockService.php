<?php


namespace LaravelSupports\Libraries\Book\Abstracts;


use FlyBookModels\Books\BookModel;

abstract class AbstractStockService
{
    protected $book;

    /**
     * AbstractStockService constructor.
     * @param BookModel $book
     */
    public function __construct(BookModel $book)
    {
        $this->book = $book;
    }

    abstract public function getBookStock();

    abstract public function hasStock();

    abstract public function findStock();
}
