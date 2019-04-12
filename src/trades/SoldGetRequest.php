<?php
namespace SimpleConcurrent\Taobao\Trades;
use SimpleConcurrent\Taobao\TaobaoRequest;
use GuzzleHttp\Promise\PromiseInterface;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * trades api: taobao.trades.sold.get
 * @link https://open.taobao.com/api.htm?docId=46&docType=2
 */
class SoldGetRequest extends TaobaoRequest
{
    
    /**
     * all fields
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
        'receiver_country',
        'receiver_town',
        'order_tax_fee',
        'shop_pick',
        'tid',
        'num',
        'num_iid',
        'status',
        'title',
        'type',
        'price',
        'discount_fee',
        'total_fee',
        'created',
        'pay_time',
        'modified',
        'end_time',
        'seller_flag',
        'buyer_nick',
        'has_buyer_message',
        'credit_card_fee',
        'step_trade_status',
        'step_paid_fee',
        'mark_desc',
        'shipping_type',
        'adjust_fee',
        'trade_from',
        'service_orders',
        'buyer_rate',
        'receiver_city',
        'receiver_district',
        'o2o',
        'o2o_guide_id',
        'o2o_shop_id',
        'o2o_guide_name',
        'o2o_shop_name',
        'o2o_delivery',
        'orders',
        'rx_audit_status',
        'es_range',
        'es_date',
        'os_date',
        'os_range',
        'post_gate_declare',
        'cross_bonded_declare',
        'order_tax_promotion_fee',
        'service_type',
        'threepl_timing',
        'is_o2o_passport',
        'tmall_delivery',
        'cn_service',
        'cutoff_minutes',
        'es_time',
        'delivery_time',
        'collect_time',
        'dispatch_time',
        'sign_time',
        'delivery_cps'
    ];
    
    /**
     * select fields
     * @var array
     */
    private $fields;
    
    /**
     * api arguments
     * @var array
     */
    private $arguments;

    /* all order status define in https://open.taobao.com/doc.htm?docId=102856&docType=1 */
    const ORDER_STATUS_WAIT_BUYER_PAY = 'WAIT_BUYER_PAY';
    const ORDER_STATUS_WAIT_SELLER_SEND_GOODS = 'WAIT_SELLER_SEND_GOODS';
    const ORDER_STATUS_SELLER_CONSIGNED_PART = 'SELLER_CONSIGNED_PART';
    const ORDER_STATUS_WAIT_BUYER_CONFIRM_GOODS = 'WAIT_BUYER_CONFIRM_GOODS';
    const ORDER_STATUS_TRADE_BUYER_SIGNED = 'TRADE_BUYER_SIGNED';
    const ORDER_STATUS_TRADE_FINISHED = 'TRADE_FINISHED';
    const ORDER_STATUS_TRADE_CLOSED = 'TRADE_CLOSED';
    const ORDER_STATUS_TRADE_CLOSED_BY_TAOBAO = 'TRADE_CLOSED_BY_TAOBAO';
    const ORDER_STATUS_TRADE_NO_CREATE_PAY = 'TRADE_NO_CREATE_PAY';
    const ORDER_STATUS_WAIT_PRE_AUTH_CONFIRM = 'WAIT_PRE_AUTH_CONFIRM';
    const ORDER_STATUS_PAY_PENDING = 'PAY_PENDING';
    const ORDER_STATUS_ALL_WAIT_PAY = 'ALL_WAIT_PAY';
    const ORDER_STATUS_ALL_CLOSED = 'ALL_CLOSED';
    const ORDER_STATUS_PAID_FORBID_CONSIGN = 'PAID_FORBID_CONSIGN';
    
    /* all trade types, use can see in doc */
    const TRADE_TYPE_FIXED = 'fixed';
    const TRADE_TYPE_AUCTION = 'auction';
    const TRADE_TYPE_GUARANTEE_TRADE = 'guarantee_trade';
    const TRADE_TYPE_SETP = 'step';
    const TRADE_TYPE_INDEPENDENT_SIMPLE = 'independent_simple_trade';
    const TRADE_TYPE_INDEPENDENT_SHOP = 'independent_shop_trade';
    const TRADE_TYPE_AUTH_DELIVERY = 'auto_delivery';
    const TRADE_TYPE_EC = 'ec';
    const TRADE_TYPE_COD = 'cod';
    const TRADE_TYPE_GAME_EQUIPMENT = 'game_equipment';
    const TRADE_TYPE_SHOPEX = 'shopex_trade';
    const TRADE_TYPE_NETCN = 'netcn_trade';
    const TRADE_TYPE_EXTERNAL = 'external_trade';
    const TRADE_TYPE_INSTANT = 'instant_trade';
    const TRADE_TYPE_B2C_COD = 'b2c_cod';
    const TRADE_TYPE_HOTEL = 'hotel_trade';
    const TRADE_TYPE_SUPER_MARKET = 'super_market_trade';
    const TRADE_TYPE_SUPER_MARKET_COD = 'super_market_cod_trade';
    const TRADE_TYPE_TAOHUA = 'taohua';
    const TRADE_TYPE_WAIMAI = 'waimai';
    const TRADE_TYPE_O2O_OFFLINETRADE = 'o2o_offlinetrade';
    const TRADE_TYPE_NOPAID = 'nopaid';
    const TRADE_TYPE_ETICKET = 'eticket';
    const TRADE_TYPE_TMALL_I18N = 'tmall_i18n';
    const TRADE_TYPE_INSURANCE_PLUS = 'insurance_plus';
    const TRADE_TYPE_FINANCE = 'finance';
    const TRADE_TYPE_PRE_AUTH = 'pre_auth_type';
    const TRADE_TYPE_LAZADA = 'lazada';

    /* all ext trade type you can see in doc */
    const TRADE_EXT_TYPE_ERSHOU = 'ershou';
    const TRADE_EXT_TYPE_SERVICE = 'service';
    const TRADE_EXT_TYPE_MARK = 'mark';

    /* all rate status you can see in doc */
    const RATE_STATUS_RATE_UNBUYER = 'RATE_UNBUYER';
    const RATE_STATUS_RATE_UNSELLER = 'RATE_UNSELLER';
    const RATE_STATUS_RATE_BUYER_UNSELLER = 'RATE_BUYER_UNSELLER';
    const RATE_STATUS_RATE_UNBUYER_SELLER = 'RATE_UNBUYER_SELLER';
    const RATE_STATUS_RATE_BUYER_SELLER = 'RATE_BUYER_SELLER';

    /* all tags you can see in doc */
    const TAG_TIME_CARD = 'time_card';
    const TAG_FEE_CARD = 'fee_card';
    
    public function __construct()
    {
        parent::__construct();
        $this->take()->nowpage(1);
        $this->addSuccessCallback(function ($json) {
            if (isset($json['trades_sold_get_response'])) {
                $res = [
                    'nowpage' => $this->requestData['page_no'] ?? 1,
                    'perpage' => $this->requestData['page_size'] ?? $this->arguments['page_size'],
                    'total' => $json['trades_sold_get_response']['total_results'],
                    'list' => [],
                ];
                $res['maxpage'] = ceil($res['total'] / $res['perpage']);
                if(isset($json['trades_sold_get_response']['trades']) && isset($json['trades_sold_get_response']['trades']['trade'])) $res['list'] = $json['trades_sold_get_response']['trades']['trade'];
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
        return 'taobao.trades.sold.get';
    }
    
    /**
     * set use fields need, default will get all fileds
     * filed can see it in doc
     * @param array $fields
     * @return self
     */
    public function fields(array $fields = []): self
    {
        if (empty($fields)) $fields = self::$defaultFields;
        $this->fields = $fields;
        return $this;
    }

    /**
     * set use session
     * @param string $session
     * @return self
     */
    public static function session(string $session): self
    {
        $req = new self();
        $req->arguments['session'] = $session;
        return $req;
    }
    
    /**
     * set start create time filter
     * @param string $startAt
     * @return self
     */
    public function whereStartAt(string $startAt): self
    {
        $this->arguments['start_created'] = $startAt;
        return $this;
    }
    
    /**
     * set end create time filter
     * @param string $endAt
     * @return self
     */
    public function whereEndAt(string $endAt): self
    {
        $this->arguments['end_created'] = $endAt;
        return $this;
    }
    
    /**
     * only order status filter
     * @param string $status
     * @throws \Exception
     * @return self
     */
    public function onlyStatus(string $status): self
    {
        if (! in_array($status, [
            self::ORDER_STATUS_ALL_CLOSED,
            self::ORDER_STATUS_ALL_WAIT_PAY,
            self::ORDER_STATUS_PAY_PENDING,
            self::ORDER_STATUS_SELLER_CONSIGNED_PART,
            self::ORDER_STATUS_TRADE_BUYER_SIGNED,
            self::ORDER_STATUS_TRADE_CLOSED,
            self::ORDER_STATUS_TRADE_CLOSED_BY_TAOBAO,
            self::ORDER_STATUS_TRADE_FINISHED,
            self::ORDER_STATUS_TRADE_NO_CREATE_PAY,
            self::ORDER_STATUS_WAIT_BUYER_CONFIRM_GOODS,
            self::ORDER_STATUS_WAIT_BUYER_PAY,
            self::ORDER_STATUS_WAIT_PRE_AUTH_CONFIRM,
            self::ORDER_STATUS_WAIT_SELLER_SEND_GOODS,
            self::ORDER_STATUS_PAID_FORBID_CONSIGN,
        ])) throw new \Exception('status not support.');
        $this->arguments['status'] = $status;
        return $this;
    }
    
    public function whereBuyer($buyerName): self
    {
        $this->arguments['buyer_nick'] = $buyerName;
        return $this;
    }
    
    /**
     * set trade type filter
     * @param string ...$tradeType
     * @return self
     */
    public function whereTradeType(string ...$tradeType): self
    {
        $trades = [];
        foreach ($tradeType as $trade) {
            if (in_array($trade, [
                self::TRADE_TYPE_FIXED,
                self::TRADE_TYPE_AUCTION,
                self::TRADE_TYPE_GUARANTEE_TRADE,
                self::TRADE_TYPE_SETP,
                self::TRADE_TYPE_INDEPENDENT_SIMPLE,
                self::TRADE_TYPE_INDEPENDENT_SHOP,
                self::TRADE_TYPE_AUTH_DELIVERY,
                self::TRADE_TYPE_EC,
                self::TRADE_TYPE_COD,
                self::TRADE_TYPE_GAME_EQUIPMENT,
                self::TRADE_TYPE_SHOPEX,
                self::TRADE_TYPE_NETCN,
                self::TRADE_TYPE_EXTERNAL,
                self::TRADE_TYPE_INSTANT,
                self::TRADE_TYPE_B2C_COD,
                self::TRADE_TYPE_HOTEL,
                self::TRADE_TYPE_SUPER_MARKET,
                self::TRADE_TYPE_SUPER_MARKET_COD,
                self::TRADE_TYPE_TAOHUA,
                self::TRADE_TYPE_WAIMAI,
                self::TRADE_TYPE_O2O_OFFLINETRADE,
                self::TRADE_TYPE_NOPAID,
                self::TRADE_TYPE_ETICKET,
                self::TRADE_TYPE_TMALL_I18N,
                self::TRADE_TYPE_INSURANCE_PLUS,
                self::TRADE_TYPE_FINANCE,
                self::TRADE_TYPE_PRE_AUTH,
                self::TRADE_TYPE_LAZADA,
            ])) {
                $trades[] = $trade;
            }
        }
        if (! empty($trades)) {
            $this->arguments['status'] = implode(',', $trades);
        }
        return $this;
    }

    /**
     * set trade ext type filter
     * @param string $extType
     * @throws \Exception
     * @return self
     */
    public function onlyTradeExtType(string $extType): self
    {
        if (! in_array($extType, [
            self::TRADE_EXT_TYPE_ERSHOU,
            self::TRADE_EXT_TYPE_MARK,
            self::TRADE_EXT_TYPE_SERVICE,
        ])) throw new \Exception('ext type not support');
        $this->arguments['ext_type'] = $extType;
        return $this;
    }

    /**
     * set rate status filter
     * @param string $status
     * @throws \Exception
     * @return self
     */
    public function onlyRateStatus(string $status): self
    {
        if (in_array($status, [
            self::RATE_STATUS_RATE_BUYER_SELLER,
            self::RATE_STATUS_RATE_BUYER_UNSELLER,
            self::RATE_STATUS_RATE_UNBUYER,
            self::RATE_STATUS_RATE_UNBUYER_SELLER,
            self::RATE_STATUS_RATE_UNSELLER,
        ])) throw new \Exception('rate statu not support');
        $this->arguments['rate_status'] = $status;
        return $this;
    }

    /**
     * set tag filter
     * @param string $tag
     * @throws \Exception
     * @return self
     */
    public function onlyTag(string $tag): self
    {
        if (in_array($tag, [
            self::TAG_FEE_CARD,
            self::TAG_TIME_CARD,
        ])) throw new \Exception('tag not support');
        $this->arguments['tag'] = $tag;
        return $this;
    }

    /**
     * whether open hasnext option , see it detail in doc
     * @param bool $isOpen
     * @return self
     */
    public function useHasNext(bool $isOpen = false): self
    {
        $this->arguments['use_has_next'] = $isOpen ? 'true' : 'false';
        return $this;
    }

    /**
     * set now page number
     * @param int $page
     * @return self
     */
    public function nowpage(int $page = 1): self
    {
        $this->arguments['page_no'] = max($page, 1);
        return $this;
    }

    /**
     * set perpage page size
     * @param int $perpage
     * @return self
     */
    public function take(int $perpage = 40): self
    {
        $this->arguments['page_size'] = max($perpage, 1);
        return $this;
    }
    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\SimpleRequest::getPromise()
     */
    public function getPromise(): PromiseInterface
    {
        if (! $this->requestData) {
            if (! $this->fields) $this->fields();
            $this->arguments['fields'] = implode(',', $this->fields);
            $this->_buildRequest($this->arguments, true);
        }
        return parent::getPromise();
    }

}

