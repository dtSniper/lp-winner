<?php
require_once('vendor/autoload.php');
include_once('initiateScript.php');

$f3->set( 'DEBUG', 3 );
$f3->set( 'LOCALES', 'dict/' );

/*
 * Error Handler
 */
//if ($f3->get( "DEBUG" ) == 0) {
//    $f3->set( 'ONERROR',
//        function ($f3) {
//            $page = new \lpwinner\pages\ErrorHandler( $f3 );
//            $page->handleError();
//        }
//    );
//}

/*
 * Session
 */
$sess = new  DB\SQL\Session( $db, 'sessions', TRUE, function ($session) {
    return true; // deactivate default behaviour
} );
if (!$f3->get( "SESSION.csrf" )) {
    $f3->CSRF = $sess->csrf();
    $f3->copy( 'CSRF', 'SESSION.csrf' );
}
\lpwinner\JSLoader::addScriptConst( "csrfToken", $f3->get( 'SESSION.csrf' ) );

$error = $f3->get( "SESSION.error" );
if ($error != null) {
    $f3->set( "SESSION.error", null );
    $f3->set( "ERROR_MESSAGE", $error );
}
$success = $f3->get( "SESSION.success" );
if ($success != null) {
    $f3->set( "SESSION.success", null );
    $f3->set( "SUCCESS", $success );
}

/*
 * Base routes
 */

$f3->route( "GET|HEAD @home: /", function ($f3) {
    $f3->set( "content", \Template::instance()->render( "template/main/home.html" ) );
    echo Template::instance()->render( "template/site.html" );
} );

$f3->route( "GET|HEAD @imprint: /imprint", function ($f3) {
    header( "Location: https://challenge-lock.com/imprint/" );
} );

$f3->route( "GET|HEAD @privacy: /privacy", function ($f3) {
    header( "Location: https://challenge-lock.com/1100-2/" );
} );


$f3->route( "POST /serials/add", "lpwinner\\pages\\MpSerials->addSerials" );
$f3->route( "GET|HEAD @blacklist: /blacklist", "lpwinner\\pages\\Blacklist->addForm" );
$f3->route( "POST /blacklist", "lpwinner\\pages\\Blacklist->addBlacklist" );
$f3->route( "GET|HEAD @blacklistValidate: /blacklist/validate/@key", "lpwinner\\pages\\Blacklist->validateBlacklist" );


//ADMIN
$f3->route( "GET|HEAD @admin: /" . $f3->get( "ADMINURL" ), "lpwinner\\pages\\Admin->dashboard" );
$f3->route( "GET|HEAD|POST @adminLogin: /" . $f3->get( "ADMINURL" ) . "/login", "lpwinner\\pages\\Admin->login" );
$f3->route( "POST @adminLogout: /" . $f3->get( "ADMINURL" ) . "/logout", "lpwinner\\pages\\Admin->logout" );
$f3->route( "GET|HEAD|POST @adminBlacklist: /" . $f3->get( "ADMINURL" ) . "/blacklist", "lpwinner\\pages\\Admin->blacklist" );
$f3->route( "POST /" . $f3->get( "ADMINURL" ) . "/blacklist/remove", "lpwinner\\pages\\Admin->removeBlacklist" );
$f3->route( array("GET|HEAD|POST @serials: /" . $f3->get( "ADMINURL" ) . "/serials","GET|HEAD|POST @serials: /" . $f3->get( "ADMINURL" ) . "/serials/@page"), "lpwinner\\pages\\Admin->serials" );
$f3->route( "GET|HEAD|POST /" . $f3->get( "ADMINURL" ) . "/winners", "lpwinner\\pages\\Admin->winners" );

/*
 * Multi Language
 */
$f3->config( "include/multilang.cfg" );
$mlglobans   = $f3->get( "MULTILANG.global" );
$mlglobans[] = $f3->get( "ADMINURL" );
$f3->set( "MULTILANG.global", $mlglobans );
$mlglobans   = $f3->get( "excludedRedirects" );
$mlglobans[] = '/' . $f3->get( "ADMINURL" );
$f3->set( "MULTILANG.global", $mlglobans );
$ml = \thirdparty\ExtendedMultilang::instance( "ML" );
$f3->set( 'ML', $ml );
$f3->set( 'ONREROUTE', function ($url, $permanent) use ($f3, $ml) {
    if (!$ml->isGlobal( $url ) && !in_array( $url, $f3->get( "excludedRedirects" ) )) {
        $f3->clear( 'ONREROUTE' );
        $ml->reroute( $url, $permanent );
    }
    else {
        return false;
    }
} );
//$f3->config( "include/globalRedirects.cfg" );

\lpwinner\JSLoader::addScriptConst( "language", $ml->current );

$f3->run();
exit();
?>
