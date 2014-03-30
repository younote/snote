<?php

/*
 * (c) Arefiev Artem, Sidorov Alexander
 * License for snote project
 */

use ST\VariablesRegistry;

// define('GET_CONTROLLERS', 1);
// define('GET_PRE_CONTROLLERS', 2);
// define('GET_POST_CONTROLLERS', 3);

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

/**
 * Передача управления существующим контроллерам
 * @param  string $controller Имя контроллера
 * @param  string $mode       Имя мода
 * @param  string $action     Имя экшена
 * @param  string $extra      Имя дополнительного параметра
 * @param  string $area       Код зоны
 * @return nothign            Функция не возвращает значение
 */
function fn_dispatch($controller = '', $mode = '', $action = '', $extra = '', $area = AREA)
{
    $runtime = fn_sys_get_route($_REQUEST);

    $controller = $runtime['controller'];
    $mode = $runtime['mode'];
    $action = $runtime['action'];
    $extra = $runtime['extra'];

    $regexp = "/^[a-zA-Z0-9_\+]+$/";
    if (!preg_match($regexp, $controller) || !preg_match($regexp, $mode)) {
        die('Access denied');
    }

    // extra
    $status = CONTROLLER_STATUS_NO_PAGE;
    $run_controllers = true;

    $controllers_list = array();
    $controllers = array('init');

    if ($run_controllers == true) {
        $controllers[] = $controller;
        $controllers = array_unuque($controllers);
    }

    foreach ($controllers as $ctrl) {
        $system_controllers = fn_init_system_controllers($ctrl);

        if (empty($system_controllers)) {
            $status = CONTROLLER_STATUS_NO_PAGE;
            $run_controllers = false;
            break;
        }

        $system_pre_controllers = fn_init_system_controllers($ctrl, GET_PRE_CONTROLLERS);
        $system_post_controllers = fn_init_system_controllers($ctrl, GET_POST_CONTROLLERS);

        $controllers_list = array_merge($controllers_list, $system_pre_controllers, $system_controllers, $system_post_controllers);

        if (empty($controllers_list)) {
            die("No controllers for: $ctrl");
        }
    }

    print_r($controllers_list);

    $config = VariablesRegistry::get('config');
    $design_path = $area == "A" ? $config['dir']['design_backend'] : $config['dir']['design_frontend'];

    unset($config);

    fn_init_controller($controller, $mode, $design_path, fn_get_area_name($area));

    exit;
}

/**
 * Подготовка списка контроллеров системы
 * @param  string $controller  Имя контроллера
 * @param  string $type        Тип контроллера (pre/post)
 * @param  string $area        Код зоны
 * @return array  $controllers Список контроллеров
 */
function fn_init_system_controllers($controller, $type = GET_CONTROLLERS, $area = AREA)
{
    $controllers = array();

    $prefix = "";
    $area_name = fn_get_area_name($area);

    if ($type == GET_PRE_CONTROLLERS) {
        $prefix = ".pre";
    } elseif ($type == GET_POST_CONTROLLERS) {
        $prefix = ".post";
    }

    // Попытка найти контроллер специфичный для зоны
    if (is_readable(DIR_ROOT . '/application/controllers/' . $area_name . '/' . $controller . $prefix . '.php')) {
        $controllers[] = DIR_ROOT . '/application/controllers/' . $area_name . '/' . $controller . $prefix . '.php';
    }

    // Попытка найти общий контроллер
    if (is_readable(DIR_ROOT . '/application/controllers/common/' . $controller . $prefix . '.php')) {
        $controllers[] = DIR_ROOT . '/application/controllers/common/' . $controller . $prefix . '.php';
    }

    return $controllers;
}