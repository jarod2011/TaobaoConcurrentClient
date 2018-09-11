<?php
namespace SimpleConcurrent\Taobao\WIRELESS;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * wireless api : taokouling decoce
 * @link http://open.taobao.com/api.htm?source=search&docId=32461&docType=2
 */
class TklDecodeRequest extends TaobaoRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['wireless_share_tpwd_query_response'])) return $json['wireless_share_tpwd_query_response'];
            throw new TaobaoResponseException($json);
        });
    }

    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\Taobao\TaobaoRequest::getMethod()
     */
    protected function getMethod(): string
    {
        return 'taobao.wireless.share.tpwd.query';
    }

    /**
     * build taokouling to url request
     * @param string $content
     * @return self
     */
    public static function buildRequest(string $content): self
    {
        $req = new self();
        $data = [
            'password_content' => $content
        ];
        return $req->_buildRequest($data, true);
    }
}