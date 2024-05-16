<?php
date_default_timezone_set('Europe/Moscow');
# Подключаем конфигурационный файл
require($_SERVER['DOCUMENT_ROOT'] . '/config.inc.php');
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
# Для поиска понадобится функция, которая очистит текст от html-кода, лишних слов и вернёт массив слов.
function get_minification_array($text) {
	// Удаление экранированных спецсимволов
	$text = stripslashes($text);

	// Преобразование мнемоник 
	$text = html_entity_decode($text);
	$text = htmlspecialchars_decode($text, ENT_QUOTES);

	// Удаление html тегов
	$text = strip_tags($text);

	// Все в нижний регистр 
	$text = mb_strtolower($text);

	// Удаление лишних символов
	$text = str_ireplace('ё', 'е', $text);
	$text = mb_eregi_replace("[^a-zа-яй0-9 ]", ' ', $text);

	// Удаление двойных пробелов
	$text = mb_ereg_replace('[ ]+', ' ', $text);

	// Преобразование текста в массив
	$words = explode(' ', $text);

	// Удаление дубликатов
	$words = array_unique($words);

	// Удаление предлогов и союзов
	$array = array(
		'без', 'близ', 'в', 'во', 'вместо', 'вне', 'для', 'до', 'за', 'и', 'из', 'изо', 'из', 'за', 'под', 'к', 'ко', 'кроме', 'между', 'на', 'над', 'о', 'об', 'обо', 'от', 'ото', 'перед', 'передо', 'пред', 'предо', 'по', 'под', 'подо', 'при', 'про', 'ради', 'с', 'со', 'сквозь', 'среди', 'у', 'через', 'но', 'или', 'по', 'ооо', 'ао', 'зао', 'оао', 'пао', 'нко', 'ип'
	);

	$words = array_diff($words, $array);

	// Удаление пустых значений в массиве
	$words = array_diff($words, array(''));

	return $words;
}


#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

// Код документа в таблице ".__MAIL_INCOMING_PREFIX."
$_inputtext = isset($_POST['text']) ? $_POST['text'] : "";

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$error_cntComments = $output_cntComments = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
	if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
		return "-3";
	} else {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && "" != $_inputtext) {

			// Получаем для неё массив слов
			$text = get_minification_array($_inputtext);
			$count = count($text);
			$out = array();

			// Выбираем контрагента
			$_reqDB_listContr = mysqlQuery("
			SELECT kodcontragent as kodcontragent, null as kodcontragentm, nameshort, namefull FROM sp_contragents WHERE koddel<>'99' AND deleted<>'1' AND liquidated<>'1' AND (nameshort<>'' OR namefull<>'') AND (nameshort<>'-' OR namefull<>'-') 
			UNION
			SELECT null as kodcontragent, kodcontragentm as kodcontragentm, nameshort, namefull FROM sp_contragents_manualinput WHERE koddel<>'99' AND (nameshort<>'' OR namefull<>'') AND (nameshort<>'-' OR namefull<>'-')
			ORDER BY nameshort ASC;
			");

			$i = 0;
			$ksim_1 = 0;
			$ksim_2 = 0;
			$ksim_3 = 0;
			$ksim_4 = 0;
			$similarity_1 = 0;
			$similarity_2 = 0;
			foreach ($_reqDB_listContr as $row) {
				$verifiableStr_1 = get_minification_array($row['nameshort']);
				$verifiableStr_2 = get_minification_array($row['namefull']);
				$realStr_1 = $row['nameshort'];
				$realStr_2 = $row['namefull'];

				$similarCounter_1 = 0;
				$similarCounter_2 = 0;
				foreach ($text as $text_row) {
					foreach ($verifiableStr_1 as $verifiableRow) {
						if ($text_row == $verifiableRow) {
							$similarCounter_1++;
							break;
						}
					}
					foreach ($verifiableStr_2 as $verifiableRow) {
						if ($text_row == $verifiableRow) {
							$similarCounter_2++;
							break;
						}
					}
				}

				$similarity_1 = similar_text(implode(' ', $text), implode(' ', $verifiableStr_1), $ksim_3);
				$similarity_2 = similar_text(implode(' ', $text), implode(' ', $verifiableStr_2), $ksim_4);

				$ksim_1 = $similarCounter_1 * 100 / $count;
				$ksim_2 = $similarCounter_2 * 100 / $count;

				if ($ksim_1 > 66 || $ksim_2 > 66 || $ksim_3 > 90 || $ksim_4 > 90) {
					$i++;
					$out[$i]['text'] = $text;
					$out[$i]['nameshort'] = $realStr_1;
					$out[$i]['namefull'] = 	str_ireplace('"', '', $realStr_2);
					$out[$i]['ksim1'] = $ksim_1;
					$out[$i]['ksim2'] = $ksim_2;
					$out[$i]['ksim3'] = $ksim_3;
					$out[$i]['ksim4'] = $ksim_4;
					$out[$i]['kodcontragent'] = !empty($row['kodcontragent']) ? $row['kodcontragent'] : null;
					$out[$i]['kodcontragentm'] = !empty($row['kodcontragentm']) ? $row['kodcontragentm'] : null;
				}
			}

			// Сортировка результатов и ограничение по количеству
			arsort($out);
			$out = array_slice($out, 0, 100, false);
			if ($_reqDB_listContr) {
				$output = json_encode($out);
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
