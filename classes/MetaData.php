<?php
/**
 * User: Sascha Wendt
 * Date: 06.12.2020
 * Time: 12:56
 */

namespace lpwinner;


class MetaData {
    public static $title = "Lockpick Winner";
    public static $pageTitle = "";
    public static $keywords = "";
    public static $description = "";
    public static $robots = "index,follow";

    public static $twitter_user = "";
    public static $twitter_card = "summary";
    public static $fb_id;

    public static $social_Data = array();

    private function __construct() {
    }

    public static function setPageTitle($pageTitle) {
        self::$pageTitle = $pageTitle;
    }

    public static function setTitle($title) {
        self::$title = $title;
    }

    public static function addKeywords($keywords) {
        if (strlen( self::$keywords ) == 0) {
            self::$keywords = $keywords;
        }
        else {
            if (substr( self::$keywords, -1 ) !== ',' && substr( $keywords, 0, 1 ) !== ',') {
                self::$keywords .= ",";
            }
            self::$keywords .= $keywords;
        }
    }

    public static function setDescription($description) {
        self::$description = $description;
    }

    public static function setRobots($robots) {
        self::$robots = $robots;
    }

    public static function setTwitterCard($card) {
        self::$twitter_card = $card;
    }

    public static function setSocialTitle($title) {
        self::$social_Data['title'] = $title;
    }

    public static function setSocialDescription($description) {
        self::$social_Data['description'] = $description;
    }

    public static function setSocialImage($image) {
        self::$social_Data['image'] = $image;
    }

    public static function setSocialImageAlt($imageAlt) {
        self::$social_Data['imageAlt'] = $imageAlt;
    }

    public static function getTitle() {
        if (strlen( self::$pageTitle ) != 0) {
            if (strlen( self::$title ) == 0) {
                return self::$pageTitle;
            }
            else {
                return self::$pageTitle . " &#8210; " . self::$title;
            }
        }
        return self::$title;
    }

    public static function getMeta() {
        $f3         = \Base::instance();
        $ml         = $f3->get( "ML" );
        $metaString = '';

        // SEO META
        if (strlen( self::$description ) != 0) {
            $metaString .= '<meta name="description" content="' . self::$description . '">';
        }
        if (strlen( self::$keywords ) != 0) {
            $metaString .= '<meta name="keywords" content="' . self::$keywords . '">';
        }
        if (strlen( self::$robots ) != 0) {
            $metaString .= '<meta name="robots" content="' . self::$robots . '">';
        }

        //FACEBOOK - TWITTER IDs
        if (strlen( self::$twitter_user ) != 0) {
            $metaString .= '<meta name="twitter:site" content="' . self::$twitter_user . '">';
            $metaString .= '<meta name="twitter:creator" content="' . self::$twitter_user . '">';
        }
        if (strlen( self::$fb_id ) != 0) {
            $metaString .= '<meta name="fb:app_id" content="' . self::$fb_id . '">';
        }

        //SHARING INFO
        $metaString .= '<meta property="og:site_name" content="Mapban.gg">';
        $metaString .= '<meta property="og:locale" content="' . $ml->current . '">';
        if (strlen( self::$social_Data['title'] ) != 0) {
            $metaString .= '<meta name="twitter:card" content="' . self::$twitter_card . '">';
            $metaString .= '<meta name="twitter:title" content="' . self::$social_Data['title'] . '">';
            $metaString .= '<meta property="og:title" content="' . self::$social_Data['title'] . '">';
            $uri_parts  = explode( '?', $_SERVER['REQUEST_URI'], 2 );
            $metaString .= '<meta property="og:url" content="https://' . $_SERVER["HTTP_HOST"] . $uri_parts[0] . '">';
        }
        if (strlen( self::$social_Data['description'] ) != 0) {
            $metaString .= '<meta name="twitter:description" content="' . self::$social_Data['description'] . '">';
            $metaString .= '<meta property="og:description" content="' . self::$social_Data['description'] . '">';
        }
        if (strlen( self::$social_Data['image'] ) != 0) {
            $metaString .= '<meta name="twitter:image" content="https://' . $_SERVER["HTTP_HOST"] . self::$social_Data['image'] . '">';
            $metaString .= '<meta property="og:image" content="https://' . $_SERVER["HTTP_HOST"] . self::$social_Data['image'] . '">';
        }
        if (strlen( self::$social_Data['imageAlt'] ) != 0) {
            $metaString .= '<meta name="twitter:image:alt" content="' . self::$social_Data['imageAlt'] . '">';
            $metaString .= '<meta property="og:image:alt" content="' . self::$social_Data['imageAlt'] . '">';
        }

        return $metaString;
    }
}