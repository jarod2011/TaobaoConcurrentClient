<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * tbk api : url to taokoulong
 * @link http://open.taobao.com/api.htm?source=search&docId=31127&docType=2
 */
class Url2TklRequest extends TaobaoRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_tpwd_create_response']) && isset($json['tbk_tpwd_create_response']['data']) && isset($json['tbk_tpwd_create_response']['data']['model'])) return $json['tbk_tpwd_create_response']['data']['model'];
            throw new TaobaoResponseException($json);
        });
    }

    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\Taobao\TaobaoRequest::getMethod()
     */
    protected function getMethod(): string
    {
        return 'taobao.tbk.tpwd.create';
    }

    /**
     * build a url to taokouling request
     * @param string $url
     * @param string $title
     * @param string $pic
     * @param int $userId
     * @return self
     */
    public static function buildRequest(string $url, string $title, string $pic = '', int $userId = 0): self
    {
        $req = new self();
        $data = [
            'url' => $url,
            'text' => $title
        ];
        if ($pic) $data['logo'] = $pic;
        if ($userId) $data['user_id'] = $userId;
        return $req->_buildRequest($data, true);
    }
}