<?php

/**
 * Abstracts the storage of environment configurations to allow an application
 * to have its own strategy for storing environment configurations.
 * 
 * @license See LICENSE in source root
 * @version 1.0
 * @since   1.0
 */

namespace Cspray\Telluris\Config;


interface Storage {

    /**
     *
     *
     * @param string $env
     * @return Config|false
     */
    public function getConfigForEnv($env);

}
