<?php
/**
 * 青云对象存储转码
 */
namespace BL\QingStor;

class Transcoder
{
    private $client;
    private $config;
    protected $errorMsg;

    public function __construct(Config $config)
    {
        $this->client = new Client();
        $this->config = $config;
    }

    public function getHost()
    {
        return Config::BASE_HOST;
    }

    public function makeUri($path)
    {
        $uri = $this->config->getProtocol() . '://';
        $uri .= 'transcoder.'.$this->config->getZone().'.'.$this->getHost();
        $uri .= $path;
        return $uri;
    }

    public function makeAuthSignature(Request $request)
    {
        $str = $request->getMethod() . "\n";
        $str .= $request->getContentMD5() . "\n";
        $str .= $request->getContentType() . "\n";
        $str .= $request->getDate() . "\n";
        $str .= $request->getCanonicalizedHeaders();
        $str .= '/transcoder'.$request->getCanonicalizedResource();

        $sign   = hash_hmac('sha256', $str, $this->config->getSecretAccessKey(), true);
        $sign64 = base64_encode($sign);
        return $sign64;
    }

    public function makeContentMd5($str)
    {
        return base64_encode(md5($str, true));
    }

    public function makeAuthorization($sign64)
    {
        $auth = 'QS ' . $this->config->getAccessKeyId() . ':' . $sign64;
        return $auth;
    }

    public function getError()
    {
        return $this->errorMsg;
    }

    public function sendRequest(Request $request, array $options = [])
    {
        $sign = $this->makeAuthSignature($request);
        $auth = $this->makeAuthorization($sign);
        try {
            $res = $this->client->send($request->withHeader('Authorization', $auth), $options);
            if ($res->getStatusCode() === 201) {
                return json_decode($res->getBody()->getContents(), true);
            }else {
                return false;
            }
        } catch (\Exception $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }
    }

    /**
     * 数据转码处理 API
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    public function create(array $options)
    {
        $method  = 'POST';
        $path    = '/v1/codec';
        $headers = [];
        $body    = '';
        if (!is_array($options) && !$options) {
            $this->errorMsg = "缺少参数";
            return false;
        }
        $body = json_encode($options);

        $headers['Content-MD5'] = $this->makeContentMd5($body);
        $headers['Content-Type'] = 'application/json';

        $uri     = $this->makeUri($path);

        $request = new Request($method, $uri, $headers, $body);
        return $this->sendRequest($request, $options);
    }
}