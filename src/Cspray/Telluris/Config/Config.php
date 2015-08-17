<?php

/**
 * 
 * @license See LICENSE in source root
 * @version 1.0
 * @since   1.0
 */

namespace Cspray\Telluris\Config;

class Config {

    private $data;
    private $secrets;

    public function __construct(array $data, array $secrets = []) {
        $this->data = $data;
        $this->secrets = $secrets;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
        $val = $this->resolveDotNotation($this->data, $key, $default);
        return $this->parseForPossibleSecret($val);
    }

    private function resolveDotNotation(array $data, $key, $default = null) {
        $current = $data;
        $p = strtok($key, '.');

        while ($p !== false) {
            if (!isset($current[$p])) {
                return $default;
            }
            $current = $current[$p];
            $p = strtok('.');
        }

        return $current;
    }

    private function parseForPossibleSecret($val) {
        $secretStrLen = 7;
        if (substr($val, 0, $secretStrLen) === 'secret(' && $val[strlen($val) -1] === ')') {
            $secretKey = substr(substr($val, 0, -1), $secretStrLen);
            $val = $this->resolveDotNotation($this->secrets, $secretKey);
        }
        return $val;
    }

}
