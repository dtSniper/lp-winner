<?php
/**
 * User: Sascha Wendt
 * Date: 09.12.2020
 * Time: 20:14
 */

namespace lpwinner\pages;


use lpwinner\EmailBlacklist;
use lpwinner\MPSerialnumber;

class Admin extends SecuredPages {
    public function __construct(\Base $f3, $loginReroute = "@login") {
        parent::__construct( $f3, array("/" . $f3->get( 'ADMINURL' ) . "/login"), array("@adminLogin"), "@adminLogin" );
    }

    protected function checkUserAccess() {
        return $this->f3->exists( "SESSION.admin" );
    }

    public function dashboard(\Base $f3) {
        $mps = new MPSerialnumber( $f3 );
        $bl  = new EmailBlacklist( $f3 );
        $f3->set( "mpsCount", $mps->count() );
        $f3->set( "blCount", $bl->count( array("validation_key IS NULL") ) );
        $f3->set( "content", \Template::instance()->render( "template/admin/dashboard.html" ) );
        echo \Template::instance()->render( "template/site.html" );
    }

    public function login(\Base $f3) {
        if ($this->checkUserAccess()) {
            $this->f3->reroute( "@admin" );
            return;
        }
        if ($f3->exists( "POST.username" )) {
            if (!$this->checkCSRF( "@adminLogin" )) {
                return;
            }
            $user     = $this->getPostString( "username" );
            $password = $this->getPostString( "password" );
            if ($f3->exists( "ADMINS.$user" )) {
                $hashedPass = $f3->get( "ADMINS.$user" );
                if (password_verify( $password, $hashedPass )) {
                    $this->f3->set( "SESSION.admin", $user );
                    $this->f3->reroute( "@admin" );
                    return;
                }
            }
            //var_dump( password_hash( $password, PASSWORD_BCRYPT ) );
            $f3->set( "ERROR_MESSAGE", array("Username or Password wrong!") );
        }
        $f3->set( "content", \Template::instance()->render( "template/admin/login.html" ) );
        echo \Template::instance()->render( "template/site.html" );
    }

    public function logout(\Base $f3) {
        if (!$this->checkCSRF( "@admin" )) {
            return;
        }
        $this->f3->clear( "SESSION.admin" );
        $this->f3->reroute( "@adminLogin" );
    }

    public function blacklist(\Base $f3) {
        if ($f3->exists( "POST.email" )) {
            if (!$this->checkCSRF( "@adminBlacklist" )) {
                return;
            }
            try {
                $address = $this->getPostString( "email" );
                $address = strtolower( $address );
                $entry   = EmailBlacklist::getEntry( $address );
                if ($entry == null) {
                    $f3->set( "ERROR_MESSAGE", array("No Blacklist Entry for this email found!") );
                }
                else {
                    $f3->set( "entry", $entry );
                }
            }
            catch (\lpwinner\exceptions\LpwinnerException $exception) {
                $f3->set( "ERROR_MESSAGE", array($exception->getMessage()) );
            }
        }
        $f3->set( "content", \Template::instance()->render( "template/admin/blacklist/index.html" ) );
        echo \Template::instance()->render( "template/site.html" );
    }

    public function removeBlacklist(\Base $f3) {
        if (!$this->checkCSRF( "@adminBlacklist" )) {
            return;
        }
        try {
            $address = $this->getPostString( "address" );
            $address = strtolower( $address );
            $entry   = EmailBlacklist::getEntry( $address );
            if ($entry === null) {
                $this->addStandardError( "tryAgain" );
                $this->f3->reroute( "@adminBlacklist" );
                return;
            }
            $entry->erase();
            $this->addSuccess( "Blacklist entry removed!" );
            $this->f3->reroute( "@adminBlacklist" );
        }
        catch (\lpwinner\exceptions\LpwinnerException $exception) {
            $this->addStandardError( $exception->getError() );
            $this->f3->reroute( "@adminBlacklist" );
        }
    }

    public function serials(\Base $f3, $params) {
        $page = 1;
        if (isset( $params['page'] )) {
            $page = intval( $params['page'] );
            if ($page < 1) {
                $page = 1;
            }
        }
        $page--;
        $filter = array();
        if ($this->f3->exists( "GET.search" )) {
            $search = trim( strip_tags( $this->f3->get( "GET.search" ) ) );
            if (!empty( $search )) {
                $search = "%$search%";
                $filter = array("serial like ? OR email like ?", $search, $search);
            }
        }
        $options = array(
            'order' => 'id DESC'
        );

        $serials     = new MPSerialnumber( $f3 );
        $serialsPage = $serials->paginate( $page, 5, $filter, $options );
        $f3->set("serialPage", $serialsPage);
        $f3->set( "content", \Template::instance()->render( "template/admin/serials/index.html" ) );
        echo \Template::instance()->render( "template/site.html" );
    }
}