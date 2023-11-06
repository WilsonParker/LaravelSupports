<?php


namespace LaravelSupports\Supports\Data;


use Illuminate\Support\Str;

trait ConvertMarkMessageTrait
{

    public static function staticReplaceMessage($message, $value, $mark = '?')
    {
        return str_replace('?', $value, $message);
    }

    public function replaceMessage($message, $value, $mark = '?')
    {
        return str_replace('?', $value, $message);
    }

    public function replaceMessageArray($message, $replaceArray = [], $mark = '?')
    {
        return Str::replaceArray('?', $replaceArray, $message);
    }

    public function replaceStrArray($message, $searchAndReplace = [])
    {
        foreach ($searchAndReplace as $search => $replace) {
            $message = str_replace($search, $replace, $message);
        }
        return $message;
    }
}
