<?php
require_once('vendor/autoload.php');
include_once('initiateScript.php');

$f3->set( 'DEBUG', 3 );
$f3->set( 'LOCALES', 'dict/' );

/*
 * Error Handler
 */
if ($f3->get( "DEBUG" ) == 0) {
    $f3->set( 'ONERROR',
        function ($f3) {
            $page = new \mapban\pages\ErrorHandler( $f3 );
            $page->handleError();
        }
    );
}

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

/*
 * Base routes
 */

$f3->route( "GET @home: /", function ($f3) {
    $f3->set( "content", Template::instance()->render( "template/main/home.html" ) );
    echo Template::instance()->render( "template/site.html" );
} );


/*
 * Multi Language
 */
$f3->config( "include/multilang.cfg" );
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
\lpwinner\JSLoader::setMatomoDimensionLanguage( $ml->displayLanguage( $ml->current ) );

$f3->run();
exit();
?>
