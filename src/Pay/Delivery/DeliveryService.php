<?php


namespace LaravelSupports\Pay\Delivery;


use FlyBookModels\Delivery\DeliveryAreaModel;

class DeliveryService
{
    const REGULAR_EXPRESSION = "/([\S]+\s[\S]+(구|군)\s[\S]+(면|동))/";
    const CONVERT_ADDRESS = [
        "인천광역시" => "인천",
        "충청남도" => "충남",
        "경상북도" => "경북",
        "경상남도" => "경남",
        "전라남도" => "전남",
        "전라북도" => "전북",
    ];

    /**
     * 배송비 정보를 제공 합니다
     *
     * @param
     * @return int
     * @author  WilsonParker
     * @added   2020/06/22
     * @updated 2020/06/22
     */
    public function getDeliveryCost($address): int
    {
        try {
            $convertedAddress = $this->convertAddressForDelivery($address);
            $model = DeliveryAreaModel::where('area', 'like', $convertedAddress)->firstOrFail();
            return $model->cost;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * 배송비 정보를 얻기 위한 주소로 변환 합니다
     *
     * @param
     * @return mixed
     * @author  WilsonParker
     * @added   2020/06/22
     * @updated 2020/06/22
     * use pref_match_all
     * @updated 2020/06/23
     */
    public function convertAddressForDelivery($address)
    {
        return collect(array_keys(self::CONVERT_ADDRESS))->filter(function ($item) use ($address) {
            return str_contains($address, $item);
        })->pipe(function ($collection) use ($address) {
            if ($collection->isNotEmpty()) {
                $convertedAddress = str_replace($collection->first(), self::CONVERT_ADDRESS[$collection->first()], $address);
            } else {
                $convertedAddress = $address;
            }
            preg_match_all(self::REGULAR_EXPRESSION, $convertedAddress, $matches);
            return $matches[0];
        });
    }
}
