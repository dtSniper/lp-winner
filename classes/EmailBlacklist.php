<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 14:55
 */

namespace lpwinner;

use lpwinner\exceptions\EmailBlacklistedException;
use lpwinner\exceptions\InvalidEmailAddressException;

class EmailBlacklist extends \DB\SQL\Mapper {

    public function __construct(\Base $f3) {
        parent::__construct( $f3->get( "DB" ), 'email_blacklist' );
    }

    public static function isBlacklisted(string $address): bool {
        return self::getEntry( $address ) !== null;
    }

    private static function hashEmail(string $address): string {
        $address = strtolower( $address );
        return hash( "sha256", $address );
    }

    public static function getEntry(string $address) {
        if (!filter_var( $address, FILTER_VALIDATE_EMAIL )) {
            throw new InvalidEmailAddressException();
        }
        $f3        = \Base::instance();
        $blacklist = new self( $f3 );
        if (!$blacklist->load( array("address = ? AND validation_key IS NULL", self::hashEmail( $address )) )) {
            return null;
        }
        return $blacklist;
    }

    public static function addToBlacklist(string $address): bool {
        if (!filter_var( $address, FILTER_VALIDATE_EMAIL )) {
            throw new InvalidEmailAddressException();
        }
        $address = strtolower( $address );
        if (self::isBlacklisted( $address )) {
            throw new EmailBlacklistedException();
        }
        $f3        = \Base::instance();
        $blacklist = new self( $f3 );
        $blacklist->load( array("address = ? AND validation_key IS NOT NULL", $address) );
        $blacklist->address        = $address;
        $blacklist->validation_key = Utility::generateID( 32 );
        $blacklist->keytime        = time();
        $blacklist->save();

        $f3->set( "blacklistKey", $blacklist->validation_key );
        echo \Template::instance()->render( "template/email/blacklistValidation.html" );
        $textToBeSend = \Template::instance()->render( "template/email/blacklistValidation.html" );
        \lpwinner\Utility::sendEmail( $f3, $f3->get( "MAIL_TO" ), '"Lockpick Winner" <' . $f3->get( "MAIL_TO" ) . '>', $address, $address, $f3->get( "email.blacklistValid.subject" ), $textToBeSend );
        return true;
    }

    public static function validate(string $key): bool {
        $f3        = \Base::instance();
        $blacklist = new self( $f3 );
        if (!$blacklist->load( array("validation_key = ? AND keytime >= ?", $key, time() - 1800) )) {
            return false;
        }
        MPSerialnumber::removeAddress( $blacklist->address );
        $blacklist->validation_key = null;
        $blacklist->address        = self::hashEmail( $blacklist->address );
        $blacklist->save();
        return true;
    }

}