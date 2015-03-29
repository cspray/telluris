<?php
/**
 * Created by PhpStorm.
 * User: cspray
 * Date: 3/29/15
 * Time: 11:43
 */

namespace Telluris\Config;


interface FileContentParser {

    /**
     * Return a, possibly, multi-dimensional array representing the configuration for
     * the contents passed; if the contents could not be parsed for some reason return
     * false.
     *
     * @param string $contents
     * @return array|false
     */
    public function parse($contents);

}