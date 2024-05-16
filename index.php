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
# Проверяем налчичие сессии
if (session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
	session_start();
}
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization_defaultDB($_SESSION['login'], $_SESSION['password']) == -1) {
		// Редирект на главную страницу
?>
<meta http-equiv="refresh" content="0; url=<?php echo __ROOT; ?>">
<?php
	} else {
		// При удачном входе пользователю выдается все, что расположено ниже между звездочками.
		// ************************************************************************************
		logActivity();
		if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 0) == 1) {
			$_reqDB = mysqli_fetch_array(mysqlQuery("SELECT * FROM mailbox_sp_users WHERE ID = {$_SESSION['id']}"));
			$_SESSION['mail_user_kodispol'] = (!empty($_reqDB['kodispol']) && $_reqDB['enable'] == '1' && $_reqDB['status_ispol'] == '1') ? $_reqDB['kodispol'] : "";
			$_SESSION['mail_user_kodispolout'] = (!empty($_reqDB['kodispolout']) && $_reqDB['enable'] == '1' && $_reqDB['status_ispolout'] == '1') ? $_reqDB['kodispolout'] : "";
			$_SESSION['mail_user_kodzayvtel'] = (!empty($_reqDB['kodzayvtel']) && $_reqDB['enable'] == '1' && $_reqDB['status_zayvtel'] == '1') ? $_reqDB['kodzayvtel'] : "";
		}
		if (checkServiceAccess_defaultDB('allservices') == 1) {
			if (checkServiceAccess_defaultDB('mailnew') == 1 or (checkServiceAccess_defaultDB('mailnew') == 0 && checkIsItSuperadmin_defaultDB($_SESSION['id']) == 1)) {
				require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_HEADER);
				include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_WORKPATH  . '/mailnew.php');
				require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_FOOTER);
			} else {
				include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/msg.inc/message_service-noaccess185.php');
			}
		} else {
			include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/msg.inc/message_service-noaccess185.php');
		}
		// ************************************************************************************
		// При удачном входе пользователю выдается все, что расположено ВЫШЕ между звездочками.
	}
} else {
	// Редирект на главную страницу
	?>
<meta http-equiv="refresh" content="0; url=<?php echo __ROOT; ?>">
<?php
}
?>