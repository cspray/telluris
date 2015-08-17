<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\Telluris\Config;

class ArrayStorage implements Storage {

    private $config;
    private $secrets;

    public function __consruct(array $config, array $secrets = null) {
        $this->config = $config;
        $this->secrets = $secrets;
    }

    /**
     *
     *
     * @param string $env
     * @return Config|false
     */
    public function getConfigForEnv($env) {
        return isset($this->config[$env]) ? $this->config[$env] : [];
    }

}