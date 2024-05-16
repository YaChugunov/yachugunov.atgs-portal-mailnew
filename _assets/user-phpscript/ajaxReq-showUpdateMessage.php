<?php
# Подключаем конфигурационный файл
require($_SERVER['DOCUMENT_ROOT'] . '/config.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
require(__DIR_ROOT . __SERVICENAME_MAILNEW . '/config.mail.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаемся к базе
require(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/dbconn/db_connection.php');
// require('../_assets/drivers/db_controller.php');
// $db_handle = new DBController();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем общие функции безопасности
require(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/functions/func.secure.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем собственные функции сервиса Почта
require(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/functions/func.mail.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
// You can access the values posted by jQuery.ajax
// through the global variable $_POST, like this:
// print_r ($_POST);
$_updateid = isset($_POST['updateid']) ? $_POST['updateid'] : "";
$_userid = isset($_POST['userid']) ? $_POST['userid'] : "";
$_donotshow = isset($_POST['donotshow']) ? $_POST['donotshow'] : "";
//
if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		return "-3";
	} else {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $_userid != "" && $_updateid != "") {
			$_reqDB1 = mysqlQuery("SELECT * FROM mailbox_updates WHERE update_id = '{$_updateid}' AND active='1'");
			if ($_reqDB1) {
				$_reqDB2 = mysqlQuery("SELECT * FROM mailbox_updates_views WHERE user_id='{$_SESSION['id']}' AND update_id='{$_updateid}'");
				if ($_reqDB2 && $_donotshow == '1') {
					$_reqDB3 = mysqlQuery("UPDATE mailbox_updates_views SET show_update='0',show_progress='0' WHERE user_id='{$_SESSION['id']}' AND update_id='{$_updateid}'");
				} elseif ($_reqDB2 && empty($_donotshow)) {
					$_reqDB4 = mysqlQuery("UPDATE mailbox_updates_views SET show_progress='0' WHERE user_id='{$_SESSION['id']}' AND update_id='{$_updateid}'");
				} else {
					$_reqDB5 = mysqlQuery("INSERT INTO mailbox_updates_views (update_id, user_id) VALUES ('{$_updateid}', {$_SESSION['id']}')");
				}
			}
			if ($_reqDB1 && ($_reqDB2 || $_reqDB3 || $_reqDB4 || $_reqDB5)) {
				$output = "1";
			} else {
				$output = "-1";
			}
		} else {
			$output = "-2";
		}
	}
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
