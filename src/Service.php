<?php

namespace BL\QingStor;

class Service
{
    private $client;
    private $config;

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
        $uri .= $this->getHost();
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
        $str .= $request->getCanonicalizedResource();

        $sign   = hash_hmac('sha256', $str, $this->config->getSecretAccessKey(), true);
        $sign64 = base64_encode($sign);
        return $sign64;
    }

    /**
     * [makeQuerySignature]
     * @param  string $path
     * @param  array  $headers
     * @param  array|string|null $query
     * @return string
     */
    public function makeQuerySignature($path, $expires, array $headers = [], $query = null)
    {
        $str = "GET\n";
        $str .= ($headers['Content-MD5'] ?? '') . "\n";
        $str .= ($headers['Content-Type'] ?? '') . "\n";
        $str .= $expires . "\n";
        $str .= Request::makeCanonicalizedHeaders($headers);
        $str .= Request::makeCanonicalizedResource($path, $query);

        $sign    = hash_hmac('sha256', $str, $this->config->getSecretAccessKey(), true);
        $sign64  = base64_encode($sign);
        $signurl = urlencode($sign64);
        return $signurl;
    }

    /**
     * [makeUploadSignature]
     * @param  string $path
     * @param  array  $headers
     * @param  array|string|null $query
     * @return string
     */
    public function makeUploadSignature($path, array $headers = [], $query = null)
    {
        $str = "PUT\n";
        $str .= ($headers['Content-MD5'] ?? '') . "\n";
        $str .= ($headers['Content-Type'] ?? '') . "\n";
        $str .= "\n";
        $str .= Request::makeCanonicalizedHeaders($headers);
        $str .= Request::makeCanonicalizedResource($path, $query);

        $sign   = hash_hmac('sha256', $str, $this->config->getSecretAccessKey(), true);
        $sign64 = base64_encode($sign);
        return $sign64;
    }

    public function makeAuthorization($sign64)
    {
        $auth = 'QS ' . $this->config->getAccessKeyId() . ':' . $sign64;
        return $auth;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function sendRequest(Request $request, array $options = [])
    {
        $sign = $this->makeAuthSignature($request);
        $auth = $this->makeAuthorization($sign);
        return $this->client->send($request->withHeader('Authorization', $auth), $options);
    }

    public function makeBucket($name = null, $zone = null)
    {
        $name = $name ?: $this->config->getDefaultBucket();
        $zone = $zone ?: $this->config->getDefaultLocation();
        if ($name && $zone) {
            return new Bucket($this, $name, $zone);
        }
        return null;
    }

    /**
     * [API Service List Buckets]
     * @return \GuzzleHttp\Psr7\Response
     */
    public function listBuckets()
    {
        $method = 'GET';
        $path   = '/';

        $uri     = $this->makeUri($path);
        $request = new Request($method, $uri);
        return $this->sendRequest($request);
    }

    /**
     * [API Service List Locations]
     * @return \GuzzleHttp\Psr7\Response
     */
    public function ListLocations(array $options = [])
    {
        $method = 'GET';
        $path   = '/?location';
        if (array_key_exists('query', $options) && is_array($options['query'])) {
            $options['query']['location'] = 1;
        }

        $uri     = $this->makeUri($path);
        $request = new Request($method, $uri);
        return $this->sendRequest($request, $options);
    }

}
