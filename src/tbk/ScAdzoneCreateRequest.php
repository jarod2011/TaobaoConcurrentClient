<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * tbk api : sc adzone create
 * @link http://open.taobao.com/api.htm?docId=34751&docType=2&scopeId=13878
 */
class ScAdzoneCreateRequest extends TaobaoRequest
{
    public static $session;
    
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_sc_adzone_create_response']) && isset($json['tbk_sc_adzone_create_response']['data']) && isset($json['tbk_sc_adzone_create_response']['data']['model'])) return $json['tbk_sc_adzone_create_response']['data']['model'];
            throw new TaobaoResponseException($json);
        });
    }
    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\Taobao\TaobaoRequest::getMethod()
     */
    protected function getMethod(): string
    {
        return 'taobao.tbk.sc.adzone.create';
    }
    
    public static function build($site_id, $adzone_name, $session = ''): self
    {
        $session = $session ? $session : static::$session;
        if (! $session) throw new TaobaoResponseException('user session is necessary');
        $req = new self();
        return $req->_buildRequest([
            'site_id' => $site_id,
            'adzone_name' => $adzone_name,
            'session' => $session
        ], true);
    }
}