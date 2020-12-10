<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 13:34
 */

namespace lpwinner\pages;


use lpwinner\Utility;

abstract class Pages {
    protected $f3;

    protected function __construct(\Base $f3) {
        $this->f3 = $f3;
    }

    protected function getPath($ignoreLanguage = true) {
        return \lpwinner\Utility::getPath( $this->f3, $ignoreLanguage );
    }

    public function checkCSRF($reroute = "@home"): bool {
        if (!$this->f3->exists( 'POST.csrfToken' ) || !$this->f3->exists( 'SESSION.csrf' )) {
            $this->csrfError( $reroute );
            return false;
        }
        $csrfToken = $this->f3->get( 'POST.csrfToken' );
        $csrf      = $this->f3->get( 'SESSION.csrf' );
        if (empty( $csrfToken ) || empty( $csrf ) || $csrfToken != $csrf) {
            $this->csrfError( $reroute );
            return false;
        }
        return true;
    }

    private function csrfError($reroute = "@home") {
        $this->addStandardError( "tryAgain" );
        $this->f3->reroute( $reroute );
    }

    public function rerouteBack($fallback) {
        $referer = $this->f3->get( 'SERVER.HTTP_REFERER' );
        if (preg_match( '/^https?:\/\/' . preg_quote( $this->f3->HOST, '/' ) . '\//', $referer )) {
            // The referer URL belongs to the website domain
            $this->f3->reroute( $referer );
        }
        else {
            $this->f3->reroute( $fallback );
        }
    }

    public function getPostString(string $variablename, bool $emptyToNull = false, bool $htmlEntityEncode = false) {
        return Utility::getPostString( $this->f3, $variablename, $emptyToNull, $htmlEntityEncode );
    }

    public function getPostArray(string $variablename, callable $valueMapper = null, bool $uniqueValues = false, bool $emptyToNull = false) {
        $array = $this->f3->get( "POST." . $variablename );
        if (!is_array( $array )) {
            return null;
        }
        if ($array !== null) {
            if ($valueMapper !== null) {
                $array = array_map( $valueMapper, $array );
            }
            if ($uniqueValues) {
                $array = array_unique( $array );
            }
            if (empty( $array ) && $emptyToNull) {
                return null;
            }
        }
        return $array;
    }

    public function getPostInt(string $variablename, bool $belowZeroToNull = false, $equalZeroToNull = false) {
        $value = intval( $this->f3->get( "POST." . $variablename ) );
        if ($belowZeroToNull && $value < 0) {
            $value = null;
        }
        if ($equalZeroToNull && $value === 0) {
            $value = null;
        }
        return $value;
    }

    public function addError(string $errorMessage) {
        $errors = $this->f3->get( "SESSION.error" );
        if ($errors === null) {
            $errors = array();
        }
        $errors[] = $errorMessage;
        $this->f3->set( "SESSION.error", $errors );
    }

    public function addStandardError(string $error) {
        $this->addError( $this->f3->get( "errormsg." . $error ) );
    }

    public function addSuccess(string $successMessage) {
        $successes = $this->f3->get( "SESSION.success" );
        if ($successes === null) {
            $successes = array();
        }
        $successes[] = $successMessage;
        $this->f3->set( "SESSION.success", $successes );
    }

    public function addStandardSuccess(string $success, array $arrgs = null) {
        $this->addSuccess( $this->f3->get( "successmsg." . $success, $arrgs ) );
    }
}