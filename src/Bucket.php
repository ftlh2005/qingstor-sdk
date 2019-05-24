<?php

namespace BL\QingStor;

class Bucket
{
    private $service;
    private $name;
    private $zone;
    protected $errorMsg;

    public function __construct(Service $service, $name, $zone)
    {
        $this->service = $service;
        $this->name    = $name;
        $this->zone    = $zone;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getZone()
    {
        return $this->zone;
    }

    public function getServiceConfig()
    {
        return $this->service->getConfig();
    }

    public function getHost()
    {
        // $host = $this->name.'.'.$this->zone.'.'.Config::BASE_HOST;
        $host = $this->zone . '.' . Config::BASE_HOST;
        return $host;
    }

    public function makeUri($path)
    {
        $uri = $this->getServiceConfig()->getProtocol() . '://';
        $uri .= $this->getHost();
        $uri .= '/' . $this->name;
        $uri .= $path;
        return $uri;
    }

    public function makeContentMd5($str)
    {
        return base64_encode(md5($str, true));
    }

    public function makeObjectFile($key)
    {
        if ($key) {
            return new ObjectFile($this, $key);
        }
        return null;
    }

    public function sendRequest(Request $request, array $options = [])
    {
        return $this->service->sendRequest($request, $options);
    }

    public function makeObjectFileUploadSignature($key, $headers)
    {
        $path = '/' . $this->name . '/' . $key;
        return $this->service->makeUploadSignature($path, $headers);
    }

    public function makeObjectFileQuerySignature($key, $expires)
    {
        $path = '/' . $this->name . '/' . $key;
        return $this->service->makeQuerySignature($path, $expires);
    }

    public function makeObjectFilesQuerySignatures(array $keys, $expires)
    {
        $signs = [];
        foreach ($keys as $key) {
            $signs[] = $this->makeObjectFileQuerySignature($key, $expires);
        }
        return $signs;
    }

    /**
     * [API Bucket List Objects]
     * @param  array  $options
     * @return \GuzzleHttp\Psr7\Response
     */
    public function listObjects(array $options = [])
    {
        $method  = 'GET';
        $path    = '/';
        $headers = [];
        if (array_key_exists('form_params', $options)) {
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }
        if (array_key_exists('json', $options)) {
            $headers['Content-Type'] = 'application/json';
        }

        $uri     = $this->makeUri($path);
        $request = new Request($method, $uri, $headers);
        return $this->sendRequest($request, $options);
    }

    /**
     * [API Bucket Delete Objects]
     * @param  array  $options
     * @return \GuzzleHttp\Psr7\Response
     */
    public function deleteObjects(array $options = [])
    {
        $method  = 'POST';
        $path    = '/?delete';
        $headers = [];
        $body    = '';

        if (array_key_exists('query', $options) && is_array($options['query'])) {
            $options['query']['delete'] = 1;
        }
        if (array_key_exists('form_params', $options) && is_array($options['form_params'])) {
            $body = json_encode($options['form_params']);
            unset($options['form_params']);
        }
        if (array_key_exists('json', $options) && is_array($options['json'])) {
            $body = json_encode($options['json']);
            unset($options['json']);
        }
        if (array_key_exists('body', $options)) {
            $body = (string) $options['body'];
            unset($options['body']);
        }
        $headers['Content-MD5'] = $this->makeContentMd5($body);

        $uri     = $this->makeUri($path);
        $request = new Request($method, $uri, $headers, $body);
        return $this->sendRequest($request, $options);
    }

    /**
     * [API Bucket Head]
     * @return \GuzzleHttp\Psr7\Response
     */
    public function head()
    {
        $method = 'HEAD';
        $path   = '/';

        $uri     = $this->makeUri($path);
        $request = new Request($method, $uri);
        return $this->sendRequest($request);
    }

    /**
     * [API Bucket Create]
     * @return \GuzzleHttp\Psr7\Response
     */
    public function create()
    {
        $method = 'PUT';
        $path   = '/';

        $uri     = $this->makeUri($path);
        $request = new Request($method, $uri);
        return $this->sendRequest($request);
    }

    /**
     * [API Bucket Delete]
     * @return \GuzzleHttp\Psr7\Response
     */
    public function delete()
    {
        $method = 'DELETE';
        $path   = '/';

        $uri     = $this->makeUri($path);
        $request = new Request($method, $uri);
        return $this->sendRequest($request);
    }

    /**
     * [API Bucket Stats]
     * @param  array  $options
     * @return \GuzzleHttp\Psr7\Response
     */
    public function stats(array $options = [])
    {
        $method = 'GET';
        $path   = '/?stats';

        if (array_key_exists('query', $options) && is_array($options['query'])) {
            $options['query']['stats'] = 1;
        }

        $uri     = $this->makeUri($path);
        $request = new Request($method, $uri);
        return $this->sendRequest($request);
    }

}
