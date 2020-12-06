<?php
/**
 * User: Sascha Wendt
 * Date: 22.12.2018
 * Time: 18:20
 */


namespace lpwinner\pages;


class ErrorHandler extends Pages {

    public function __construct($f3) {
        parent::__construct( $f3 );
    }

    public function handleError() {
        $this->error();
    }

    public function error() {
        $text = "";
        if ($this->f3->get( 'AJAX' )) {
            $text = "[AJAX] ";
        }
        $path = $this->f3->get( "ERROR.code" );
        switch ($path) {
            case 404:
                $text .= $this->f3->get( "PATH" );
                break;

            default:
                $text .= "[" . $this->f3->get( "ERROR.code" ) . "] ";
                if (!file_exists( 'template/f3/errorhandle/' . $path . '.html' )) {
                    $path = 'default';
                }

                $errorFile = $this->saveErrorData();
                $text      .= $errorFile;
                $this->f3->set( "errorfile", $errorFile );
                break;
        }

        $logger = new \Log( 'logs/error_' . $path . '.log' );
        $logger->write( $text );
        if (!$this->f3->get( 'AJAX' )) {
            echo $path;
            $this->f3->set( "content", \Template::instance()->render( "template/errorhandle/' . $path . '.html" ) );
            echo \Template::instance()->render( "template/errorhandle/' . $path . '.html" );
            echo \Template::instance()->render( "template/site.html" );
        }
        else {
            $json['success'] = false;
            $json['error']   = $this->f3->get( "ERROR.code" );
            if ($this->f3->exists( "errorfile" )) {
                $json['errorfile'] = $this->f3->get( "errorfile" );
            }

            header( 'Content-Type: application/json' );
            echo json_encode( $json );
        }
    }

    public function saveErrorData() {
        $errorFile = uniqid( '', true ) . mt_rand( 1000, 9999 ) . ".html";

        $content = \Template::instance()->render( 'template/errorhandle/errortemplate.html' );
        if (!$this->f3->write( "./logs/html/" . $errorFile, $content )) {
            return "Couldn't create Error Log!";
        }
        return $errorFile;
    }

    // CSRF
    public function csrfError() {
        $this->f3->set( "SESSION.error", "csrf" );
        $this->f3->reroute( "/" );
    }

    public function csrfAjaxError() {
        $json                 = array();
        $json['success']      = false;
        $json['error']        = "csrf";
        $json['error_html'][] = \Template::instance()->render( "template/f3/error/" . $json['error'] . ".html" );

        header( 'Content-Type: application/json' );
        echo json_encode( $json );
    }
}