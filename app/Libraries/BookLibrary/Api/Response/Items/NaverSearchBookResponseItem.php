<?php


namespace LaravelSupports\Libraries\BookLibrary\Api\Response\Items;


use App\View\Items\Contracts\BookItemizable;
use LaravelSupports\Libraries\Supports\Objects\Traits\ReflectionTrait;

class NaverSearchBookResponseItem implements BookItemizable
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

    public function getBookModelData(): array
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

    public function getID(): int
    {
        return -1;
    }

    public function getBookID(): int
    {
        return -1;
    }

    public function getStockCount(): int
    {
        return 0;
    }

    public function getImageUrl(): string
    {
        return $this->image;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getPublisher(): string
    {
        return $this->publisher;
    }

    public function getISBN(): string
    {
        return $this->isbn13;
    }

    public function getPubDate(): string
    {
        return $this->pubdate;
    }
}
