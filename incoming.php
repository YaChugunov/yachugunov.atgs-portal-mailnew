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
# Включаем режим сессии
// session_start();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization_defaultDB($_SESSION['login'], $_SESSION['password']) == -1) {
		// Редирект на главную страницу
?>
		<meta http-equiv="refresh" content="0; url=<?php echo __ROOT; ?>">
	<?php
	} else {
		// при удачном входе пользователю выдается все, что расположено ниже между звездочками.
		// ************************************************************************************
		logActivity();

		if (checkServiceAccess_defaultDB('allservices') == 1) {
			if ((checkServiceAccess_defaultDB('mailnew') == 1 && $_SESSION['id'] != '1011') or (checkServiceAccess_defaultDB('mailnew') == 0 && checkIsItSuperadmin_defaultDB($_SESSION['id']) == 1)) {
				if (isset($_GET['mode']) && $_GET['mode'] != "archive" && $_GET['mode'] != "profile") {
					require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_HEADER);
					if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 5, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR5', '/restr_5.test');
						} else {
							define('__MAIL_RESTR5', '/restr_5');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5 . '/mailbox-incoming.php');
					} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 4, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR4', '/restr_4.test');
						} else {
							define('__MAIL_RESTR4', '/restr_4');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR4 . '/mailbox-incoming.php');
					} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR3', '/restr_3.test');
						} else {
							define('__MAIL_RESTR3', '/restr_3');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR3 . '/mailbox-incoming.php');
					} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR2', '/restr_2.test');
						} else {
							define('__MAIL_RESTR2', '/restr_2');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR2 . '/mailbox-incoming.php');
					} else {
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/msg.inc/message_service-nopermission185.php');
					}
					require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_FOOTER);
				} elseif (isset($_GET['mode']) && $_GET['mode'] == "archive") {
					require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_HEADER);
					if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 5, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR5', '/restr_5.test');
						} else {
							define('__MAIL_RESTR5', '/restr_5');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5 . '/mailbox-incoming.php');
					} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 4, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR4', '/restr_4.test');
						} else {
							define('__MAIL_RESTR4', '/restr_4');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR4 . '/mailbox-incoming-archive.php');
					} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR3', '/restr_3.test');
						} else {
							define('__MAIL_RESTR3', '/restr_3');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3 . '/mailbox-incoming-archive.php');
					} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR2', '/restr_2.test');
						} else {
							define('__MAIL_RESTR2', '/restr_2');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR2 . '/mailbox-incoming-archive.php');
					} else {
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/msg.inc/message_service-nopermission185.php');
					}
					require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_FOOTER);
				} elseif (isset($_GET['mode']) && $_GET['mode'] == "profile") {
					require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_HEADER_PROFILE);
					if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 5, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR5', '/restr_5.test');
						} else {
							define('__MAIL_RESTR5', '/restr_5');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_PROFILE_WORKPATH . __MAIL_RESTR5 . '/mailbox-incoming-profile.php');
					} else {
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/msg.inc/message_service-nopermission185.php');
					}
					require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_FOOTER_PROFILE);
				}
				// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
				// ДЛЯ ТЕСТИРОВАНИЯ
				// ТОЛЬКО для пользователя TestMode UserName с userID = 1011
				//
			} elseif ((checkServiceAccess_defaultDB('mailnew') == 1) && $_SESSION['id'] == '1011') {
				if (isset($_GET['mode']) && $_GET['mode'] != "archive" && $_GET['mode'] != "profile") {
					require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_HEADER);
					if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 5, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR5', '/restr_5.test');
						} else {
							define('__MAIL_RESTR5', '/restr_5');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5 . '/mailbox-incoming.php');
					} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 4, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR4', '/restr_4.test');
						} else {
							define('__MAIL_RESTR4', '/restr_4');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR4 . '/mailbox-incoming.php');
					} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR3', '/restr_3.test');
						} else {
							define('__MAIL_RESTR3', '/restr_3');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR3 . '/mailbox-incoming.php');
					} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR2', '/restr_2.test');
						} else {
							define('__MAIL_RESTR2', '/restr_2');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR2 . '/mailbox-incoming.php');
					} else {
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/msg.inc/message_service-nopermission185.php');
					}
					require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_FOOTER);
				} elseif (isset($_GET['mode']) && $_GET['mode'] == "archive") {
					require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_HEADER);
					if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 5, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR5', '/restr_5.test');
						} else {
							define('__MAIL_RESTR5', '/restr_5');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5 . '/mailbox-incoming.php');
					} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 4, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR4', '/restr_4.test');
						} else {
							define('__MAIL_RESTR4', '/restr_4');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR4 . '/mailbox-incoming-archive.php');
					} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR3', '/restr_3.test');
						} else {
							define('__MAIL_RESTR3', '/restr_3');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3 . '/mailbox-incoming-archive.php');
					} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR2', '/restr_2.test');
						} else {
							define('__MAIL_RESTR2', '/restr_2');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR2 . '/mailbox-incoming-archive.php');
					} else {
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/msg.inc/message_service-nopermission185.php');
					}
					require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_FOOTER);
				} elseif (isset($_GET['mode']) && $_GET['mode'] == "profile") {
					require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_HEADER_PROFILE);
					if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 5, 1) == 1) {
						if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
							define('__MAIL_RESTR5', '/restr_5.test');
						} else {
							define('__MAIL_RESTR5', '/restr_5');
						}
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_PROFILE_WORKPATH . __MAIL_RESTR5 . '/mailbox-incoming-profile.php');
					} else {
						include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/msg.inc/message_service-nopermission185.php');
					}
					require(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_FOOTER_PROFILE);
				}
				//
				// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
			} else {
				include(__DIR_ROOT . '/_assets/includes/msg.inc/message_service-noaccess185.php');
			}
		} elseif (checkServiceAccess_defaultDB('allservices') == 0 && checkIsItSuperadmin_defaultDB($_SESSION['id']) == 1) {
			if (!isset($_GET['mode']) or $_GET['mode'] != "archive") {
				if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 5, 1) == 1) {
					if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
						define('__MAIL_RESTR5', '/restr_5.test');
					} else {
						define('__MAIL_RESTR5', '/restr_5');
					}
					include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5 . '/mailbox-incoming.php');
				} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 4, 1) == 1) {
					if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
						define('__MAIL_RESTR4', '/restr_4.test');
					} else {
						define('__MAIL_RESTR4', '/restr_4');
					}
					include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR4 . '/mailbox-incoming.php');
				} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
					if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
						define('__MAIL_RESTR3', '/restr_3.test');
					} else {
						define('__MAIL_RESTR3', '/restr_3');
					}
					include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR3 . '/mailbox-incoming.php');
				} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
					include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR2 . '/mailbox-incoming.php');
				} else {
					include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/msg.inc/message_service-nopermission185.php');
				}
			} elseif ($_GET['mode'] == "archive") {
				if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 5, 1) == 1) {
					if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
						define('__MAIL_RESTR5', '/restr_5.test');
					} else {
						define('__MAIL_RESTR5', '/restr_5');
					}
					include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5 . '/mailbox-incoming.php');
				} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 4, 1) == 1) {
					if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
						define('__MAIL_RESTR4', '/restr_4.test');
					} else {
						define('__MAIL_RESTR4', '/restr_4');
					}
					include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR4 . '/mailbox-incoming-archive.php');
				} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
					if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
						define('__MAIL_RESTR3', '/restr_3.test');
					} else {
						define('__MAIL_RESTR3', '/restr_3');
					}
					include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3 . '/mailbox-incoming-archive.php');
				} elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
					if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
						define('__MAIL_RESTR2', '/restr_2.test');
					} else {
						define('__MAIL_RESTR2', '/restr_2');
					}
					include(__DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR2 . '/mailbox-incoming-archive.php');
				} else {
					include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/msg.inc/message_service-nopermission185.php');
				}
			}
		} else {
			include(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/msg.inc/message_service-nopermission185.php');
		}
		// ************************************************************************************
		// при удачном входе пользователю выдается все, что расположено ВЫШЕ между звездочками.
	}
} else {
	// Редирект на главную страницу
	?>
	<meta http-equiv="refresh" content="0; url=<?php echo __ROOT; ?>">
<?php
}
?>