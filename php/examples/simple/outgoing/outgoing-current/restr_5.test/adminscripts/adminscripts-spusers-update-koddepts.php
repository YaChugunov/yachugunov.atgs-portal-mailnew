<?php
date_default_timezone_set('Europe/Moscow');
# Подключаем конфигурационный файл
// require($_SERVER['DOCUMENT_ROOT'] . "/config.inc.php");
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаемся к базе
require_once(__DIR_ROOT . __SERVICENAME_MAIL . '/_assets/drivers/db_connection.php');
require_once(__DIR_ROOT . __SERVICENAME_MAIL . '/_assets/drivers/db_controller.php');
$db_handle = new DBController();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем общие функции безопасности
require_once(__DIR_ROOT . '/_assets/functions/funcSecure.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем собственные функции сервиса Почта
require_once(__DIR_ROOT . __SERVICENAME_MAIL . '/_assets/functions/funcMail.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Включаем режим сессии
// session_start();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$output = "---";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		$output = "Error -1";
	} else {
		// if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		if ($_SESSION['id'] == '999') {
			$_req1 = mysqlQuery("SELECT koddocmail, inbox_docContractor_kodzayvispol FROM mailbox_incoming_test WHERE inbox_docDate BETWEEN '2021-01-01 00:00:00' AND '2022-12-31 23:59:59'");
			while ($_req1Rows = mysqli_fetch_assoc($_req1)) {
				$arrispol = explode(",", $_req1Rows['inbox_docContractor_kodzayvispol']);
				$koddocmail = $_req1Rows['koddocmail'];
				$ispoldepts = "";
				foreach ($arrispol as $value) {
					$_req2 = mysqli_fetch_assoc(mysqlQuery("SELECT koddept FROM dept_list WHERE dept_num = (SELECT dept_num FROM mailbox_sp_users WHERE kodispol='{$value}')"));
					$ispoldepts .= !empty($_req2['koddept']) ? $_req2['koddept'] . "," : '0,';
				}
				$ispoldepts = rtrim($ispoldepts, ",");
				$_req3 = mysqlQuery("UPDATE mailbox_incoming_test SET inbox_docContractorDEPT='{$ispoldepts}' WHERE koddocmail='{$koddocmail}'");
				$output = "Ok";
			}
		} else {
			$output = 'Error -2';
		}
	}
}
// unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
