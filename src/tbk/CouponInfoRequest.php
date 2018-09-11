<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * tbk api : get coupon info
 * @link http://open.taobao.com/api.htm?docId=31106&docType=2
 */
class CouponInfoRequest extends TaobaoRequest
{

    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_coupon_get_response']) && isset($json['tbk_coupon_get_response']['data'])) return $json['tbk_coupon_get_response']['data'];
            throw new TaobaoResponseException($json);
        });
    }

    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\Taobao\TaobaoRequest::getMethod()
     */
    protected function getMethod(): string
    {
        return 'taobao.tbk.coupon.get';
    }

    /**
     * build a coupon info request
     * @param int $itemId
     * @param string $activityId
     * @param string $me
     * @return self
     */
    public static function buildRequest(int $itemId, string $activityId = '', string $me = ''): self
    {
        $req = new self();
        if ($me) {
            $data['me'] = $me;
        } else {
            $data = [
                'item_id' => $itemId,
                'activity_id' => $activityId
            ];
        }
        return $req->_buildRequest($data, true);
    }
}
