<?php


namespace LaravelSupports\Metrics;


use Illuminate\Support\Str;

class AddressService
{
    const PREG = '^([가-힣]+(시|도))( |)([가-힣]+(시|군|구))(( |)([가-힣]+(시|군|구)))?^';
    const PREG_SIDO = '([가-힣]+(시|도))';
    const PREG_SIGU = self::PREG_SIDO . '( |)([가-힣]+(시|군|구))';
    const PREG_GUGUN = self::PREG_SIGU . '( |)([가-힣]+(시|군|구))( )';

    protected array $addressList = [
        '서울' => '서울특별시',
        '경기' => '경기도',
        '강원' => '강원도',
        '충남' => '충청남도',
        '충북' => '충청북도',
        '경남' => '경상남도',
        '경북' => '경상북도',
        '전남' => '전라남도',
        '전북' => '전라북도',
        '제주' => '제주특별자치도',
        '인천' => '인천광역시',
        '부산' => '부산광역시',
        '대구' => '대구광역시',
        // '광주' => '광주광역시',
        '대전' => '대전광역시',
        '울산' => '울산광역시',
    ];

    public function getSiDo(string $address, bool $needConvert = true)
    {
        return $this->match($address, self::PREG_SIDO, $needConvert)[0] ?? '';
    }

    public function match(string $address, string $reg, bool $needConvert = true)
    {
        $addr = $needConvert ? $this->replaceAddress($address) : $address;
        preg_match($this->buildReg($reg), $addr, $match);
        return $match;
    }

    public function replaceAddress(string $address): string
    {
        $replacedAddress = $address;
        foreach ($this->addressList as $key => $value) {
            $str = Str::of($replacedAddress);
            if (!$str->contains($value) && $str->contains($key)) {
                $replacedAddress = $str->replace($key, $value);
            }
        }
        return $replacedAddress;
    }

    private function buildReg(string $reg)
    {
        return "^$reg^";
    }

    public function getSiGu(string $address, bool $needConvert = true)
    {
        return explode(' ', $this->match($address, self::PREG_SIGU, $needConvert)[0] ?? '')[1] ?? '';
    }

    public function getGuGun(string $address, bool $needConvert = true)
    {
        return explode(' ', $this->match($address, self::PREG_GUGUN, $needConvert)[0] ?? '')[2] ?? '';
    }


}
