<?

/*
 * (c) Arefiev Artem, Sidorov Alexander
 * License for snote project
 */

if (!defined('AREA')) die('ACCESS DENIED');

//
// Forbid posts to index script
//
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return array(CONTROLLER_STATUS_NO_PAGE);
}