<?php

namespace App\Library\LaravelSupports\app\ViewModels;


class BasicViewModel extends BaseViewModel
{

    public function __construct($data = null, $searchData = [])
    {
        $this->data = $data;
        $this->searchData = $searchData;
        $this->load();
    }

}
