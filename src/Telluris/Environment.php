<?php

/**
 *
 * @license See LICENSE in source root
 * @version 1.0
 * @since   1.0
 */

namespace Telluris;

use Telluris\Exception\ConfigNotFoundException;

class Environment {

    const PRODUCTION = 'production';
    const STAGING = 'staging';
    const DEVELOPMENT = 'development';
    const TEST = 'test';

    private $env;
    private $allInitializers = [];
    private $envSpecificInitializers = [];
    private $config;
    private $configStorage;

    /**
     * @param Config\Storage $storage
     * @param string $env
     */
    public function __construct(Config\Storage $storage, $env = self::DEVELOPMENT) {
        $this->configStorage = $storage;
        $this->env = (string) $env;
    }

    public function getEnv() {
        return $this->env;
    }

    public function isProduction() {
        return $this->env === self::PRODUCTION;
    }

    public function isStaging() {
        return $this->env === self::STAGING;
    }

    public function isDevelopment() {
        return $this->env === self::DEVELOPMENT;
    }

    public function isTest() {
        return $this->env === self::TEST;
    }

    public function getConfig() {
        if (!$this->config) {
            $config = $this->configStorage->getConfigForEnv($this->env);
            if (!$config) {
                $msg = sprintf('Could not find a configuration for environment named "%s".', $this->env);
                throw new ConfigNotFoundException($msg);
            }

            $this->config = $config;
        }

        return $this->config;
    }

    public function registerInitializer(Initializer $initializer, ...$environments) {
        if (empty($environments)) {
            $this->allInitializers[] = $initializer;
        } else {
            foreach ($environments as $environment) {
                if (!isset($this->envSpecificInitializers[$environment])) {
                    $this->envSpecificInitializers[$environment] = [];
                }

                $this->envSpecificInitializers[$environment][] = $initializer;
            }
        }
    }

    public function runInitializers() {
        $envSpecificInitializers = isset($this->envSpecificInitializers[$this->env]) ? $this->envSpecificInitializers[$this->env] : [];
        /** @var Initializer $initializer */
        foreach ($this->allInitializers + $envSpecificInitializers as $initializer) {
            $initializer->execute($this);
        }
    }

}
