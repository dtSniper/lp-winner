<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 12:58
 */

namespace lpwinner\exceptions;

class LpwinnerException extends \Exception {
    private $error;

    public function __construct($message, $error) {

        $this->error   = $error;
        $this->message = $message;
    }

    public function getError() {
        return $this->error;
    }

}