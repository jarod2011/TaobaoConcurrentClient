<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\Taobao\TaobaoResponseException;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * tbk api : tbk sc order get
 * @link http://open.taobao.com/api.htm?docId=38078&docType=2&scopeId=14814
 */
class ScOrderGetRequest extends TaobaoRequest
{
    
    private $requestFileds = [];
    private $startAt;
    private $timeSpan = 60;
    private $orderType;
    private $orderScene;
    private $orderCountType;
    private $pageNo;
    private $pageSize;
    private $orderStatus;
    private $session;
    
    const ORDER_QUERY_TYPE_CREATE = 1;
    const ORDER_QUERY_TYPE_SETTLE = 2;
    
    const ORDER_SCENE_NORMAL = 1;
    const ORDER_SCENE_DISTRIBUTION = 2;
    const ORDER_SCENE_MEMBER = 3;
    
    const ORDER_COUNT_TYPE_MUTUAL = 1;
    const ORDER_COUNT_TYPE_TRIPARTITE = 2;
    
    const ORDER_STATUS_ALL = 1;
    const ORDER_STATUS_FINISH = 3;
    const ORDER_STATUS_PAYED = 12;
    const ORDER_STATUS_EXPIRED = 13;
    const ORDER_STATUS_SUCCESS = 14;
    
    public static $defaultOrderScene = self::ORDER_SCENE_NORMAL;
    public static $defaultOrderType = self::ORDER_QUERY_TYPE_CREATE;
    public static $defaultOrderCountType = self::ORDER_COUNT_TYPE_MUTUAL;
    public static $defaultOrderStatus = self::ORDER_STATUS_ALL;
    public static $defaultTimeSpan = 60;
    public static $defaultPageSize = 20;
    public static $defaultSession = '';
    
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_sc_order_get_response']) && isset($json['tbk_sc_order_get_response']['results'])) return $json['tbk_sc_order_get_response']['results']['n_tbk_order'] ?? $json['tbk_sc_order_get_response']['results'];
            throw new TaobaoResponseException($json);
        });
        $this->whereOrderScene(self::$defaultOrderScene);
        $this->whereOrderType(self::$defaultOrderType);
        $this->whereOrderStatus(self::$defaultOrderStatus);
        $this->whereOrderCountType(self::$defaultOrderCountType);
        $this->withTimeSpan(self::$defaultTimeSpan);
        $this->pageNow(1);
        $this->pageSize(self::$defaultPageSize);
    }
    
    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\Taobao\TaobaoRequest::getMethod()
     */
    protected function getMethod(): string
    {
        return 'taobao.tbk.sc.order.get';
    }
    
    public function addField($fieldName): self
    {
        if (! in_array($fieldName, $this->requestFileds)) $this->requestFileds[] = $fieldName;
        return $this;
    }
    
    public function clearField(): self
    {
        $this->requestFileds = [];
        return $this;
    }
    
    public function defaultFields(): self
    {
        $defaultFields = ['tb_trade_parent_id', 'tb_trade_id', 'num_iid', 'item_title', 'item_num', 'price', 'pay_price', 'seller_nick', 'seller_shop_title', 'commission', 'commission_rate', 'unid', 'create_time', 'earning_time', 'tk3rd_pub_id', 'tk3rd_site_id', 'tk3rd_adzone_id', 'relation_id', 'tb_trade_parent_id', 'tb_trade_id', 'num_iid', 'item_title', 'item_num', 'price', 'pay_price', 'seller_nick', 'seller_shop_title', 'commission', 'commission_rate', 'unid', 'create_time', 'earning_time', 'tk3rd_pub_id', 'tk3rd_site_id', 'tk3rd_adzone_id', 'special_id'];
        foreach ($defaultFields as $field) {
            $this->addField($field);
        }
        return $this;
    }
    
    public static function whereStartAt(string $startAt): self
    {
        $req = new self();
        $req->startAt = $startAt;
        return $req;
    }
    
    public function withSession(string $session): self
    {
        $this->session = $session;
        return $this;
    }
    
    public function withTimeSpan(int $timeSpan): self
    {
        $this->timeSpan = min(max($timeSpan, 60), 1200);
        return $this;
    }
    
    public function whereOrderType(int $orderType): self
    {
        if (in_array($orderType, [self::ORDER_QUERY_TYPE_CREATE, self::ORDER_QUERY_TYPE_SETTLE])) {
            $this->orderType = $orderType;
        }
        return $this;
    }
    
    public function whereOrderScene(int $orderScene): self
    {
        if (in_array($orderScene, [self::ORDER_SCENE_NORMAL, self::ORDER_SCENE_DISTRIBUTION, self::ORDER_SCENE_MEMBER])) {
            $this->orderScene = $orderScene;
        }
        return $this;
    }
    
    public function whereOrderStatus(int $orderStatus): self
    {
        if (in_array($orderStatus, [self::ORDER_STATUS_ALL, self::ORDER_STATUS_EXPIRED, self::ORDER_STATUS_FINISH, self::ORDER_STATUS_PAYED, self::ORDER_STATUS_SUCCESS])) {
            $this->orderStatus = $orderStatus;
        }
        return $this;
    }
    
    public function whereOrderCountType(int $orderCountType): self
    {
        if (in_array($orderCountType, [self::ORDER_COUNT_TYPE_MUTUAL, self::ORDER_COUNT_TYPE_TRIPARTITE])) {
            $this->orderCountType = $orderCountType;
        }
        return $this;
    }
    
    public function pageNow(int $nowpage = 1): self
    {
        $this->pageNo = max(1, $nowpage);
        return $this;
    }
    
    public function pageSize(int $pageSize): self
    {
        $this->pageSize = min(100, max($pageSize, 1));
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\SimpleRequest::getPromise()
     */
    public function getPromise(): PromiseInterface
    {
        if (! $this->requestData) {
            if (! $this->requestFileds) $this->defaultFields();
            if (! in_array('trade_id', $this->requestFileds)) $this->addField('trade_id');
            if (! in_array('trade_parent_id', $this->requestFileds)) $this->addField('trade_parent_id');
            if (! in_array('num_iid', $this->requestFileds)) $this->addField('num_iid');
            $condition = [
                'fields' => implode(',', $this->requestFileds),
                'start_time' => $this->startAt,
                'order_scene' => $this->orderScene,
                'order_count_type' => $this->orderCountType,
                'page_no' => $this->pageNo,
                'page_size' => $this->pageSize,
                'tk_status' => $this->orderStatus
            ];
            if ($this->orderScene === self::ORDER_SCENE_NORMAL || $this->orderCountType === self::ORDER_COUNT_TYPE_TRIPARTITE) {
                $condition['span'] = min(1200, max(60, $this->timeSpan));
            }
            switch ($this->orderType) {
                case self::ORDER_QUERY_TYPE_CREATE:
                    $condition['order_query_type'] = 'create_time';
                    break;
                case self::ORDER_QUERY_TYPE_SETTLE:
                    $condition['order_query_type'] = 'settle_time';
                    break;
            }
            $condition['session'] = $this->session ? $this->session : self::$defaultSession;
            $this->_buildRequest($condition, true);
        }
        return parent::getPromise();
    }
}