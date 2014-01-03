<?php

/* 
 * (c) Arefiev Artem, Sidorov Andrew
 * License for snote project
 */

use ST\Database;
use ST\VariablesRegistry;

$this_dir = dirname(__FILE__);
$config = require ($this_dir . '/config.php');

// Auto-load uses classes
require($this_dir . '/application/lib/classloader/Loader.php');
Loader::addClasses('ST', $this_dir . '/application/core');

$fn_list = array(
    'fn.common.php',
//    'fn.init.php', 
//    'main.php',
//    'connect_session.php',
//    'connect_server.php',
//    'users_privileges.php',
//    'active_tab_setting.php',
//    'background_get.php',
//    'tables_list_output.php',
//    'stickers_output.php',
//    'users.php',
//    'class.login.php'    
);

foreach ($fn_list as $file_name) {
    require ($config['dir']['functions'] . $file_name);
}

if (!Database::connect($config)) {
    die('Can\'t connect to database');
}

// Save config
VariablesRegistry::set('config', $config);

unset($config);

//fn_dispatch($_REQUEST);