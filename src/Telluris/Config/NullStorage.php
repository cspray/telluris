<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Telluris\Config;

class NullStorage implements Storage {

    /**
     * @param string $env
     * @return Config|false
     */
    public function getConfigForEnv($env) {
        return new Config([], []);
    }

}