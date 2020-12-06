<?php

use DB\SQL;

$f3 = Base::instance();
$f3->set( 'DEBUG', 3 );

$f3->set( "CACHE", TRUE );
if ((float)PCRE_VERSION < 7.9) {
    trigger_error( 'PCRE version is out of date' );
}
$f3->set( "TEMP", "/tmp/" );
$port = $f3->get( "PORT" );
$f3->set( "FULL_URL", $f3->get( "SCHEME" ) . '://' . $f3->get( "HOST" ) . ($port && !in_array( $port, [80, 443] ) ? (':' . $port) : '') . $f3->get( "BASE" ) );

$f3->config( 'config.cfg' );
$MYSQL = $f3->get( "MYSQL" );

$db = new SQL( "mysql:host=$MYSQL[SERVER];port=$MYSQL[PORT];dbname=$MYSQL[DB]", "$MYSQL[USER]", "$MYSQL[PASSWORD]" );
$f3->set( "DB", $db );
?>