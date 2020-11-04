<?php


namespace LaravelSupports\Libraries\BookLibrary\Api\Response\Items;


use LaravelSupports\Libraries\Supports\Objects\Traits\ReflectionTrait;

class NaverSearchBookResponseItem
{
    use ReflectionTrait;

    public string $title = '';
    public string $link = '';
    public string $image = '';
    public string $author = '';
    public string $price = '';
    public string $discount = '';
    public string $publisher = '';
    public string $pubdate = '';
    public string $description = '';
    public string $isbn = '';
    public string $isbn10 = '';
    public string $isbn13 = '';

    public function afterBind()
    {
        foreach (explode(' ', $this->isbn) as $isbn) {
            $this->setISBN($isbn);
        }
    }

    public function setISBN($isbn)
    {
        if (strlen($isbn) == 10) {
            $this->isbn10 = $isbn;
        } else if (strlen($isbn) == 13) {
            $this->isbn13 = $isbn;
        }
    }

    public function getBookModelData()
    {
        return [
            'isbn' => '',
            'isbn10' => $this->isbn10,
            'isbn13' => $this->isbn13,
            'title' => $this->isbn13,
            'publisher' => $this->isbn13,
            'author' => $this->isbn13,
            'book_img' => $this->isbn13,
        ];
    }
}
