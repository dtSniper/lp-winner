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
        $email   = $this->getPostString( "email" );
        $serials = $this->getPostArray( "serials", "intval", true, true );

        try {
            $array = MPSerialnumber::addSerialsToEmail( $email, $serials );
            $int   = count( $array );
            $this->addStandardSuccess( "newSerialsAdded", array($int) );
            $this->rerouteBack( "@home" );
        }
        catch (\lpwinner\exceptions\LpwinnerException $exception) {
            $this->addStandardError( $exception->getError() );
            $this->rerouteBack( "@home" );
            return;
        }
    }
}