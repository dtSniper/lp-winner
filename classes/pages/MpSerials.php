<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 14:55
 */

namespace lpwinner\pages;


use lpwinner\MPSerialnumber;

class MpSerials extends Pages {

    public function __construct($f3) {
        parent::__construct( $f3 );
    }

    public function addSerials($f3) {
        $email = $this->getPostString("email");
        $serials = $this->getPostArray("serials", "intval", true, true);

        try {
            $array = MPSerialnumber::addSerialsToEmail( $email, $serials );
            $int   = count( $array );
            echo "We added $int Serialnumbers to your email. Please check for the confirmation email you should have received.";
        }
        catch (\lpwinner\exceptions\LpwinnerException $exception) {
            $this->f3->set( "SESSION.error", $exception->getError() );
            $this->rerouteBack( "@home" );
            return;
        }
    }
}