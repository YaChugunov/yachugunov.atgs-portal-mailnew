<?php
date_default_timezone_set('Europe/Moscow');
# Подключаем конфигурационный файл
require($_SERVER['DOCUMENT_ROOT'] . "/config.inc.php");
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/config.mail.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаемся к базе
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/dbconn/db_connection.php');
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/dbconn/db_controller.php');
$db_handle = new DBController();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем общие функции безопасности
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/functions/func.secure.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем собственные функции сервиса Почта
require_once(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/functions/func.mail.inc.php');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Включаем режим сессии
// session_start();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

// Код документа в таблице mailbox_incoming
$action = isset($_POST['action']) ? $_POST['action'] : '';
$userID = isset($_POST['userID']) ? $_POST['userID'] : $_SESSION['id'];
$use_personalSettings_userSelected = 1;
// $use_personalSettings_userSelected = isset($_POST['use_personalSettings']) ? $_POST['use_personalSettings'] : 0;
$use_pushMessages_userSelected = isset($_POST['use_pushMessages']) ? $_POST['use_pushMessages'] : 0;
$incoming_showDashboard_userSelected = isset($_POST['incoming_showDashboard']) ? $_POST['incoming_showDashboard'] : 0;
$outgoing_showDashboard_userSelected = isset($_POST['outgoing_showDashboard']) ? $_POST['outgoing_showDashboard'] : 0;
$mailbox_showLegend_userSelected = isset($_POST['mailbox_showLegend']) ? $_POST['mailbox_showLegend'] : 0;
$outgoing_kodSenderDefault_userSelected = isset($_POST['outgoing_kodSenderDefault']) ? $_POST['outgoing_kodSenderDefault'] : "000000000000000";
$outgoing_kodRecipientOrgDefault_userSelected = isset($_POST['outgoing_kodRecipientOrgDefault']) && $_POST['outgoing_kodRecipientOrgDefault'] != "000000000000000" && $_POST['outgoing_kodRecipientOrgDefault'] != "" ? $_POST['outgoing_kodRecipientOrgDefault'] : NULL;
$outgoing_kodIspolDefault_userSelected = isset($_POST['outgoing_kodIspolDefault']) ? $_POST['outgoing_kodIspolDefault'] : "000000000000000";
$outgoing_setMeIspolOnStart_userSelected = isset($_POST['outgoing_setMeIspolOnStart']) ? $_POST['outgoing_setMeIspolOnStart'] : 0;
$outgoing_textAboutDefault_userSelected = isset($_POST['outgoing_textAboutDefault']) ? $_POST['outgoing_textAboutDefault'] : "Исходящее письмо (" . $_SESSION['lastname'] . ")";
$incoming_subscribeWeekReminder_userSelected = isset($_POST['incoming_subscribeWeekReminder']) ? $_POST['incoming_subscribeWeekReminder'] : 0;

$incoming_setControlIspolOnStart_userSelected = isset($_POST['incoming_setControlIspolOnStart']) ? $_POST['incoming_setControlIspolOnStart'] : 0;
$incoming_setDeadlineOnStart_userSelected = isset($_POST['incoming_setDeadlineOnStart']) ? $_POST['incoming_setDeadlineOnStart'] : 0;
$outgoing_setControlIspolOnStart_userSelected = isset($_POST['outgoing_setControlIspolOnStart']) ? $_POST['outgoing_setControlIspolOnStart'] : 0;
$outgoing_setDeadlineOnStart_userSelected = isset($_POST['outgoing_setDeadlineOnStart']) ? $_POST['outgoing_setDeadlineOnStart'] : 0;
$use_lightTheme_userSelected = isset($_POST['use_lightTheme']) ? $_POST['use_lightTheme'] : 0;
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
$error = $output1 = $output2 = $output3 = $output4 = "";
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		return -2;
	} else {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $userID != "") {
			$_req_loadFromDB = mysqli_fetch_array(mysqlQuery("SELECT * FROM mailbox_userSettingsUI WHERE `ID` = '{$userID}'"));
			$use_personalSettings_loadFromDB = isset($_req_loadFromDB['use_personalSettings']) ? $_req_loadFromDB['use_personalSettings'] : 0;
			$use_pushMessages_loadFromDB = isset($_req_loadFromDB['use_pushMessages']) ? $_req_loadFromDB['use_pushMessages'] : 0;
			$incoming_showDashboard_loadFromDB = isset($_req_loadFromDB['incoming_showDashboard']) ? $_req_loadFromDB['incoming_showDashboard'] : 0;
			$outgoing_showDashboard_loadFromDB = isset($_req_loadFromDB['outgoing_showDashboard']) ? $_req_loadFromDB['outgoing_showDashboard'] : 0;
			$mailbox_showLegend_loadFromDB = isset($_req_loadFromDB['mailbox_showLegend']) ? $_req_loadFromDB['mailbox_showLegend'] : 0;
			$outgoing_kodSenderDefault_loadFromDB = isset($_req_loadFromDB['outgoing_kodSenderDefault']) ? $_req_loadFromDB['outgoing_kodSenderDefault'] : "";
			$outgoing_kodRecipientOrgDefault_loadFromDB = isset($_req_loadFromDB['outgoing_kodRecipientOrgDefault']) ? $_req_loadFromDB['outgoing_kodRecipientOrgDefault'] : NULL;
			$outgoing_kodIspolDefault_loadFromDB = isset($_req_loadFromDB['outgoing_kodIspolDefault']) ? $_req_loadFromDB['outgoing_kodIspolDefault'] : "";
			$outgoing_setMeIspolOnStart_loadFromDB = isset($_req_loadFromDB['outgoing_setMeIspolOnStart']) ? $_req_loadFromDB['outgoing_setMeIspolOnStart'] : 0;
			$outgoing_textAboutDefault_loadFromDB = isset($_req_loadFromDB['outgoing_textAboutDefault']) ? $_req_loadFromDB['outgoing_textAboutDefault'] : "";
			$incoming_subscribeWeekReminder_loadFromDB = isset($_req_loadFromDB['incoming_subscribeWeekReminder']) ? $_req_loadFromDB['incoming_subscribeWeekReminder'] : 0;

			$incoming_setControlIspolOnStart_loadFromDB = isset($_req_loadFromDB['incoming_setControlIspolOnStart']) ? $_req_loadFromDB['incoming_setControlIspolOnStart'] : 0;
			$incoming_setDeadlineOnStart_loadFromDB = isset($_req_loadFromDB['incoming_setDeadlineOnStart']) ? $_req_loadFromDB['incoming_setDeadlineOnStart'] : 0;
			$outgoing_setControlIspolOnStart_loadFromDB = isset($_req_loadFromDB['outgoing_setControlIspolOnStart']) ? $_req_loadFromDB['outgoing_setControlIspolOnStart'] : 0;
			$outgoing_setDeadlineOnStart_loadFromDB = isset($_req_loadFromDB['outgoing_setDeadlineOnStart']) ? $_req_loadFromDB['outgoing_setDeadlineOnStart'] : 0;
			$use_lightTheme_loadFromDB = isset($_req_loadFromDB['use_lightTheme']) ? $_req_loadFromDB['use_lightTheme'] : 0;

			$arrLoadFromDB = [
				'useSettings'		=> 1,
				'pushMessages'		=> $use_pushMessages_loadFromDB,
				'incDashboard'		=> $incoming_showDashboard_loadFromDB,
				'outDashboard'		=> $outgoing_showDashboard_loadFromDB,
				'mailboxLegend'		=> $mailbox_showLegend_loadFromDB,
				'outSenderDefault'	=> $outgoing_kodSenderDefault_loadFromDB,
				'outRecOrgDefault'	=> $outgoing_kodRecipientOrgDefault_loadFromDB,
				'outIspolDefault'	=> $outgoing_kodIspolDefault_loadFromDB,
				'outSetMeIspol'		=> $outgoing_setMeIspolOnStart_loadFromDB,
				'outAboutDefault'	=> $outgoing_textAboutDefault_loadFromDB,
				'incWeekReminder1'	=> $incoming_subscribeWeekReminder_loadFromDB,

				'incSetCIOnStart'	=> $incoming_setControlIspolOnStart_loadFromDB,
				'incSetDLOnStart'	=> $incoming_setDeadlineOnStart_loadFromDB,
				'outSetCIOnStart'	=> $outgoing_setControlIspolOnStart_loadFromDB,
				'outSetDLOnStart'	=> $outgoing_setDeadlineOnStart_loadFromDB,

				'lightTheme'		=> $use_lightTheme_loadFromDB,
			];
			$arrUserSelected = [
				'useSettings'		=> 1,
				'pushMessages'		=> $use_pushMessages_userSelected,
				'incDashboard'		=> $incoming_showDashboard_userSelected,
				'outDashboard'		=> $outgoing_showDashboard_userSelected,
				'mailboxLegend'		=> $mailbox_showLegend_userSelected,
				'outSenderDefault'	=> $outgoing_kodSenderDefault_userSelected,
				'outRecOrgDefault'	=> $outgoing_kodRecipientOrgDefault_userSelected,
				'outIspolDefault'	=> $outgoing_kodIspolDefault_userSelected,
				'outSetMeIspol'		=> $outgoing_setMeIspolOnStart_userSelected,
				'outAboutDefault'	=> $outgoing_textAboutDefault_userSelected,
				'incWeekReminder1'	=> $incoming_subscribeWeekReminder_userSelected,

				'incSetCIOnStart'	=> $incoming_setControlIspolOnStart_userSelected,
				'incSetDLOnStart'	=> $incoming_setDeadlineOnStart_userSelected,
				'outSetCIOnStart'	=> $outgoing_setControlIspolOnStart_userSelected,
				'outSetDLOnStart'	=> $outgoing_setDeadlineOnStart_userSelected,

				'lightTheme'		=> $use_lightTheme_userSelected,
			];
			$noChanges = ($arrLoadFromDB === $arrUserSelected);
			if (!$noChanges && $action == "save") {
				$_req_saveToDB = mysqlQuery("UPDATE mailbox_userSettingsUI SET use_pushMessages = '{$use_pushMessages_userSelected}', incoming_showDashboard = '{$incoming_showDashboard_userSelected}', outgoing_showDashboard = '{$outgoing_showDashboard_userSelected}', mailbox_showLegend = '{$mailbox_showLegend_userSelected}', outgoing_kodSenderDefault = '{$outgoing_kodSenderDefault_userSelected}', outgoing_kodRecipientOrgDefault = '{$outgoing_kodRecipientOrgDefault_userSelected}', outgoing_kodIspolDefault = '{$outgoing_kodIspolDefault_userSelected}', outgoing_setMeIspolOnStart = '{$outgoing_setMeIspolOnStart_userSelected}', outgoing_textAboutDefault = '{$outgoing_textAboutDefault_userSelected}', incoming_subscribeWeekReminder = '{$incoming_subscribeWeekReminder_userSelected}', incoming_setControlIspolOnStart = '{$incoming_setControlIspolOnStart_userSelected}', incoming_setDeadlineOnStart = '{$incoming_setDeadlineOnStart_userSelected}', outgoing_setControlIspolOnStart = '{$outgoing_setControlIspolOnStart_userSelected}', outgoing_setDeadlineOnStart = '{$outgoing_setDeadlineOnStart_userSelected}', use_lightTheme = '{$use_lightTheme_userSelected}' WHERE `userid` = '{$userID}'");
				if ($_req_saveToDB) {
					$output1 = $use_personalSettings_userSelected .  "///" . $use_pushMessages_userSelected .  "///" . $incoming_showDashboard_userSelected .  "///" . $outgoing_showDashboard_userSelected .  "///" . $mailbox_showLegend_userSelected .  "///" . $outgoing_kodSenderDefault_userSelected .  "///" . $outgoing_kodRecipientOrgDefault_userSelected .  "///" . $outgoing_kodIspolDefault_userSelected .  "///" . $outgoing_setMeIspolOnStart_userSelected .  "///" . $outgoing_textAboutDefault_userSelected .  "///" . $incoming_subscribeWeekReminder_userSelected .  "///" . $incoming_setControlIspolOnStart_userSelected .  "///" . $incoming_setDeadlineOnStart_userSelected .  "///" . $outgoing_setControlIspolOnStart_userSelected .  "///" . $outgoing_setDeadlineOnStart_userSelected .  "///" . $use_lightTheme_userSelected;
				} else {
					$output1 = 0;
				}
			} else {
				$output1 = $use_personalSettings_loadFromDB .  "///" . $use_pushMessages_loadFromDB .  "///" . $incoming_showDashboard_loadFromDB .  "///" . $outgoing_showDashboard_loadFromDB .  "///" . $mailbox_showLegend_loadFromDB .  "///" . $outgoing_kodSenderDefault_loadFromDB .  "///" . $outgoing_kodRecipientOrgDefault_loadFromDB .  "///" . $outgoing_kodIspolDefault_loadFromDB .  "///" . $outgoing_setMeIspolOnStart_loadFromDB .  "///" . $outgoing_textAboutDefault_loadFromDB .  "///" . $incoming_subscribeWeekReminder_loadFromDB .  "///" . $incoming_setControlIspolOnStart_loadFromDB .  "///" . $incoming_setDeadlineOnStart_loadFromDB .  "///" . $outgoing_setControlIspolOnStart_loadFromDB .  "///" . $outgoing_setDeadlineOnStart_loadFromDB .  "///" . $use_lightTheme_loadFromDB;
			}
		} else {
			$output1 = -1;
		}
	}
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output1 . " | " . $output2 . " | " . $output3 . " | " . $output4;
