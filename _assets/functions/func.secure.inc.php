<?php
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция проверки доступности сайта/хоста
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# See code status - http://curl.haxx.se/libcurl/c/libcurl-errors.html
# Пример использования:
/*
$url = array( 'http://site1.com/', 'http://site2.com/', 'http://site3.com/', 'http://site4.com/' );
foreach ($url as $val)
{
	$answer = check_http_status($val);
	$answer = check_http_status($val);
	if ($answer == 200)
		echo 'Site '.$val.' is avaliable.', PHP_EOL;
	else {
		if ($answer == 28) {
			echo 'Resource '.$val.' is not responding. Time out operation (more than 10 sec)'. PHP_EOL;
		}
		else {
			echo 'Resource '.$val.' is not avaliable. Reason: '.$answer.'. ', PHP_EOL;
		}
	}
}
*/
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция для генерации случайной строки (hash)
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function getServiceVersion($service) {
	$result = array();
	$QRY = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM aconf_versions WHERE service='$service'"));
	if ($QRY['beta'] !== 1) {
		$result = !empty($QRY) ? '<span class="badge badge-primary px-2 py-1"><span class="">' . $QRY['name'] . '</span><i class="fa-solid fa-ellipsis-vertical mx-1"></i><span class="mr-1">версия</span>' . $QRY['version_s'] . '</span>' : "";
	} else {
		$result = !empty($QRY) ? '<span class="badge badge-danger px-2 py-1"><span class="">' . $QRY['name'] . '</span><i class="fa-solid fa-ellipsis-vertical mx-1"></i><span class="mr-1">версия</span>' . $QRY['version_s'] . ' beta</span>' : "";
	}
	return $result;
}

# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function check_http_status($url) {
	$user_agent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	$page = curl_exec($ch);

	$error = curl_errno($ch);
	if (!empty($error)) {
		return "err:" . $error;
	} else {
		return ($httpcode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE));
	}
	curl_close($ch);
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function mysqlQuery($sql) {
	$result = mysqli_query(db::$linkDB, $sql) or die(mysqli_error(db::$linkDB));
	return $result;
}
function mysqlQuery_Remote($sql) {
	$result = mysqli_query(db::$linkDB_Remote, $sql) or die(mysqli_error(db::$linkDB_Remote));
	return $result;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция для генерации случайной строки (hash)
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function generateCode($length) {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
	$code = "";
	$clen = strlen($chars) - 1;
	while (strlen($code) < $length) {
		$code .= $chars[mt_rand(0, $clen)];
	}
	return $code;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция проверки авторизации
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function checkUserAuthorization($_funcLogin, $_funcPass) {
	// «AND activation='1'» - пользователь будет искаться только среди активированных
	mysqlQuery(
		"SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'"
	);
	$query_checkUser = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM users WHERE login='$_funcLogin' AND password='$_funcPass' AND activation='1'"));
	// 	echo mysqli_error();
	if (!empty($query_checkUser['id'])) {
		$_funcStatus = $query_checkUser['id'];
	} else {
		$_funcStatus = -1;
	}
	return $_funcStatus;
}
function checkUserAuthorization_defaultDB($_funcLogin, $_funcPass) {
	// «AND activation='1'» - пользователь будет искаться только среди активированных
	mysqlQuery_defaultDB(
		"SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'"
	);
	$query_checkUser = mysqli_fetch_assoc(mysqlQuery_defaultDB("SELECT * FROM users WHERE login='$_funcLogin' AND password='$_funcPass' AND activation='1'"));
	// 	echo mysqli_error();
	if (!empty($query_checkUser['id'])) {
		$_funcStatus = $query_checkUser['id'];
	} else {
		$_funcStatus = -1;
	}
	return $_funcStatus;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция сохранения номера сессии
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function _saveSessionID($_funcLogin, $_funcPass, $_funcCookiesSave, $_funcAutoLogin) {
	$checkUserStatus = checkUserAuthorization($_funcLogin, $_funcPass);
	if ($checkUserStatus != -1) {
		// «AND activation='1'» - пользователь будет искаться только среди активированных
		mysqlQuery("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
		$query_checkUser = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM users WHERE login='$_funcLogin' AND password='$_funcPass' AND activation='1'"));
		$_id = $query_checkUser['id'];
		// Если пароли совпадают, то запускаем пользователю сессию! Можете его поздравить, он вошел!
		$_SESSION['password'] = $query_checkUser['password'];
		$_SESSION['login'] = $query_checkUser['login'];
		// Эти данные очень часто используются, вот их и будет "носить с собой" вошедший пользователь
		$_SESSION['id'] = $_id;
		$_SESSION['permissions'] = $query_checkUser['permissions'];
		$_SESSION['firstname'] = $query_checkUser['firstname'];
		$_SESSION['middlename'] = $query_checkUser['middlename'];
		$_SESSION['lastname'] = $query_checkUser['lastname'];
		// -----
		$query_checkRestrictions = mysqli_fetch_assoc(mysqlQuery(" SELECT * FROM users_restrictions WHERE id='$_id' AND status='1' "));
		if ($query_checkRestrictions) {
			$_SESSION['restrictions']['portal'] 	= $query_checkRestrictions['portal'];
			$_SESSION['restrictions']['mail'] 		= $query_checkRestrictions['mail'];
			$_SESSION['restrictions']['dognet']	= $query_checkRestrictions['dognet'];
			$_SESSION['restrictions']['ism']	 	= $query_checkRestrictions['ism'];
			$_SESSION['restrictions']['hr'] 		= $query_checkRestrictions['hr'];
			$_SESSION['restrictions']['piter'] 	= $query_checkRestrictions['piter'];
			$_SESSION['restrictions']['auto'] 		= $query_checkRestrictions['auto'];
		}
		/*
		Далее мы запоминаем данные в куки, для последующего входа.
		ВНИМАНИЕ!!! ДЕЛАЙТЕ ЭТО НА ВАШЕ УСМОТРЕНИЕ, ТАК КАК ДАННЫЕ ХРАНЯТСЯ В КУКАХ БЕЗ ШИФРОВКИ
		*/
		if ($_funcCookiesSave == 'saveCookies') {
			// Если пользователь хочет, чтобы его данные сохранились для последующего входа, то сохраняем в куках его браузера
			setcookie("login", $_POST["login"], time() + 9999999);
			setcookie("password", $_POST["password"], time() + 9999999);
			setcookie("id", $query_checkUser['id'], time() + 9999999);
		}
		if ($_funcAutoLogin == 'autoLogin') {
			// Если пользователь хочет входить на сайт автоматически
			setcookie("auto", "yes", time() + 9999999);
			setcookie("login", $_POST["login"], time() + 9999999);
			setcookie("password", $_POST["password"], time() + 9999999);
			setcookie("id", $query_checkUser['id'], time() + 9999999);
		}
	}
	return;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция вывода русского названия месяца в стандартном формате
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function rdate($param, $time = 0) {
	if (intval($time) == 0) $time = time();
	$MonthNames = array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
	if (strpos($param, 'M') === false) return date($param, $time);
	else return date(str_replace('M', $MonthNames[date('n', $time) - 1], $param), $time);
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция проверки прав администратора
# +++++ (  )
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function checkIsItSuperadmin($id) {
	$query = mysqlQuery(" SELECT superadmin FROM users WHERE id='$id' ");
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if ($row['superadmin'] == '1') {
		return 1;
	} else {
		return 0;
	}
}
function checkIsItSuperadmin_defaultDB($id) {
	$query = mysqlQuery_defaultDB(" SELECT superadmin FROM users WHERE id='$id' ");
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if ($row['superadmin'] == '1') {
		return 1;
	} else {
		return 0;
	}
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция проверки прав администратора
# +++++ (  )
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function getUserDeptNum($id) {
	$query = mysqlQuery(" SELECT dept_num FROM users WHERE id='$id' ");
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if ($row['dept_num'] != 0) {
		return $row['dept_num'];
	} else {
		return 0;
	}
}
function getUserDeptNum_defaultDB($id) {
	$query = mysqlQuery_defaultDB(" SELECT dept_num FROM users WHERE id='$id' ");
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if ($row['dept_num'] != 0) {
		return $row['dept_num'];
	} else {
		return 0;
	}
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция проверки является ли пользователь договорной программы ГИПом
# +++++ (  )
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function checkKoduseGIP($id) {
	$query1 = mysqlQuery(" SELECT kodusegip, kodispol FROM dognet_users_kods WHERE id='$id' ");
	$row1 = mysqli_fetch_array($query1, MYSQLI_ASSOC);
	if ($row1['kodispol'] != 0) {
		if ($row1['kodusegip'] == 1) {
			return 1;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}
function checkKoduseGIP_defaultDB($id) {
	$query1 = mysqlQuery_defaultDB(" SELECT kodusegip, kodispol FROM dognet_users_kods WHERE id='$id' ");
	$row1 = mysqli_fetch_array($query1, MYSQLI_ASSOC);
	if ($row1['kodispol'] != 0) {
		if ($row1['kodusegip'] == 1) {
			return 1;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция проверки является ли пользователь договорной программы ГИПом
# +++++ (  )
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function checkIsItGIP($id) {
	$query = mysqlQuery(" SELECT dognet, dognet_gip FROM users_restrictions WHERE id='$id' ");
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (($row['dognet'] >= 3) and ($row['dognet_gip'] > 0)) {
		return 1;
	} else {
		return 0;
	}
}
function checkIsItGIP_defaultDB($id) {
	$query = mysqlQuery_defaultDB(" SELECT dognet, dognet_gip FROM users_restrictions WHERE id='$id' ");
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (($row['dognet'] >= 3) and ($row['dognet_gip'] > 0)) {
		return 1;
	} else {
		return 0;
	}
}
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция проверки является ли пользователь заявителем на закупку (ZAYVTEL)
# +++++ (  )
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function checkIsItZAYV($id) {
	$query = mysqlQuery(" SELECT dognet, dognet_zayv FROM users_restrictions WHERE id='$id' ");
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (($row['dognet'] >= 1) and ($row['dognet_zayv'] > 0)) {
		return 1;
	} else {
		return 0;
	}
}
function checkIsItZAYV_defaultDB($id) {
	$query = mysqlQuery_defaultDB(" SELECT dognet, dognet_zayv FROM users_restrictions WHERE id='$id' ");
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (($row['dognet'] >= 1) and ($row['dognet_zayv'] > 0)) {
		return 1;
	} else {
		return 0;
	}
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function checkUserRestrictions2($id, $service_name, $min_restr, $max_restr) {
	$checkUserStatus = checkUserAuthorization($_SESSION['login'], $_SESSION['password']);
	if ($checkUserStatus != -1) {
		$query_userRestrictions = mysqlQuery(" SELECT " . $service_name . " FROM users_restrictions WHERE id='$id' AND status='1' ");
		$row_userRestrictions = mysqli_fetch_array($query_userRestrictions, MYSQLI_ASSOC);
		if ($row_userRestrictions[$service_name] >= $min_restr && $row_userRestrictions[$service_name] <= $max_restr) {
			return 1;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function getUserRestrictions($id, $service_name) {
	$checkUserStatus = checkUserAuthorization($_SESSION['login'], $_SESSION['password']);
	if ($checkUserStatus != -1) {
		$query_userRestrictions = mysqlQuery(" SELECT " . $service_name . " FROM users_restrictions WHERE id='$id' AND status='1' ");
		$row_userRestrictions = mysqli_fetch_array($query_userRestrictions, MYSQLI_ASSOC);
		$USER_ACCESS_LVL = $row_userRestrictions[$service_name];
		return $USER_ACCESS_LVL;
	} else {
		return 0;
	}
}
function getUserRestrictions_defaultDB($id, $service_name) {
	$checkUserStatus = checkUserAuthorization_defaultDB($_SESSION['login'], $_SESSION['password']);
	if ($checkUserStatus != -1) {
		$query_userRestrictions = mysqlQuery_defaultDB(" SELECT " . $service_name . " FROM users_restrictions WHERE id='$id' AND status='1' ");
		$row_userRestrictions = mysqli_fetch_array($query_userRestrictions, MYSQLI_ASSOC);
		$USER_ACCESS_LVL = $row_userRestrictions[$service_name];
		return $USER_ACCESS_LVL;
	} else {
		return 0;
	}
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### 
##### ПРОВЕРЯЕМ НАХОДИТСЯ ЛИ ПОЛЬЗОВАТЕЛЬ В РЕЖИМЕ ТЕСТИРОВАНИЯ СЕРВИСА
##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function checkUserRestrictions_defaultDB($id, $service_name, $min_restr, $flag) {
	$checkUserStatus = checkUserAuthorization($_SESSION['login'], $_SESSION['password']);
	if ($checkUserStatus != -1) {
		$query_userRestrictions = mysqlQuery_defaultDB(" SELECT " . $service_name . " FROM users_restrictions WHERE id='$id' AND status='1' ");
		$row_userRestrictions = mysqli_fetch_array($query_userRestrictions, MYSQLI_ASSOC);
		if ($flag == 1) {
			if ($row_userRestrictions[$service_name] == $min_restr) {
				return 1;
			} else {
				return 0;
			}
		} else {
			if ($row_userRestrictions[$service_name] >= $min_restr) {
				return 1;
			} else {
				return 0;
			}
		}
	} else {
		return 0;
	}
}
function checkUserRestrictions($id, $service_name, $min_restr, $flag) {
	$checkUserStatus = checkUserAuthorization($_SESSION['login'], $_SESSION['password']);
	if ($checkUserStatus != -1) {
		$query_userRestrictions = mysqlQuery(" SELECT " . $service_name . " FROM users_restrictions WHERE id='$id' AND status='1' ");
		$row_userRestrictions = mysqli_fetch_array($query_userRestrictions, MYSQLI_ASSOC);
		if ($flag == 1) {
			if ($row_userRestrictions[$service_name] == $min_restr) {
				return 1;
			} else {
				return 0;
			}
		} else {
			if ($row_userRestrictions[$service_name] >= $min_restr) {
				return 1;
			} else {
				return 0;
			}
		}
	} else {
		return 0;
	}
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### 
##### Функция проверки открыт/закрыт ли сервис
##### (используется для закрытия сервиса на техническое обслуживание)
##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function checkServiceAccess($service) {
	$query_allServicesAccess = mysqlQuery("SELECT access FROM service_access WHERE service_name='allservices'");
	$query_serviceAccess = mysqlQuery("SELECT access FROM service_access WHERE service_name='$service'");
	$row_allServicesAccess = mysqli_fetch_array($query_allServicesAccess, MYSQLI_ASSOC);
	$row_serviceAccess = mysqli_fetch_array($query_serviceAccess, MYSQLI_ASSOC);
	if ($row_allServicesAccess['access'] == '1') {
		if ($row_serviceAccess['access'] == '1') {
			return 1;
		} elseif ($row_serviceAccess['access'] == '0' && checkIsItSuperadmin($_SESSION['id']) == 1) {
			return 1;
		} else {
			return 0;
		}
	} elseif ($row_allServicesAccess['access'] == '0' && checkIsItSuperadmin($_SESSION['id']) == 1) {
		return 1;
	} else {
		return 0;
	}
}
#
function checkServiceAccess_defaultDB($service) {
	$query_allServicesAccess = mysqlQuery_defaultDB("SELECT access FROM service_access WHERE service_name='allservices'");
	$query_serviceAccess = mysqlQuery_defaultDB("SELECT access FROM service_access WHERE service_name='$service'");
	$row_allServicesAccess = mysqli_fetch_array($query_allServicesAccess, MYSQLI_ASSOC);
	$row_serviceAccess = mysqli_fetch_array($query_serviceAccess, MYSQLI_ASSOC);
	if ($row_allServicesAccess['access'] == '1') {
		if ($row_serviceAccess['access'] == '1') {
			return 1;
		} elseif ($row_serviceAccess['access'] == '0' && checkIsItSuperadmin_defaultDB($_SESSION['id']) == 1) {
			return 1;
		} else {
			return 0;
		}
	} elseif ($row_allServicesAccess['access'] == '0' && checkIsItSuperadmin_defaultDB($_SESSION['id']) == 1) {
		return 1;
	} else {
		return 0;
	}
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция проверки открыт/закрыт ли сервис
# +++++ (используется для закрытия сервиса на техническое обслуживание)
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function checkDognetSectionAccess($section, $type) {
	$query_sectionAccess = mysqlQuery("SELECT * FROM dognet_section_access WHERE section_name='$section'");
	$row_sectionAccess = mysqli_fetch_array($query_sectionAccess, MYSQLI_ASSOC);
	if ($row_sectionAccess[$type] == '1') {
		return 1;
	} elseif ($row_sectionAccess[$type] == '0' && checkIsItSuperadmin($_SESSION['id']) == 1) {
		return 1;
	} else {
		return 0;
	}
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция проверки открыт/закрыт ли сервис
# +++++ (используется для закрытия сервиса на техническое обслуживание)
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function checkUsergroupAccess($service) {
	$query_serviceAccess = mysqlQuery(" SELECT access FROM service_access WHERE service_name='$service' ");
	$row_serviceAccess = mysqli_fetch_array($query_serviceAccess, MYSQLI_ASSOC);
	if ($row_serviceAccess['access'] == '1') {
		return 1;
	} else {
		return 0;
	}
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция проверки открыта/закрыта ли ВСЯ СИСТЕМА на техобслуживание
# +++++ (используется для закрытия ВСЕЙ СИСТЕМЫ на техническое обслуживание)
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function checkUnderconstructionMode() {
	$query_sysAccess = mysqlQuery_defaultDB(" SELECT access FROM service_access WHERE service_name='underconstruction' ");
	$row_sysAccess = mysqli_fetch_array($query_sysAccess, MYSQLI_ASSOC);
	if ($row_sysAccess['access'] == '1') {
		return 1;
	} else {
		return 0;
	}
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция фиксации времени активности и страницы пребывания пользователя
# +++++ (  )
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function logActivity() {

	// Обновляем метку последней активности в БД
	if (!empty($_SESSION['login']) && !empty($_SESSION['password'])) {
		$__id = $_SESSION['id'];
		$__login = $_SESSION['login'];
		$__lastname = $_SESSION['lastname'];
		$__password = $_SESSION['password'];
		$__script = $_SERVER['PHP_SELF'];
		$__SESSID = session_id();
		$__userAgent = $_SERVER['HTTP_USER_AGENT'];
		$__ip = $_SERVER['REMOTE_ADDR'];

		// Если пользователь авторизуется с новой сессией, то помечаем status = 2, что означает, что предыдущая сессия была удалена по времени или по закрытию браузера
		// status = 0 , если сесиия была закрыта пользователем по выходу из сервиса

		mysqlQuery("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

		// 	mysqlQuery( "UPDATE log_auth SET status = '2', comment='Сессия была закрыта' WHERE user_id = '$__id' AND NOT SESSID = '$__SESSID' AND NOT status = '0'" );

		// 	mysqlQuery( "INSERT INTO log_auth ( user_id , user_login , status , login_timestamp , ip , SESSID , user_agent , comment ) VALUES ( '$__id' , '$__login' , '1' , NOW() , '$__ip' , '$__SESSID' , '$__userAgent' , 'Новая сессия' )" );

		$result1 = mysqlQuery("SELECT * FROM log_auth WHERE user_login = '$__login' AND status = '1' AND SESSID = '$__SESSID'");
		$row1 = mysqli_fetch_assoc($result1);
		if ($row1) {
			// ----- ----- ----- ----- -----
			$__loginTimestamp = $row1['login_timestamp'];
			$__logoutTimestamp = $row1['logout_timestamp'];
			$__sessID0 = $row1['SESSID'];

			if ($row1['ip'] == $__ip && $row1['SESSID'] == $__SESSID) {
				mysqlQuery("UPDATE log_auth SET lastactivity_timestamp = NOW(), lastactivity_script = '$__script' WHERE user_login = '$__login' AND SESSID = '$__SESSID' AND status = '1'");
			} elseif ($row1['ip'] != $__ip && $row1['SESSID'] == $__SESSID) {
				mysqlQuery("UPDATE log_auth SET lastactivity_timestamp = NOW(), lastactivity_script = '$__script', status = '0', comment = 'Сессия устарела, более не активна' WHERE user_login = '$__login' AND SESSID = '$__SESSID' AND status = '1'");
				mysqlQuery("INSERT INTO log_auth (user_id, ip, user_login, user_lastname, comment, login_timestamp, logout_timestamp, lastactivity_timestamp, lastactivity_script, SESSID, user_agent, status) VALUES ('$__id', '$__ip', '$__login', '$__lastname', 'Смена IP в текущей сессии', '$__loginTimestamp', '$__logoutTimestamp', NOW(), '$__script', '$__SESSID', '$__userAgent', '2')");
			} elseif ($row1['ip'] == $__ip && $row1['SESSID'] != $__SESSID) {
				mysqlQuery("UPDATE log_auth SET lastactivity_timestamp = NOW(), lastactivity_script = '$__script', status = '0', comment = 'Сессия устарела, более не активна' WHERE user_login = '$__login' AND SESSID = '$__SESSID' AND status = '1'");
				mysqlQuery("INSERT INTO log_auth (user_id, ip, user_login, user_lastname, comment, login_timestamp, logout_timestamp, lastactivity_timestamp, lastactivity_script, SESSID, user_agent, status) VALUES ('$__id', '$__ip', '$__login', '$__lastname', 'Новая сессия на активном IP', '$__loginTimestamp', '$__logoutTimestamp', NOW(), '$__script', '$__SESSID', '$__userAgent', '1')");
			}
			// ----- ----- ----- ----- -----
		}
	}
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция загрузки баннер с объявлением
# +++++ (используется для закрытия сервиса на техническое обслуживание)
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function loadBanner($service) {
	$query_serviceAccess = mysqlQuery(" SELECT access FROM service_access WHERE service_name='$service' ");
	$row_serviceAccess = mysqli_fetch_array($query_serviceAccess, MYSQLI_ASSOC);
	if ($row_serviceAccess['access'] == '1') {
		return 1;
	} else {
		return 0;
	}
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function PORTAL_SYSLOG($kodgroup, $kodmsg, $row_id, $param1, $param2, $param3) {
	#
	# Определяем сервис с которым работаем
	$__service_id = substr($kodgroup, 3, 1);
	$__subservice_lvl0 = substr($kodgroup, 4, 1);
	$__subservice_lvl1 = substr($kodgroup, 5, 1);
	$__subservice_lvl2 = substr($kodgroup, 6, 1);
	$__subservice_lvl3 = substr($kodgroup, 7, 1);
	# --- --- ---
	if ($row_id !== '') {
		$__docID = '';
		$__docNumber = '';
		$__info1 = '';
		$__info2 = $param3;
		$__msgSyslog_forPortalMain = '';
		switch ($__service_id) {
			case "2":
				if ($__subservice_lvl0 == "1") {
					if (substr($kodmsg, -1) == "F") {
						$_QRY_20 = mysqlQuery("SELECT * FROM mailbox_incoming_files WHERE id = '$row_id'");
						$_ROW_20 = mysqli_fetch_array($_QRY_20, MYSQLI_ASSOC);
						$__docID = !empty($param1) ? $param1 : "";
						$__docNumber = $param2;
						$__info1 = "";
						$__info2 = "";
					} elseif (substr($kodmsg, -1) == "D") {
						$_QRY_20 = mysqlQuery("SELECT * FROM mailbox_incoming_files WHERE id = '$row_id'");
						$_ROW_20 = mysqli_fetch_array($_QRY_20, MYSQLI_ASSOC);
						$__docID = !empty($param1) ? $param1 : "";
						$__docNumber = $param2;
						$__info1 = "";
						$__info2 = "";
					} elseif (substr($kodmsg, -1) == "1") {
						$_QRY_20 = mysqlQuery("SELECT * FROM mailbox_incoming WHERE id = '$row_id'");
						$_ROW_20 = mysqli_fetch_array($_QRY_20, MYSQLI_ASSOC);
						$__docID = $param1;
						$__docNumber = $param2;
						$__info1 = "Создано письмо # " . $param2;
						$__info2 = "";
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_20 = mysqlQuery("SELECT * FROM mailbox_incoming WHERE id = '$row_id'");
						$_ROW_20 = mysqli_fetch_array($_QRY_20, MYSQLI_ASSOC);
						$__docID = $_ROW_20['koddocmail'];
						$__docNumber = $_ROW_20['inbox_docID'];
						$__info1 = "Изменено письмо # " . $_ROW_20['inbox_docID'];
						$__info2 = "";
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_20 = mysqlQuery("SELECT * FROM mailbox_incoming WHERE id = '$row_id'");
						$_ROW_20 = mysqli_fetch_array($_QRY_20, MYSQLI_ASSOC);
						$__docID = $_ROW_20['koddocmail'];
						$__docNumber = $_ROW_20['inbox_docID'];
						$__info1 = "Удалено письмо # " . $_ROW_20['inbox_docID'];
						$__info2 = "";
					}
				}
				if ($__subservice_lvl0 == "2") {
					if (substr($kodmsg, -1) == "F") {
						$_QRY_20 = mysqlQuery("SELECT * FROM mailbox_outgoing_files WHERE id = '$row_id'");
						$_ROW_20 = mysqli_fetch_array($_QRY_20, MYSQLI_ASSOC);
						$__docID = !empty($param1) ? $param1 : "";
						$__docNumber = $param2;
						$__info1 = "";
						$__info2 = "";
					} elseif (substr($kodmsg, -1) == "D") {
						$_QRY_20 = mysqlQuery("SELECT * FROM mailbox_outgoing_files WHERE id = '$row_id'");
						$_ROW_20 = mysqli_fetch_array($_QRY_20, MYSQLI_ASSOC);
						$__docID = !empty($param1) ? $param1 : "";
						$__docNumber = $param2;
						$__info1 = "";
						$__info2 = "";
					} elseif (substr($kodmsg, -1) == "1") {
						$_QRY_20 = mysqlQuery("SELECT * FROM mailbox_outgoing WHERE id = '$row_id'");
						$_ROW_20 = mysqli_fetch_array($_QRY_20, MYSQLI_ASSOC);
						$__docID = $param1;
						$__docNumber = $param2;
						$__info1 = "Создано письмо # " . $param2;
						$__info2 = "";
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_20 = mysqlQuery("SELECT * FROM mailbox_outgoing WHERE id = '$row_id'");
						$_ROW_20 = mysqli_fetch_array($_QRY_20, MYSQLI_ASSOC);
						$__docID = $_ROW_20['koddocmail'];
						$__docNumber = $_ROW_20['outbox_docID'];
						$__info1 = "Изменено письмо # " . $_ROW_20['outbox_docID'];
						$__info2 = "";
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_20 = mysqlQuery("SELECT * FROM mailbox_outgoing WHERE id = '$row_id'");
						$_ROW_20 = mysqli_fetch_array($_QRY_20, MYSQLI_ASSOC);
						$__docID = $_ROW_20['koddocmail'];
						$__docNumber = $_ROW_20['outbox_docID'];
						$__info1 = "Удалено письмо # " . $_ROW_20['outbox_docID'];
						$__info2 = "";
					}
				}
				break;
			case "3":
				#
				# ...
				if ($__subservice_lvl0 == "1") {
					if (substr($kodmsg, -1) == "1") {
						$_QRY_31000 = mysqlQuery("SELECT * FROM dognet_docbase WHERE id = '$row_id'");
						$_ROW_31000 = mysqli_fetch_array($_QRY_31000, MYSQLI_ASSOC);
						$__docID = $param1;
						$__info1 = "Создан договор #" . $param2;
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_31000 = mysqlQuery("SELECT * FROM dognet_docbase WHERE id = '$row_id'");
						$_ROW_31000 = mysqli_fetch_array($_QRY_31000, MYSQLI_ASSOC);
						$__docID = $_ROW_31000['koddoc'];
						$__info1 = "Изменен договор #" . $_ROW_31000['docnumber'];
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_31000 = mysqlQuery("SELECT * FROM dognet_docbase WHERE id = '$row_id'");
						$_ROW_31000 = mysqli_fetch_array($_QRY_31000, MYSQLI_ASSOC);
						$__docID = $_ROW_31000['koddoc'];
						$__info1 = "Удален договор #" . $_ROW_31000['docnumber'];
					}
				}
				#
				# ...
				if ($__subservice_lvl1 == "2") {
					if (substr($kodmsg, -1) == "1") {
						$_QRY_30200 = mysqlQuery("SELECT * FROM dognet_dockalplan WHERE id = '$row_id'");
						$_ROW_30200 = mysqli_fetch_array($_QRY_30200, MYSQLI_ASSOC);
						$__docID = $param1;
						$__info1 = "Создан этап #" . $param2;
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_30200 = mysqlQuery("SELECT * FROM dognet_dockalplan WHERE id = '$row_id'");
						$_ROW_30200 = mysqli_fetch_array($_QRY_30200, MYSQLI_ASSOC);
						$__docID = $_ROW_30200['kodkalplan'];
						$__info1 = "Изменен этап #" . $_ROW_30200['numberstage'];
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_30200 = mysqlQuery("SELECT * FROM dognet_dockalplan WHERE id = '$row_id'");
						$_ROW_30200 = mysqli_fetch_array($_QRY_30200, MYSQLI_ASSOC);
						$__docID = $_ROW_30200['kodkalplan'];
						$__info1 = "Удален этап #" . $_ROW_30200['numberstage'];
					}
				}
				#
				# Обрабатываем операции по счетам-фактурам этапа счета
				if ($__subservice_lvl2 == "1") {
					if (substr($kodmsg, -1) == "1") {
						$_QRY_30010 = mysqlQuery("SELECT * FROM dognet_kalplanchf WHERE id = '$row_id'");
						$_ROW_30010 = mysqli_fetch_array($_QRY_30010, MYSQLI_ASSOC);
						$__docID = $param1;
						$__info1 = "Добавлен счет-фактура #" . $param3;
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_30010 = mysqlQuery("SELECT * FROM dognet_kalplanchf WHERE id = '$row_id'");
						$_ROW_30010 = mysqli_fetch_array($_QRY_30010, MYSQLI_ASSOC);
						$__docID = $_ROW_30010['kodchfact'];
						$__info1 = "Изменен счет-фактура #" . $_ROW_30010['chetfnumber'];
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_30010 = mysqlQuery("SELECT * FROM dognet_kalplanchf WHERE id = '$row_id'");
						$_ROW_30010 = mysqli_fetch_array($_QRY_30010, MYSQLI_ASSOC);
						$__docID = $_ROW_30010['kodchfact'];
						$__info1 = "Удален счет-фактура #" . $_ROW_30010['chetfnumber'];
					}
				}
				#
				# Обрабатываем операции над внесенными авансами по этапу счета
				if ($__subservice_lvl2 == "2") {
					if (substr($kodmsg, -1) == "1") {
						$_QRY_30020 = mysqlQuery("SELECT * FROM dognet_docavans WHERE id = '$row_id'");
						$_ROW_30020 = mysqli_fetch_array($_QRY_30020, MYSQLI_ASSOC);
						$__docID = $param1;
						$__info1 = "Добавлен аванс от " . $param2;
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_30020 = mysqlQuery("SELECT * FROM dognet_docavans WHERE id = '$row_id'");
						$_ROW_30020 = mysqli_fetch_array($_QRY_30020, MYSQLI_ASSOC);
						$__docID = $_ROW_30020['kodavans'];
						$__info1 = "Изменен аванс от " . $_ROW_30020['dateavans'];
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_30020 = mysqlQuery("SELECT * FROM dognet_docavans WHERE id = '$row_id'");
						$_ROW_30020 = mysqli_fetch_array($_QRY_30020, MYSQLI_ASSOC);
						$__docID = $_ROW_30020['kodavans'];
						$__info1 = "Удален аванс от " . $_ROW_30020['dateavans'];
					}
				}
				#
				# Обрабатываем операции над зачтенными платежами по счету-фактуре
				if ($__subservice_lvl3 == "1") {
					if (substr($kodmsg, -1) == "1") {
						$_QRY_30001 = mysqlQuery("SELECT * FROM dognet_oplatachf WHERE id = '$row_id'");
						$_ROW_30001 = mysqli_fetch_array($_QRY_30001, MYSQLI_ASSOC);
						$__docID = $param1;
						$__info1 = "Получен платеж от " . $param2 . " по счету ID " . $_ROW_30001['kodchfact'];
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_30001 = mysqlQuery("SELECT * FROM dognet_oplatachf WHERE id = '$row_id'");
						$_ROW_30001 = mysqli_fetch_array($_QRY_30001, MYSQLI_ASSOC);
						$__docID = $_ROW_30001['kodoplata'];
						$__info1 = "Изменен платеж от " . $_ROW_30001['dateopl'] . " по счету ID " . $_ROW_30001['kodchfact'];
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_30001 = mysqlQuery("SELECT * FROM dognet_oplatachf WHERE id = '$row_id'");
						$_ROW_30001 = mysqli_fetch_array($_QRY_30001, MYSQLI_ASSOC);
						$__docID = $_ROW_30001['kodoplata'];
						$__info1 = "Удален платеж от " . $_ROW_30001['dateopl'] . " по счету ID " . $_ROW_30001['kodchfact'];
					}
				}
				#
				# Обрабатываем операции над зачтенными авансами по счету-фактуре
				if ($__subservice_lvl3 == "2") {
					if (substr($kodmsg, -1) == "1") {
						$_QRY_30002 = mysqlQuery("SELECT * FROM dognet_chfavans WHERE id = '$row_id'");
						$_ROW_30002 = mysqli_fetch_array($_QRY_30002, MYSQLI_ASSOC);
						$__docID = $param1;
						$__info1 = "Зачтен аванс на сумму " . $param2 . " по счету ID " . $_ROW_30002['kodchfact'];
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_30002 = mysqlQuery("SELECT * FROM dognet_chfavans WHERE id = '$row_id'");
						$_ROW_30002 = mysqli_fetch_array($_QRY_30002, MYSQLI_ASSOC);
						$__docID = $_ROW_30002['kodoplata'];
						$__info1 = "Изменен зачтенный аванс на сумму " . $_ROW_30002['summaoplav'] . " по счету ID " . $_ROW_30002['kodchfact'];
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_30002 = mysqlQuery("SELECT * FROM dognet_chfavans WHERE id = '$row_id'");
						$_ROW_30002 = mysqli_fetch_array($_QRY_30002, MYSQLI_ASSOC);
						$__docID = $_ROW_30002['kodoplata'];
						$__info1 = "Удален зачтенный аванс на сумму " . $_ROW_30002['summaoplav'] . " по счету ID " . $_ROW_30002['kodchfact'];
					}
				}
				#
				#
				break;
			case "4":
				#
				# ...
				# KODGROUP = 99941XXX
				if ($__subservice_lvl0 == "1") {
					if (substr($kodmsg, -1) == "1") {
						$_QRY_41000 = mysqlQuery("SELECT * FROM dognet_docbase WHERE id = '$row_id'");
						$_ROW_41000 = mysqli_fetch_array($_QRY_41000, MYSQLI_ASSOC);
						$__docID = $param1;
						$__docNumber = $param2;
						$__info1 = "Создан договор #" . $__docNumber . " / ID:" . $__docID;
						$__info2 = $param3;
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_41000 = mysqlQuery("SELECT * FROM dognet_docbase WHERE id = '$row_id'");
						$_ROW_41000 = mysqli_fetch_array($_QRY_41000, MYSQLI_ASSOC);
						$__docID = $param1;
						$__docNumber = $param2;
						$__info1 = "Внесены изменения в договор #" . $__docNumber . " / ID:" . $__docID;
						$__info2 = $param3;
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_41000 = mysqlQuery("SELECT * FROM dognet_docbase WHERE id = '$row_id'");
						$_ROW_41000 = mysqli_fetch_array($_QRY_41000, MYSQLI_ASSOC);
						$__docID = $param1;
						$__docNumber = $param2;
						$__info1 = "Удален договор #" . $__docNumber . " / ID:" . $__docID;
						$__info2 = $param3;
					} elseif (substr($kodmsg, -1) == "F") {
						$_QRY_41000 = mysqlQuery("SELECT * FROM dognet_docbase WHERE id = '$row_id'");
						$_ROW_41000 = mysqli_fetch_array($_QRY_41000, MYSQLI_ASSOC);
						$__docID = $param1;
						//
						$_KODDOC = $param3;
						$_FILEID = $param2;
						$_QRY_DOCBASE = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '{$_KODDOC}'"), MYSQLI_ASSOC);
						$__docNumber = $_QRY_DOCBASE['docnumber'];
						$_QRY_DOCPAPER = mysqli_fetch_array(mysqlQuery("SELECT kodpaper, docFileID FROM dognet_docpaper WHERE koddocpaper = '{$__docID}'"), MYSQLI_ASSOC);
						$_KODPAPER = $_QRY_DOCPAPER['kodpaper'];
						$_QRY_TIPPAPER = mysqli_fetch_array(mysqlQuery("SELECT namepaper FROM dognet_sptippaper WHERE kodpaper = '{$_KODPAPER}'"), MYSQLI_ASSOC);
						$_NAMEPAPER = $_QRY_TIPPAPER['namepaper'];
						$__info1 = "Прикреплен документ (" . mb_strtolower($_NAMEPAPER, 'UTF-8') . ") / ID:" . $__docID . " к договору #" . $__docNumber . " (файл ID:" . $_FILEID . ")";
						$__info2 = "";
						//
					} elseif (substr($kodmsg, -1) == "D") {
						$_QRY_41000 = mysqlQuery("SELECT * FROM dognet_docbase WHERE id = '$row_id'");
						$_ROW_41000 = mysqli_fetch_array($_QRY_41000, MYSQLI_ASSOC);
						$__docID = $param1;
						//
						$_KODDOC = $param3;
						$_FILEID = $param2;
						$_QRY_DOCBASE = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '{$_KODDOC}'"), MYSQLI_ASSOC);
						$__docNumber = $_QRY_DOCBASE['docnumber'];
						$_QRY_DOCPAPER = mysqli_fetch_array(mysqlQuery("SELECT kodpaper, docFileID FROM dognet_docpaper WHERE koddocpaper = '{$__docID}'"), MYSQLI_ASSOC);
						$_KODPAPER = $_QRY_DOCPAPER['kodpaper'];
						$_QRY_TIPPAPER = mysqli_fetch_array(mysqlQuery("SELECT namepaper FROM dognet_sptippaper WHERE kodpaper = '{$_KODPAPER}'"), MYSQLI_ASSOC);
						$_NAMEPAPER = $_QRY_TIPPAPER['namepaper'];
						$__info1 = "Удален документ (" . mb_strtolower($_NAMEPAPER, 'UTF-8') . ") / ID:" . $__docID . " из договора #" . $__docNumber . " (файл ID:" . $_FILEID . ")";
						$__info2 = "";
						//
					}
				}
				#
				# ...
				# KODGROUP = 9994X1XX
				if ($__subservice_lvl1 == "1") {
					if (substr($kodmsg, -1) == "1") {
						$_QRY_40100 = mysqlQuery("SELECT * FROM dognet_docbase WHERE id = '$row_id'");
						$_ROW_40100 = mysqli_fetch_array($_QRY_40100, MYSQLI_ASSOC);
						$__docID = $param1;
						$__docNumber = $param2;
						$__info1 = "Договор # " . $param2;
						$__info2 = $param3;
					} else {
						$__docID = "";
						$__info1 = "";
						$__info2 = "";
					}
				}
				#
				# ...
				# KODGROUP = 9994X2XX
				if ($__subservice_lvl1 == "2") {
					if (substr($kodmsg, -1) == "1") {
						$_QRY_40200 = mysqlQuery("SELECT * FROM dognet_dockalplan WHERE id = '$row_id'");
						$_ROW_40200 = mysqli_fetch_array($_QRY_40200, MYSQLI_ASSOC);
						$__docID = $param1;
						//
						$_KODDOC = $param3;
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = $_QRY_DOCNUMBER['docnumber'];
						//
						$__info1 = "Создан этап #" . $param2 . " / ID:" . $__docID . " в договоре #" . $__docNumber . " / ID:" . $_KODDOC;
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_40200 = mysqlQuery("SELECT * FROM dognet_dockalplan WHERE id = '$row_id'");
						$_ROW_40200 = mysqli_fetch_array($_QRY_40200, MYSQLI_ASSOC);
						$__docID = $_ROW_40200['kodkalplan'];
						//
						$_KODDOC = $_ROW_40200['koddoc'];
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = $_QRY_DOCNUMBER['docnumber'];
						//
						$__info1 = "Внесены изменения в этап #" . $_ROW_40200['numberstage'] . " / ID:" . $__docID . " в договоре #" . $__docNumber . " / ID:" . $_KODDOC;
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_40200 = mysqlQuery("SELECT * FROM dognet_dockalplan WHERE id = '$row_id'");
						$_ROW_40200 = mysqli_fetch_array($_QRY_40200, MYSQLI_ASSOC);
						$__docID = $_ROW_40200['kodkalplan'];
						//
						$_KODDOC = $_ROW_40200['koddoc'];
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = $_QRY_DOCNUMBER['docnumber'];
						//
						$__info1 = "Удален этап #" . $_ROW_40200['numberstage'] . " / ID:" . $__docID . " в договоре #" . $__docNumber . " / ID:" . $_KODDOC;
					}
				}
				#
				# Обрабатываем операции по счетам-фактурам этапа договора
				if ($__subservice_lvl2 == "1") {
					if (substr($kodmsg, -1) == "1") {
						$_QRY_40010 = mysqlQuery("SELECT * FROM dognet_kalplanchf WHERE id = '$row_id'");
						$_ROW_40010 = mysqli_fetch_array($_QRY_40010, MYSQLI_ASSOC);
						$__docID = $param1;
						//
						$_KODKALPLAN = $param3;
						$_QRY_KODDOC = mysqli_fetch_array(mysqlQuery("SELECT koddoc, numberstage FROM dognet_dockalplan WHERE kodkalplan = '$_KODKALPLAN'"), MYSQLI_ASSOC);
						$_NUMBERSTAGE = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['numberstage'] : "--";
						$_KODDOC = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['koddoc'] : $_KODKALPLAN;
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = (!empty($_QRY_DOCNUMBER)) ? $_QRY_DOCNUMBER['docnumber'] : "";
						//
						$__info1 = "Добавлен счет-фактура #" . $param2 . " / ID:" . $__docID . " в этапе #" . $_NUMBERSTAGE . " / ID:" . $_KODKALPLAN;
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_40010 = mysqlQuery("SELECT * FROM dognet_kalplanchf WHERE id = '$row_id'");
						$_ROW_40010 = mysqli_fetch_array($_QRY_40010, MYSQLI_ASSOC);
						$__docID = $_ROW_40010['kodchfact'];
						//
						$_KODKALPLAN = $_ROW_40010['kodkalplan'];
						$_QRY_KODDOC = mysqli_fetch_array(mysqlQuery("SELECT koddoc, numberstage FROM dognet_dockalplan WHERE kodkalplan = '$_KODKALPLAN'"), MYSQLI_ASSOC);
						$_NUMBERSTAGE = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['numberstage'] : "--";
						$_KODDOC = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['koddoc'] : $_KODKALPLAN;
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = (!empty($_QRY_DOCNUMBER)) ? $_QRY_DOCNUMBER['docnumber'] : "";
						//
						$__info1 = "Внесены изменения в счет-фактуру #" . $_ROW_40010['chetfnumber'] . " / ID:" . $__docID . " в этапе #" . $_NUMBERSTAGE . " / ID:" . $_KODKALPLAN;
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_40010 = mysqlQuery("SELECT * FROM dognet_kalplanchf WHERE id = '$row_id'");
						$_ROW_40010 = mysqli_fetch_array($_QRY_40010, MYSQLI_ASSOC);
						$__docID = $_ROW_40010['kodchfact'];
						//
						$_KODKALPLAN = $_ROW_40010['kodkalplan'];
						$_QRY_KODDOC = mysqli_fetch_array(mysqlQuery("SELECT koddoc, numberstage FROM dognet_dockalplan WHERE kodkalplan = '$_KODKALPLAN'"), MYSQLI_ASSOC);
						$_NUMBERSTAGE = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['numberstage'] : "--";
						$_KODDOC = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['koddoc'] : $_KODKALPLAN;
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = (!empty($_QRY_DOCNUMBER)) ? $_QRY_DOCNUMBER['docnumber'] : "";
						//
						$__info1 = "Удален счет-фактура #" . $_ROW_40010['chetfnumber'] . " / ID:" . $__docID . " в этапе #" . $_NUMBERSTAGE . " / ID:" . $_KODKALPLAN;
					}
				}
				#
				# Обрабатываем операции над внесенными авансами по этапу договора
				if ($__subservice_lvl2 == "2") {
					if (substr($kodmsg, -1) == "1") {
						$__docID = $param1;
						//
						$_KODKALPLAN = $param3;
						$_QRY_KODDOC = mysqli_fetch_array(mysqlQuery("SELECT koddoc, numberstage FROM dognet_dockalplan WHERE kodkalplan = '$_KODKALPLAN'"), MYSQLI_ASSOC);
						$_NUMBERSTAGE = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['numberstage'] : "--";
						$_KODDOC = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['koddoc'] : $_KODKALPLAN;
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = (!empty($_QRY_DOCNUMBER)) ? $_QRY_DOCNUMBER['docnumber'] : "";
						$_ROW_40020 = mysqli_fetch_array(mysqlQuery("SELECT summaavans FROM dognet_docavans WHERE kodavans = '$__docID'"), MYSQLI_ASSOC);
						$_SUMMAAVANS = $_ROW_40020['summaavans'];
						//
						$__info1 = "Добавлен аванс от " . date('d.m.Y', strtotime($param2)) . " / ID:" . $__docID . " в этапе #" . $_NUMBERSTAGE . " / ID:" . $_KODKALPLAN;
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_40020 = mysqlQuery("SELECT * FROM dognet_docavans WHERE kodavans = '$param1'");
						$_ROW_40020 = mysqli_fetch_array($_QRY_40020, MYSQLI_ASSOC);
						$__docID = $_ROW_40020['kodavans'];
						//
						$_KODKALPLAN = $_ROW_40020['koddoc'];
						$_QRY_KODDOC = mysqli_fetch_array(mysqlQuery("SELECT koddoc, numberstage FROM dognet_dockalplan WHERE kodkalplan = '$_KODKALPLAN'"), MYSQLI_ASSOC);
						$_NUMBERSTAGE = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['numberstage'] : "--";
						$_KODDOC = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['koddoc'] : $_KODKALPLAN;
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = (!empty($_QRY_DOCNUMBER)) ? $_QRY_DOCNUMBER['docnumber'] : "";
						//
						$__info1 = "Внесены изменения в аванс от " . date('d.m.Y', strtotime($_ROW_40020['dateavans'])) . " / ID:" . $__docID . " в этапе #" . $_NUMBERSTAGE . " / ID:" . $_KODKALPLAN . " (сумма: " . $_ROW_40020['summaavans'] . ")";
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_40020 = mysqlQuery("SELECT * FROM dognet_docavans WHERE kodavans = '$param1'");
						$_ROW_40020 = mysqli_fetch_array($_QRY_40020, MYSQLI_ASSOC);
						$__docID = $_ROW_40020['kodavans'];
						//
						$_KODKALPLAN = $_ROW_40020['koddoc'];
						$_QRY_KODDOC = mysqli_fetch_array(mysqlQuery("SELECT koddoc, numberstage FROM dognet_dockalplan WHERE kodkalplan = '$_KODKALPLAN'"), MYSQLI_ASSOC);
						$_NUMBERSTAGE = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['numberstage'] : "--";
						$_KODDOC = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['koddoc'] : $_KODKALPLAN;
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = (!empty($_QRY_DOCNUMBER)) ? $_QRY_DOCNUMBER['docnumber'] : "";
						//
						$__info1 = "Удален аванс от " . date('d.m.Y', strtotime($_ROW_40020['dateavans'])) . " / ID:" . $__docID . " в этапе #" . $_NUMBERSTAGE . " / ID:" . $_KODKALPLAN;
					}
				}
				#
				# Обрабатываем операции над зачтенными платежами по счету-фактуре
				if ($__subservice_lvl3 == "1") {
					if (substr($kodmsg, -1) == "1") {
						$_QRY_40001 = mysqlQuery("SELECT * FROM dognet_oplatachf WHERE kodoplata = '$param1'");
						$_ROW_40001 = mysqli_fetch_array($_QRY_40001, MYSQLI_ASSOC);
						$__docID = $param1;
						//
						$_CHF = $param2;
						$_QRY_CHF = mysqli_fetch_array(mysqlQuery("SELECT chetfnumber FROM dognet_kalplanchf WHERE kodchfact = '$_CHF'"), MYSQLI_ASSOC);
						$_KODKALPLAN = $param3;
						$_QRY_KODDOC = mysqli_fetch_array(mysqlQuery("SELECT koddoc, numberstage FROM dognet_dockalplan WHERE kodkalplan = '$_KODKALPLAN'"), MYSQLI_ASSOC);
						$_NUMBERSTAGE = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['numberstage'] : "--";
						$_KODDOC = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['koddoc'] : $_KODKALPLAN;
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = (!empty($_QRY_DOCNUMBER)) ? $_QRY_DOCNUMBER['docnumber'] : "";
						//
						$__info1 = "Добавлен платеж ID:" . $__docID . " по счету-фактуре #" . $_QRY_CHF['chetfnumber'] . " / ID:" . $_CHF . " в этапе #" . $_NUMBERSTAGE;
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_40001 = mysqlQuery("SELECT * FROM dognet_oplatachf WHERE kodoplata = '$param1'");
						$_ROW_40001 = mysqli_fetch_array($_QRY_40001, MYSQLI_ASSOC);
						$__docID = $_ROW_40001['kodoplata'];
						//
						$_CHF = $param2;
						$_QRY_CHF = mysqli_fetch_array(mysqlQuery("SELECT chetfnumber FROM dognet_kalplanchf WHERE kodchfact = '$_CHF'"), MYSQLI_ASSOC);
						$_KODKALPLAN = $_ROW_40001['kodkalplan'];
						$_QRY_KODDOC = mysqli_fetch_array(mysqlQuery("SELECT koddoc, numberstage FROM dognet_dockalplan WHERE kodkalplan = '$_KODKALPLAN'"), MYSQLI_ASSOC);
						$_NUMBERSTAGE = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['numberstage'] : "--";
						$_KODDOC = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['koddoc'] : $_KODKALPLAN;
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = (!empty($_QRY_DOCNUMBER)) ? $_QRY_DOCNUMBER['docnumber'] : "";
						//
						$__info1 = "Внесены изменения в платеж ID:" . $__docID . " по счету-фактуре #" . $_QRY_CHF['chetfnumber'] . " / ID:" . $_CHF . " в этапе #" . $_NUMBERSTAGE;
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_40001 = mysqlQuery("SELECT * FROM dognet_oplatachf WHERE kodoplata = '$param1'");
						$_ROW_40001 = mysqli_fetch_array($_QRY_40001, MYSQLI_ASSOC);
						$__docID = $_ROW_40001['kodoplata'];
						//
						$_CHF = $param2;
						$_QRY_CHF = mysqli_fetch_array(mysqlQuery("SELECT chetfnumber FROM dognet_kalplanchf WHERE kodchfact = '$_CHF'"), MYSQLI_ASSOC);
						$_KODKALPLAN = $_ROW_40001['kodkalplan'];
						$_QRY_KODDOC = mysqli_fetch_array(mysqlQuery("SELECT koddoc, numberstage FROM dognet_dockalplan WHERE kodkalplan = '$_KODKALPLAN'"), MYSQLI_ASSOC);
						$_NUMBERSTAGE = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['numberstage'] : "--";
						$_KODDOC = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['koddoc'] : $_KODKALPLAN;
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = (!empty($_QRY_DOCNUMBER)) ? $_QRY_DOCNUMBER['docnumber'] : "";
						//
						$__info1 = "Удален платеж ID:" . $__docID . " по счету-фактуре #" . $_QRY_CHF['chetfnumber'] . " / ID:" . $_CHF . " в этапе #" . $_NUMBERSTAGE;
					}
				}
				#
				# Обрабатываем операции над зачтенными авансами по счету-фактуре
				if ($__subservice_lvl3 == "2") {
					if (substr($kodmsg, -1) == "1") {
						$_QRY_40003 = mysqlQuery("SELECT * FROM dognet_chfavans WHERE id = '$row_id'");
						$_ROW_40003 = mysqli_fetch_array($_QRY_40003, MYSQLI_ASSOC);
						$__docID = $param1;
						//
						$_CHF = $param2;
						$_QRY_CHF = mysqli_fetch_array(mysqlQuery("SELECT kodkalplan, chetfnumber FROM dognet_kalplanchf WHERE kodchfact = '$_CHF'"), MYSQLI_ASSOC);
						$_CHFNUMBER = $_QRY_CHF['chetfnumber'];
						$_KODAVANS = $param3;
						$_KODKALPLAN = $_QRY_CHF['kodkalplan'];
						$_QRY_KODDOC = mysqli_fetch_array(mysqlQuery("SELECT koddoc, numberstage FROM dognet_dockalplan WHERE kodkalplan = '$_KODKALPLAN'"), MYSQLI_ASSOC);
						$_KODDOC = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['koddoc'] : $_KODKALPLAN;
						$_NUMBERSTAGE = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['numberstage'] : "--";
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = (!empty($_QRY_DOCNUMBER)) ? $_QRY_DOCNUMBER['docnumber'] : "";
						//
						$__info1 = "Зачет / ID:" . $__docID . " из аванса / ID:" . $_KODAVANS . " по счету-фактуре #" . $_CHFNUMBER . " в этапе #" . $_NUMBERSTAGE;
					} elseif (substr($kodmsg, -1) == "2") {
						$_QRY_40003 = mysqlQuery("SELECT * FROM dognet_chfavans WHERE id = '$row_id'");
						$_ROW_40003 = mysqli_fetch_array($_QRY_40003, MYSQLI_ASSOC);
						$__docID = $_ROW_40003['kodchfavans'];
						//
						$_CHF = $param2;
						$_QRY_CHF = mysqli_fetch_array(mysqlQuery("SELECT kodkalplan, chetfnumber FROM dognet_kalplanchf WHERE kodchfact = '$_CHF'"), MYSQLI_ASSOC);
						$_CHFNUMBER = $_QRY_CHF['chetfnumber'];
						$_KODAVANS = $param3;
						$_KODKALPLAN = $_QRY_CHF['kodkalplan'];
						$_QRY_KODDOC = mysqli_fetch_array(mysqlQuery("SELECT koddoc, numberstage FROM dognet_dockalplan WHERE kodkalplan = '$_KODKALPLAN'"), MYSQLI_ASSOC);
						$_KODDOC = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['koddoc'] : $_KODKALPLAN;
						$_NUMBERSTAGE = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['numberstage'] : "--";
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = (!empty($_QRY_DOCNUMBER)) ? $_QRY_DOCNUMBER['docnumber'] : "";
						//
						$__info1 = "Внесены изменения в зачет / ID:" . $__docID . " из аванса / ID:" . $_KODAVANS . " по счету-фактуре #" . $_CHFNUMBER . " в этапе #" . $_NUMBERSTAGE;
					} elseif (substr($kodmsg, -1) == "3") {
						$_QRY_40003 = mysqlQuery("SELECT * FROM dognet_chfavans WHERE id = '$row_id'");
						$_ROW_40003 = mysqli_fetch_array($_QRY_40003, MYSQLI_ASSOC);
						$__docID = $_ROW_40003['kodchfavans'];
						//
						$_CHF = $param2;
						$_QRY_CHF = mysqli_fetch_array(mysqlQuery("SELECT kodkalplan, chetfnumber FROM dognet_kalplanchf WHERE kodchfact = '$_CHF'"), MYSQLI_ASSOC);
						$_CHFNUMBER = $_QRY_CHF['chetfnumber'];
						$_KODAVANS = $param3;
						$_KODKALPLAN = $_QRY_CHF['kodkalplan'];
						$_QRY_KODDOC = mysqli_fetch_array(mysqlQuery("SELECT koddoc, numberstage FROM dognet_dockalplan WHERE kodkalplan = '$_KODKALPLAN'"), MYSQLI_ASSOC);
						$_KODDOC = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['koddoc'] : $_KODKALPLAN;
						$_NUMBERSTAGE = (!empty($_QRY_KODDOC)) ? $_QRY_KODDOC['numberstage'] : "--";
						$_QRY_DOCNUMBER = mysqli_fetch_array(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc = '$_KODDOC'"), MYSQLI_ASSOC);
						$__docNumber = (!empty($_QRY_DOCNUMBER)) ? $_QRY_DOCNUMBER['docnumber'] : "";
						//
						$__info1 = "Удален зачет / ID:" . $__docID . " из аванса / ID:" . $_KODAVANS . " по счету-фактуре #" . $_CHFNUMBER . " в этапе #" . $_NUMBERSTAGE;
					}
				}
				#
				# Обрабатываем операции с отчетами
				# KODGROUP = 99942XXX
				if ($__subservice_lvl0 == "2") {
					if ($__subservice_lvl1 == "1") {
						$__docID = $param1;
						$__info1 = $param2;
					} elseif ($__subservice_lvl1 == "2") {
						$__docID = $param1;
						$__info1 = $param2 . " (в формат " . $param3 . ")";
					}
				}
				#
				# Обрабатываем операции с актами
				# KODGROUP = 99942XXX
				if ($__subservice_lvl0 == "3") {
					if ($__subservice_lvl1 == "0") {
						$__docID = $param1;
						$__info1 = $param2;
					} elseif ($__subservice_lvl1 == "2") {
						$__docID = $param1;
						$__info1 = $param2 . " (в формат " . $param3 . ")";
					}
				}
				#
				# Обрабатываем операции с письмами
				# KODGROUP = 99942XXX
				if ($__subservice_lvl0 == "4") {
					if ($__subservice_lvl1 == "0") {
						$__docID = $param1;
						$__info1 = $param2;
					} elseif ($__subservice_lvl1 == "2") {
						$__docID = $param1;
						$__info1 = $param2 . " (в формат " . $param3 . ")";
					}
				}
				break;
				#
				# ...
				#
				#
		}
	}

	# Собираем информацию о пользователе
	$__id = $_SESSION['id'];
	$__login = $_SESSION['login'];
	$__firstname = $_SESSION['firstname'];
	$__lastname = $_SESSION['lastname'];
	$__password = $_SESSION['password'];
	$__script = $_SERVER['PHP_SELF'];
	$__SESSID = session_id();
	$__userAgent = $_SERVER['HTTP_USER_AGENT'];
	$__ip = $_SERVER['REMOTE_ADDR'];
	# --- --- ---
	$_QRY_messages = mysqlQuery("SELECT * FROM portal_log_messages WHERE kodgroup = '$kodgroup' AND kodmsg = '$kodmsg'");
	$_ROW_messages = mysqli_fetch_array($_QRY_messages, MYSQLI_ASSOC);
	$__namegrp = $_ROW_messages['name_group'];
	$__accesslvl = $_ROW_messages['access_level'];
	$__msg = $_ROW_messages['msgtxt_short'];


	$_QRY_service = mysqlQuery("SELECT * FROM service_access WHERE id = '$__service_id'");
	$_ROW_service = mysqli_fetch_array($_QRY_service, MYSQLI_ASSOC);
	$__service = $_ROW_service['service_title'];


	mysqlQuery("INSERT INTO portal_syslog ( timestamp, service, subgroup, user_id, user_ip, user_login, user_firstname, user_lastname, SESSID, user_agent, access_level, message, row_id, doc_id, field_info1, field_info2, comment, doc_number, fullmsg ) VALUES (NOW(), '$__service', '$__namegrp', '$__id', '$__ip', '$__login', '$__firstname', '$__lastname', '$__SESSID', '$__userAgent', '$__accesslvl', '$__msg', '$row_id', '$__docID', '$__info1', '$__info2', '', '$__docNumber', '' )");
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function checkDognetMainpageViewBlock($lvlaccess, $blockname) {
	$checkUserStatus = checkUserAuthorization($_SESSION['login'], $_SESSION['password']);
	if ($checkUserStatus != -1) {
		$query_viewblock = mysqlQuery(" SELECT " . $blockname . " FROM dognet_view_mainpage WHERE lvl_access='$lvlaccess'");
		$row_viewblock = mysqli_fetch_array($query_viewblock, MYSQLI_ASSOC);
		$VIEWBLOCK = $row_viewblock[$blockname];
		return $VIEWBLOCK;
	} else {
		return 0;
	}
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function is_session_exists() {
	$sessionName = session_name();
	if (isset($_COOKIE[$sessionName]) || isset($_REQUEST[$sessionName])) {
		if (!isset($_SESSION)) {
			session_start();
		}
		return !empty($_SESSION);
	}
	return false;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# +++++ Функция сохранения состояния панели фильтров для списков (договора, заявки прочее)
# Created: 21.01.2021
# Comment: Пока только для списка текущих договоров
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
function _saveFiltersPanelState($_idFiltersPanel, $_nameElement, $_val) {
	$_SESSION[$_idFiltersPanel][$_nameElement] = $_val;
	return;
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### 
##### ПРОВЕРЯЕМ НАХОДИТСЯ ЛИ ПОЛЬЗОВАТЕЛЬ В РЕЖИМЕ ТЕСТИРОВАНИЯ СЕРВИСА
##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function checkUserInTestMode($id, $servicename) {
	$query = mysqlQuery(" SELECT {$servicename}, {$servicename}_testmode FROM users_restrictions WHERE id='$id' ");
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (($row[$servicename] >= 3) && ($row[$servicename . '_testmode'] > 0)) {
		return 1;
	} else {
		return 0;
	}
}
#
#
function checkUserInTestMode_defaultDB($id, $servicename) {
	$query = mysqlQuery_defaultDB(" SELECT {$servicename}, {$servicename}_testmode FROM users_restrictions WHERE id='$id' ");
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (($row[$servicename] >= 3) && ($row[$servicename . '_testmode'] > 0)) {
		return 1;
	} else {
		return 0;
	}
}
