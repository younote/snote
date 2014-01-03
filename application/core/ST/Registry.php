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
    private $values = array();
    private static $_instance;
    
    private function __construct() { }
    
    static function instance() {
        if ( ! isset(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    protected function _get( $key ) {
        if ( isset( $this->values[$key] ) ) {
            return $this->values[$key];
        }
        return NULL;
    }
    
    protected function _set( $key, $value ) {
        $this->values[$key] = $value;
    }
    
    protected function _del ( $key ) {
        if ( isset( $this->values[$key] ) ) {
            unset($this->values[$key]);
            return true;
        }
        return NULL;
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