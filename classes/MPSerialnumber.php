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
        $address = strtolower( $address );
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
        $address = strtolower( $address );
        $mps     = new self( \Base::instance() );
        if (!$mps->load( array("email = ?", $address) )) {
            return false;
        }
        foreach ($mps->query as $entry) {
            $entry->erase();
        }
        return true;
    }

    public static function getById(int $id): ?self {
        $mps = new self( \Base::instance() );
        if (!$mps->load( array("id = ?", $id) )) {
            return null;
        }
        return $mps;
    }

    public static function notifyWinningSerials(array $serials): int {
        $f3 = \Base::instance();
        Config::saveValue( $f3, "lastWinnings", 0, Config::TYPE_INT );
        Config::saveValue( $f3, "lastWinners", 0, Config::TYPE_INT );
        Config::saveValue( $f3, "lastNotification", time(), Config::TYPE_INT );
        $query     = array();
        $parameter = array();
        foreach ($serials as $serial) {
            if ($serial === null) {
                continue;
            }
            $serial      = intval( $serial );
            $query[]     = "serial = ?";
            $parameter[] = $serial;
        }
        if (!empty( $parameter )) {
            $filter  = array_merge( array(implode( ' OR ', $query )), $parameter );
            $mps     = new self( $f3 );
            $serials = null;
            if (!$mps->load( $filter, array('order' => 'email ASC') )) {
                $serials = $mps->query;
            }
        }

        $winners     = 0;
        $winnerMails = array();
        $email       = null;
        $winNumbers  = array();

        if (is_array( $serials ) && !empty( $serials )) {
            foreach ($serials as $serial) {
                if ($email === null || strtolower( $email ) !== strtolower( $serial->email )) {
                    if ($email !== null) {
                        self::sendWinNotification( $f3, $email, $winNumbers );
                        $winNumbers = array();
                    }
                    $winners++;
                    $email         = strtolower( $serial->email );
                    $winnerMails[] = strtolower( $serial->email );
                }
                $winNumbers[] = $serial->serial;
            }
            self::sendWinNotification( $f3, $email, $winNumbers );
        }

        $totalWinnings = $f3->get( "CFG.totalWinnings" );
        $totalWinnings += count( $serials );
        $totalWinners  = $f3->get( "CFG.totalWinners" );
        $totalWinners  += $winners;
        Config::saveValue( $f3, "totalWinnings", $totalWinnings, Config::TYPE_INT );
        Config::saveValue( $f3, "lastWinnings", count( $serials ), Config::TYPE_INT );
        Config::saveValue( $f3, "lastWinners", $winners, Config::TYPE_INT );
        Config::saveValue( $f3, "totalWinners", $totalWinners, Config::TYPE_INT );
        self::sendLostNotification( $f3, $winnerMails );
        return $winners;
    }

    private static function sendWinNotification($f3, string $address, array $serials): bool {
        $f3->set( "numbers", $serials );
        $textToBeSend = \Template::instance()->render( "template/email/notification.html" );
        \lpwinner\Utility::sendEmail( $f3, $f3->get( "MAIL_TO" ), '"Lockpick Winner" <' . $f3->get( "MAIL_TO" ) . '>', $address, $address, "[WIN]Multipick Giveaway Notification", $textToBeSend );
        return true;
    }

    private static function sendLostNotification(\Base $f3, array $winners = null) {
        $textToBeSend = \Template::instance()->render( "template/email/lostNotification.html" );
        $query        = array();
        $parameter    = array();
        $results      = null;
        if (is_array( $winners ) && !empty( $winners )) {
            foreach ($winners as $winner) {
                if ($winner === null) {
                    continue;
                }
                $query[]     = "email != ?";
                $parameter[] = $winner;
            }
            $results = $f3->get( "DB" )->exec(
                "SELECT DISTINCT email FROM mp_serialnumbers WHERE " . implode( ' AND ', $query ),
                $parameter
            );
        }
        else {
            $results = $f3->get( "DB" )->exec( "SELECT DISTINCT email FROM mp_serialnumbers" );
        }
        if (is_array( $results ) && count( $results ) > 1) {
            foreach ($results as $looser) {
                \lpwinner\Utility::sendEmail( $f3, $f3->get( "MAIL_TO" ), '"Lockpick Winner" <' . $f3->get( "MAIL_TO" ) . '>', $looser['email'], $looser['email'], "[NOT WON]Multipick Giveaway Notification", $textToBeSend );
            }
        }
    }
}