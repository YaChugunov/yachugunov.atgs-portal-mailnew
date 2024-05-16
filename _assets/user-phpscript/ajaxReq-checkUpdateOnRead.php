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
$_action = isset($_POST['action']) ? $_POST['action'] : "";
$output = "none";
$views = "";
//
if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		return "error -1";
	} else {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $_userid != "" && $_updateid != "") {
			if ($_userid !== '999') {
				$_reqDB_Main = mysqli_fetch_assoc(mysqlQuery("SELECT update_id FROM mailbox_updates WHERE active='1' ORDER BY id DESC LIMIT 1"));
				if (isset($_reqDB_Main) && !empty($_reqDB_Main['update_id'])) {
					$_reqDB_Views = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM mailbox_updates_views WHERE user_id='{$_userid}' AND update_id='{$_updateid}'"));
					if ($_reqDB_Views && $_reqDB_Views['show_update'] == '1') {
						$progress = $_reqDB_Views['show_progress'];
						$views += $_reqDB_Views['views'];
						if ($_action == 'check') {
							$_reqDB_Upd = mysqlQuery("UPDATE mailbox_updates_views SET show_progress='0', views='{$views}' WHERE user_id='{$_userid}' AND update_id='{$_updateid}'");
							$output = "ok," . $progress;
						} elseif ($_action == 'mark') {
							$_reqDB_Upd = mysqlQuery("UPDATE mailbox_updates_views SET show_update='0', views='{$views}' WHERE user_id='{$_userid}' AND update_id='{$_updateid}'");
							$output = "none";
						} else {
							$output = "ok," . $progress;
						}
					} elseif (!$_reqDB_Views) {
						$_reqDB_Ins = mysqlQuery("INSERT INTO mailbox_updates_views (update_id, user_id, user_name) VALUES ('{$_updateid}', '{$_SESSION['id']}', '{$_SESSION['lastname']}')");
						$output = "ok";
					} else {
						$output = "none";
					}
				} else {
					$output = "none";
				}
			} else {
				$_reqDB_Main = mysqli_fetch_assoc(mysqlQuery("SELECT update_id FROM mailbox_updates WHERE testmode='1' ORDER BY id DESC LIMIT 1"));
				if (isset($_reqDB_Main) && !empty($_reqDB_Main['update_id'])) {
					$_reqDB_Views = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM mailbox_updates_views WHERE user_id='{$_userid}' AND update_id='{$_updateid}'"));
					if ($_reqDB_Views && $_reqDB_Views['show_update'] == '1') {
						$progress = $_reqDB_Views['show_progress'];
						$views += $_reqDB_Views['views'];
						if ($_action == 'check') {
							$_reqDB_Upd = mysqlQuery("UPDATE mailbox_updates_views SET show_progress='0', views='{$views}' WHERE user_id='{$_userid}' AND update_id='{$_updateid}'");
							$output = "ok," . $progress;
						} elseif ($_action == 'mark') {
							$_reqDB_Upd = mysqlQuery("UPDATE mailbox_updates_views SET show_update='0', views='{$views}' WHERE user_id='{$_userid}' AND update_id='{$_updateid}'");
							$output = "none";
						} else {
							$output = "ok," . $progress;
						}
					} elseif (!$_reqDB_Views) {
						$_reqDB_Ins = mysqlQuery("INSERT INTO mailbox_updates_views (update_id, user_id, user_name) VALUES ('{$_updateid}', '{$_SESSION['id']}', '{$_SESSION['lastname']}')");
						$output = "ok";
					} else {
						$output = "none";
					}
				} else {
					$output = "none";
				}
			}
		} else {
			$output = "error -2";
		}
	}
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
