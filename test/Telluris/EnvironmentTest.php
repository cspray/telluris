<?php

/**
 * 
 * @license See LICENSE in source root
 * @version 1.0
 * @since   1.0
 */

namespace Telluris;

use Telluris\Config\Config;
use Telluris\Config\Storage;
use PHPUnit_Framework_TestCase as UnitTestCase;

class EnvironmentTest extends UnitTestCase {

    public function isDataProviders() {
        return [
            [Environment::DEVELOPMENT, 'isProduction', false],
            [Environment::DEVELOPMENT, 'isDevelopment', true],
            [Environment::DEVELOPMENT, 'isStaging', false],
            [Environment::DEVELOPMENT, 'isTest', false],
            [Environment::PRODUCTION, 'isProduction', true],
            [Environment::PRODUCTION, 'isDevelopment', false],
            [Environment::PRODUCTION, 'isStaging', false],
            [Environment::PRODUCTION, 'isTest', false],
            [Environment::STAGING, 'isProduction', false],
            [Environment::STAGING, 'isDevelopment', false],
            [Environment::STAGING, 'isStaging', true],
            [Environment::STAGING, 'isTest', false],
            [Environment::TEST, 'isProduction', false],
            [Environment::TEST, 'isDevelopment', false],
            [Environment::TEST, 'isStaging', false],
            [Environment::TEST, 'isTest', true]
        ];
    }

    /**
     * @param $env
     * @param $method
     * @param $expected
     * @dataProvider isDataProviders
     */
    public function testIsEnvMethods($env, $method, $expected) {
        /** @var Storage $mockStorage */
        $mockStorage = $this->getMock(Storage::class);
        $environment = new Environment($mockStorage, $env);
        $this->assertSame($expected, $environment->$method());
    }

    public function testGettingConfig() {
        $mockConfig = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        /** @var Storage $mockStorage */
        $mockStorage = $this->getMock(Storage::class);
        $mockStorage->expects($this->once())
                    ->method('getConfigForEnv')
                    ->with('dev')
                    ->willReturn($mockConfig);

        $env = new Environment($mockStorage, 'dev');
        $this->assertInstanceOf(Config::class, $env->getConfig());
    }

    public function testSettingInitializerForOneEnvironment() {
        /** @var Storage $mockStorage */
        $mockStorage = $this->getMock(Storage::class);
        $environment = new Environment($mockStorage);

        /** @var Initializer $initializer */
        $initializer = $this->getMock(Initializer::class);
        $environment->registerInitializer($initializer, Environment::DEVELOPMENT);

        $expected = [Environment::DEVELOPMENT => [$initializer]];
        $this->assertAttributeSame($expected, 'envSpecificInitializers', $environment);
    }

    public function testSettingInitializerForMultipleEnvironments() {
        /** @var Storage $mockStorage */
        $mockStorage = $this->getMock(Storage::class);
        $environment = new Environment($mockStorage);

        /** @var Initializer $initializer */
        $initializer = $this->getMock(Initializer::class);
        $environment->registerInitializer($initializer, Environment::DEVELOPMENT, Environment::TEST);

        $expected = [Environment::DEVELOPMENT => [$initializer], Environment::TEST => [$initializer]];
        $this->assertAttributeSame($expected, 'envSpecificInitializers', $environment);
    }

    public function testRunningInitializerForEnvironment() {
        /** @var Storage $mockStorage */
        $mockStorage = $this->getMock(Storage::class);
        $environment = new Environment($mockStorage);

        /** @var Initializer $initializer */
        $initializer = $this->getMock(Initializer::class);
        $initializer->expects($this->once())
                    ->method('execute')
                    ->with($environment);
        $environment->registerInitializer($initializer, Environment::DEVELOPMENT);
        $environment->runInitializers();
    }

    public function testAddingInitializersForAllEnvironments() {
        /** @var Storage $mockStorage */
        $mockStorage = $this->getMock(Storage::class);
        $environment = new Environment($mockStorage);

        /** @var Initializer $initializer */
        $initializer = $this->getMock(Initializer::class);
        $environment->registerInitializer($initializer);

        $expected = [$initializer];
        $this->assertAttributeSame($expected, 'allInitializers', $environment);
    }

    public function testRunningInitializersForAllEnvironments() {
        /** @var Storage $mockStorage */
        $mockStorage = $this->getMock(Storage::class);
        $environment = new Environment($mockStorage);

        /** @var Initializer $initializer */
        $initializer = $this->getMock(Initializer::class);
        $initializer->expects($this->once())
                    ->method('execute')
                    ->with($environment);
        $environment->registerInitializer($initializer);
        $environment->runInitializers();
    }

    public function testGettingConfigNotPresentThrowsException() {
        /** @var Storage $mockStorage */
        $mockStorage = $this->getMock(Storage::class);
        $mockStorage->expects($this->once())
                    ->method('getConfigForEnv')
                    ->with('not_present')
                    ->willReturn(false);
        $environment = new Environment($mockStorage, 'not_present');

        $msg = 'Could not find a configuration for environment named "not_present".';
        $this->setExpectedException(Exception\ConfigNotFoundException::class, $msg);
        $environment->getConfig();
    }

}
