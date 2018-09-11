<?php
namespace SimpleConcurrent\Taobao;

use SimpleConcurrent\SimpleRequest;
use GuzzleHttp\Psr7\Request;

/**
 * all taobao api request base class
 * @abstract
 */
abstract class TaobaoRequest extends SimpleRequest
{
    /**
     * taobao app key
     * @var string
     */
    public static $appKey;
    
    /**
     * taobao app secret
     * @var string
     */
    public static $appSecret;
    
    /**
     * request base uri
     * @var string
     */
    public static $baseUri = 'http://gw.api.taobao.com/router/rest';
    
    /**
     * the true request data
     * @var array
     */
    protected $requestData = [];
    
    public function __construct()
    {
        parent::__construct();
        $this->responseIsJson();
        $this->addSuccessCallback(function ($res) {
            if (isset($res['error_response'])) throw new TaobaoResponseException($res['error_response']);
            return $res;
        });
    }
    
    /**
     * pass the taobao app base uri
     * @param string $baseUri
     */
    public static function setBaseUri(string $baseUri)
    {
        self::$baseUri = $baseUri;
    }
    
    /**
     * pass the taobao app key
     * @param string $appKey
     */
    public static function setAppKey(string $appKey)
    {
        self::$appKey = $appKey;
    }
    
    /**
     * pass the taobao app secret
     * @param string $appSecret
     */
    public static function setAppSecret(string $appSecret)
    {
        self::$appSecret = $appSecret;
    }
    
    /**
     * sign the requst data
     * @param array $data
     * @return string
     */
    private function sign(array $data): string
    {
        ksort($data, SORT_STRING);
        $sign = '';
        foreach ($data as $k => $v) {
            $sign .= $k . $v;
        }
        return strtoupper(md5(self::$appSecret . $sign . self::$appSecret));
    }
    
    /**
     * get request taobao api method
     * @return string
     */
    abstract protected function getMethod(): string;
    
    /**
     * build a request
     * @param array $data
     * @param bool $isSign
     * @return \SimpleConcurrent\Taobao\TaobaoRequest
     */
    protected function _buildRequest(array $data, bool $isSign = true)
    {
        if ($isSign) {
            $data['method'] = $this->getMethod();
            $data['timestamp'] = date('Y-m-d H:i:s', time());
            $data['sign_method'] = 'md5';
            $data['format'] = 'json';
            $data['v'] = '2.0';
            $data['app_key'] = self::$appKey;
            $data['sign'] = $this->sign($data);
        }
        $this->requestData = $data;
        return $this->setRequest(new Request('POST', self::$baseUri), [
            'form_params' => $this->requestData
        ]);
    }
    
    /**
     * get last request data
     * @return array
     */
    public function getRequestData(): array
    {
        return $this->requestData;
    }
    
}

/**
 * taobao response exception
 */
class TaobaoResponseException extends \Exception
{
    const TB_ERROR_CODE = 400001;
    /**
     * @var array
     */
    private $errorOriginal;
    
    private $errorMsg;
    
    private $errorSubMsg;
    
    private $errorCode;
    
    public function __construct(array $res)
    {
        $this->errorOriginal = $res;
        $this->errorMsg = $res['msg'] ?? 'unknow error msg';
        $this->errorCode = $res['code'] ?? 0;
        $this->errorSubMsg = $res['sub_msg'] ?? '';
        $msg = $this->errorMsg;
        if ($this->errorSubMsg) $msg .= ' - ' . $this->errorSubMsg;
        parent::__construct($msg, self::TB_ERROR_CODE);
    }
    
    public function getOriginalError()
    {
        return $this->errorOriginal;
    }
    
    public function getOriginalMsg()
    {
        return $this->errorMsg;
    }
    
    public function getOriginalCode()
    {
        return $this->errorCode;
    }
    
    public function getOriginalSubMsg()
    {
        return $this->errorSubMsg;
    }
}
