<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Telluris;

class CallbackInitializer implements Initializer {

    private $cb;

    public function __construct(callable $cb) {
        $this->cb = $cb;
    }

    public function execute(Environment $environment) {
        $cb = $this->cb;
        return $cb($environment);
    }

}