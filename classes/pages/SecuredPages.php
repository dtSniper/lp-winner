<?php
/**
 * User: Sascha Wendt
 * Date: 09.12.2020
 * Time: 20:06
 */

namespace lpwinner\pages;


abstract class SecuredPages extends Pages {
    private $ignore;
    private $ignoreAlias;
    private $loginReroute;

    protected function __construct(\Base $f3, array $ignore = array(), $ignoreAlias = array(), $loginReroute = "@login") {
        $this->ignore       = $ignore;
        $this->ignoreAlias  = $ignoreAlias;
        $this->loginReroute = $loginReroute;
        parent::__construct( $f3 );
    }

    protected function checkUserAccess() {
        return true;
    }

    public function beforeRoute() {
        if (!in_array( $this->f3->get( "ALIAS" ), $this->ignoreAlias ) && !in_array( $this->getPath(), $this->ignore )) {
            if (!$this->checkUserAccess()) {
                $this->f3->set( "SESSION.error", "no-access" );
                $this->f3->reroute( $this->loginReroute );
                return;
            }
        }
    }
}