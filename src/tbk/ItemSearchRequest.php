<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use GuzzleHttp\Promise\PromiseInterface;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * tbk api : item info get
 * @link http://open.taobao.com/api.htm?docId=24515&docType=2&source=search
 */
class ItemSearchRequest extends TaobaoRequest
{
    
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_item_get_response'])) {
                $res = [
                    'nowpage' => $this->requestData['page_no'] ?? 1,
                    'perpage' => $this->requestData['page_size'] ?? $this->condition['page_size'],
                    'total' => $json['tbk_item_get_response']['total_results'],
                    'list' => []
                ];
                $res['maxpage'] = ceil($res['total'] / $res['perpage']);
                if(isset($json['tbk_item_get_response']['results']) && isset($json['tbk_item_get_response']['results']['n_tbk_item'])) $res['list'] = $json['tbk_item_get_response']['results']['n_tbk_item'];
                return $res;
            }
            throw new TaobaoResponseException($json);
        });
        $this->platform()->take(20);
    }
    
    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\Taobao\TaobaoRequest::getMethod()
     */
    protected function getMethod(): string
    {
        return 'taobao.tbk.item.get';
    }
    
    /**
     * query condition
     * @var array
     */
    private $condition;
    
    /**
     * define result fields
     * @param array $fields
     * @return self
     */
    public static function fields(array $fields = ['num_iid','title','pict_url','small_images','reserve_price','zk_final_price','user_type','provcity','item_url','nick','seller_id','volume','cat_leaf_name','cat_name','category_id']): self
    {
        $req = new self();
        if (! in_array('num_iid', $fields)) $fields[] = 'num_iid';
        $req->condition['fields'] = implode(',', $fields);
        return $req;
    }
    
    /**
     * pass the search keyword
     * @param string $keyword
     * @return self
     */
    public function keyword(string $keyword): self
    {
        $this->condition['q'] = $keyword;
        return $this;
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
     * @return \SimpleConcurrent\Taobao\TBK\ItemSearchRequest
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
            $this->_buildRequest($this->condition, true);
        }
        return parent::getPromise();
    }
    
    /**
     * sort result by items sales
     * @param bool $isDesc
     * @return \SimpleConcurrent\Taobao\TBK\ItemSearchRequest
     */
    public function sortBySales(bool $isDesc = true)
    {
        $this->condition['sort'] = 'total_sales_' . ($isDesc ? 'des' : 'asc');
        return $this;
    }
    
    /**
     * sort result by commission rate
     * @param bool $isDesc
     * @return \SimpleConcurrent\Taobao\TBK\ItemSearchRequest
     */
    public function sortByCommissionRate(bool $isDesc = true)
    {
        $this->condition['sort'] = 'tk_rate_' . ($isDesc ? 'des' : 'asc');
        return $this;
    }
    
    /**
     * sort result by tao ke sales
     * @param bool $isDesc
     * @return \SimpleConcurrent\Taobao\TBK\ItemSearchRequest
     */
    public function sortByTkSales(bool $isDesc = true)
    {
        $this->condition['sort'] = 'tk_total_sales_' . ($isDesc ? 'des' : 'asc');
        return $this;
    }
    
    /**
     * sort result by commission
     * @param bool $isDesc
     * @return \SimpleConcurrent\Taobao\TBK\ItemSearchRequest
     */
    public function sortByCommission(bool $isDesc = true)
    {
        $this->condition['sort'] = 'tk_total_commi_' . ($isDesc ? 'des' : 'asc');
        return $this;
    }
}