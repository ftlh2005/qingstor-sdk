<?php

namespace BL\QingStor;

class Config
{
    const BASE_HOST = 'qingstor.com';

    protected $access_key_id     = '';
    protected $secret_access_key = '';
    protected $default_bucket    = '';
    protected $default_location  = '';
    protected $protocol          = 'https';

    public function __construct($access_key_id = '', $secret_access_key = '', array $options = [])
    {
        if ($access_key_id) {
            $this->access_key_id = $access_key_id;
        }
        if ($secret_access_key) {
            $this->secret_access_key = $secret_access_key;
        }
        if (array_key_exists('bucket', $options)) {
            $this->default_bucket = $options['bucket'];
        }
        if (array_key_exists('location', $options)) {
            $this->default_location = $options['location'];
        }
        if (array_key_exists('protocol', $options)) {
            $this->protocol = $options['protocol'];
        }
    }

    public function getAccessKeyId()
    {
        return $this->access_key_id;
    }

    public function getSecretAccessKey()
    {
        return $this->secret_access_key;
    }

    public function getDefaultBucket()
    {
        return $this->default_bucket;
    }

    public function getDefaultLocation()
    {
        return $this->default_location;
    }

    public function getProtocol()
    {
        return $this->protocol;
    }
}
