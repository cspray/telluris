<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Telluris\Config;

use Telluris\Exception\ConfigNotFoundException;
use PHPUnit_Framework_TestCase as UnitTestCase;
use Vfs\FileSystem as VirtualFileSystem;
use Vfs\Node\Directory as VirtualDir;
use Vfs\Node\File as VirtualFile;

class MultipleFileStorageTest extends UnitTestCase {

    private $fs;

    public function setUp() {
        $this->fs = VirtualFileSystem::factory('vfs://');
    }

    public function tearDown() {
        $this->fs->unmount();
    }

    public function testGettingConfigIsAConfigObject() {
        $fs = $this->fs;
        $envContent = json_encode([]);
        $configDir = new VirtualDir(['development.json' => new VirtualFile($envContent)]);
        $fs->get('/')->add('config', $configDir);

        $storage = new MultipleFileStorage('vfs:///config');
        $this->assertInstanceOf(Config::class, $storage->getConfigForEnv('development'));
    }

    public function testGettingConfigKeyForEnv() {
        /** @var VirtualFileSystem $fs */
        $fs = $this->fs;
        $json = json_encode(['foo' => 'bar']);
        $configDir = new VirtualDir(['development.json' => new VirtualFile($json)]);
        $fs->get('/')->add('config', $configDir);

        $storage = new MultipleFileStorage('vfs:///config');
        $config = $storage->getConfigForEnv('development');
        $this->assertSame('bar', $config->get('foo'));
    }

    public function testGettingConfigNotPresentReturnsFalse() {
        /** @var VirtualFileSystem $fs */
        $fs = $this->fs;
        $json = json_encode([]);
        $configDir = new VirtualDir(['development.json' => new VirtualFile($json)]);
        $fs->get('/')->add('config', $configDir);

        $storage = new MultipleFileStorage('vfs:///config');
        $this->assertFalse($storage->getConfigForEnv('production'));
    }

    public function testSettingNotFoundDirectoryPathThrowsException() {
        $msg = 'Could not find a directory located at "vfs:///config"';
        $this->setExpectedException(ConfigNotFoundException::class, $msg);

        new MultipleFileStorage('vfs:///config');
    }

    public function testSettingSecretsFilePathPassesToConfigMulti() {
        /** @var VirtualFileSystem $fs */
        $fs = $this->fs;
        $configJson = json_encode(['foo' => 'secret(bar.baz)']);
        $secretJson = json_encode([
            'bar' => [
                'baz' => 'little bunny foo foo'
            ]
        ]);

        $configDir = new VirtualDir([
            'development.json' => new VirtualFile($configJson),
            'env_secrets.json' => new VirtualFile($secretJson)
        ]);
        $fs->get('/')->add('config', $configDir);

        $configPath = 'vfs:///config';
        $storage = new MultipleFileStorage($configPath, 'env_secrets');
        $config = $storage->getConfigForEnv('development');

        $this->assertSame('little bunny foo foo', $config->get('foo'));
    }

}