<?php

/* 
 * (c) Arefiev Artem, Sidorov Alexander
 * License for snote project
 */

use ST\VariablesRegistry;

function fn_get_area_name($area)
{
    if ( empty( $area ) ) {
        return;
    }
    
    return $area == "A" ? 'backend' : 'frontend';
}

function fn_sys_get_route($req = '')
{
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
    
    return $runtime;
}

function fn_init_controller($controller, $mode, $design_path, $area)
{
    $controller_path = DIR_ROOT . '/application/controllers/' . $area . '/' . $controller . '.php';
    $template_path = ($controller == "index" && $mode == "index") ?
            $design_path . 'templates' . '/index.php' :
            $design_path . 'templates/' . $controller . '/' . $mode . '.php';
    
    if ( is_readable( $controller_path ) ) {
        require ($controller_path);
    } else {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }
    
    if ( is_readable( $template_path ) ) {
        require ($template_path);           
    } else {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }
}

/* @var $_REQUEST type */
function fn_dispatch($controller = '', $mode = '', $action = '', $area = AREA)
{
    $runtime = fn_sys_get_route($_REQUEST);
    
    $controller = $runtime['controller'];
    $mode = $runtime['mode'];
    $action = $runtime['action'];
    
    $config = VariablesRegistry::get('config');
    $design_path = $area == "A" ? 
        $config['dir']['design_backend'] : 
        $config['dir']['design_frontend']; 
    unset($config);
    
    fn_init_controller($controller, $mode, $design_path, fn_get_area_name($area));
    
    exit;
}