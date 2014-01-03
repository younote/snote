<?php

/* 
 * (c) Arefiev Artem, Sidorov Alexander
 * License for snote project
 */

// Directories
define('DIR_ROOT', dirname(__FILE__));

// Controller return statuses
define('CONTROLLER_STATUS_REDIRECT', 302);
define('CONTROLLER_STATUS_OK', 200);
define('CONTROLLER_STATUS_NO_PAGE', 404);
define('CONTROLLER_STATUS_DENIED', 403);

// Session
define('SESSIONS_COOKIE_ALIVE_TIME', 600);

// Config
$config = array();

$config['dir'] = array(
    'controllers' => DIR_ROOT . '/application/controllers/', 
    'functions' => DIR_ROOT . '/application/core/functions/', 
    'design_backend' => DIR_ROOT . '/design/backend/', 
    'design_frontend' => DIR_ROOT . '/design/frontend/'
);

// Database
$config['system']['db_host'] = 'localhost';
$config['system']['db_user'] = 'root';
$config['system']['db_password'] = '';
$config['system']['db_name'] = 'aarefiev_stickdev';
//$config['system']['db_type'] = 'mysqli';

$config['system']['http_host'] = 'localhost';
$config['system']['http_path'] = '/snote2';

$config['system']['http_location'] = 'http://' . $config['system']['http_host'] . $config['system']['http_path'];
$config['system']['current_location'] = $config['system']['http_location'];

return $config;