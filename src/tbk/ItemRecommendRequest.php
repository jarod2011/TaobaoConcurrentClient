<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * tbk api : good recommend
 * @link http://open.taobao.com/api.htm?spm=a219a.7386797.0.0.ybRDkn&source=search&docId=24517&docType=2
 */
class ItemRecommendRequest extends TaobaoRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_item_recommend_get_response']) && isset($json['tbk_item_recommend_get_response']['results']) && isset($json['tbk_item_recommend_get_response']['results']['n_tbk_item'])) return $json['tbk_item_recommend_get_response']['results']['n_tbk_item'];
            throw new TaobaoResponseException($json);
        });
    }

    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\Taobao\TaobaoRequest::getMethod()
     */
    protected function getMethod(): string
    {
        return 'taobao.tbk.item.recommend.get';
    }

    /**
     *
     * @param int $itemId
     * @param int $count
     * @param int $platform
     * @param array $fields
     * @return self
     */
    public static function buildRequest(int $itemId, int $count = 40, int $platform = 2, array $fields = ['num_iid','title','pict_url','small_images','reserve_price','zk_final_price','user_type','provcity','item_url','nick','seller_id','volume','cat_leaf_name','cat_name','category_id']): self
    {
        $req = new self();
        $data = [
            'num_iid' => $itemId,
            'count' => $count,
            'platform' => $platform,
            'fields' => implode(',', $fields)
        ];
        return $req->_buildRequest($data, true);
    }
}