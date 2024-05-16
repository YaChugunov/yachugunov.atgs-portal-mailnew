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

$_QRY_SystemSettings = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM mailbox_systemSettings WHERE typeMailbox = 'incoming'"));
define("DL_WARN_SEC", $_QRY_SystemSettings['DLwarning_inSecs']);
define("DL_WARN_DAY", $_QRY_SystemSettings['DLwarning_inDays']);
define("DL_ALRM_SEC", $_QRY_SystemSettings['DLalarm_inSecs']);
define("DL_ALRM_DAY", $_QRY_SystemSettings['DLalarm_inDays']);
define("DL_DEFL_SEC", $_QRY_SystemSettings['DLdefault_inSecs']);
define("DL_DEFL_DAY", $_QRY_SystemSettings['DLdefault_inDays']);
define("CONTROL_H", $_QRY_SystemSettings['controlHour']);
define("CRON_H", $_QRY_SystemSettings['cronHour']);

define("REMINDER_WARN", 86400 * $_QRY_SystemSettings['DLwarning_inDays']);
define("REMINDER_ALRM", 86400 * $_QRY_SystemSettings['DLalarm_inDays']);

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
# Функция проверки строки на правильность даты

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
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
$_SQLReq_SelectIncoming = "SELECT * FROM mailbox_incoming WHERE (inbox_docDate BETWEEN '$datetime1' AND '$datetime2') AND inbox_controlIspolActive='1' AND inbox_controlIspolCheckout<>'1'";
// $sqlClear_selectincoming = str_replace(array('\'', '"'), '`', $_SQLReq_SelectIncoming);
$sqlClear_selectincoming = $_SQLReq_SelectIncoming;

$_QRY_INCOMING = mysqlQuery($_SQLReq_SelectIncoming);
//
if (isset($_QRY_INCOMING)) {
    while ($_ROW_INCOMING = mysqli_fetch_assoc($_QRY_INCOMING)) {
        //
        $__docID       = $_ROW_INCOMING['inbox_docID'];
        $__docFileID   = $_ROW_INCOMING['inbox_docFileID'];
        $__contrID     = $_ROW_INCOMING['inbox_docContractorID'];
        $__contrKOD    = $_ROW_INCOMING['inbox_docContractor_kodzayvispol'];
        $__deadline    = $_ROW_INCOMING['inbox_docDateDeadline'];
        $__ispolMULTI  = $_ROW_INCOMING['inbox_docContractorMULTI'];
        $__senderORG   = $_ROW_INCOMING['inbox_docSender'];
        $__senderNAME  = $_ROW_INCOMING['inbox_docSenderName'];
        $__sourceID    = $_ROW_INCOMING['inbox_docSourceID'];
        $__sourceDate  = date_create($_ROW_INCOMING['inbox_docSourceDate']);
        $__docABOUT    = $_ROW_INCOMING['inbox_docAbout'];
        $__docTYPE     = $_ROW_INCOMING['inbox_docTypeSTR'];
        $__toSendEmail = $_ROW_INCOMING['toSendEmail'];
        //
        $__koddocmail  = $_ROW_INCOMING['koddocmail'];
        $__ispolIDs    = $_ROW_INCOMING['inbox_docContractorID'];
        $__ispolNames  = $_ROW_INCOMING['inbox_docContractorSTR'];
        $__ispolEmails = $_ROW_INCOMING['inbox_docContractorEMAIL'];
        //
        $__controlActive = $_ROW_INCOMING['inbox_controlIspolActive'];
        $__controlStatus = $_ROW_INCOMING['inbox_controlIspolStatus'];
        //        
        $__reminder1 = $_ROW_INCOMING['inbox_controlIspolMailReminder1'];
        $__reminder2 = $_ROW_INCOMING['inbox_controlIspolMailReminder2'];
        //
        $date1 = new DateTime(date('Y-m-d ' . CRON_H . ':30:00', strtotime('now')));
        $date2 = new DateTime(date('Y-m-d ' . CRON_H . ':30:00', strtotime($__deadline)));
        $diffInSeconds = $date2->getTimestamp() - $date1->getTimestamp();
        if ($diffInSeconds > REMINDER_ALRM && $diffInSeconds <= REMINDER_WARN) {
            $setWarning = 1;
            $setAlarm = 0;
        } elseif ($diffInSeconds <= REMINDER_ALRM) {
            $setWarning = 1;
            $setAlarm = 1;
        } else {
            $setWarning = 0;
            $setAlarm = 0;
        }
        $_SQLReq_Update = "UPDATE mailbox_incoming SET inbox_controlIspolWarning = '$setWarning', inbox_controlIspolAlarm = '$setAlarm' WHERE koddocmail = '$__koddocmail'";
        # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
        # ТЕСТОВЫЙ ВЫВОД В LOG
        echo "SQL-запрос: " . $_SQLReq_SelectIncoming;
        echo "\n";
        echo 'Koddocmail : ' . $__koddocmail . ' / Дедлайн : ' . $__deadline;
        echo "\n";
        echo "date1 : " . $date1->format('Y-m-d H:i:s') . " / date2 : " . $date2->format('Y-m-d H:i:s');
        echo "\n";
        echo "REMINDER_ALRM : " . REMINDER_ALRM . " / REMINDER_WARN : " . REMINDER_WARN;
        echo "\n";
        echo "setAlarm : " . $setAlarm . " / setWarning : " . $setWarning;
        echo "\n";
        echo $diffInSeconds;
        echo "\n\n";
        # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
        $_QRY_Update = mysqlQuery($_SQLReq_Update);
    }
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
// Выорка по разнице (TIMESTAMPDIFF) между текущей датой и дедлайном
$_SQLReq_SelectOutgoing = "SELECT * FROM mailbox_outgoing WHERE (outbox_docDate BETWEEN '$datetime1' AND '$datetime2') AND outbox_controlIspolActive='1' AND outbox_controlIspolCheckout<>'1'";
// $sqlClear_selectoutgoing = str_replace(array('\'', '"'), '`', $_SQLReq_SelectOutgoing);
$sqlClear_selectoutgoing = $_SQLReq_SelectOutgoing;

$_QRY_OUTGOING = mysqlQuery($_SQLReq_SelectOutgoing);
//
if (isset($_QRY_OUTGOING)) {
    while ($_ROW_OUTGOING = mysqli_fetch_assoc($_QRY_OUTGOING)) {
        //
        $__docID       = $_ROW_OUTGOING['outbox_docID'];
        $__docFileID   = $_ROW_OUTGOING['outbox_docFileID'];
        $__contrID     = $_ROW_OUTGOING['outbox_docContractorID'];
        $__contrKOD    = $_ROW_OUTGOING['outbox_docContractor_kodispolout'];
        $__deadline    = $_ROW_OUTGOING['outbox_docDateDeadline'];
        $__ispolMULTI  = $_ROW_OUTGOING['outbox_docContractorMULTI'];
        $__senderORG   = $_ROW_OUTGOING['outbox_docRecipient'];
        $__senderNAME  = $_ROW_OUTGOING['outbox_docRecipientName'];
        $__sourceID    = $_ROW_OUTGOING['outbox_docSourceID'];
        $__sourceDate  = date_create($_ROW_OUTGOING['outbox_docSourceDate']);
        $__docABOUT    = $_ROW_OUTGOING['outbox_docAbout'];
        $__docTYPE     = $_ROW_OUTGOING['outbox_docTypeSTR'];
        $__toSendEmail = $_ROW_OUTGOING['toSendEmail'];
        //
        $__koddocmail  = $_ROW_OUTGOING['koddocmail'];
        $__ispolIDs    = $_ROW_OUTGOING['outbox_docContractorID'];
        $__ispolNames  = $_ROW_OUTGOING['outbox_docContractorSTR'];
        $__ispolEmails = $_ROW_OUTGOING['outbox_docContractorEMAIL'];
        //
        $__controlActive = $_ROW_OUTGOING['outbox_controlIspolActive'];
        $__controlStatus = $_ROW_OUTGOING['outbox_controlIspolStatus'];
        //        
        $__reminder1 = $_ROW_OUTGOING['outbox_controlIspolMailReminder1'];
        $__reminder2 = $_ROW_OUTGOING['outbox_controlIspolMailReminder2'];
        //
        $date1 = new DateTime(date('Y-m-d ' . CRON_H . ':00:00', strtotime('now')));
        $date2 = new DateTime(date('Y-m-d ' . CRON_H . ':00:00', strtotime($__deadline)));
        $diffInSeconds = $date2->getTimestamp() - $date1->getTimestamp();
        if ($diffInSeconds > REMINDER_ALRM && $diffInSeconds <= REMINDER_WARN) {
            $setWarning = 1;
            $setAlarm = 0;
        } elseif ($diffInSeconds <= REMINDER_ALRM) {
            $setWarning = 1;
            $setAlarm = 1;
        } else {
            $setWarning = 0;
            $setAlarm = 0;
        }
        $_SQLReq_Update = "UPDATE mailbox_outgoing SET outbox_controlIspolWarning = '$setWarning', outbox_controlIspolAlarm = '$setAlarm' WHERE koddocmail = '$__koddocmail'";
        # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
        # ТЕСТОВЫЙ ВЫВОД В LOG
        echo "SQL-запрос : " . $_SQLReq_SelectOutgoing;
        echo "\n";
        echo 'Koddocmail : ' . $__koddocmail . ' / Дедлайн : ' . $__deadline;
        echo "\n";
        echo "date1 : " . $date1->format('Y-m-d H:i:s') . " / date2 : " . $date2->format('Y-m-d H:i:s');
        echo "\n";
        echo "REMINDER_ALRM : " . REMINDER_ALRM . " / REMINDER_WARN : " . REMINDER_WARN;
        echo "\n";
        echo "setAlarm : " . $setAlarm . " / setWarning : " . $setWarning;
        echo "\n";
        echo $diffInSeconds;
        echo "\n\n";
        # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
        $_QRY_Update = mysqlQuery($_SQLReq_Update);
    }
}
unset($_IS_CRONTAB);
