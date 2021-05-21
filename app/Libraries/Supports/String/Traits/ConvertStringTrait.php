<?php


namespace LaravelSupports\Libraries\Supports\String\Traits;

trait ConvertStringTrait
{
    /**
     * 말줄임
     *
     * @param string $string
     * @param int $length
     * @return string
     * @author  seul
     * @added   2020-09-01
     * @updated 2020-09-01
     */
    public function shortenString(string $string, int $length = 10) : string
    {
        return mb_strlen($string) > $length ? mb_substr($string, 0, $length) . "..." : $string;
    }

    /**
     * 책 제목 줄임
     *
     * @param $books
     * @param int $length
     * @return string
     * @author  seul
     * @added   2021-03-24
     * @updated 2020-03-24
     */
    public function convertBookTitles($books, int $length = 10) : string
    {
        if (isset($books->title)) {
            $bookTitle = $this->shortenString($books->title, $length);
        } else {
            $bookCount = count($books) - 1;

            if ($bookCount) {
                $bookTitle = $this->shortenString($books->first()->title, $length);
                $bookTitle .= " 외 {$bookCount}권";
            }
        }

        return $bookTitle;
    }

    /**
     * 숫자에서 금액으로 변경
     *
     * @param $number
     * @param string $currency
     * @return string
     * @author  seul
     * @added   2021-03-24
     * @updated 2020-03-24
     */
    public function convertCurrency($number, $currency = '원') : string
    {
        $converted = '';
        switch ($currency) {
            case '원':
                return number_format($number).$currency;
                break;
        }

        return $converted;
    }
}
