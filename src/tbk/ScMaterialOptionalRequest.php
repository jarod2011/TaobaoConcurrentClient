<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use GuzzleHttp\Promise\PromiseInterface;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * api: taobao.tbk.sc.material.optional
 * @link http://open.taobao.com/api.htm?docId=35263&docType=2
 */
class ScMaterialOptionalRequest extends TaobaoRequest
{
    protected static $session;
    protected static $adzoneId;
    protected static $siteId;
    
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_sc_material_optional_response'])) {
                $res = [
                    'nowpage' => $this->requestData['page_no'] ?? 1,
                    'perpage' => $this->requestData['page_size'] ?? $this->condition['page_size'],
                    'total' => $json['tbk_sc_material_optional_response']['total_results'],
                    'list' => []
                ];
                $res['maxpage'] = ceil($res['total'] / $res['perpage']);
                if(isset($json['tbk_sc_material_optional_response']['result_list']) && isset($json['tbk_sc_material_optional_response']['result_list']['map_data'])) $res['list'] = $json['tbk_sc_material_optional_response']['result_list']['map_data'];
                return $res;
            }
            throw new TaobaoResponseException($json);
        });
        $this->platform()->take(20);
    }
    
    /**
     * query condition
     * @var array
     */
    private $condition;
    
    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\Taobao\TaobaoRequest::getMethod()
     */
    protected function getMethod(): string
    {
        return 'taobao.tbk.sc.material.optional';
    }
    
    public static function setAdzoneId($adzoneId)
    {
        static::$adzoneId = $adzoneId;
    }
    
    public static function setSession($session)
    {
        static::$session = $session;
    }
    
    public static function setSiteId($siteId) {
        static::$siteId = $siteId;
    }
    
    /**
     * pass the search keyword
     * @param string $keyword
     * @return self
     */
    public static function keyword(string $keyword): self
    {
        $req = new self();
        $req->condition['q'] = $keyword;
        return $req;
    }
    
    /**
     * pass a array where category ids you search
     * @param array $cats
     * @return self
     */
    public function whereCatIn(array $cats): self
    {
        $this->condition['cat'] = implode(',', $cats);
        return $this;
    }
    
    /**
     * pass provcity where you search
     * @param string $provcity
     * @return self
     */
    public function whereProvcityIs(string $provcity): self
    {
        $this->condition['itemloc'] = $provcity;
        return $this;
    }
    
    /**
     * define only search tmall items
     * @return self
     */
    public function onlyTmall(): self
    {
        $this->condition['is_tmall'] = 'true';
        return $this;
    }
    
    public function onlyCoupon(): self
    {
        $this->condition['has_coupon'] = 'true';
        return $this;
    }
    
    /**
     * define only search oversea items
     * @return self
     */
    public function onlyOverSeas(): self
    {
        $this->condition['is_overseas'] = 'true';
        return $this;
    }
    
    /**
     * define perpage return
     * default perpage is 20
     * the valid range is 1 ~ 100
     * @param int $perpage
     * @return self
     */
    public function take(int $perpage): self
    {
        $this->condition['page_size'] = max($perpage, 1);
        return $this;
    }
    
    /**
     * define nowpage
     * default nowpage is 1
     * @param number $page
     * @return self
     */
    public function nowpage($page = 1): self
    {
        $this->condition['page_no'] = max($page, 1);
        return $this;
    }
    
    /**
     * define price between start and end
     * @param int $startPrice
     * @param int $endPrice
     * @return self
     */
    public function wherePriceBetween(int $startPrice, int $endPrice): self
    {
        $this->condition['start_price'] = $startPrice;
        $this->condition['end_price'] = $endPrice;
        return $this;
    }
    
    /**
     * define commission rate between start and end
     * @param int $startRate
     * @param int $endRate
     * @return self
     */
    public function whereCommissionRateBetween(int $startRate, int $endRate): self
    {
        $this->condition['end_tk_rate'] = $startRate;
        $this->condition['start_tk_rate'] = $endRate;
        return $this;
    }
    
    /**
     * define the link style (1: pc 2: wireless)
     * default value is 2
     * @param int $platform
     * @return \SimpleConcurrent\Taobao\TBK\ScMaterialOptionalRequest
     */
    public function platform(int $platform = 2)
    {
        $this->condition['platform'] = $platform;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\SimpleRequest::getPromise()
     */
    public function getPromise(): PromiseInterface
    {
        if (! $this->requestData) {
            if (! isset($this->condition['site_id'])) $this->condition['site_id'] = self::$siteId;
            if (! isset($this->condition['adzone_id'])) $this->condition['adzone_id'] = self::$adzoneId;
            if (! isset($this->condition['session'])) $this->condition['session'] = self::$session;
            $this->_buildRequest($this->condition, true);
        }
        return parent::getPromise();
    }
    
    /**
     * sort result by items sales
     * @param bool $isDesc
     * @return \SimpleConcurrent\Taobao\TBK\ScMaterialOptionalRequest
     */
    public function sortBySales(bool $isDesc = true):self
    {
        $this->condition['sort'] = 'total_sales_' . ($isDesc ? 'des' : 'asc');
        return $this;
    }
    
    /**
     * sort result by commission rate
     * @param bool $isDesc
     * @return \SimpleConcurrent\Taobao\TBK\ScMaterialOptionalRequest
     */
    public function sortByCommissionRate(bool $isDesc = true):self
    {
        $this->condition['sort'] = 'tk_rate_' . ($isDesc ? 'des' : 'asc');
        return $this;
    }
    
    /**
     * sort result by tao ke sales
     * @param bool $isDesc
     * @return \SimpleConcurrent\Taobao\TBK\ScMaterialOptionalRequest
     */
    public function sortByTkSales(bool $isDesc = true):self
    {
        $this->condition['sort'] = 'tk_total_sales_' . ($isDesc ? 'des' : 'asc');
        return $this;
    }
    
    /**
     * sort result by commission
     * @param bool $isDesc
     * @return \SimpleConcurrent\Taobao\TBK\ScMaterialOptionalRequest
     */
    public function sortByCommission(bool $isDesc = true):self
    {
        $this->condition['sort'] = 'tk_total_commi_' . ($isDesc ? 'des' : 'asc');
        return $this;
    }
    
    /**
     * sort result by price
     * @param bool $isDesc
     * @return \SimpleConcurrent\Taobao\TBK\ScMaterialOptionalRequest
     */
    public function sortByPrice(bool $isDesc = false):self
    {
        $this->condition['sort'] = 'price_' . ($isDesc ? 'des' : 'asc');
        return $this;
    }
}