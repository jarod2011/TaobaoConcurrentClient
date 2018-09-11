<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * tbk api : shop info
 * @link http://open.taobao.com/api.htm?source=search&docId=24521&docType=2
 */
class ShopInfoRequest extends TaobaoRequest
{
    private $sellerId;
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_shop_get_response']) && isset($json['tbk_shop_get_response']['results']) && isset($json['tbk_shop_get_response']['results']['n_tbk_shop'])) {
                if ($this->sellerId) {
                    foreach ($json['tbk_shop_get_response']['results']['n_tbk_shop'] as $v) {
                        if ($v['user_id'] == $this->sellerId) return $v;
                    }
                }
                return $json['tbk_shop_get_response']['results']['n_tbk_shop'];
            }
            throw new TaobaoResponseException($json);
        });
    }

    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\Taobao\TaobaoRequest::getMethod()
     */
    protected function getMethod(): string
    {
        return 'taobao.tbk.shop.get';
    }

    /**
     * build a seller search request
     * @param string $sellerNick
     * @param int $sellerId
     * @param array $fields
     * @return self
     */
    public static function buildRequest(string $sellerNick, int $sellerId  = 0, array $fields = ['user_id','shop_title','shop_type','seller_nick','pict_url','shop_url']): self
    {
        $req = new self();
        if (! in_array('user_id', $fields)) $fields[] = 'user_id';
        $data = [
            'fields' => implode(',', $fields),
            'q' => $sellerNick,
            'page_size' => 100
        ];
        $req->sellerId = $sellerId;
        return $req->_buildRequest($data, true);
    }
}
