<?php

/**
 * 
 * @license See LICENSE in source root
 * @version 1.0
 * @since   1.0
 */

namespace Telluris\Config;

use Telluris\Exception\ConfigNotFoundException;
use PHPUnit_Framework_TestCase as UnitTestCase;
use Vfs\FileSystem as VirtualFileSystem;
use Vfs\Node\Directory as VirtualDir;
use Vfs\Node\File as VirtualFile;

class SingleFileStorageTest extends UnitTestCase {

    private $fs;

    public function setUp() {
        $this->fs = VirtualFileSystem::factory('vfs://');
    }

    public function tearDown() {
        $this->fs->unmount();
    }

    public function testGettingConfigIsAConfigObject() {
        /** @var VirtualFileSystem $fs */
        $fs = $this->fs;
        $json = json_encode(['anything' => []]);
        $configDir = new VirtualDir(['env_config.json' => new VirtualFile($json)]);
        $fs->get('/')->add('config', $configDir);

        $storage = new SingleFileStorage('vfs:///config/env_config.json');
        $this->assertInstanceOf(Config::class, $storage->getConfigForEnv('anything'));
    }

    public function testGettingConfigKeyForEnv() {
        /** @var VirtualFileSystem $fs */
        $fs = $this->fs;
        $json = json_encode(['dev' => ['foo' => 'bar']]);
        $configDir = new VirtualDir(['env_config.json' => new VirtualFile($json)]);
        $fs->get('/')->add('config', $configDir);

        $storage = new SingleFileStorage('vfs:///config/env_config.json');
        $config = $storage->getConfigForEnv('dev');
        $this->assertSame('bar', $config->get('foo'));
    }

    public function testGettingConfigNotPresentReturnsFalse() {
        /** @var VirtualFileSystem $fs */
        $fs = $this->fs;
        $json = json_encode([]);
        $configDir = new VirtualDir(['env_config.json' => new VirtualFile($json)]);
        $fs->get('/')->add('config', $configDir);

        $storage = new SingleFileStorage('vfs:///config/env_config.json');
        $this->assertFalse($storage->getConfigForEnv('not_there'));
    }

    public function testSettingNotFoundFilePathThrowsException() {
        $msg = 'Could not find a file located at "vfs:///config/not_present.json"';
        $this->setExpectedException(ConfigNotFoundException::class, $msg);

        new SingleFileStorage('vfs:///config/not_present.json');
    }

    public function testSettingSecretsFilePathPassesToConfig() {
        /** @var VirtualFileSystem $fs */
        $fs = $this->fs;
        $configJson = json_encode([
            'dev' => [
                'foo' => 'secret(bar.baz)'
            ]
        ]);
        $secretJson = json_encode([
            'bar' => [
                'baz' => 'little bunny foo foo'
            ]
        ]);

        $configDir = new VirtualDir([
            'env_config.json' => new VirtualFile($configJson),
            'env_secrets.json' => new VirtualFile($secretJson)
        ]);
        $fs->get('/')->add('config', $configDir);

        $configPath = 'vfs:///config/env_config.json';
        $secretPath = 'vfs:///config/env_secrets.json';
        $storage = new SingleFileStorage($configPath, $secretPath);
        $config = $storage->getConfigForEnv('dev');

        $this->assertSame('little bunny foo foo', $config->get('foo'));
    }

}
