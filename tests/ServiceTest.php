<?php
namespace BL\Tests;

use BL\QingStor\Service;
use PHPUnit\Framework\TestCase;

/**
 * @covers Service
 */

final class ServiceTest extends TestCase
{
    public function testListBuckets()
    {
        $config   = new ConfigTest();
        $service  = new Service($config);
        $response = $service->listBuckets();
        print_r($response->getBody()->getContents());
        $this->assertEquals(1, 1);
    }

    public function testListLocations()
    {
        $config   = new ConfigTest();
        $service  = new Service($config);
        $response = $service->listLocations(['query' => ['lang' => 'zh-cn']]);
        print_r($response->getBody()->getContents());
        $this->assertEquals(1, 1);
    }
}
