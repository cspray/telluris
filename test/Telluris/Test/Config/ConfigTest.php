<?php
/**
 * Created by PhpStorm.
 * User: cspray
 * Date: 3/21/15
 * Time: 12:02
 */

namespace Cspray\Telluris\Test\Config;

use Cspray\Telluris\Config\Config;
use PHPUnit_Framework_TestCase as UnitTestCase;

class ConfigTest extends UnitTestCase {

    public function testGetKeyWithDotNotation() {
        $config = new Config(['foo' => ['bar' => ['baz' => 42]]]);
        $this->assertSame(42, $config->get('foo.bar.baz'));
    }

    public function testPopulateSecret() {
        $rawConfig = [
            'foo' => 'secret(db.host)'
        ];
        $secrets = [
            'db' => [
                'host' => 'bar'
            ]
        ];

        $config = new Config($rawConfig, $secrets);
        $this->assertSame('bar', $config->get('foo'));
    }


}