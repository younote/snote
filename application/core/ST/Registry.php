<?php

/*
 * (c) Arefiev Artem, Sidorov Andrew
 * License for snote project
 */

namespace ST;

abstract class Registry {
    protected function _get( $key ) { }
    protected function _set( $key, $value ) { }
    protected function _del( $key ) { }
}

class VariablesRegistry extends Registry {
    private static $_values = array();
    private static $_instance;

    private function __construct() { }
    private function __clone() { }

    static function instance() {
        if ( ! isset( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function _get($key) {
        $keys = (strpos($key, '.') !== false) ? explode('.', $key) : array($key);
        $result = & self::$_values;
        $i = 0;

        foreach ($keys as $k) {
            $i++;

            // if (is_array($result) && array_key_exists($k, $result)) {
                // unset($result[$k]);
            // }

            $result = & $result[$k];

            continue;
        }

        return !empty($result) ? $result : NULL;
    }

    protected function _set( $key, $value ) {
        $keys = explode('.', $key);
        $result = $_result = array();
        $first_step = true;
        $i = 0;

        foreach ($keys as $k) {
            $i++;
            if ( $first_step ) {
                $_result = &$result[$k];
                $first_step = !$first_step;
            } else {
                $_result = &$_result[$k];
            }

            if ( $i == count( $keys ) ) {
                $_result = $value;
            }
        }


        self::$_values = array_merge_recursive(self::$_values, $result);
    }

    protected function _del( $key ) {
        // if ( isset( $this->values[$key] ) ) {
        //     unset($this->values[$key]);
        //     return true;
        // }
        // return NULL;
    }

    static function get( $key ) {
        return self::instance()->_get($key);
    }

    static function set( $key, $value ) {
        return self::instance()->_set($key, $value);
    }

    static function del( $key ) {
        return self::instance()->_del($key);
    }
}

class SessionRegistry extends Registry {
    private static $_instance;

    private function __construct() {
        session_start();
    }
}