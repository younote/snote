<?php

/* 
 * (c) Arefiev Artem, Sidorov Andrew
 * License for snote project
 */

namespace ST;

use ST\Control;

class Application {
    private static $_controller;
    private static $_mode;
    private static $_action;
    private static $_area;
    private static $_instance;
    
    private function __construct() { }
        
    protected function _dispatch($controller = '', $mode = '', $action = '', $area = AREA) {
        $req = $_REQUEST;
        
        if ( ! ( empty( $req ) ) && isset( $req['path'] ) && $req['path'] ) {
            $path = $req['path'];
        } else {
            $path = "index.index";
        }

        rtrim($path, '/.');
        $path = str_replace('/', '.', $path);

        @list($c, $m, $a) = explode('.', $path);

        $runtime['controller'] = !empty($c) ? $c : 'index';
        $runtime['mode'] = !empty($m) ? $m : 'index';
        $runtime['action'] = !empty($a) ? $a : '';

        VariablesRegistry::set('runtime', $runtime);
    
        $controller = $runtime['controller'];
        $mode = $runtime['mode'];
        $action = $runtime['action'];
        
        print_r(VariablesRegistry::get('config'));
        
        Control::init(VariablesRegistry::get('config.dir.controllers'), $runtime);
        
        return true;
    }
    
    static function instance() {
        if ( ! isset(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    static function run() {
        return self::instance()->_dispatch();
    }
}