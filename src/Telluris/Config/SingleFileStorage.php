<?php

/**
 * 
 * @license See LICENSE in source root
 * @version 1.0
 * @since   1.0
 */

namespace Telluris\Config;

use Telluris\Exception\ConfigNotFoundException;

class SingleFileStorage implements Storage {

    private $configFilePath;

    public function __construct($configFilePath) {
        $configFilePath = (string) $configFilePath;
        if (!file_exists($configFilePath)) {
            $msg = 'Could not find a file located at "%s"';
            throw new ConfigNotFoundException(sprintf($msg, $configFilePath));
        }
        $this->configFilePath = (string) $configFilePath;
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

        return new Config($rawConfig[$env]);
    }

    private function fetchConfig() {
        $contents = file_get_contents($this->configFilePath);
        $config = json_decode($contents, true);

        return $config;
    }

}
