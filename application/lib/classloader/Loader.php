<?php

/* 
 * (c) Arefiev Artem, Sidorov Alexander
 * License for snote project
 */

class Loader {
    private static $instance;
    private $base_dir;
    private $namespace;
    
    private function __construct() {}
    
    static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    protected function add( $namespace, $base_dir ) {
        $this->base_dir = $base_dir;
        $this->namespace = explode(', ', $namespace);
        
        foreach ( $this->namespace as $ns ) {
            $this->files = glob("$this->base_dir/$ns/*.php");
            
            foreach ( $this->files as $file ) {
                if ( file_exists( $file ) ) {
                    require_once $file;
                }
            }
        }
    }
    
    static function addClasses( $namespace, $base_dir ) {
        return self::instance()->add($namespace, $base_dir);
    }
}