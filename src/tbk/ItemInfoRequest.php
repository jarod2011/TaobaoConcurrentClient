<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * tbk api : get item info
 * @link http://open.taobao.com/api.htm?docId=24518&docType=2
 */
class ItemInfoRequest extends TaobaoRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_item_info_get_response']) && isset($json['tbk_item_info_get_response']['results']) && isset($json['tbk_item_info_get_response']['results']['n_tbk_item'])) {
                if (count($json['tbk_item_info_get_response']['results']['n_tbk_item']) === 1) return $json['tbk_item_info_get_response']['results']['n_tbk_item'][0];
                $res = [];
                foreach ($json['tbk_item_info_get_response']['results']['n_tbk_item'] as $v) {
                    $res[$v['num_iid']] = $v;
                }
                return $res;
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
        return 'taobao.tbk.item.info.get';
    }

    /**
     * build a item info request
     * @param array|int $itemIds
     * @param array $fields
     * @return self
     */
    public static function buildRequest($itemIds, array $fields = ['num_iid','title','pict_url','small_images','reserve_price','zk_final_price','user_type','provcity','item_url','nick','seller_id','volume','cat_leaf_name','cat_name','category_id']): self
    {
        $req = new self();
        if (! in_array('num_iid', $fields)) $fields[] = 'num_iid';
        $data = [
            'num_iids' => is_array($itemIds) ? implode(',', $itemIds) : $itemIds,
            'fields' => implode(',', $fields)
        ];
        return $req->_buildRequest($data, true);
    }
}