#!/usr/bin/php
<?php
#
$_IS_CRONTAB = TRUE;
#
$path_parts = pathinfo($_SERVER['SCRIPT_FILENAME']); // определяем директорию скрипта
chdir($path_parts['dirname']); // задаем директорию выполнение скрипта
#
date_default_timezone_set('Europe/Moscow');
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем конфигурационный файл
require_once("/var/www/html/atgs-portal.local/www/config.inc.php");
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
require_once("/var/www/html/atgs-portal.local/www/mailnew/config.mail.inc.php");
#
# Подключаемся к базе
require_once("/var/www/html/atgs-portal.local/www/mailnew/_assets/dbconn/db_connection.php");
require_once("/var/www/html/atgs-portal.local/www/mailnew/_assets/dbconn/db_controller.php");
$db_handle = new DBController();
#
# Подключаем общие функции безопасности
require("/var/www/html/atgs-portal.local/www/mailnew/_assets/functions/func.secure.inc.php");
# Подключаем собственные функции сервиса Почта
require("/var/www/html/atgs-portal.local/www/mailnew/_assets/functions/func.mail.inc.php");
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

define("REMINDER_1", 86400 * 3);
define("REMINDER_2", 86400);

$_QRY_SystemSettings = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM mailbox_systemSettings WHERE typeMailbox = 'incoming'"));
define("DL_WARN_SEC", $_QRY_SystemSettings['DLwarning_inSecs']);
define("DL_WARN_DAY", $_QRY_SystemSettings['DLwarning_inDays']);
define("DL_DEFL_SEC", $_QRY_SystemSettings['DLdefault_inSecs']);
define("DL_DEFL_DAY", $_QRY_SystemSettings['DLdefault_inDays']);
define("CONTROL_H", $_QRY_SystemSettings['controlHour']);

/**
 * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
 * param  $number Integer Число на основе которого нужно сформировать окончание
 * param  $endingsArray  Array Массив слов или окончаний для чисел (1, 4, 5),
 *         например array('яблоко', 'яблока', 'яблок')
 * return String
 */
function getNumEnding($number, $endingArray) {
    $number = $number % 100;
    if ($number >= 11 && $number <= 19) {
        $ending = $endingArray[2];
    } else {
        $i = $number % 10;
        switch ($i) {
            case (1):
                $ending = $endingArray[0];
                break;
            case (2):
            case (3):
            case (4):
                $ending = $endingArray[1];
                break;
            default:
                $ending = $endingArray[2];
        }
    }
    return $ending;
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function dateDiff($date_earlier, $date_later, $output_format) {
    // $date_later - поздняя дата (ГГГГ-ММ-ДД ЧЧ:ММ:СС)
    // $date_earlier - ранняя дата (ГГГГ-ММ-ДД ЧЧ:ММ:СС)
    // $output_format - в чем выводить разницу (years, month, days, hours, mins, secs)

    // Declare and define two dates
    $date1 = date_create('2023-06-11');
    $date2 = date_create('2023-06-13');
    $interval = date_diff($date1, $date2);
    if ($interval) {
        switch ($output_format) {
            case 'years':
                return $interval->format('%y');
                break;
            case 'months':
                return $interval->format('%m');
                break;
            case 'days':
                return $interval->format('%a');
                break;
            case 'hours':
                return $interval->format('%h');
                break;
            case 'mins':
                return $interval->format('%i');
                break;
            case 'secs':
                return $interval->s;
                break;
            default:
                return $interval->s;
        }
    } else {
        return 'error';
    }
    return $interval->format('%s');
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
// Функция проверки строки на правильность даты
//
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
$datetimenow = date("Y-m-d H:i:s");
$datenow     = date("Y-m-d");
$datetime1   = date("Y-m-d H:i:s", strtotime('-1 month', strtotime($datetimenow)));
$datetime2   = date("Y-m-d H:i:s", strtotime($datetimenow));
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
// Выорка по разнице (TIMESTAMPDIFF) между текущей датой и дедлайном
$_SQLReq_Select1 = "
SELECT * FROM mailbox_incoming
WHERE (inbox_docDate BETWEEN '$datetime1' AND '$datetime2')
AND inbox_controlIspolActive = '1' AND inbox_controlIspolCheckout <> '1' AND TIMESTAMPDIFF(SECOND, '$datetimenow', inbox_docDateDeadline) <= 0 AND inbox_docContractorID != ''";
// $sqlClear_select1 = str_replace(array('\'', '"'), '`', $_SQLReq_Select1);
$sqlClear_select1 = $_SQLReq_Select1;
echo '<br>' . $sqlClear_select1 . '<br>' . REMINDER_1 . '<br>' . REMINDER_2 . '<br>';
echo '----- ----- -----';
echo '<br>';
$_SQLReq_Insert1 = "";
$_QRY_SELECT = mysqlQuery($_SQLReq_Select1);
//
if (isset($_QRY_SELECT)) {
    while ($_ROW_SELECT = mysqli_fetch_assoc($_QRY_SELECT)) {
        //
        $__koddocmail  = $_ROW_SELECT['koddocmail'];
        $__docID       = $_ROW_SELECT['inbox_docID'];
        $__docFileID   = $_ROW_SELECT['inbox_docFileID'];
        $__contrID     = $_ROW_SELECT['inbox_docContractorID'];
        $__contrKOD    = $_ROW_SELECT['inbox_docContractor_kodzayvispol'];
        $__deadline    = $_ROW_SELECT['inbox_docDateDeadline'];
        $__ispolMULTI  = $_ROW_SELECT['inbox_docContractorMULTI'];
        $__senderORG   = $_ROW_SELECT['inbox_docSender'];
        $__senderNAME  = $_ROW_SELECT['inbox_docSenderName'];
        $__sourceID    = $_ROW_SELECT['inbox_docSourceID'];
        $__sourceDate  = date_create($_ROW_SELECT['inbox_docSourceDate']);
        $__docABOUT    = $_ROW_SELECT['inbox_docAbout'];
        $__docTYPE     = $_ROW_SELECT['inbox_docTypeSTR'];
        $__toSendEmail = $_ROW_SELECT['toSendEmail'];
        //
        $__koddocmail  = $_ROW_SELECT['koddocmail'];
        $__ispolIDs    = $_ROW_SELECT['inbox_docContractorID'];
        $__ispolNames  = $_ROW_SELECT['inbox_docContractorSTR'];
        $__ispolEmails = $_ROW_SELECT['inbox_docContractorEMAIL'];
        //
        $__controlActive = $_ROW_SELECT['inbox_controlIspolActive'];
        $__controlStatus = $_ROW_SELECT['inbox_controlIspolStatus'];
        //        
        $__reminder1 = $_ROW_SELECT['inbox_controlIspolMailReminder1'];
        $__reminder2 = $_ROW_SELECT['inbox_controlIspolMailReminder2'];
        //
        $arrIspolIDs    = explode(",", $__ispolIDs);
        $arrIspolNames  = explode(",", $__ispolNames);
        $arrIspolEmails = explode(",", $__ispolEmails);
        $arrispol       = explode(",", $__contrKOD);
        //
        $diff = date_diff(date_create($datetimenow), date_create($__deadline));
        $deadline = date_create($__deadline);
        $_docDeadlineRemains = $diff->format('%a (полные дни)');
        // $_docDeadlineRemains = round($diff / (60 * 60 * 24));
        $_docDeadlineDate        = date_format($deadline, 'd.m.Y H:i');
        $date1                   = new DateTime($datetimenow);
        $date2                   = $deadline;
        // $_intDiffSeconds         = intval($diff->format('%h'));
        //
        $_docDeadline = validateDate($_ROW_SELECT['inbox_docDateDeadline'], "Y-m-d") ? date('d.m.Y', strtotime($_ROW_SELECT['inbox_docDateDeadline'])) : "---";
        $_sourceDate = validateDate($_ROW_SELECT['inbox_docSourceDate'], "Y-m-d") ? date('d.m.Y', strtotime($_ROW_SELECT['inbox_docSourceDate'])) : "---";

        $controlActiveStr = $__controlActive ? 'Контроль активен' : 'Контроль не активен';
        // Статус исполнения 
        switch ($__controlStatus) {
            case 0:
                $controlStatusStr = 'Режим контроля исполнения не активен';
                break;
            case 1:
                $controlStatusStr = 'Режим контроля исполнения активен и документ выполнен полностью (всеми исполнителями)';
                break;
            case 2:
                $controlStatusStr = 'Режим контроля исполнения активен, но документ выполнен не полностью (не всеми исполнителями)';
                break;
            case 3:
                $controlStatusStr = 'Контроль активен и по документу просрочен дедлайн';
                break;
            default:
                $controlStatusStr = "---";
        }

        $_intDiffSeconds = strtotime($__deadline) - strtotime($datetimenow);

        $_docDeadlineRemainsSrok = intval($diff->format('%a')) < 1 ? "менее суток" : $diff->format('%a ') . getNumEnding(intval($diff->format('%a')), array('день', 'дня', 'дней'));
        $_docDeadlineRemainsText = ($date2 < $date1) ? 'Выполнение просрочено на ' . $diff->format('%a ') . getNumEnding(intval($diff->format('%a')), array('день', 'дня', 'дней')) . ' (полных)' : 'На выполнение осталось ' . $_docDeadlineRemainsSrok . ' (полных)';
        # ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        // Определяем триггер
        if ($_intDiffSeconds > 0) {
            $_trigg1 = $__reminder1 * ($_intDiffSeconds <= REMINDER_1 ? 1 : 0);
            $_trigg2 = $__reminder2 * ($_intDiffSeconds <= REMINDER_2 ? 1 : 0);
            $_trigg = $_trigg1 + 2 * $_trigg2;
        } else {
            $_trigg1 = -1;
            $_trigg2 = -1;
            $_trigg = 4;
        }
        //
        //
        //
        switch ($_trigg) {
            case 0:
                $_triggStr = "";
                $_triggTitleStr = "";
                $_triggTitleStyle = "background-color:transparent;";
                $_triggTextStr = "";
                break;
            case 1:
                $_triggStr = "менее 3-x дней";
                $_triggTitleStr = "осталось менее 3-x дней";
                $_triggTitleStyle = "background-color:#FF8C00;";
                $_triggTextStr = "До истечения срока исполнения";
                break;
            case 2:
                $_triggStr = "менее 1-го дня";
                $_triggTitleStr = "осталось менее дня";
                $_triggTitleStyle = "background-color:#800000;";
                $_triggTextStr = "На исполнение";
                break;
            case 3:
                $_triggStr = "менее 1-го дня, менее 3-х дней";
                $_triggTitleStr = "осталось менее дня";
                $_triggTitleStyle = "background-color:#800000;";
                $_triggTextStr = "На исполнение";
                break;
            case 4:
                $_triggStr = "просрочен";
                $_triggTitleStr = "просрочен";
                $_triggTitleStyle = "background-color:#FF0000;";
                $_triggTextStr = "Срок исполнения";
                break;
            default:
                $_triggStr = "";
                $_triggTitleStr = "";
                $_triggTitleStyle = "background-color:transparent;";
        }
        $date_A = strtotime('now');
        $date_B = strtotime($__deadline);

        $date1 = new DateTime(date('Y-m-d ' . CONTROL_H . ':00:00', strtotime('now')));
        $date2 = new DateTime(date('Y-m-d ' . CONTROL_H . ':00:00', strtotime($_docDeadline)));
        $diffInSeconds = $date2->getTimestamp() - $date1->getTimestamp();

        echo $diffInSeconds;
        echo '<br>';
        echo $__docID . " - " . $__docABOUT . " (" . $__ispolNames . ") - " . $_triggStr . " (" . date('d.m.Y', strtotime($_ROW_SELECT['inbox_docDateDeadline'])) . ")";
        echo '<br>';
        $_QRY_Update = "UPDATE mailbox_incoming SET inbox_controlIspolAlarm = '1' WHERE koddocmail = '$__koddocmail'";
    }
}
unset($_IS_CRONTAB);