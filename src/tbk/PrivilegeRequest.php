<?php
namespace SimpleConcurrent\Taobao\TBK;

use SimpleConcurrent\Taobao\TaobaoRequest;
use SimpleConcurrent\Taobao\TaobaoResponseException;

/**
 * tbk api : good privilege
 * @link http://open.taobao.com/api.htm?docId=28625&docType=2
 */
class PrivilegeRequest extends TaobaoRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->addSuccessCallback(function ($json) {
            if (isset($json['tbk_privilege_get_response']) && isset($json['tbk_privilege_get_response']['result']) && isset($json['tbk_privilege_get_response']['result']['data'])) return $json['tbk_privilege_get_response']['result']['data'];
            throw new TaobaoResponseException($json);
        });
    }

    /**
     * {@inheritDoc}
     * @see \SimpleConcurrent\Taobao\TaobaoRequest::getMethod()
     */
    protected function getMethod(): string
    {
        return 'taobao.tbk.privilege.get';
    }

    /**
     * build a good privilege request
     * @param int $itemId
     * @param string $pid
     * @param string $session
     * @param string $me
     * @param int $platform
     * @return self
     */
    public static function buildRequest(int $itemId, string $pid, string $session, string $me = '', int $platform = 2): self
    {
        $req = new self();
        $pid = explode('_', $pid);
        $data = [
            'item_id' => $itemId,
            'adzone_id' => $pid[3] ?? '',
            'site_id' => $pid[2] ?? '',
            'platform' => $platform,
            'session' => $session
        ];
        if ($me) $data['me'] = $me;
        return $req->_buildRequest($data, true);
    }
}