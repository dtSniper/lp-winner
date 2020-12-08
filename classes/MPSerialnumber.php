<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 14:55
 */

namespace lpwinner;


use lpwinner\exceptions\EmailBlacklistedException;
use lpwinner\exceptions\InvalidEmailAddressException;

class MPSerialnumber extends \DB\SQL\Mapper {

    public function __construct(\Base $f3) {
        parent::__construct( $f3->get( "DB" ), 'mp_serialnumbers' );
    }

    public static function addSerialsToEmail(string $address, array $serials): array {
        if (!filter_var( $address, FILTER_VALIDATE_EMAIL )) {
            throw new InvalidEmailAddressException();
        }
        if (EmailBlacklist::isBlacklisted( $address )) {
            throw new EmailBlacklistedException();
        }
        $array = [];
        if (count( $serials ) === 0) {
            return $array;
        }
        $f3  = \Base::instance();
        $mps = new self( $f3 );
        foreach ($serials as $serial) {
            if (!$mps->load( array("email = ? AND serial = ?", $address, $serial) )) {
                $mps->email  = $address;
                $mps->serial = $serial;
                $mps->save();
                $array[] = $serial;;
            }
        }
        if (count( $array ) !== 0) {
            $f3->set( "numbers", $array );
            $textToBeSend = \Template::instance()->render( "template/email/addedSerials.html" );
            \lpwinner\Utility::sendEmail( $f3, $f3->get( "MAIL_TO" ), '"Lockpick Winner" <' . $f3->get( "MAIL_TO" ) . '>', $address, $address, $f3->get( "email.addedSerials.subject" ), $textToBeSend );
        }
        return $array;
    }

    public static function removeAddress(string $address): bool {
        $mps = new self( \Base::instance() );
        if (!$mps->load( array("email = ?", $address) )) {
            return false;
        }
        foreach ($mps->query as $entry) {
            $entry->erase();
        }
        return true;
    }

}