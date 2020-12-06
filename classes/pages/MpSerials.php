<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 14:55
 */

namespace lpwinner\pages;


class MpSerials extends Pages {

    public function __construct($f3) {
        parent::__construct( $f3 );
    }

    public function addSerials($f3) {
        $email = $this->getPostString("email");
    }
}