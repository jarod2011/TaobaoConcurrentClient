<?php
namespace SimpleConcurrent\Taobao\Trades;
use SimpleConcurrent\Taobao\TaobaoRequest;
use GuzzleHttp\Promise\PromiseInterface;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * api: taobao.trade.fullinfo.get
 * @link https://open.taobao.com/api.htm?docId=54&docType=2
 */
class FullinfoGetRequest extends TaobaoRequest
{
    /**
     * default fields
     * @var array
     */
    public static $defaultFields = [
        'seller_nick',
        'pic_path',
        'payment',
        'seller_rate',
        'post_fee',
        'receiver_name',
        'receiver_state',
        'receiver_address',
        'receiver_zip',
        'receiver_mobile',
        'receiver_phone',
        'consign_time',
        'received_payment',
        'promotion_details',
        'est_con_time',
        'receiver_country',
        'receiver_town',
        'order_tax_fee',
        'paid_coupon_fee',
        'shop_pick',
        'tid_str',
        'biz_code',
        'cloud_store',
        'new_presell',
        'you_xiang',
        'pay_channel',
        'tid',
        'num',
        'num_iid',
        'status',
        'title',
        'type',
        'price',
        'discount_fee',
        'has_post_fee',
        'total_fee',
        'created',
        'pay_time',
        'modified',
        'end_time',
        'buyer_message',
        'buyer_memo',
        'buyer_flag',
        'seller_memo',
        'seller_flag',
        'buyer_nick',
        'trade_attr',
        'credit_card_fee',
        'step_trade_status',
        'step_paid_fee',
        'mark_desc',
        'shipping_type',
        'buyer_cod_fee',
        'adjust_fee',
        'trade_from',
        'service_orders',
        'buyer_rate',
        'receiver_city',
        'receiver_district',
        'service_tags',
        'o2o',
        'o2o_guide_id',
        'o2o_shop_id',
        'o2o_guide_name',
        'o2o_shop_name',
        'o2o_delivery',
        'orders',
        'trade_ext',
        'eticket_service_addr',
        'rx_audit_status',
        'es_range',
        'es_date',
        'os_date',
        'os_range',
        'coupon_fee',
        'o2o_et_order_id',
        'post_gate_declare',
        'cross_bonded_declare',
        'omnichannel_param',
        'assembly',
        'top_hold',
        'omni_attr',
        'omni_param',
        'forbid_consign',
        'identity',
        'team_buy_hold',
        'share_group_hold',
        'ofp_hold',
        'o2o_step_trade_detail',
        'o2o_step_order_id',
        'o2o_voucher_price',
        'order_tax_promotion_fee',
        'delay_create_delivery',
        'toptype',
        'service_type',
        'o2o_service_mobile',
        'o2o_service_name',
        'o2o_service_state',
        'o2o_service_city',
        'o2o_service_district',
        'o2o_service_town',
        'o2o_service_address',
        'o2o_step_trade_detail_new',
        'o2o_xiaopiao',
        'o2o_contract',
        'retail_store_code',
        'retail_out_order_id',
        'recharge_fee',
        'platform_subsidy_fee',
        'nr_offline',
        'wtt_param',
        'logistics_infos',
        'nr_store_order_id',
        'nr_shop_id',
        'nr_shop_name',
        'nr_shop_guide_id',
        'nr_shop_guide_name',
        'sort_info',
        'sorted',
        'nr_no_handle',
        'buyer_open_uid',
        'is_gift',
        'donee_nick',
        'donee_open_uid',
        'suning_shop_code',
        'suning_shop_valid',
        'retail_store_id',
        'is_istore',
        'ua',
        'linkedmall_ext_info',
        'rt_omni_send_type',
        'rt_omni_store_id',
        'rt_omni_outer_store_id',
        'tcps_start',
        'tcps_code',
        'tcps_end',
        'is_sh_ship',
        'o2o_snatch_status',
        'market',
        'et_type',
        'et_shop_id',
        'obs',
    ];
    
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['trade_fullinfo_get_response']) && isset($json['trade_fullinfo_get_response']['trade'])) {
                return $json['trade_fullinfo_get_response']['trade'];
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
        return 'taobao.trade.fullinfo.get';
    }
    
    /**
     * build a request
     * @param mixed $tid taobao order id
     * @param string $session user session
     * @param array $fields want return fields
     * @return \SimpleConcurrent\Taobao\TaobaoRequest
     */
    public static function buildRequest($tid, string $session , array $fields = [])
    {
        if (empty($fields)) {
            $fields = self::$defaultFields;
        } else {
            $fields = array_intersect(self::$defaultFields, $fields);
        }
        $data = [
            'tid' => $tid,
            'session' => $session,
            'fields' => implode(',', $fields),
        ];
        $req = new self();
        return $req->_buildRequest($data, true);
    }
}