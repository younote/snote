<?php

/*
 * (c) Arefiev Artem, Sidorov Andrew
 * License for snote project
 */

namespace ST;

define('GET_CONTROLLERS', 1);
define('GET_PRE_CONTROLLERS', 2);
define('GET_POST_CONTROLLERS', 3);

class Application {
    private static $_instance;

    private function __construct() { }
    private function __clone() { }

    /**
     * Получение названия зоны
     * @param  string $area Код зоны
     * @return string       Название зоны (frontend/backend)
     */
    public function get_area_name($area)
    {
        if ( empty( $area ) ) {
            return;
        }

        return $area == "A" ? 'backend' : 'frontend';
    }

    /**
     * Разбор параметра "path" из URI запроса и извлечение controller, mode, action, extra
     * @param  string $request URI запрос
     * @return boolean true
     */
    private function _getroute($request = "")
    {
        if (!empty($request['path'])) {
            $path = $request['path'];
        } else {
            $path = "index.index";
        }

        rtrim($path, '/.');
        $path = str_replace('/', '.', $path);

        @list($c, $m, $a, $e) = explode('.', $path);

        // Сохранение в регистр
        VariablesRegistry::set('runtime.controller', !empty($c) ? $c : 'index');
        VariablesRegistry::set('runtime.mode', !empty($m) ? $m : 'index');
        VariablesRegistry::set('runtime.action', $a);
        VariablesRegistry::set('runtime.extra', $e);
        VariablesRegistry::set('runtime.root_template', 'index.tpl');

        if (isset($request['sl'])) {
            VariablesRegistry::set('current_language', $request['sl']);
        } else {
            VariablesRegistry::set('current_language', 'en');
        }

        return true;
    }

    /**
    * Передача управления существующим контроллерам
    * @param  string $controller Controller
    * @param  string $mode       Mode
    * @param  string $action     Action
    * @param  string $extra      Extra
    * @param  string $area       Код зоны
    * @return nothing            Функция не возвращает значение
    */
    protected function _dispatch($controller = '', $mode = '', $action = '', $extra = '', $area = AREA)
    {
        self::instance()->_getroute($_REQUEST);

        $controller = !empty($controller) ? $controller : VariablesRegistry::get('runtime.controller');
        $mode = !empty($mode) ? $mode : VariablesRegistry::get('runtime.mode');
        $action = !empty($action) ? $action : VariablesRegistry::get('runtime.action');
        $extra = !empty($extra) ? $extra : VariablesRegistry::get('runtime.extra');

        $area_name = self::instance()->get_area_name($area);

        $regexp = "/^[a-zA-Z0-9_\+]+$/";
        if (!preg_match($regexp, $controller) || !preg_match($regexp, $mode)) {
            die('Access denied');
        }

        // extra
        $status = CONTROLLER_STATUS_NO_PAGE;
        $run_controllers = true;

        $controllers_factory = array();
        $controllers = array('init');

        if ($run_controllers == true) {
            $controllers[] = $controller;
            $controllers = array_unique($controllers);
        }

        foreach ($controllers as $ctrl) {

            $system_controllers = self::instance()->_init_system_controllers($ctrl);

            if (empty($system_controllers)) {
                $status = CONTROLLER_STATUS_NO_PAGE;
                $run_controllers = false;
                break;
            }

            $system_pre_controllers = self::instance()->_init_system_controllers($ctrl, GET_PRE_CONTROLLERS);
            $system_post_controllers = self::instance()->_init_system_controllers($ctrl, GET_POST_CONTROLLERS);

            $controllers_factory = array_merge($controllers_factory, $system_pre_controllers, $system_controllers, $system_post_controllers);

            if (empty($controllers_factory)) {
                die("No controllers for: $ctrl");
            }
        }

        // Подключение шаблона
        $view = new Template($area_name);
        // $view = Template::create();

        if ($view->template_exists('views/' . $controller . '/' . $controller . '.tpl')) {
            $view->set_to_include('views/' . $controller . '/' . $controller . '.tpl');

            $status = CONTROLLER_STATUS_OK;
        // } else {
            // $status = CONTROLLER_STATUS_NO_PAGE;
        }

        // Подключение языка
        $language = new Languages(VariablesRegistry::get('current_language'));
        $lang_variables = $language->get_language_variables();

        if (!empty($lang_variables))
            $view->assign('lang_var', $lang_variables);

        // Подключение конфига
        $view->assign('config', VariablesRegistry::get('config'));

        // Подключение шаблона
        VariablesRegistry::set('view', $view);

        foreach ($controllers_factory as $factory) {
            $res = self::instance()->_run_controller($factory, $controller, $mode, $action, $extra);

            if ($run_controllers == true) {
                $status = !empty($res[0]) ? $res[0] : CONTROLLER_STATUS_OK;
                $redirect_url = !empty($res[1]) ? $res[1] : "";
            }
        }

        if (in_array($status, array(CONTROLLER_STATUS_OK, CONTROLLER_STATUS_REDIRECT)) && !empty($_REQUEST['redirect_url'])) {
            $redirect_url = $_REQUEST['redirect_url'];
        }

        if ($status == CONTROLLER_STATUS_REDIRECT && empty($redirect_url)) {
            $status = CONTROLLER_STATUS_NO_PAGE;
        }

        if (!$view->include_template && $status == CONTROLLER_STATUS_OK) {
            $status = CONTROLLER_STATUS_NO_PAGE;
        }

        if ($status == CONTROLLER_STATUS_NO_PAGE) {
            VariablesRegistry::get('view')->assign('page_title', 'page not found');
        }

        VariablesRegistry::get('view')->display(VariablesRegistry::get('runtime.root_template'));

        exit;
    }

    /**
     * Подготовка списка контроллеров системы
     * @param  string $controller  Controller
     * @param  string $type        Тип контроллера (pre/post)
     * @param  string $area        Название зоны
     * @return array  $controllers Список контроллеров
     */
    private function _init_system_controllers($controller, $type = GET_CONTROLLERS, $area = AREA)
    {
        $controllers = array();
        $area_name = self::instance()->get_area_name($area);

        $prefix = "";

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

    private function _run_controller($path, $controller, $mode, $action, $extra)
    {
        if (is_readable($path))
            return include($path);

        return false;
    }

    static function instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    static function run() {
        return self::instance()->_dispatch();
    }
}