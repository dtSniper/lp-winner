<?php
/**
 * User: Sascha Wendt
 * Date: 12.12.2020
 * Time: 14:00
 */

namespace lpwinner;


class Config extends \DB\SQL\Mapper {
    const TYPE_INT    = "INT";
    const TYPE_STRING = "STRING";
    const TYPE_FLOAT  = "FLOAT";
    const TYPE_JSON   = "JSON";

    public function __construct(\Base $f3) {
        parent::__construct( $f3->get( "DB" ), 'config' );
    }

    public function &__get($field) {
        $parentField = parent::__get( $field );
        switch ($field) {
            case "value":
                switch ($this->type) {
                    case self::TYPE_INT:
                        return intval( $parentField );
                    case self::TYPE_FLOAT:
                        return floatval( $parentField );
                    case self::TYPE_JSON:
                        return json_decode( $parentField );
                    case null:
                        $this->type = self::TYPE_STRING;
                        $this->save();
                    case self::TYPE_STRING:
                    default:
                        return $parentField;

                }
                break;
        }
        return $parentField;
    }

    public function all() {
        $this->load( null, array(
            'order' => 'config ASC'
        ) );
        return $this->query;
    }

    public static function loadAll(&$f3) {
        $config     = new Config( $f3 );
        $configData = $config->all();
        foreach ($configData as $cfg) {
            $f3->set( "CFG." . $cfg->config, $cfg->value );
        }
    }

    public static function saveValue(&$f3, $configName, $value, $type = self::TYPE_STRING) {
        $config = new Config( $f3 );
        if (!$config->load( array("config=?", $configName) )) {
            $config->config = $configName;
        }
        if ($config->type == null) {
            $config->type = $type;
        }
        switch ($config->type) {
            case self::TYPE_INT:
                $config->value = intval( $value );
            case self::TYPE_FLOAT:
                $config->value = floatval( $value );
            case self::TYPE_JSON:
                $config->value = json_decode( $value );
            case self::TYPE_STRING:
            default:
                $config->value = $value;
        }
        $config->save();
        $f3->set( "CFG." . $config->config, $config->value );
    }

}