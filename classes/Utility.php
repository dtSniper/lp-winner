<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 12:17
 */

namespace lpwinner;

class Utility {

    public static function generateID($length = 16, $addCharacters = null) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($addCharacters !== null && is_string( $addCharacters )) {
            $characters .= $addCharacters;
        }
        $id = '';

        for ($i = 0; $i < $length; $i++) {
            $id .= $characters[random_int( 0, strlen( $characters ) - 1 )];
        }

        return $id;
    }

    public static function isJson($string) {
        return is_string( $string ) && is_array( json_decode( $string, true ) ) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public static function getPath($f3, $ignoreLanguage = true) {
        if (!$f3->get( "FEATURES.MULTILANGUAGE" )) {
            return $f3->get( "PATH" );
        }
        return self::getPathEx( $f3->get( "PATH" ), $f3->get( 'ML' )->languages(), $ignoreLanguage );
    }

    public static function getPathEx($path, $languages, $ignoreLanguage = true) {
        if ($ignoreLanguage) {
            $pathParts = explode( '/', ltrim( $path, '/' ) );
            if (sizeof( $pathParts ) >= 1) {
                if (in_array( $pathParts[0], $languages )) {
                    unset( $pathParts[0] );
                    $path = implode( "/", $pathParts );
                    return "/" . $path;
                }
            }
        }
        return $path;
    }

    public static function isPathLocalized($path, array $languages) {
        $pathParts = explode( '/', ltrim( $path, '/' ) );
        if (sizeof( $pathParts ) >= 1) {
            if (in_array( $pathParts[0], $languages )) {
                return true;
            }
        }
        return false;
    }

    public static function localizePath($path, $alias, $params, $ml, $language) {
        if ($alias !== null) {
            return $ml->alias( $alias, $params, $language );
        }
        else if (in_array( $language, $ml->languages() )) {
            return '/' . $language . self::getPathEx( $path, $ml->languages() );
        }
        return $path;
    }

    public static function sendEmail($f3, $from, $replyTo, $toEmail, $toName, $subject, $text) {
        if ($toName === null) {
            $toName = "Mapban.gg";
        }

        $SMPTDATA = $f3->get( "SMPT" );
        $smtp     = new \SMTP ( $SMPTDATA['SERVER'], $SMPTDATA['PORT'], $SMPTDATA['SHEME'], $SMPTDATA['USER'], $SMPTDATA['PASSWORD'] );
        $smtp->set( 'MIME-Version', '1.0' );
        $smtp->set( 'Content-type', 'text/html; charset=utf-8' );
        $smtp->set( 'X-Mailer', 'PHP ' . phpversion() );
        $smtp->set( 'Errors-to', $f3->get( "MAIL_TO" ) );
        $smtp->set( 'To', '"' . $toName . '" <' . $toEmail . '>' );
        $smtp->set( 'From', '"' . $f3->get( "HOST" ) . '" <' . $from . '>' );
        $smtp->set( 'Reply-to', $replyTo );
        $smtp->set( 'Subject', $subject );
        $smtp->send( $text );
    }

    public static function sanitizeUrlFolder($folderName, $maxLength = 0) {
        $folderName = str_replace( ' ', '_', trim( $folderName ) );
        $folderName = preg_replace( '/[^A-Za-z0-9\_]/', '', $folderName );
        $folderName = strtolower( $folderName );
        if ($maxLength > 0) {
            $folderName = substr( $folderName, 0, $maxLength );
        }
        return $folderName;
    }

    public static function getImageBaseName($imagename) {
        return strip_tags( basename( trim( $imagename ) ) );
    }

    public static function getPostString(\Base $f3, string $variablename, bool $emptyToNull = false, bool $htmlEntityEncode = false) {
        $value = $f3->get( "POST." . $variablename );
        if ($htmlEntityEncode) {
            $value = htmlentities( $value );
        }
        $value = trim( strip_tags( $value ) );
        if (strlen( $value ) === 0 && $emptyToNull) {
            return null;
        }
        return $value;
    }

}