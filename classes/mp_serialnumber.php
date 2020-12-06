<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 14:55
 */

namespace lpwinner;


use lpwinner\exceptions\EmailBlacklistedException;
use lpwinner\exceptions\InvalidEmailAdressException;

class mp_serialnumber extends \DB\SQL\Mapper {

    public function __construct(\Base $f3) {
        parent::__construct( $f3->get( "DB" ), 'mp_serialnumbers' );
    }

    public static function addSerialToEmail(string $address, string $serial): mp_serialnumber {
        if (!filter_var( $address, FILTER_VALIDATE_EMAIL )) {
            throw new InvalidEmailAdressException();
        }
        if (EmailBlacklist::isBlacklisted( $address )) {
            throw new EmailBlacklistedException();
        }
        $f3  = \Base::instance();
        $mps = new self( $f3 );
        if (!$mps->load( array("email = ? AND serial = ?", $address, $serial) )) {
            $mps->email  = $address;
            $mps->serial = $serial;
            $mps->save();
        }
        return $mps;
    }

}