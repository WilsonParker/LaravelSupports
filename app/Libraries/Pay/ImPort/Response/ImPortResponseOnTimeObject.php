<?php


namespace LaravelSupports\Libraries\Pay\ImPort\Response;


class ImPortResponseOnTimeObject extends ImPortResponse
{
    /**
     * @return mixed
     */
    public function getCardName()
    {
        return $this->card_name;
    }

}
