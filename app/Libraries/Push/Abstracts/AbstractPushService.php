<?php


namespace LaravelSupports\Libraries\Push\Abstracts;


use LaravelSupports\Libraries\Push\Contracts\HasPushToken;
use LaravelSupports\Libraries\Push\Exceptions\HasPushTokenException;
use LaravelSupports\Libraries\Push\Objects\MobileResultObject;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

abstract class AbstractPushService
{
    const TYPE_ANDROID = 'android';
    const TYPE_IOS = 'ios';

    // FCM server key
    protected string $key;

    // FCM send url
    protected string $url = "https://fcm.googleapis.com/fcm/send";

    /**
     * Class that implements HasPushToken interface
     *
     * @var string
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    protected string $tokenModelClass = AbstractTokenModel::class;

    /**
     * Push 후 올바른 값이 아닌 token 을 제거할 지 여부 값 입니다
     *
     * @param
     * @return
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    protected bool $clearJunk = false;

    /**
     * token 을 chunk 해서 Push 를 보낼 갯수 입니다
     *
     * @param
     * @return
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    protected int $chunkSize = 1000;

    /**
     * fcm push for android
     *
     * @param array $tokens
     * @param $data
     * @return array
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    public function androidPush(array $tokens, $data)
    {
        return $this->chunkPush(self::TYPE_ANDROID, $tokens, $data);
    }

    /**
     * fcm push for ios
     *
     * @param array $tokens
     * @param $data
     * @return array
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    public function iosPush(array $tokens, $data)
    {
        return $this->chunkPush(self::TYPE_IOS, $tokens, $data);
    }

    /**
     * $chunkSize 만큼 token 을 나누어서 발송 합니다
     *
     * @param string $type
     * @param array $tokens
     * @param
     * @return array
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    protected function chunkPush(string $type, array $tokens, $data)
    {
        $chunkList = collect($tokens)->chunk($this->chunkSize);
        $resultList = [];
        foreach ($chunkList as $chunk) {
            array_push($resultList, $this->push($type, array_values($chunk->toArray()), $data));
        }
        return $resultList;
    }

    protected function push(string $type, array $tokens, $data)
    {
        $fcmData = [];
        if ($type == self::TYPE_ANDROID) {
            $fcmData = $this->buildAndroidData($data);
        } else if ($type == self::TYPE_IOS) {
            $fcmData = $this->buildIosData($data);
        }

        // Check if there is ore than one sender
        if (is_array($tokens) && count($tokens) > 1) {
            $sendBody = [
                'registration_ids' => $tokens
            ];
        } else {
            $sendBody = [
                'to' => $tokens[0]
            ];
        }
        $json = json_encode(
            array_merge(
                $fcmData,
                $sendBody,
                [
                    'priority' => 'high'
                ])
        );

        $client = new Client();
        $headers = [
            'Authorization' => 'key=' . $this->key,
            'Content-Type' => 'application/json'
        ];
        $response = $client->request('POST', $this->url, [
            'headers' => $headers,
            'body' => $json,
        ]);

        return $this->getResult($tokens, $response->getBody()->getContents());
    }

    /**
     * Give the push result as boolean
     *
     * @param array $tokens
     * @param string $result
     * @return MobileResultObject
     * @throws \Throwable
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    protected function getResult(array $tokens, string $result)
    {
        $model = new MobileResultObject();
        $model->bind($result);
        if ($this->clearJunk) {
            $junkList = self::setJunkTokens($model, $tokens);
            $tokenModel = new $this->tokenModelClass();
            throw_unless($tokenModel instanceof HasPushToken, HasPushTokenException::class);
            if (!empty($junkList)) {
                $tokenModel->whereIn($tokenModel->getTokenName(), $junkList)->delete();
            }
        }
        return $model;
    }

    /**
     * Incorrect tokens are provided as an array in the FCM push result
     *
     * FCM Push 결과에서 올바르지 않는 token 들을 array 로 제공합니다
     *
     * @param MobileResultObject $obj
     * @param   $tokens
     * junk token 으로 이루어진 array 입니다
     * @return array
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    protected function setJunkTokens(MobileResultObject $obj, $tokens): array
    {
        $result = [];
        $filterResults = Arr::where($obj->results, function ($value, $_) {
            return isset($value->error);
        });
        $filterResultKeys = array_keys($filterResults);
        foreach ($filterResultKeys as $idx) {
            array_push($result, $tokens[$idx]);
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function isClearJunk(): bool
    {
        return $this->clearJunk;
    }

    /**
     * @param bool $clearJunk
     */
    public function setClearJunk(bool $clearJunk): void
    {
        $this->clearJunk = $clearJunk;
    }

    /**
     * @return int
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    /**
     * @param int $chunkSize
     */
    public function setChunkSize(int $chunkSize): void
    {
        $this->chunkSize = $chunkSize;
    }

    /**
     * build fcm data for android
     *
     * @param
     * @return array
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    abstract protected function buildAndroidData($data): array;

    /**
     * build fcm data for ios
     *
     * @param
     * @return array
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    abstract protected function buildIosData($data): array;
}


