<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 13:35
 */

namespace lpwinner\exceptions;


class InvalidEmailAddressException extends \lpwinner\exceptions\LpwinnerException {
    public function __construct() {
        parent::__construct( "Email Address is not valid!", "not-valid-email" );
    }
}