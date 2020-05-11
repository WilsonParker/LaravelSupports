<?php


namespace App\LaravelSupports\Library\BookLibrary\API\Traits;


use Illuminate\Support\Str;

trait ValidateISBNContract
{
    protected $validators = [
        "blank" => [
            "valid" => "hasBlank",
            "validate" => "validateBlank",
        ]
    ];

    public function isValid($isbn)
    {
        foreach ($this->validators as $validator) {
            if (!$this->{$validator["valid"]}($isbn)) {
                return false;
            }
        }
        return true;
    }

    public function validate($isbn)
    {
        foreach ($this->validators as $validator) {
            $isbn = $this->{$validator["validate"]}($isbn);
        }
        return $isbn;
    }

    public function hasBlank($isbn)
    {
        return Str::contains($isbn, " ");
    }

    public function validateBlank($isbn)
    {
        if ($this->hasBlank($isbn)) {
            $isbnList = explode(" ", $isbn);
            return $isbnList[sizeof($isbnList) - 1];
        }
        return $isbn;
    }
}
