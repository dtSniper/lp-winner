<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 14:55
 */

namespace lpwinner\pages;


use lpwinner\EmailBlacklist;

class Blacklist extends Pages {

    public function __construct($f3) {
        parent::__construct( $f3 );
    }

    public function addForm($f3) {
        $f3->set( "content", \Template::instance()->render( "template/blacklist/add.html" ) );
        echo \Template::instance()->render( "template/site.html" );
    }

    public function addBlacklist($f3) {
        if(!$this->checkCSRF("@blacklist")) return;
        $email   = $this->getPostString( "email" );
        try {
            EmailBlacklist::addToBlacklist( $email );
            $this->addStandardSuccess( "blacklist1" );
            $this->f3->reroute( "@blacklist" );
        }
        catch (\lpwinner\exceptions\LpwinnerException $exception) {
            $this->addStandardError( $exception->getError() );
            $this->f3->reroute( "@blacklist" );
            return;
        }
    }

    public function validateBlacklist($f3, $params) {
        try {
            $key = strip_tags($params['key']);
            if(EmailBlacklist::validate( $key )) {
                $this->addStandardSuccess( "blacklist2" );
            }
            else {
                $this->addStandardError( "tryAgain" );
            }
            $this->f3->reroute( "@blacklist" );
        }
        catch (\lpwinner\exceptions\LpwinnerException $exception) {
            $this->addStandardError( $exception->getError() );
            $this->f3->reroute( "@blacklist" );
            return;
        }
    }

}