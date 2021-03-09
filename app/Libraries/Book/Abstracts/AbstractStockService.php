<?php


namespace App\Services\Book\Abstracts;


use App\Book;

abstract class AbstractStockService
{
    protected $book;

    /**
     * AbstractStockService constructor.
     * @param Book $book
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    abstract public function getBookStock();

    abstract public function hasStock();

    abstract public function findStock();
}