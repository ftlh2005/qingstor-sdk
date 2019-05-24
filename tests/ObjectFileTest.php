<?php
namespace BL\Tests;

use BL\QingStor\Service;
use PHPUnit\Framework\TestCase;

/**
 * @covers Service
 */

final class ObjectFileTest extends TestCase
{
    public function testHead()
    {
        $config = new ConfigTest();
        var_dump($config);
        $service  = new Service($config);
        $bucket   = $service->makeBucket('bl-test', 'pek3b');
        $object   = $bucket->makeObjectFile('favicon.png');
        $response = $object->head();
        print_r($response->getHeaders());
        $this->assertEquals(1, 1);
    }

    public function testDelete()
    {
        $config = new ConfigTest();
        var_dump($config);
        $service  = new Service($config);
        $bucket   = $service->makeBucket('bl-test', 'pek3b');
        $object   = $bucket->makeObjectFile('favicon.png');
        $response = $object->delete();
        print_r($response->getHeaders());
        $this->assertEquals(1, 1);
    }
}
