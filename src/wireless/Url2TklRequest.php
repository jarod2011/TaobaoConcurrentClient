<?php
namespace SimpleConcurrent\Taobao\WIRELESS;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * wireless api : url to taokouling
 * @link http://open.taobao.com/api.htm?spm=a219a.7386797.0.0.ecea2cbftl06Z3&source=search&docId=26520&docType=2
 */
class Url2TklRequest extends TaobaoRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['wireless_share_tpwd_create_response']) && isset($json['wireless_share_tpwd_create_response']['model'])) return $json['wireless_share_tpwd_create_response']['model'];
            throw new TaobaoResponseException($json);
        });
    }
    
    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\Taobao\TaobaoRequest::getMethod()
     */
    protected function getMethod(): string
    {
        return 'taobao.wireless.share.tpwd.create';
    }
    
    /**
     * build a url to toakouling request
     * @param string $url
     * @param string $title
     * @param string $pic
     * @param int $user_id
     * @param array $ext
     * @return self
     */
    public static function buildRequest(string $url, string $title, string $pic = '', int $user_id = 0, array $ext = []): self
    {
        $req = new self();
        $data = [
            'url' => $url,
            'text' => $title
        ];
        if ($pic) $data['logo'] = $pic;
        if ($user_id > 0) $data['user_id'] = $user_id;
        if ($ext) $data['ext'] = json_encode($ext);
        return $req->_buildRequest([
            'tpwd_param' => json_encode($data)
        ], true);
    }
}