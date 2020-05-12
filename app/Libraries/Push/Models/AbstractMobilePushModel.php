<?php

namespace LaravelSupports\Libraries\Push\Models;

use LaravelSupports\Models\Common\BaseModel;
use LaravelSupports\Libraries\Supports\Data\Traits\FileSaveTrait;
use Illuminate\Http\Request;

abstract class AbstractMobilePushModel extends BaseModel
{

    /**
     * String 타입의 json 값을 이용해서 serialize 에 관련된 database column 에 대입 합니다
     *
     * @param   String $json
     * @return  void
     * @author  WilsonParker
     * @added   2019-04-11
     * @updated 2019-04-11
     */
    abstract function setSerializeData(String $json);

    /**
     * Push 기능을 수행할 때 실행 합니다
     *
     * @return  void
     * @author  WilsonParker
     * @added   2019-04-11
     * @updated 2019-04-11
     */
    abstract function execute();

    /**
     * Database 에 저장할 Push 결과를 설정 합니다
     *
     * @param array $result
     * @return void
     * @author  WilsonParker
     * @added   2019-04-11
     * @updated 2019-04-11
     */
    public function setResult(array $result)
    {
        $this->result = "success : $result[success] / failure : $result[failure]";
    }

    /**
     * Database 에 저장하기 전에 json 으로 변환한 값을 설정 합니다
     *
     * @param   array $options
     * @return  bool
     * @author  WilsonParker
     * @added   2019-04-11
     * @updated 2019-04-11
     */
    public function save(array $options = []): bool
    {
        $this->setSerializeData(json_encode($this));
        return parent::save($options);
    }
}
