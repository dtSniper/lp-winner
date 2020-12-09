<?php
/**
 * User: Sascha Wendt
 * Date: 09.12.2020
 * Time: 20:14
 */

namespace lpwinner\pages;


class Admin extends SecuredPages {
    public function __construct(\Base $f3, $loginReroute = "@login") {
        parent::__construct( $f3, array("/" . $f3->get( 'ADMINURL' ) . "/login"), array("@adminLogin"), "@adminLogin" );
    }

    protected function checkUserAccess() {
        return $this->f3->exists( "SESSION.admin" );
    }

    public function dashboard(\Base $f3) {
        echo \Template::instance()->render( "template/site.html" );
    }

    public function login(\Base $f3) {
        if($this->checkUserAccess()) {
            $this->f3->reroute( "@admin" );
            return;
        }
        if ($f3->exists( "POST.username" )) {
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
        $this->f3->clear( "SESSION.admin" );
        $this->f3->reroute( "@adminLogin" );
    }
}