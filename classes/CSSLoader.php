<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 12:59
 */

namespace lpwinner;


class CSSLoader {
    public static $neededCSS = array();
    public static $faSolid = false;
    public static $faRegular = false;
    public static $faBrands = false;
    public static $fa = false;
    public static $premium = false;

    private function __construct() {
    }

    public static function addCSS($cssPath) {
        if (in_array( $cssPath, self::$neededCSS )) {
            return false;
        }
        self::$neededCSS[] = $cssPath;
    }

    public static function setPremium($state) {
        if ($state === true || $state === false) {
            self::$premium = $state;
        }
    }

    public static function addFA($type) {
        switch ($type) {
            case 'solid':
                self::$faSolid = true;
                self::$fa      = true;
                break;
            case 'regular':
                self::$faRegular = true;
                self::$fa        = true;
                break;
            case 'brands':
                self::$faBrands = true;
                self::$fa       = true;
                break;
        }
    }

    public static function getCSS() {
        $string = "";
        if (self::$fa) {
            if (self::$faSolid) {
                //$string .= '<link rel="stylesheet" href="/fonts/fa/solid.min.css" media="print" onload="this.onload=null;this.media=\'all\'">';
                //$string .= '<noscript><link rel="stylesheet" href="/fonts/fa/solid.min.css"></noscript>';
            }
            if (self::$faRegular) {
                $string .= '<link rel="stylesheet" href="/fonts/fa/regular.min.css" media="print" onload="this.onload=null;this.media=\'all\'" importance="low">';
                $string .= '<noscript><link rel="stylesheet" href="/fonts/fa/regular.min.css"></noscript>';
            }
            /*if(self::$faBrands) {
                $string .= '<link rel="stylesheet" href="/fonts/fa/brands.min" media="print" onload="this.onload=null;this.media=\'all\'">';
                $string .= '<noscript><link rel="stylesheet" href="/fonts/fa/brands.min.css"></noscript>';
            }*/
            $string .= '<link rel="stylesheet" href="/fonts/fa/fontawesome.min.css">';
        }
        foreach (self::$neededCSS as $value) {
            $string .= '<link href="/css/' . $value . '" rel="stylesheet">';
        }
        return $string;
    }

    public static function getPreload() {
        $string = "";
        if (self::$fa) {
            if (self::$faSolid) {
                $string .= '<link rel="preload" href="/fonts/webfonts/fa-solid-900.woff2" crossorigin="anonymous" as="font" type="font/woff2" importance="low">';
                //$string .= '<link rel="preload" href="/fonts/fa/solid.min.css" as="style">';
            }
            if (self::$faRegular) {
                $string .= '<link rel="preload" href="/fonts/webfonts/fa-regular-400.woff2" crossorigin="anonymous" as="font" type="font/woff2" importance="low">';
            }
            $string .= '<link rel="preload" href="/fonts/fa/fontawesome.min.css" as="style" importance="low">';
        }
        return $string;
    }
}