<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\SimpleRequest;
use GuzzleHttp\Promise\PromiseInterface;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * tbk api : tao qiang gou get
 * @link http://open.taobao.com/api.htm?spm=a219a.7386797.0.0.2d912cbf8dif1B&source=search&docId=27543&docType=2
 */
class JuTqgListRequest extends TaobaoRequest
{
    
    private $condition = [];
    
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_ju_tqg_get_response'])) {
                $res = [
                    'nowpage' => $this->condition['page_no'] ?? 1,
                    'perpage' => $this->condition['page_size'],
                    'total' => $json['tbk_ju_tqg_get_response']['total_results'] ?? 0,
                    'list' => []
                ];
                if (isset($json['tbk_ju_tqg_get_response']['results']) && isset($json['tbk_ju_tqg_get_response']['results']['results'])) $res['list'] = $json['tbk_ju_tqg_get_response']['results']['results'];
                $res['maxcount'] = ceil($res['total'] / $res['perpage']);
                return $res;
            }
            throw new TaobaoResponseException($json);
        });
        $this->fields()->betweenDatetime(date('Y-m-d H:i:s', strtotime('today')), date('Y-m-d H:i:s', strtotime('tomorrow')))->take()->nowpage();
    }
    
    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\Taobao\TaobaoRequest::getMethod()
     */
    protected function getMethod(): string
    {
        return 'taobao.tbk.ju.tqg.get';
    }
    
    /**
     * pass the pid
     * @param string $pid
     * @return self
     */
    public static function pid(string $pid): self
    {
        $req = new self();
        $pid = explode('_', $pid);
        $req->condition['adzone_id'] = $pid[3] ?? '';
        return $req;
    }
    
    /**
     * pass you require fields
     * @param array $fields
     * @return self
     */
    public function fields(array $fields = ['num_iid', 'click_url','pic_url','reserve_price','zk_final_price','total_amount','sold_num','title','category_name','start_time','end_time']): self
    {
        if (! in_array('num_iid', $fields)) $fields[] = 'num_iid';
        $this->condition['fields'] = implode(',', $fields);
        return $this;
    }
    
    /**
     * pass the item between start datetime to end datetime
     * @param string $start_datetime
     * @param string $end_datetime
     * @return self
     */
    public function betweenDatetime(string $start_datetime, string $end_datetime): self
    {
        $this->condition['start_time'] = $start_datetime;
        $this->condition['end_time'] = $end_datetime;
        return $this;
    }
    
    /**
     * pass perpage you want to get
     * @param int $perpage
     * @return self
     */
    public function take(int $perpage = 20): self
    {
        $this->condition['page_size'] = $perpage;
        return $this;
    }
    
    /**
     * define where page you want to get
     * @param int $page
     * @return self
     */
    public function nowpage(int $page = 1): self
    {
        $this->condition['page_no'] = $page;
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
}