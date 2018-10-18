<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * api: taobao.tbk.sc.optimus.material
 * @link http://open.taobao.com/api.htm?docId=37884&docType=2
 */
class ScOptimusMaterial extends TaobaoRequest
{
    protected static $session;
    protected static $adzoneId;
    protected static $siteId;
    
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_sc_optimus_material_response']) && isset($json['tbk_sc_optimus_material_response']['result_list'])) {
                return $json['tbk_sc_optimus_material_response']['result_list']['map_data'] ?? $json['tbk_sc_optimus_material_response']['result_list'];
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
        return 'taobao.tbk.sc.optimus.material';
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
    
    public static function build(int $materialId, int $pageSize = 20, int $pageNo = 1)
    {
        $req = new self();
        $data = [
            'page_no' => max(1, $pageNo),
            'page_size' => min(100, max(1, $pageSize)),
            'site_id' => static::$siteId,
            'adzone_id' => static::$adzoneId,
            'session' => static::$session
        ];
        if ($materialId > 0) $data['material_id'] = $materialId;
        return $req->_buildRequest($data, true);
    }
}