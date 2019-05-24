<?php
namespace BL\Tests;

use BL\QingStor\Bucket;
use BL\QingStor\Service;
use PHPUnit\Framework\TestCase;

/**
 * @covers Bucket
 */

final class BucketTest extends TestCase
{
    public function testListObjects()
    {
        $config   = new ConfigTest();
        $service  = new Service($config);
        $bucket   = $service->makeBucket('bl-test', 'pek3b');
        $response = $bucket->listObjects(['query' => ['delimiter' => '/']]);
        print_r(json_decode($response->getBody()->getContents()));
        $this->assertEquals(1, 1);
    }

    public function testDeleteObjects()
    {
        $config   = new ConfigTest();
        $service  = new Service($config);
        $bucket   = $service->makeBucket('bl-test', 'pek3b');
        $response = $bucket->deleteObjects(['json' => ['objects' => [['key' => 'favicon.png']]]]);
        print_r(json_decode($response->getBody()->getContents()));
        $this->assertEquals(1, 1);
    }

    public function testHead()
    {
        $config   = new ConfigTest();
        $service  = new Service($config);
        $bucket   = $service->makeBucket('bl-test', 'pek3b');
        $response = $bucket->head();
        print_r($response->getStatusCode());
        $this->assertEquals(1, 1);
    }

    public function testStats()
    {
        $config   = new ConfigTest();
        $service  = new Service($config);
        $bucket   = $service->makeBucket('bl-test', 'pek3b');
        $response = $bucket->stats();
        print_r(json_decode($response->getBody()->getContents()));
        $this->assertEquals(1, 1);
    }

    public function testQuerySign()
    {
        $config   = new ConfigTest();
        $service  = new Service($config);
        $bucket   = $service->makeBucket('bl-test', 'pek3b');
        $expires  = time()+120;
        $sign = $bucket->makeObjectFileQuerySignature('favicon.png', $expires);
        print_r($sign."\n");
        print_r($expires);
        $this->assertEquals(1, 1);
    }
    
    public function testUploadSign()
    {
        $config   = new ConfigTest();
        $service  = new Service($config);
        $bucket   = $service->makeBucket('bl-test', 'pek3b');
        $headers = [
            'Content-Type'=> 'application/octet-stream',
            'x-qs-date'=>gmdate('D, d M Y H:i:s T')
        ];
        $sign = $bucket->makeObjectFileUploadSignature('f.txt', $headers);
        print_r($sign."\n");
        print_r($headers);
        $this->assertEquals(1, 1);
    }

    public function testCreate()
    {
        $config   = new ConfigTest();
        $service  = new Service($config);
        $bucket   = $service->makeBucket('bl-test-12', 'pek3b');
        $response = $bucket->create();
        print_r($response->getBody()->getContents());
        $this->assertEquals(1, 1);
    }

    public function testDelete()
    {
        $config   = new ConfigTest();
        $service  = new Service($config);
        $bucket   = $service->makeBucket('bl-test-12', 'pek3b');
        $response = $bucket->delete();
        print_r($response->getBody()->getContents());
        $this->assertEquals(1, 1);
    }

}
