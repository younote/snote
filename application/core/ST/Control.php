<?php

/* 
 * (c) Arefiev Artem, Sidorov Andrew
 * License for snote project
 */

namespace ST;

class Control {
    private $_control_path;
    private $_controllers_dir;
    private static $_instance;
    
    private function __construct() { }
    
    protected function _init( $controllers_dir, $control_path ) {
        $this->_control_path = $control_path;
        $this->_controllers_dir = $controllers_dir;
        
        print_r($control_path);
        print_r($controllers_dir);

        
//        if ( is_readable(  ) )
    }
    
    static function instance() {
        if ( ! isset(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    static function init( $controllers_dir, $control_path ) {
        return self::instance()->_init($controllers_dir, $control_path);
    }
}