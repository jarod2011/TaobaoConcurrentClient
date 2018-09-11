<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\SimpleRequest;
use GuzzleHttp\Promise\PromiseInterface;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * tbk api : uatm event get
 * @link http://open.taobao.com/api.htm?docId=26449&docType=2&source=search
 *
 */
class UatmEventListRequest extends TaobaoRequest
{
    private $condition = [];
    
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_uatm_event_get_response'])) {
                $res = [
                    'nowpage' => $this->condition['page_no'] ?? 1,
                    'perpage' => $this->condition['page_size'],
                    'total' => $json['tbk_uatm_event_get_response']['total_results'] ?? 0,
                    'list' => []
                ];
                if (isset($json['tbk_uatm_event_get_response']['results']) && isset($json['tbk_uatm_event_get_response']['results']['tbk_event'])) $res['list'] = $json['tbk_uatm_event_get_response']['results']['tbk_event'];
                $res['maxcount'] = ceil($res['total'] / $res['perpage']);
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
        return 'taobao.tbk.uatm.event.get';
    }
    
    /**
     * define where fields you need
     * @param array $fields
     * @return self
     */
    public static function fields(array $fields = ['event_id' ,'event_title' ,'start_time' ,'end_time']): self
    {
        if (! in_array('event_id', $fields)) $fields[] = 'event_id';
        $req = new self();
        $req->condition['fields'] = implode(',', $fields);
        return $req;
    }
    
    /**
     * define perpage count
     * @param int $perpage
     * @return self
     */
    public function take(int $perpage = 20): self
    {
        $this->condition['page_size'] = $perpage;
        return $this;
    }
    
    /**
     * define the now page
     * @param int $nowpage
     * @return self
     */
    public function nowpage(int $nowpage = 1): self
    {
        $this->condition['page_no'] = $nowpage;
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