<?php
/**
 * Created by PhpStorm.
 * User: cspray
 * Date: 3/29/15
 * Time: 11:26
 */

namespace Cspray\Telluris\Config;

use Cspray\Telluris\Exception\ConfigNotFoundException;

class MultipleFileStorage implements Storage {

    private $configDir;
    private $secretsFileName;

    public function __construct($configDir, $secretsFileName = 'secrets') {
        $configDir = (string) $configDir;
        $this->validateConfigDirExists($configDir);
        $this->configDir = $configDir;
        $this->secretsFileName = (string) $secretsFileName;
    }

    private function validateConfigDirExists($dir) {
        if (!is_dir($dir)) {
            $msg = "Could not find a directory located at \"{$dir}\"";
            throw new ConfigNotFoundException($msg);
        }
    }

    /**
     *
     *
     * @param string $env
     * @return Config|false
     */
    public function getConfigForEnv($env) {
        $config = $this->fetchConfig($env);
        if (!is_array($config)) {
            return false;
        }

        return new Config($config, $this->fetchSecretConfig($env));
    }

    private function fetchConfig($env) {
        $filePath = $this->configDir . '/' . $env . '.json';
        if (!file_exists($filePath)) {
            return false;
        }

        $contents = file_get_contents($filePath);
        return json_decode($contents, true);
    }

    private function fetchSecretConfig() {
        $config = [];
        $filePath = $this->configDir . '/' . $this->secretsFileName . '.json';
        if (file_exists($filePath)) {
            $contents = file_get_contents($filePath);
            $config = json_decode($contents, true);
        }

        return $config;
    }

}