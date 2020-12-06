<?php

/**
 * Extends Multilang Libary
 */

namespace thirdparty;

class ExtendedMultilang extends Multilang {

    protected $cookieVar;
    private $f3;

    function __construct($cookieVar = null) {
        $this->f3        = \Base::instance();
        $this->cookieVar = "COOKIE." . $cookieVar;
        parent::__construct();
    }

    //! Detects the current language
    protected function detect($uri = NULL) {
        parent::detect( $uri );
        if ($this->auto) { //Save Language for Max 14 days.
            if (!$this->f3->exists( $this->cookieVar )) {
                $this->f3->set( $this->cookieVar, $this->current, (3600 * 24 * 14) );
            }
            if ($this->f3->get( $this->cookieVar ) != $this->current && in_array( $this->f3->get( $this->cookieVar ), $this->languages() )) {
                $this->current = $this->f3->get( $this->cookieVar );
                //$this->auto = false;
                $this->f3->set( 'LANGUAGE', $this->languages[$this->current] );
            }
        }
        else {
            $this->f3->set( $this->cookieVar, $this->current, (3600 * 24 * 14) );
        }
    }

    public function isGlobal($name) {
        return (in_array( $name, $this->global_aliases ) || (isset( $this->global_regex ) && preg_match( $this->global_regex, $name )));
    }

}

?>