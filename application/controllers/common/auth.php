<?

/*
 * (c) Arefiev Artem, Sidorov Alexander
 * License for snote project
 */

use ST\VariablesRegistry;

if (!defined('AREA')) die('ACCESS DENIED');

if ($mode == "login") {

	// Подключение view
	$view = VariablesRegistry::get('view');

	$view->assign('page_title', $view->lang_var['login']['value']);
	$view->set_to_include('views/auth/login.tpl');

	return array(CONTROLLER_STATUS_OK);
}