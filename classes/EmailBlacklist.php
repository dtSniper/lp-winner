<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 14:55
 */

namespace lpwinner;

class EmailBlacklist extends \DB\SQL\Mapper {

    public function __construct(\Base $f3) {
        parent::__construct( $f3->get( "DB" ), 'email_blacklist' );
    }

    public static function isBlacklisted(string $address): bool {
        if (!filter_var( $address, FILTER_VALIDATE_EMAIL )) {
            throw new InvalidEmailAdressException();
        }
        $f3        = \Base::instance();
        $blacklist = new self( $f3 );
        if (!$blacklist->load( array("address = ?", $address) )) {
            return false;
        }
        return true;
    }

    public static function addToBlacklist(string $address): bool {
        if (!filter_var( $address, FILTER_VALIDATE_EMAIL )) {
            throw new InvalidEmailAdressException();
        }
        if (self::isBlacklisted( $address )) {
            return false;
        }
        $f3             = \Base::instance();
        $email          = new self( $f3 );
        $email->address = $address;
        $email->save();
        return true;
    }

}