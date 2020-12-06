<?php
/**
 * User: Sascha Wendt
 * Date: 06.11.2020
 * Time: 12:15
 */

namespace lpwinner;


class JSLoader {
    public static $scriptVars = array();
    //public static $scriptConst = array();
    public static $neededScripts = array();

    private function __construct() {
    }

    public static function addScript($scriptPath) {
        if (in_array( $scriptPath, self::$neededScripts )) {
            return false;
        }
        self::$neededScripts[] = $scriptPath;
    }

    public static function getScripts() {
        return self::$neededScripts;
    }

    public static function outputScripts($jsVersions) {
        $html = '';
        foreach (self::$neededScripts as $value) {
            if (!empty( $jsVersions[$value] )) {
                $value = $jsVersions[$value];
            }
            $html .= '<script src="/js/' . $value . '" defer></script>';
        }
        return $html;
    }

    public static function addScriptVar($key, $value) {
        self::$scriptVars[$key] = $value;
    }

    public static function getScriptVars() {
        $string = "";
        foreach (self::$scriptVars as $key => $value) {
            if (gettype( $value ) == "NULL") {
                continue;
            }
            $key    = htmlspecialchars( $key );
            $string .= "var " . $key;
            switch (gettype( $value )) {
                case "array":
                    for ($i = 0; $i < count( $value ); $i++) {
                        $value[$i] = htmlspecialchars( $value[$i] );
                    }
                    $string .= "=JSON.parse('" . json_encode( $value ) . "')";
                    break;
                case "string":
                    $value = htmlspecialchars( $value );
                    if (strpos( $value, "new Array(" ) === 0) {
                        $string .= '=' . $value;
                    }
                    else {
                        $string .= '="' . $value . '"';
                    }
                    break;
                default:
                    $string .= '=' . $value;
                    break;
            }
            $string .= ";";
        }
        return $string;
    }

    public static function addScriptConst($key, $value) {
        self::addScriptVar( $key, $value ); // we don't ship es6 code yet, so there is no `const`.
        // self::$scriptConst[$key] = $value;
    }

    public static function getScriptConst() {
        return '';
        /*
        $string = "";
        foreach (self::$scriptConst as $key => $value) {
            $string .= "const ". $key;
            switch (gettype ($value)) {
                case "string":
                    if(strpos($value, "new Array(") === 0) {
                        $string .= ' = ' . $value;
                    }
                    else {
                        $string .= ' = "' . $value . '"';
                    }
                    break;
                default:
                    $string .= ' = '.$value;
                    break;
            }
            $string .= ";\n";
        }
        return $string;*/
    }

    public static function outputVars() {
        return '<script>' . self::getScriptVars() . self::getScriptConst() . '</script>';
    }
}