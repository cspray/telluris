<?php

/**
 * 
 * @license See LICENSE in source root
 * @version 1.0
 * @since   1.0
 */

namespace Cspray\Telluris\Config;

use Cspray\Telluris\Exception\ConfigNotFoundException;

class SingleFileStorage implements Storage {

    private $configFilePath;
    private $secretsFilePath;

    public function __construct($configFilePath, $secretsFilePath = null) {
        $configFilePath = (string) $configFilePath;
        $this->validateConfigFileExists($configFilePath);

        $this->configFilePath = (string) $configFilePath;
        $this->secretsFilePath = (string) $secretsFilePath;
    }

    private function validateConfigFileExists($configFilePath) {
        if (!file_exists($configFilePath)) {
            $msg = 'Could not find a file located at "%s"';
            throw new ConfigNotFoundException(sprintf($msg, $configFilePath));
        }
    }

    /**
     * @param string $env
     * @return Config
     */
    public function getConfigForEnv($env) {
        $rawConfig = $this->fetchConfig();
        if (!isset($rawConfig[$env])) {
            return false;
        }

        return new Config($rawConfig[$env], $this->fetchSecretConfig());
    }

    private function fetchConfig() {
        $contents = file_get_contents($this->configFilePath);
        return json_decode($contents, true);
    }

    private function fetchSecretConfig() {
        $config = [];
        if ($this->secretsFilePath) {
            $contents = file_get_contents($this->secretsFilePath);
            $config = json_decode($contents, true);
        }

        return $config;
    }

}
