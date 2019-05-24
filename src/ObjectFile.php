<?php

namespace BL\QingStor;

class ObjectFile
{
    private $bucket;
    private $key;
    protected $errorMsg;

    public function __construct(Bucket $bucket, $key)
    {
        $this->bucket = $bucket;
        $this->key    = $key;
    }

    public function getServiceConfig()
    {
        return $this->bucket->getServiceConfig();
    }

    public function getBucketName()
    {
        return $this->bucket->getName();
    }

    public function getBucketZone()
    {
        return $this->bucket->getZone();
    }

    public function getHost()
    {
        $host = $this->getBucketZone() . '.' . Config::BASE_HOST;
        return $host;
    }

    public function makeUri()
    {
        $uri = $this->getServiceConfig()->getProtocol() . '://';
        $uri .= $this->getHost();
        $uri .= '/' . $this->getBucketName();
        $uri .= '/' . $this->key;
        return $uri;
    }

    public function sendRequest(Request $request, array $options = [])
    {
        return $this->bucket->sendRequest($request, $options);
    }

    /**
     * [API ObjectFile Delete]
     * @return \GuzzleHttp\Psr7\Response
     */
    public function delete()
    {
        $method  = 'DELETE';
        $uri     = $this->makeUri();
        $request = new Request($method, $uri);
        return $this->sendRequest($request);
    }

    /**
     * [API ObjectFile Head]
     * @return \GuzzleHttp\Psr7\Response
     */
    public function head()
    {
        $method  = 'HEAD';
        $uri     = $this->makeUri();
        $request = new Request($method, $uri);
        return $this->sendRequest($request);
    }

    /**
     * [API ObjectFile Get]
     * @return \GuzzleHttp\Psr7\Response
     */
    public function get()
    {

    }
}
