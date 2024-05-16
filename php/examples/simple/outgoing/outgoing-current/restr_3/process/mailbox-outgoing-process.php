<?php
date_default_timezone_set('Europe/Moscow');
# Подключаем конфигурационный файл
require $_SERVER['DOCUMENT_ROOT'] . '/config.inc.php';
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
require_once __DIR_ROOT . __SERVICENAME_MAILNEW . '/config.mail.inc.php';
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаемся к базе
require_once __DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/dbconn/db_connection.php';
require_once __DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/dbconn/db_controller.php';
$db_handle = new DBController();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем общие функции безопасности
require_once __DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/functions/func.secure.inc.php';
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Подключаем собственные функции сервиса Почта
require_once __DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/functions/func.mail.inc.php';
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
# Включаем режим сессии
// session_start();
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
$__startDate = $_SESSION['out_startTableDate'];
$__endDate   = $_SESSION['out_endTableDate'];
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && __MAIL_TESTMODE_TYPE < 3) {
    define('__MAIL_RESTR3', '/restr_3.test');
} else {
    define('__MAIL_RESTR3', '/restr_3');
}
# Import PHPMailer classes into the global namespace
# These must be at the top of your script, not inside a function
use DataTables\Editor;
use DataTables\Editor\Field;
use DataTables\Editor\Format;

function outputTempTest($msgFrom, $msgMain, $msgText1, $msgText2, $msgText3, $msgText4, $msgText5) {
    $ip   = $_SERVER['REMOTE_ADDR'];
    $date = date("Y-m-d H:i:s");
    $col  = "0";
    $_req = mysqlQuery("UPDATE outputTemp_test SET ip='{$ip}', msgDate='{$date}', msgCol='{$col}', msgFrom='{$msgFrom}', msgMain='{$msgMain}', msgText1='{$msgText1}', msgText2='{$msgText2}', msgText3='{$msgText3}' WHERE msgFrom LIKE '%{$msgFrom}%'");
}

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция проверки строки на правильность даты
//
function validateDate($date, $format) {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения нового кода документа/записи (koddocmail) для таблицы записей
//
function newKodcontragentm() {
    $query  = mysqlQuery("SELECT MAX(kodcontragentm) as lastKod FROM sp_contragents_manualinput ORDER BY id DESC");
    $row    = mysqli_fetch_assoc($query);
    $lastKod = $row['lastKod'];
    $newKod = $lastKod + rand(3, 9);
    return $newKod;
}
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения нового кода документа/записи (koddocmail) для таблицы записей
//
function newKoddocmail() {
    $query  = mysqlQuery("SELECT MAX(koddocmail) as lastKod FROM " . __MAIL_OUTGOING_TABLENAME . " ORDER BY id DESC");
    $row    = mysqli_fetch_assoc($query);
    $newKod = $row['lastKod'];
    $newKod++;
    return $newKod;
}
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения текущего номера документа/записи (docID) открытого для редактирования
//
function editingDocID($id) {
    $query                    = mysqlQuery("SELECT outbox_docID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id);
    $row                      = mysqli_fetch_assoc($query);
    $DocID                    = $row['outbox_docID'];
    $_SESSION['editingDocID'] = $DocID;
    return $DocID;
}
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения нового номера документа/записи (docID) для таблицы записей
//
function newDocID() {
    $query    = mysqlQuery("SELECT MAX(outbox_docID) as lastDocID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE YEAR(outbox_docDate)=YEAR(NOW()) ORDER BY id DESC");
    $row      = mysqli_fetch_assoc($query);
    $newDocID = $row['lastDocID'];
    $newDocID++;
    return $newDocID;
}
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения нового номера файла (file_id) для таблицы файлов
//
function newFileID() {
    $query     = mysqlQuery("SELECT MAX(file_id) as lastFileID FROM " . __MAIL_OUTGOING_FILES_TABLENAME . " ORDER BY id DESC");
    $row       = mysqli_fetch_assoc($query);
    $newFileID = $row['lastFileID'];
    $newFileID++;
    return $newFileID;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
/*
ПУТИ ДЛЯ СОХРАНЕНИЯ ЗАГРУЖАЕМЫХ ФАЙЛОВ
> Массив директорий и вспомогательных параметров
 */
if ("POST" == $_SERVER["REQUEST_METHOD"]) {
    $d            = dir(__MAIL_OUTGOING_STORAGE_SERVERPATH . __MAIL_OUTGOING_STORAGE_SUBFOLDER . "/");
    $docpath      = $d->path;
    $webpath      = __SERVICENAME_MAIL . __MAIL_OUTGOING_STORAGE_WORKPATH . __MAIL_OUTGOING_STORAGE_SUBFOLDER . "/";
    $syspath      = __DIR_ROOT . __SERVICENAME_MAIL . __MAIL_OUTGOING_STORAGE_WORKPATH . __MAIL_OUTGOING_STORAGE_SUBFOLDER . "/";
    $newFileID    = newFileID();
    $varFileArray = [
        "year"    => date("Y"),
        "fID"     => "",
        "docpath" => $docpath,
        "webpath" => $webpath,
        "syspath" => $syspath,
    ];
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
# Записываем в БД в таблицу mailbox_log отчет о действии пользователя
function insRecord2log($db, $action, $id, $values) {
    $query    = $db->sql("SELECT MAX(outbox_docID) as lastDocID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE YEAR(outbox_docDate)=YEAR(NOW()) ORDER BY id DESC")->fetchAll();
    $newDocID = $query[0]['lastDocID'];
    $newDocID++;
    $db->insert('mailbox_log', array(
        'log_whoID'   => $_SESSION['id'],
        'log_whoNAME' => $_SESSION['lastname'],
        'log_action'  => $action,
        'log_values'  => json_encode($values),
        'log_row'     => $id,
        //         'log_docID'        => json_encode( $values['inbox_docID'], JSON_NUMERIC_CHECK ),
        'log_docID'   => $newDocID,
        'log_when'    => date('Y-m-d H:i:s'),
    ));
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
function fixCreate($db, $action, $id, $values) {
    $db->update(__MAIL_OUTGOING_TABLENAME, array(
        'outbox_docCreatedByID'  => $_SESSION['id'],
        'outbox_docCreatedWhen'  => date('Y-m-d H:i:s'),
        'outbox_docCreatedBySTR' => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
        'outbox_docUpdatedByID'  => $_SESSION['id'],
        'outbox_docUpdatedBySTR' => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
        'outbox_docUpdatedWhen'  => date('Y-m-d H:i:s'),
        'docmailext'             => 'Mailnew',
    ), array(
        'id' => $id,
    ));
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
function mbLcfirst($str) {
    return mb_strtolower(mb_substr($str, 0, 1)) . mb_substr($str, 1);
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
#
function checkChanges($db, $id, $values) {
    unset($_SESSION['outbox_checkChanges_is']);
    unset($_SESSION['outbox_checkChanges_dataForm']);
    unset($_SESSION['outbox_checkChanges_dataDB']);
    $reqDB     = $db->sql("SELECT * FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $arrValues = [
        'outbox_docID'                     => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docID'],
        'outbox_docDate'                   => validateDate($values[__MAIL_OUTGOING_TABLENAME]['outbox_docDate'], "d.m.Y H:i:s") ? date("Y-m-d H:i:s", strtotime($values[__MAIL_OUTGOING_TABLENAME]['outbox_docDate'])) : null,
        'outbox_docType'                   => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docType'],
        'outbox_docAbout'                  => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docAbout'],
        'outbox_docRecipient_kodzakaz'     => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docRecipient_kodzakaz'],
        'outbox_docSourceID'               => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docSourceID'],
        'outbox_docSourceDate'             => validateDate($values[__MAIL_OUTGOING_TABLENAME]['outbox_docSourceDate'], "d.m.Y") ? date("Y-m-d", strtotime($values[__MAIL_OUTGOING_TABLENAME]['outbox_docSourceDate'])) : null,
        // 'inbox_koddocmail_rel' => $values[__MAIL_OUTGOING_TABLENAME]['inbox_koddocmail_rel'],
        // 'inbox_docID_rel' => $values[__MAIL_OUTGOING_TABLENAME]['inbox_docID_rel'],
        'inbox_rowIDadd_rel'               => ("" == $values[__MAIL_OUTGOING_TABLENAME]['inbox_rowIDadd_rel']) ? null : $values[__MAIL_OUTGOING_TABLENAME]['inbox_rowIDadd_rel'],
        // 'outbox_docFileID' => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docFileIDtmp'],
        // 'outbox_docFileIDadd' => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docFileIDadd'],
        'outbox_docSender_kodzayvtel'      => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docSender_kodzayvtel'],
        'outbox_docContractor_kodispolout' => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docContractor_kodispolout'],
        'outbox_controlIspolActive'        => $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolActive'],
        'outbox_controlIspolUseDeadline'   => $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolUseDeadline'],
        'outbox_docDateDeadline'           => validateDate($values[__MAIL_OUTGOING_TABLENAME]['outbox_docDateDeadline'], "d.m.Y") ? date("Y-m-d", strtotime($values[__MAIL_OUTGOING_TABLENAME]['outbox_docDateDeadline'])) : null,
        'outbox_controlIspolMailReminder1' => $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolMailReminder1'],
        'outbox_controlIspolMailReminder2' => $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolMailReminder2'],
        'outbox_controlIspolCheckout'      => $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolCheckout'],
    ];
    $arrFromDB = [
        'outbox_docID'                     => $reqDB[0]['outbox_docID'],
        'outbox_docDate'                   => validateDate($reqDB[0]['outbox_docDate'], "Y-m-d H:i:s") ? $reqDB[0]['outbox_docDate'] : null,
        'outbox_docType'                   => $reqDB[0]['outbox_docType'],
        'outbox_docAbout'                  => $reqDB[0]['outbox_docAbout'],
        'outbox_docRecipient_kodzakaz'     => $reqDB[0]['outbox_docRecipient_kodzakaz'],
        'outbox_docSourceID'               => $reqDB[0]['outbox_docSourceID'],
        'outbox_docSourceDate'             => validateDate($reqDB[0]['outbox_docSourceDate'], "Y-m-d") ? $reqDB[0]['outbox_docSourceDate'] : null,
        // 'inbox_koddocmail_rel' => $reqDB[0]['inbox_koddocmail_rel'],
        // 'inbox_docID_rel' => $reqDB[0]['inbox_docID_rel'],
        'inbox_rowIDadd_rel'               => $reqDB[0]['inbox_rowIDadd_rel'],
        // 'outbox_docFileID' => $reqDB[0]['outbox_docFileID'],
        // 'outbox_docFileIDadd' => $reqDB[0]['outbox_docFileIDadd'],
        'outbox_docSender_kodzayvtel'      => $reqDB[0]['outbox_docSender_kodzayvtel'],
        'outbox_docContractor_kodispolout' => $reqDB[0]['outbox_docContractor_kodispolout'],
        'outbox_controlIspolActive'        => $reqDB[0]['outbox_controlIspolActive'],
        'outbox_controlIspolUseDeadline'   => $reqDB[0]['outbox_controlIspolUseDeadline'],
        'outbox_docDateDeadline'           => validateDate($reqDB[0]['outbox_docDateDeadline'], "Y-m-d") ? $reqDB[0]['outbox_docDateDeadline'] : null,
        'outbox_controlIspolMailReminder1' => $reqDB[0]['outbox_controlIspolMailReminder1'],
        'outbox_controlIspolMailReminder2' => $reqDB[0]['outbox_controlIspolMailReminder2'],
        'outbox_controlIspolCheckout'      => $reqDB[0]['outbox_controlIspolCheckout'],
    ];
    $checkChanges_dataForm = "";
    $checkChanges_dataDB   = "";
    foreach ($arrValues as $key => $row) {
        $checkChanges_dataForm .= ($key) . ' = ' . $row . "\r\n";
    }
    foreach ($arrFromDB as $key => $row) {
        $checkChanges_dataDB .= ($key) . ' = ' . $row . "\r\n";
    }

    $_SESSION['outbox_checkChanges_is']       = ($arrValues === $arrFromDB) ? 'nochanges' : "changed";
    $_SESSION['outbox_checkChanges_dataDB']   = $checkChanges_dataDB;
    $_SESSION['outbox_checkChanges_dataForm'] = $checkChanges_dataForm;
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
#
function fixLog($db, $action, $id, $values) {

    $__USERID = $_SESSION['id'];
    // Проверем был ли отмечен документ как выполненный
    // @ $checkout
    $reqDB     = $db->sql("SELECT * FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $arrValues = [
        'outbox_docID'                     => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docID'],
        'outbox_docDate'                   => validateDate($values[__MAIL_OUTGOING_TABLENAME]['outbox_docDate'], "d.m.Y H:i:s") ? date("Y-m-d H:i:s", strtotime($values[__MAIL_OUTGOING_TABLENAME]['outbox_docDate'])) : null,
        'outbox_docType'                   => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docType'],
        'outbox_docAbout'                  => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docAbout'],
        'outbox_docRecipient_kodzakaz'     => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docRecipient_kodzakaz'],
        'outbox_docSourceID'               => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docSourceID'],
        'outbox_docSourceDate'             => validateDate($values[__MAIL_OUTGOING_TABLENAME]['outbox_docSourceDate'], "d.m.Y") ? date("Y-m-d", strtotime($values[__MAIL_OUTGOING_TABLENAME]['outbox_docSourceDate'])) : null,
        // 'inbox_koddocmail_rel' => $values[__MAIL_OUTGOING_TABLENAME]['inbox_koddocmail_rel'],
        // 'inbox_docID_rel' => $values[__MAIL_OUTGOING_TABLENAME]['inbox_docID_rel'],
        'inbox_rowIDadd_rel'               => ("" == $values[__MAIL_OUTGOING_TABLENAME]['inbox_rowIDadd_rel']) ? null : $values[__MAIL_OUTGOING_TABLENAME]['inbox_rowIDadd_rel'],
        // 'outbox_docFileID' => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docFileIDtmp'],
        // 'outbox_docFileIDadd' => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docFileIDadd'],
        'outbox_docSender_kodzayvtel'      => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docSender_kodzayvtel'],
        'outbox_docContractor_kodispolout' => $values[__MAIL_OUTGOING_TABLENAME]['outbox_docContractor_kodispolout'],
        'outbox_controlIspolActive'        => $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolActive'],
        'outbox_controlIspolUseDeadline'   => $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolUseDeadline'],
        'outbox_docDateDeadline'           => validateDate($values[__MAIL_OUTGOING_TABLENAME]['outbox_docDateDeadline'], "d.m.Y") ? date("Y-m-d", strtotime($values[__MAIL_OUTGOING_TABLENAME]['outbox_docDateDeadline'])) : null,
        'outbox_controlIspolMailReminder1' => $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolMailReminder1'],
        'outbox_controlIspolMailReminder2' => $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolMailReminder2'],
        'outbox_controlIspolCheckout'      => $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolCheckout'],
    ];
    $arrFromDB = [
        'outbox_docID'                     => $reqDB[0]['outbox_docID'],
        'outbox_docDate'                   => validateDate($reqDB[0]['outbox_docDate'], "Y-m-d H:i:s") ? $reqDB[0]['outbox_docDate'] : null,
        'outbox_docType'                   => $reqDB[0]['outbox_docType'],
        'outbox_docAbout'                  => $reqDB[0]['outbox_docAbout'],
        'outbox_docRecipient_kodzakaz'     => $reqDB[0]['outbox_docRecipient_kodzakaz'],
        'outbox_docSourceID'               => $reqDB[0]['outbox_docSourceID'],
        'outbox_docSourceDate'             => validateDate($reqDB[0]['outbox_docSourceDate'], "Y-m-d") ? $reqDB[0]['outbox_docSourceDate'] : null,
        // 'inbox_koddocmail_rel' => $reqDB[0]['inbox_koddocmail_rel'],
        // 'inbox_docID_rel' => $reqDB[0]['inbox_docID_rel'],
        'inbox_rowIDadd_rel'               => $reqDB[0]['inbox_rowIDadd_rel'],
        // 'outbox_docFileID' => $reqDB[0]['outbox_docFileID'],
        // 'outbox_docFileIDadd' => $reqDB[0]['outbox_docFileIDadd'],
        'outbox_docSender_kodzayvtel'      => $reqDB[0]['outbox_docSender_kodzayvtel'],
        'outbox_docContractor_kodispolout' => $reqDB[0]['outbox_docContractor_kodispolout'],
        'outbox_controlIspolActive'        => $reqDB[0]['outbox_controlIspolActive'],
        'outbox_controlIspolUseDeadline'   => $reqDB[0]['outbox_controlIspolUseDeadline'],
        'outbox_docDateDeadline'           => validateDate($reqDB[0]['outbox_docDateDeadline'], "Y-m-d") ? $reqDB[0]['outbox_docDateDeadline'] : null,
        'outbox_controlIspolMailReminder1' => $reqDB[0]['outbox_controlIspolMailReminder1'],
        'outbox_controlIspolMailReminder2' => $reqDB[0]['outbox_controlIspolMailReminder2'],
        'outbox_controlIspolCheckout'      => $reqDB[0]['outbox_controlIspolCheckout'],
    ];
    //
    //
    $timestamp  = date('Y-m-d H:i:s');
    $koddocmail = $reqDB[0]['koddocmail'];
    $recordID   = "MA.O." . $koddocmail . ".L." . time() . "-" . str_pad($_SESSION['id'], 4, "0", STR_PAD_LEFT);
    //
    //
    /*
    $results = array();
    foreach ($one as $key => $value) {
    $result = array();
    if ($value != $two->$key) {
    $result['key_name'] = $key;
    $result['old_value'] = $value;
    $result['new_value'] = $two->$key;
    array_push($results, $result);
    }
    }
     */
    if ("CRT" == $action) {
        $reqDB     = $db->sql("SELECT * FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();
        $arrFromDB = [
            'outbox_docID'                     => $reqDB[0]['outbox_docID'],
            'outbox_docDate'                   => validateDate($reqDB[0]['outbox_docDate'], "Y-m-d H:i:s") ? $reqDB[0]['outbox_docDate'] : null,
            'outbox_docType'                   => $reqDB[0]['outbox_docType'],
            'outbox_docAbout'                  => $reqDB[0]['outbox_docAbout'],
            'outbox_docRecipient_kodzakaz'     => $reqDB[0]['outbox_docRecipient_kodzakaz'],
            'outbox_docSourceID'               => $reqDB[0]['outbox_docSourceID'],
            'outbox_docSourceDate'             => validateDate($reqDB[0]['outbox_docSourceDate'], "Y-m-d") ? $reqDB[0]['outbox_docSourceDate'] : null,
            // 'inbox_koddocmail_rel' => $reqDB[0]['inbox_koddocmail_rel'],
            // 'inbox_docID_rel' => $reqDB[0]['inbox_docID_rel'],
            'inbox_rowIDadd_rel'               => $reqDB[0]['inbox_rowIDadd_rel'],
            // 'outbox_docFileID' => $reqDB[0]['outbox_docFileID'],
            // 'outbox_docFileIDadd' => $reqDB[0]['outbox_docFileIDadd'],
            'outbox_docSender_kodzayvtel'      => $reqDB[0]['outbox_docSender_kodzayvtel'],
            'outbox_docContractor_kodispolout' => $reqDB[0]['outbox_docContractor_kodispolout'],
            'outbox_controlIspolActive'        => $reqDB[0]['outbox_controlIspolActive'],
            'outbox_controlIspolUseDeadline'   => $reqDB[0]['outbox_controlIspolUseDeadline'],
            'outbox_docDateDeadline'           => validateDate($reqDB[0]['outbox_docDateDeadline'], "Y-m-d") ? $reqDB[0]['outbox_docDateDeadline'] : null,
            'outbox_controlIspolMailReminder1' => $reqDB[0]['outbox_controlIspolMailReminder1'],
            'outbox_controlIspolMailReminder2' => $reqDB[0]['outbox_controlIspolMailReminder2'],
            'outbox_controlIspolCheckout'      => $reqDB[0]['outbox_controlIspolCheckout'],
        ];
        $oldSettings = json_decode(json_encode($arrFromDB), JSON_FORCE_OBJECT);
        //
        $reqDB1   = $db->sql("SELECT kodispolout FROM mailbox_sp_users WHERE id='" . $_SESSION['id'] . "'")->fetchAll();
        $kodispol = (!empty($reqDB1) && "" != $reqDB1[0]['kodispolout']) ? $reqDB1[0]['kodispolout'] : null;
        //
        $db->insert(__MAIL_OUTGOING_PREFIX . '_logChanges', array(
            'koddel'        => "",
            'recordID'      => $recordID,
            'recordType'    => "0",
            'recordNum'     => "1",
            'timestamp'     => $timestamp,
            'action'        => $action,
            'koddocmail'    => $reqDB[0]['koddocmail'],
            'userid'        => $_SESSION['id'],
            'kodispol'      => $kodispol,
            'oldSettings'   => null,
            'newSettings'   => json_encode($oldSettings),
            'changes'       => null,
            'changesOldVal' => null,
            'changesNewVal' => null,
            'changesTitle'  => "Создана запись о документе",
            'changesStr'    => "Создана запись о документе",
            'changesText'   => "Создана запись о документе",
            'changesCount'  => null,
        ));
    }
    //
    //
    if ("UPD" == $action) {

        $noChanges = ($arrValues === $arrFromDB);
        // Далее отрабатываем все только при наличии отличий, чтобы не засорять лог записями без изменений
        if (!$noChanges) {
            $newSettings = json_decode(json_encode($arrValues), JSON_FORCE_OBJECT);
            $oldSettings = json_decode(json_encode($arrFromDB), JSON_FORCE_OBJECT);
            // Определяем отличия двух структур (массивов)incomi
            $changes = json_encode(array_diff_assoc($newSettings, $oldSettings));
            // Переводим структуру в массив
            $arrChanges   = json_decode(json_encode(array_diff_assoc($newSettings, $oldSettings)), true);
            $changesStr   = "";
            $changesText  = "";
            $countChanges = 0;
            // Пробегаемся по массиву изменений
            foreach ($arrChanges as $key => $value) {
                $reqDB_0 = $db->sql("SELECT * FROM " . __MAIL_OUTGOING_PREFIX . "_logMapping WHERE parameter='" . $key . "'")->fetchAll();
                if (1 == $reqDB_0[0]['bin']) {
                    $binText     = explode("/", $reqDB_0[0]['msgOnchange']);
                    $msgOnchange = (0 == $value) ? $binText[0] : $binText[1];
                } else {
                    $msgOnchange = $reqDB_0[0]['msgOnchange'];
                }
                /* --- -- --- -- --- --- -- --- -- ---
                Если изменяются параметры с датой, то приводим к рос. формату
                Если изменяются параметры с кодом вместо значения, то получаем значение
                 */
                if ('outbox_docDate' == $key) {
                    $oldVal = empty($oldSettings[$key]) ? "Нет даты" : (validateDate($oldSettings[$key], "Y-m-d H:i:s") ? date('d.m.Y H:i:s', strtotime($oldSettings[$key])) : "Нет даты");
                    $newVal = empty($value) ? "Нет даты" : (validateDate($value, "d.m.Y H:i:s") ? date('d.m.Y H:i:s', strtotime($value)) : "Нет даты");
                } elseif ('outbox_docDateDeadline' == $key) {
                    $oldVal = empty($oldSettings[$key]) ? "Нет даты" : (validateDate($oldSettings[$key], "Y-m-d") ? date('d.m.Y', strtotime($oldSettings[$key])) : "Нет даты");
                    $newVal = empty($value) ? "Нет даты" : (validateDate($value, "Y-m-d") ? date('d.m.Y', strtotime($value)) : "Нет даты");
                } elseif ('outbox_docSourceDate' == $key) {
                    $oldVal = empty($oldSettings[$key]) ? "Нет даты" : (validateDate($oldSettings[$key], "Y-m-d") ? date('d.m.Y', strtotime($oldSettings[$key])) : "Нет даты");
                    $newVal = empty($value) ? "Нет даты" : (validateDate($value, "d.m.Y") ? date('d.m.Y', strtotime($value)) : "Нет даты");
                } elseif ('outbox_docRecipient_kodzakaz' == $key) {
                    $reqDB_old = $db->sql("SELECT * FROM sp_contragents WHERE kodcontragent='" . $oldSettings[$key] . "'")->fetchAll();
                    $reqDB_new = $db->sql("SELECT * FROM sp_contragents WHERE kodcontragent='" . $value . "'")->fetchAll();
                    $oldVal    = $reqDB_old[0]['nameshort'];
                    $newVal    = $reqDB_new[0]['nameshort'];
                } elseif ('outbox_docSender_kodzayvtel' == $key) {
                    $reqDB_old = $db->sql("SELECT * FROM mailbox_sp_users WHERE kodzayvtel='" . $oldSettings[$key] . "'")->fetchAll();
                    $reqDB_new = $db->sql("SELECT * FROM mailbox_sp_users WHERE kodzayvtel='" . $value . "'")->fetchAll();
                    $oldVal    = $reqDB_old[0]['namezayvfio'];
                    $newVal    = $reqDB_new[0]['namezayvfio'];
                } elseif ('outbox_docContractor_kodispolout' == $key) {
                    //
                    $arrIspol_old = explode(",", $oldSettings[$key]);
                    $arrIspol_new = explode(",", $value);
                    $oldVal       = "";
                    $newVal       = "";
                    foreach ($arrIspol_old as $valOld) {
                        $reqDB_old = $db->sql("SELECT * FROM mailbox_sp_users WHERE kodispolout=" . $valOld)->fetchAll();
                        if ($reqDB_old) {
                            $oldVal .= !empty($reqDB_old[0]['namezayvfio']) ? ('000000000000000' == $reqDB_old[0]['kodispolout']) ? '---,' : $reqDB_old[0]['namezayvfio'] . "," : ',';
                        }
                    }
                    foreach ($arrIspol_new as $valNew) {
                        $reqDB_new = $db->sql("SELECT * FROM mailbox_sp_users WHERE kodispolout=" . $valNew)->fetchAll();
                        if ($reqDB_new) {
                            $newVal .= !empty($reqDB_new[0]['namezayvfio']) ? ('000000000000000' == $reqDB_new[0]['kodispolout']) ? '---,' : $reqDB_new[0]['namezayvfio'] . "," : ',';
                        }
                    }
                    $oldVal = rtrim($oldVal, ",");
                    $newVal = rtrim($newVal, ",");
                    $oldVal = ltrim($oldVal, ",");
                    $newVal = ltrim($newVal, ",");
                } elseif ('outbox_docType' == $key) {
                    $reqDB_old = $db->sql("SELECT type_name_short FROM mailbox_sp_doctypes_outgoing WHERE type_id='" . $oldSettings[$key] . "'")->fetchAll();
                    $reqDB_new = $db->sql("SELECT type_name_short FROM mailbox_sp_doctypes_outgoing WHERE type_id='" . $value . "'")->fetchAll();
                    $oldVal    = $reqDB_old[0]['type_name_short'];
                    $newVal    = $reqDB_new[0]['type_name_short'];
                } elseif ('outbox_controlIspolCheckout' == $key) {
                    //
                    // Фиксируем время выставления метки об исполнении документа в самой записи документа
                    if ('0' == $oldSettings[$key] && '1' == $value) {
                        $db->update(__MAIL_OUTGOING_TABLENAME, array(
                            'outbox_controlIspolCheckoutWhen' => $timestamp,
                        ), array(
                            'koddocmail' => $koddocmail,
                        ));
                    } elseif ('1' == $oldSettings[$key] && '0' == $value) {
                        $db->update(__MAIL_OUTGOING_TABLENAME, array(
                            'outbox_controlIspolCheckoutWhen' => null,
                        ), array(
                            'koddocmail' => $koddocmail,
                        ));
                    }
                    $oldVal = $oldSettings[$key];
                    $newVal = $value;
                } elseif ('inbox_rowIDadd_rel' == $key) {
                    $arrRowIDaddRel_old = explode(",", $oldSettings[$key]);
                    $arrRowIDaddRel_new = explode(",", $value);

                    $resVal_old = '';
                    foreach ($arrRowIDaddRel_old as $tmpVal1) {
                        $reqDB_old = $db->sql("SELECT inbox_docID FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id='" . $tmpVal1 . "'")->fetchAll();
                        $resVal_old .= !empty($reqDB_old) ? $reqDB_old[0]['inbox_docID'] . "," : '';
                    }
                    $oldVal = rtrim($resVal_old, ",");
                    $oldVal = ltrim($resVal_old, ",");
                    //
                    $resVal_new = '';
                    foreach ($arrRowIDaddRel_new as $tmpVal2) {
                        $reqDB_new = $db->sql("SELECT inbox_docID FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id='" . $tmpVal2 . "'")->fetchAll();
                        $resVal_new .= !empty($reqDB_new) ? $reqDB_new[0]['inbox_docID'] . "," : '';
                    }
                    $newVal = rtrim($resVal_new, ",");
                    $newVal = ltrim($resVal_new, ",");
                } elseif ('outbox_controlIspolUseDeadline' == $key) {
                    if (1 == $oldSettings[$key] && 0 == $value) {
                        $oldVal = $oldSettings[$key] . " / " . date('d.m.Y', strtotime($arrFromDB['outbox_docDateDeadline']));
                        $newVal = $value;
                    } elseif (0 == $oldSettings[$key] && 1 == $value) {
                        $oldVal = $oldSettings[$key];
                        $newVal = $value . " / " . date('d.m.Y', strtotime($arrValues['outbox_docDateDeadline']));
                    }
                } else {
                    $oldVal = $oldSettings[$key];
                    $newVal = $value;
                }
                // --- -- --- -- ---
                $changesStr = "<span class='text-warning' style='margin-right:10px'>" . ("" == $oldVal ? "---" : $oldVal) . "</span>>>><span class='text-success' style='margin-left:10px'>" . ("" == $newVal ? "---" : $newVal) . "</span>";
                $changesText .= "<span class='bg-info'><b>" . $msgOnchange . " " . mbLcfirst($reqDB_0[0]['description']) . "</b></span>";
                $changesText .= "<br>";
                $changesText .= $changesStr;
                $changesText .= "///";
                $countChanges++;
                //
                $reqDB1   = $db->sql("SELECT kodispolout FROM mailbox_sp_users WHERE id='" . $_SESSION['id'] . "'")->fetchAll();
                $kodispol = (!empty($reqDB1) && "" != $reqDB1[0]['kodispolout']) ? $reqDB1[0]['kodispolout'] : null;
                //
                $db->insert(__MAIL_OUTGOING_PREFIX . '_logChanges', array(
                    'koddel'        => "",
                    'recordType'    => "1",
                    'recordNum'     => $countChanges,
                    'recordID'      => $recordID,
                    'timestamp'     => $timestamp,
                    'action'        => $action,
                    'koddocmail'    => $reqDB[0]['koddocmail'],
                    'userid'        => $_SESSION['id'],
                    'kodispol'      => $kodispol,
                    'oldSettings'   => json_encode($oldSettings),
                    'newSettings'   => json_encode($newSettings),
                    'changes'       => $changes,
                    'changesOldVal' => "" == $oldVal ? "---" : $oldVal,
                    'changesNewVal' => "" == $newVal ? "---" : $newVal,
                    'changesTitle'  => $msgOnchange . " " . mbLcfirst($reqDB_0[0]['description']),
                    'changesStr'    => $msgOnchange . " " . mbLcfirst($reqDB_0[0]['description']),
                    'changesText'   => $msgOnchange . " " . mbLcfirst($reqDB_0[0]['description']),
                    'changesCount'  => null,
                ));
            }
            $changesText = "" != $changesText ? substr($changesText, 0, -3) : "";
            //
            $reqDB1   = $db->sql("SELECT kodispolout FROM mailbox_sp_users WHERE id='" . $_SESSION['id'] . "'")->fetchAll();
            $kodispol = (!empty($reqDB1) && "" != $reqDB1[0]['kodispolout']) ? $reqDB1[0]['kodispolout'] : null;
            //
            //
            //
            //
            //
            $db->insert(__MAIL_OUTGOING_PREFIX . '_logChanges', array(
                'koddel'       => "",
                'recordID'     => $recordID,
                'timestamp'    => $timestamp,
                'action'       => $action,
                'koddocmail'   => $reqDB[0]['koddocmail'],
                'userid'       => $_SESSION['id'],
                'kodispol'     => $kodispol,
                'oldSettings'  => json_encode($oldSettings),
                'newSettings'  => json_encode($newSettings),
                'changes'      => $changes,
                'changesTitle' => $msgOnchange,
                'changesText'  => $changesText,
                'changesCount' => $countChanges,
            ));
        }
    }
    //
    //
    if ("DEL" == $action) {
        //
        $reqDB1   = $db->sql("SELECT kodispolout FROM mailbox_sp_users WHERE id='" . $_SESSION['id'] . "'")->fetchAll();
        $kodispol = (!empty($reqDB1) && "" != $reqDB1[0]['kodispolout']) ? $reqDB1[0]['kodispolout'] : null;
        //
        $oldSettings = json_decode(json_encode($arrFromDB), JSON_FORCE_OBJECT);
        //
        $db->insert(__MAIL_OUTGOING_PREFIX . '_logChanges', array(
            'koddel'        => "",
            'recordType'    => "-1",
            'recordNum'     => null,
            'recordID'      => null,
            'timestamp'     => $timestamp,
            'action'        => $action,
            'koddocmail'    => $reqDB[0]['koddocmail'],
            'userid'        => $_SESSION['id'],
            'kodispol'      => $kodispol,
            'oldSettings'   => json_encode($oldSettings),
            'newSettings'   => null,
            'changes'       => null,
            'changesOldVal' => null,
            'changesNewVal' => null,
            'changesTitle'  => "Запись была удалена",
            'changesStr'    => null,
            'changesText'   => null,
            'changesCount'  => null,
        ));
    }
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
function fixUpdate($db, $action, $id, $values) {
    $db->update(__MAIL_OUTGOING_TABLENAME, array(
        'outbox_docUpdatedByID'  => $_SESSION['id'],
        'outbox_docUpdatedBySTR' => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
        'outbox_docUpdatedWhen'  => date('Y-m-d H:i:s'),
    ), array(
        'id' => $id,
    ));
    PORTAL_SYSLOG('99922000', '0000002', $id, null, null, null);
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#####
# ФУНКЦИЯ
# Назначение:
#####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
#
function updateLogCheckouts($db, $id, $values) {
    // Берем из сессии ID пользователя
    $userid = $_SESSION['id'];
    // Текущая временная метка
    $datenow = date("Y-m-d H:i:s");
    // SQL: (reqSQL_1) Снимаем из текущей записи ID документа (koddocmail)
    $_reqSQL_1  = $db->sql("SELECT koddocmail, outbox_docContractor_kodispolout FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $koddocmail = $_reqSQL_1[0]['koddocmail'];
    $kodispol   = $_reqSQL_1[0]['outbox_docContractor_kodispolout'];
    // --- --- --- --- ---
    // Берем набор исполнителей из формы редактирования
    $ispolNew = $values[__MAIL_OUTGOING_TABLENAME]['outbox_docContractor_kodispolout'];
    // SQL: (reqSQL_3) Удаляем из таблицы исполнения (".__MAIL_INCOMING_PREFIX."_logCheckouts) отстутствующих исполнителей в списке по документу
    $_reqSQL_3 = $db->sql("DELETE FROM " . __MAIL_OUTGOING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND kodispol NOT IN ({$ispolNew})");
    // --- --- --- --- ---
    // SQL: (reqSQL_2) Снимаем из таблицы отметок об исполнении (TBLPREFIX_logCheckouts) статус текущего документа (koddocmail) по текущему пользователю (userid)
    $_sqlString_2 = "SELECT ispolStatus FROM " . __MAIL_OUTGOING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'";
    $_reqSQL_2    = $db->sql($_sqlString_2)->fetchAll();
    //
    $ispolStatusOld = isset($_reqSQL_2[0]['ispolStatus']) ? $_reqSQL_2[0]['ispolStatus'] : "0";
    $ispolStatusNew = $values['ispolStatus'];
    // SQL: (reqSQL_4) Если новый статус не соответствует старому - обновляем его
    if ($ispolStatusOld != $ispolStatusNew) {
        $__reqSQL_5 = $db->sql("SELECT * FROM " . __MAIL_OUTGOING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'")->fetchAll();
        if ($__reqSQL_5) {
            $__reqSQL_41 = $db->sql("UPDATE " . __MAIL_OUTGOING_PREFIX . "_logCheckouts SET timestamp='{$datenow}', ispolStatus='{$ispolStatusNew}' WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'");
        } else {
            $__reqSQL_42 = $db->sql("INSERT INTO " . __MAIL_OUTGOING_PREFIX . "_logCheckouts (koddel, timestamp, koddocmail, userid, kodispol, ispolStatus, comment) VALUES ('', '{$datenow}', '{$koddocmail}', '{$userid}', '{$kodispol}', '{$ispolStatusNew}', '')");
        }
    }
    //
    // ОТЛАДКА >>>>>
    $msgMain  = $koddocmail;
    $msgText1 = "ID исполнителей: " . $ispolNew;
    $msgText2 = "Старый статус: " . $ispolStatusOld;
    $msgText3 = "Новый сатус: " . $ispolStatusNew;
    $msgText4 = "";
    $msgText5 = "";
    outputTempTest("updateLogCheckouts", $msgMain, $msgText1, $msgText2, $msgText3, $msgText4, $msgText5);
    // <<<<< ОТЛАДКА
    //
}
#####
#####
#####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
#
function updateIspolStatus($db, $id, $values) {

    $userid = $_SESSION['id'];
    //
    $__reqKoddocmail = $db->sql("SELECT koddocmail, outbox_docContractor_kodispolout FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    if ($__reqKoddocmail) {
        $koddocmail = $__reqKoddocmail[0]['koddocmail'];
        $kodispol   = $__reqKoddocmail[0]['outbox_docContractor_kodispolout'];
    }
    //
    if ("" != $koddocmail && "" != $kodispol) {
        $__reqIspolStatus = $db->sql("SELECT ispolStatus FROM " . __MAIL_OUTGOING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND userid='{$userid}'")->fetchAll();
        $ispolStatus      = isset($__reqIspolStatus[0]['ispolStatus']) ? $__reqIspolStatus[0]['ispolStatus'] : "0";
        //
        // ОТЛАДКА >>>>>
        $msgMain  = $koddocmail;
        $msgText1 = $koddocmail . " // " . $kodispol . " >>> " . $ispolStatus . " // " . $values['ispolStatus'];
        $msgText2 = "";
        $msgText3 = "";
        $msgText4 = "";
        $msgText5 = "";
        outputTempTest("updateIspolStatus", $msgMain, $msgText1, $msgText2, $msgText3, $msgText4, $msgText5);
        // <<<<< ОТЛАДКА
        //
        if ($__reqIspolStatus && $ispolStatus != $values['ispolStatus']) {
            $db->update(__MAIL_OUTGOING_PREFIX . '_logCheckouts', array(
                'timestamp'   => date("Y-m-d H:i:s"),
                'ispolStatus' => $values['ispolStatus'],
            ), array(
                'koddocmail' => $koddocmail,
                'userid'     => $userid,
            ));
        }
    }
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#####
#####
#####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
#
function clearRelFields($db, $id, $values) {
    #
    $_REQ_1 = $db->sql("SELECT koddocmail, outbox_docID, outbox_docComment, inbox_rowID_rel, inbox_rowIDadd_rel FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    if ($_REQ_1) {
        $inbox_rowID_rel     = $_REQ_1[0]['inbox_rowID_rel'];
        $inbox_rowID_relV    = $values[__MAIL_OUTGOING_TABLENAME]['inbox_rowID_rel'];
        $inbox_rowIDadd_rel  = $_REQ_1[0]['inbox_rowIDadd_rel'];
        $inbox_rowIDadd_relV = $values[__MAIL_OUTGOING_TABLENAME]['inbox_rowIDadd_rel'];
        if ("" != $_REQ_1[0]['inbox_rowID_rel'] && NULL != $_REQ_1[0]['inbox_rowID_rel']) {
            $outbox_docTypeV = $values[__MAIL_OUTGOING_TABLENAME]['outbox_docType'];

            if (empty($inbox_rowID_relV) && !empty($inbox_rowID_rel)) {
                # ----- ----- ----- ----- -----
                # Тип документа
                $_REQ_2      = $db->sql("SELECT inbox_docType_prev FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $inbox_rowID_rel)->fetchAll();
                $docTYPEprev = !empty($_REQ_2) ? $_REQ_2[0]['inbox_docType_prev'] : null;
                if ("" != $docTYPEprev && null != $docTYPEprev) {
                    $_REQ_3         = $db->sql("SELECT * FROM mailbox_sp_doctypes_incoming WHERE type_id = " . $docTYPEprev)->fetchAll();
                    $docTypeSTRprev = !empty($_REQ_3[0]['type_name_short']) ? $_REQ_3[0]['type_name_short'] : "---";
                }
                #
                $db->update(__MAIL_OUTGOING_TABLENAME, array(
                    'inbox_docID_rel'      => null,
                    'inbox_UID_rel'        => null,
                    'inbox_koddocmail_rel' => null,
                    'inbox_fileID_rel'     => null,
                ), array(
                    'id' => $id,
                ));
                # UPDATE 20230809-01 >>>>>
                # Переносим ID другого ответного документа или из дополнительных ответов в основной ответ входящего, если таковой находится
                $_REQ_4 = $db->sql("SELECT * FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE inbox_rowID_rel='{$inbox_rowID_rel}' AND id != '{$id}' ORDER BY id DESC LIMIT 1")->fetchAll();
                if (!empty($_REQ_4)) {
                    if (!empty($inbox_rowID_rel)) {
                        $db->update(__MAIL_INCOMING_TABLENAME, array(
                            'outbox_docID_rel'      => $_REQ_4[0]['outbox_docID'],
                            'outbox_rowID_rel'      => $_REQ_4[0]['ID'],
                            'outbox_UID_rel'        => $_REQ_4[0]['outbox_UID'],
                            'outbox_koddocmail_rel' => $_REQ_4[0]['koddocmail'],
                            'outbox_fileID_rel'     => $_REQ_4[0]['outbox_docFileID'],
                        ), array(
                            'id' => $inbox_rowID_rel,
                        ));
                    }
                } else {
                    $_REQ_5 = $db->sql("SELECT * FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE inbox_rowIDadd_rel LIKE '%{$inbox_rowID_rel}%' ORDER BY id DESC LIMIT 1")->fetchAll();
                    if (!empty($_REQ_5)) {
                        $db->update(__MAIL_INCOMING_TABLENAME, array(
                            'outbox_docID_rel'      => $_REQ_5[0]['outbox_docID'],
                            'outbox_rowID_rel'      => $_REQ_5[0]['ID'],
                            'outbox_UID_rel'        => $_REQ_5[0]['outbox_UID'],
                            'outbox_koddocmail_rel' => $_REQ_5[0]['koddocmail'],
                            'outbox_fileID_rel'     => $_REQ_5[0]['outbox_docFileID'],
                        ), array(
                            'id' => $inbox_rowID_rel,
                        ));
                    } else {
                        # UPDATE 20230809-01 <<<<<
                        $db->update(__MAIL_INCOMING_TABLENAME, array(
                            'outbox_docID_rel'      => null,
                            'outbox_rowID_rel'      => null,
                            'outbox_UID_rel'        => null,
                            'outbox_koddocmail_rel' => null,
                            'outbox_fileID_rel'     => null,
                        ), array(
                            'id' => $inbox_rowID_rel,
                        ));
                    }
                }
                if ($outbox_docTypeV < 2 || empty($inbox_rowID_relV)) {
                    $db->update(__MAIL_INCOMING_TABLENAME, array(
                        'inbox_docType'      => $docTYPEprev,
                        'inbox_docTypeSTR'   => $docTypeSTRprev,
                        'inbox_docType_lock' => '0',
                    ), array(
                        'id' => $inbox_rowID_rel,
                    ));
                }
            }
        }
        # UPDATE 20230810-01 >>>>>
        # Пересчитываем и обновляем поля доп. ответов во входящих
        $arrInbox_rowIDadd_rel  = explode(",", $inbox_rowIDadd_rel);
        $arrInbox_rowIDadd_relV = explode(",", $inbox_rowIDadd_relV);
        //
        $arrInbox_rowIDadd_rel_cnt  = count($arrInbox_rowIDadd_rel);
        $arrInbox_rowIDadd_relV_cnt = count($arrInbox_rowIDadd_relV);
        //
        if ($arrInbox_rowIDadd_rel != $arrInbox_rowIDadd_relV) {

            foreach ($arrInbox_rowIDadd_relV as $tmpVal) {
                $_REQ_6 = $db->sql("SELECT * FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id LIKE '%{$tmpVal}%' ORDER BY id DESC LIMIT 1")->fetchAll();
            }

            if ($arrInbox_rowIDadd_rel_cnt > $arrInbox_rowIDadd_relV_cnt) {
            } elseif ($arrInbox_rowIDadd_rel_cnt < $arrInbox_rowIDadd_relV_cnt) {
            }
            //
            // Если что-то добавлено в список документов
            //
            $arrDiff_relV = array_diff($arrInbox_rowIDadd_relV, $arrInbox_rowIDadd_rel);
            if (count($arrDiff_relV) > 0) {
                $outbox_rowIDadd_relNew = "";
                foreach ($arrDiff_relV as $tmpVal) {
                    $_REQ_INC = $db->sql("SELECT outbox_rowID_rel, outbox_rowIDadd_rel FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id LIKE '%{$tmpVal}%' ORDER BY id DESC LIMIT 1")->fetchAll();
                    if (empty($_REQ_INC[0]['outbox_rowID_rel'])) {
                        $db->update(__MAIL_INCOMING_TABLENAME, array(
                            'inbox_docType_lock'    => '1',
                            'outbox_rowID_rel'      => $id,
                            'outbox_docID_rel'      => $_REQ_1[0]['outbox_docID'],
                            'outbox_koddocmail_rel' => $_REQ_1[0]['koddocmail'],
                        ), array(
                            'id' => $tmpVal,
                        ));
                    } else {
                        $outbox_rowIDadd_rel = $_REQ_INC[0]['outbox_rowIDadd_rel'];
                        if (empty($outbox_rowIDadd_rel)) {
                            $outbox_rowIDadd_relNew = $id;
                        } else {
                            $outbox_rowIDadd_relNew = strpos($outbox_rowIDadd_rel, $id) !== false ? $outbox_rowIDadd_rel : $outbox_rowIDadd_rel . "," . $id;
                        }
                        $db->update(__MAIL_INCOMING_TABLENAME, array(
                            'outbox_rowIDadd_rel' => $outbox_rowIDadd_relNew,
                        ), array(
                            'id' => $tmpVal,
                        ));
                    }
                    $db->insert('mailbox_testTable', array(
                        'id_1'    => $_REQ_1[0]['koddocmail'],
                        'date'    => date("Y-m-d H:i:s"),
                        'field_1' => 'Insidez foreach 1',
                        'field_2' => '$arrInbox_rowIDadd_rel > ' . implode(',', $arrInbox_rowIDadd_rel),
                        'field_3' => '$arrInbox_rowIDadd_relV > ' . implode(',', $arrInbox_rowIDadd_relV),
                        'field_4' => '$arrDiff_relV > ' . implode(',', $arrDiff_relV),
                        'field_5' => '$tmpVal > ' . $tmpVal,
                    ));
                }
                $db->insert('mailbox_testTable', array(
                    'id_1'    => $_REQ_1[0]['koddocmail'],
                    'date'    => date("Y-m-d H:i:s"),
                    'field_1' => 'testVal_1 1',
                    'field_2' => '$arrInbox_rowIDadd_rel (cnt) > ' . implode(',', $arrInbox_rowIDadd_rel) . ' (' . $arrInbox_rowIDadd_rel_cnt . ')',
                    'field_3' => '$arrInbox_rowIDadd_relV (cnt) > ' . implode(',', $arrInbox_rowIDadd_relV) . ' (' . $arrInbox_rowIDadd_relV_cnt . ')',
                    'field_4' => 'print_r($arrDiff_relV) > ' . print_r($arrDiff_relV),
                    'field_5' => 'count($arrDiff_relV) > ' . count($arrDiff_relV),
                ));
            }
            //
            // Если что-то удалено из списка документов
            //
            $arrDiff_rel = array_diff($arrInbox_rowIDadd_rel, $arrInbox_rowIDadd_relV);
            if (count($arrDiff_rel) > 0) {
                $outbox_rowIDadd_relNew = "";
                $outbox_rowIDadd_relNew = '-1';
                foreach ($arrDiff_rel as $tmpVal) {
                    $_REQ_INC = $db->sql("SELECT outbox_rowID_rel, outbox_rowIDadd_rel FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id LIKE '%{$tmpVal}%' ORDER BY id DESC LIMIT 1")->fetchAll();
                    //
                    // Если удаляемый документ из списка дополнительных есть в поле основного контрдокумента, то он удаляется отттуда
                    if (!empty($_REQ_INC[0]['outbox_rowID_rel']) && $_REQ_INC[0]['outbox_rowID_rel'] == $id) {
                        $db->update(__MAIL_INCOMING_TABLENAME, array(
                            'outbox_rowID_rel'      => null,
                            'outbox_docID_rel'      => null,
                            'outbox_koddocmail_rel' => null,
                            'inbox_docType_lock'    => '0',
                        ), array(
                            'id' => $tmpVal,
                        ));
                    } else {
                        $arrOutbox_rowIDadd_rel = explode(",", $_REQ_INC[0]['outbox_rowIDadd_rel']);
                        if (!empty($arrOutbox_rowIDadd_rel)) {
                            $tmpID = $arrOutbox_rowIDadd_rel[0];
                            $_REQ_XX = $db->sql("SELECT koddocmail, inbox_docID FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id LIKE '%{$tmpID}%' ORDER BY id DESC LIMIT 1")->fetchAll();
                            $db->update(__MAIL_INCOMING_TABLENAME, array(
                                'inbox_docType_lock'    => '1',
                                'outbox_rowID_rel'      => $tmpID,
                                'outbox_docID_rel'      => $_REQ_XX[0]['inbox_docID'],
                                'outbox_koddocmail_rel' => $_REQ_XX[0]['koddocmail'],
                            ), array(
                                'id' => $tmpVal,
                            ));
                        }
                    }
                    //
                    // Удаляем из списка дополнительных документов текущий документ и перезаписываем список заново
                    $outbox_rowIDadd_rel = $_REQ_INC[0]['outbox_rowIDadd_rel'];
                    if (strpos($outbox_rowIDadd_rel, $id) !== false) {
                        $outbox_rowIDadd_relNew = str_replace($id, "", $outbox_rowIDadd_rel);
                        $outbox_rowIDadd_relNew = str_replace(",,", ",", $outbox_rowIDadd_relNew);
                        $outbox_rowIDadd_relNew = rtrim($outbox_rowIDadd_relNew, ",");
                        $outbox_rowIDadd_relNew = ltrim($outbox_rowIDadd_relNew, ",");
                        // $outbox_rowIDadd_relNew = '-1';
                    } else {
                        $outbox_rowIDadd_relNew = $outbox_rowIDadd_rel;
                    }
                    $db->update(__MAIL_INCOMING_TABLENAME, array(
                        'outbox_rowIDadd_rel'   => $outbox_rowIDadd_relNew,
                    ), array(
                        'id' => $tmpVal,
                    ));
                    //
                    //
                    $db->insert('mailbox_testTable', array(
                        'id_1'    => $_REQ_1[0]['koddocmail'],
                        'date'    => date("Y-m-d H:i:s"),
                        'field_1' => 'Insidez foreach 2',
                        'field_2' => '$arrInbox_rowIDadd_rel > ' . implode(',', $arrInbox_rowIDadd_rel),
                        'field_3' => '$arrInbox_rowIDadd_relV > ' . implode(',', $arrInbox_rowIDadd_relV),
                        'field_4' => '$arrDiff_rel > ' . implode(',', $arrDiff_rel),
                        'field_5' => '$tmpVal > ' . $tmpVal,
                    ));
                }
                $db->insert('mailbox_testTable', array(
                    'id_1'    => $_REQ_1[0]['koddocmail'],
                    'date'    => date("Y-m-d H:i:s"),
                    'field_1' => 'testVal_1 2',
                    'field_2' => '$arrInbox_rowIDadd_rel (cnt) > ' . implode(',', $arrInbox_rowIDadd_rel) . ' (' . $arrInbox_rowIDadd_rel_cnt . ')',
                    'field_3' => '$arrInbox_rowIDadd_relV (cnt) > ' . implode(',', $arrInbox_rowIDadd_relV) . ' (' . $arrInbox_rowIDadd_relV_cnt . ')',
                    'field_4' => 'print_r($arrDiff_rel) > ' . print_r($arrDiff_rel),
                    'field_5' => 'count($arrDiff_rel) > ' . count($arrDiff_rel),
                ));
            }
        }
        # UPDATE 20230810-01 <<<<<
    }
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#####
##### ОСНОВНАЯ ФУНКЦИЯ ОБРАБОТКИ ФОРМЫ
#####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
#
function updateFields($db, $action, $id, $values, $row) {
    # ----- ----- ----- ----- -----
    # Отправитель документа
    $senderKoddocmail = $row[__MAIL_OUTGOING_TABLENAME]['koddocmail'];
    $senderKodzayvtel = $row[__MAIL_OUTGOING_TABLENAME]['outbox_docSender_kodzayvtel'];
    $__senderDATA     = $db->sql("SELECT id, namezayvfio FROM mailbox_sp_users WHERE kodzayvtel=" . $senderKodzayvtel)->fetchAll();
    $senderID         = $__senderDATA[0]['id'];
    $senderSTR        = $__senderDATA[0]['namezayvfio'];
    $_sessionUserID   = $_SESSION['id'];
    # ----- ----- ----- ----- -----
    # Исполнитель документа
    $contractorKOD = $row[__MAIL_OUTGOING_TABLENAME]['outbox_docContractor_kodispolout'];
    if (!empty($contractorKOD)) {
        $__contractorDATA = $db->sql("SELECT id, kodispolout, namezayvfio, emailaddress, dept_num, koddept FROM mailbox_sp_users WHERE kodispolout=" . $contractorKOD)->fetchAll();
        if ($__contractorDATA) {
            !empty($__contractorDATA[0]['id']) ? $contractorID                          = $__contractorDATA[0]['id'] : $contractorID                          = '0';
            !empty($__contractorDATA[0]['dept_num']) ? $contractorDEPT                  = $__contractorDATA[0]['dept_num'] : $contractorDEPT                  = '0';
            !empty($__contractorDATA[0]['koddept']) ? $contractorDEPTKOD                = $__contractorDATA[0]['koddept'] : $contractorDEPTKOD                = '0';
            !empty($__contractorDATA[0]['namezayvfio']) ? $contractorSTR                = $__contractorDATA[0]['namezayvfio'] : $contractorSTR                = '---';
            '000000000000000' == $__contractorDATA[0]['kodispolout'] ? $contractorSTR   = '---' : $contractorSTR   = $__contractorDATA[0]['namezayvfio'];
            !empty($__contractorDATA[0]['emailaddress']) ? $contractorEMAIL             = $__contractorDATA[0]['emailaddress'] : $contractorEMAIL             = '';
            '000000000000000' == $__contractorDATA[0]['kodispolout'] ? $contractorEMAIL = '---' : $contractorEMAIL = $__contractorDATA[0]['emailaddress'];
        }
    }
    # ----- ----- ----- ----- -----
    # Получатель документа
    $zakazKOD = $row[__MAIL_OUTGOING_TABLENAME]['outbox_docRecipient_kodzakaz'];
    if (json_encode($values['enblRecipientManual'], JSON_NUMERIC_CHECK) != '[1]') {
        $__zakazDATA = $db->sql("SELECT kodcontragent, nameshort, zakfio FROM sp_contragents WHERE kodcontragent=" . $zakazKOD)->fetchAll();
        if ('000000000000000' != $zakazKOD) {
            $zakaz     = $__zakazDATA[0]['nameshort'];
            $zakazName = $__zakazDATA[0]['zakfio'];
            $db->update(__MAIL_OUTGOING_TABLENAME, array(
                'outbox_docRecipient'     => $zakaz,
                'outbox_docRecipientName' => $zakazName,
            ), array(
                'id' => $id,
            ));
        }
    }
    # ----- ----- ----- ----- -----
    # Тип документа
    $docTYPE       = $row[__MAIL_OUTGOING_TABLENAME]['outbox_docType'];
    $__docTypeDATA = $db->sql("SELECT * FROM mailbox_sp_doctypes_outgoing WHERE type_id = " . $docTYPE)->fetchAll();
    $docTypeNAME   = !empty($__docTypeDATA[0]['type_name_full']) ? $__docTypeDATA[0]['type_name_full'] : "---";
    $docTypeSTR    = !empty($__docTypeDATA[0]['type_name_short']) ? $__docTypeDATA[0]['type_name_short'] : "---";
    # ----- ----- ----- ----- -----
    $__newKod   = newKoddocmail();
    $__newDocID = newDocID();
    # ----- ----- ----- ----- -----
    # Формируем переменные текущей записи
    $outbox_koddocmail = $row[__MAIL_OUTGOING_TABLENAME]['koddocmail'];
    $outbox_docType    = $row[__MAIL_OUTGOING_TABLENAME]['outbox_docType'];
    $outbox_docID      = $row[__MAIL_OUTGOING_TABLENAME]['outbox_docID'];
    $outbox_UID        = $row[__MAIL_OUTGOING_TABLENAME]['outbox_UID'];
    $outbox_docFileID  = $row[__MAIL_OUTGOING_TABLENAME]['outbox_docFileID'];
    $inbox_rowID_rel   = $row[__MAIL_OUTGOING_TABLENAME]['inbox_rowID_rel'];
    #
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    # 12.04.2023
    # Статус исполнения документа
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    $ctl              = $row[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolActive'];
    $_SQLReq_countChk = $db->sql("SELECT COUNT(*) as countChk FROM " . __MAIL_OUTGOING_PREFIX . "_logCheckouts WHERE koddocmail='{$outbox_koddocmail}' AND ispolStatus = '1'")->fetchAll();

    $arrispol   = explode(",", $row[__MAIL_OUTGOING_TABLENAME]['outbox_docContractor_kodispolout']);
    $ispol_cnt  = count($arrispol);
    $chkout_cnt = $_SQLReq_countChk[0]['countChk'];
    $CHECK_CNT  = ($ispol_cnt == $chkout_cnt) ? 1 : 0;

    // $_SQLReq_userChk = $db->sql("SELECT ispolStatus FROM " . __MAIL_OUTGOING_PREFIX . "_logCheckouts WHERE koddocmail='{$outbox_koddocmail}' AND userid='{$_sessionUserID}'")->fetchAll();
    // $chkoutUser_db = $_SQLReq_userChk[0]['ispolStatus'];
    // $chkoutUser_formVal = $values['ispolStatus'];
    // $CHECK_STATUS = ($chkoutUser_db == $chkoutUser_formVal) ? 1 : 0;

    $chkout_row = $row[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolCheckout'];
    $chkout     = (('1' == $chkout_row) & ($ispol_cnt == $chkout_cnt)) ? 1 : 0;
    $DL         = date('Y-m-d', strtotime($row[__MAIL_OUTGOING_TABLENAME]['outbox_docDateDeadline']));
    $datenow    = date('Y-m-d', time());
    $useDL      = $row[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolUseDeadline'];

    if ('1' == $ctl) {
        if ('1' == $chkout) {
            $checkStatus = 1;
        } else {
            if ($useDL && ($datenow >= $DL)) {
                $checkStatus = 3;
            } else {
                $checkStatus = 2;
            }
        }
    } else {
        $checkStatus = 0;
    }

    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    #
    # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    # СОЗДАНИЕ ЗАПИСИ
    #
    if ('CRT' == $action) {

        PORTAL_SYSLOG('99922000', '0000001', $id, $__newKod, $__newDocID, null);

        $__newUID = date('Y') . "-" . $__newDocID;
        $db->update(__MAIL_OUTGOING_TABLENAME, array(
            'koddocmail'                => $__newKod,
            'koddocmailmain'            => $__newKod,
            'outbox_UID'                => $__newUID,
            'outbox_docID'              => $__newDocID,
            'outbox_docIDSTR'           => $__newDocID,
            'outbox_docTypeSTR'         => $docTypeSTR,
            'outbox_docSenderID'        => $senderID,
            'outbox_docSenderSTR'       => $senderSTR,
            'outbox_docContractorID'    => $contractorID,
            'outbox_docContractorDEPT'  => $contractorDEPTKOD,
            'outbox_docContractorSTR'   => $contractorSTR,
            'outbox_docContractorEMAIL' => $contractorEMAIL,
            'outbox_docCreatedByID'     => $_SESSION['id'],
            'outbox_docCreatedWhen'     => date('Y-m-d H:i:s'),
            'outbox_docCreatedBySTR'    => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
            'outbox_docUpdatedByID'     => $_SESSION['id'],
            'outbox_docUpdatedBySTR'    => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
            'outbox_docUpdatedWhen'     => date('Y-m-d H:i:s'),
            'outbox_controlIspolStatus' => $checkStatus,
        ), array(
            'id' => $id,
        ));
        $db->update('mailbox_counters', array(
            'newDocID_outgoing' => $__newDocID,
        ), array(
            'id' => 1,
        ));
        # ----- ----- ----- ----- -----
        #
        if (!empty($inbox_rowID_rel) and $outbox_docType >= 2) {

            $_REQ_Incoming = $db->sql("SELECT inbox_docType, inbox_docID, inbox_UID, koddocmail, inbox_docFileID FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $inbox_rowID_rel)->fetchAll();
            if (isset($_REQ_Incoming) && !empty($_REQ_Incoming)) {
                $db->update(__MAIL_OUTGOING_TABLENAME, array(
                    'inbox_docID_rel'      => $_REQ_Incoming[0]['inbox_docID'],
                    'inbox_UID_rel'        => $_REQ_Incoming[0]['inbox_UID'],
                    'inbox_koddocmail_rel' => $_REQ_Incoming[0]['koddocmail'],
                    'inbox_fileID_rel'     => $_REQ_Incoming[0]['inbox_docFileID'],
                ), array(
                    'id' => $id,
                ));
                if (3 == $_REQ_Incoming[0]['inbox_docType']) {
                    $db->update(__MAIL_INCOMING_TABLENAME, array(
                        'outbox_docID_rel'      => $outbox_docID,
                        'outbox_rowID_rel'      => $id,
                        'outbox_UID_rel'        => $outbox_UID,
                        'outbox_koddocmail_rel' => $outbox_koddocmail,
                        'outbox_fileID_rel'     => $outbox_docFileID,
                    ), array(
                        'id' => $inbox_rowID_rel,
                    ));
                }
            }
        } else {
            $db->update(__MAIL_OUTGOING_TABLENAME, array(
                'inbox_docID_rel'           => null,
                'inbox_rowID_rel'           => null,
                'inbox_UID_rel'             => null,
                'inbox_koddocmail_rel'      => null,
                'inbox_fileID_rel'          => null,
            ), array(
                'id' => $id,
            ));
        }
        #
        #
        ###### UPD 18.10.23
        ###### Сохраняем в специальную таблицу sp_contragents_manualinput 
        ###### вручную введенные названия контрагентов
        if ($row[__MAIL_OUTGOING_TABLENAME]['outbox_docRecipient_kodzakaz'] === '000000000000000') {
            $userID = $_SESSION['id'];
            $userLastname = $_SESSION['lastname'];
            $newKodcontragentm = newKodcontragentm();
            $senderName = $row[__MAIL_OUTGOING_TABLENAME]['outbox_docRecipient'];

            $req_manualInputs = $db->sql("SELECT nameshort FROM sp_contragents_manualinput WHERE koddel<>'99'")->fetchAll();
            $saveManualInput = '';
            foreach ($req_manualInputs as $key => $value) {
                $string1 = $req_manualInputs[$key]['nameshort'];
                $string2 = $senderName;
                similar_text($string1, $string2, $percent);
                if ($percent > 90) {
                    $saveManualInput = '';
                    break;
                } else {
                    $saveManualInput = 'save';
                }
            }
            if ($saveManualInput == 'save') {
                $timecode = date('Y-m-d H:i:s');
                $req = $db->sql("INSERT INTO sp_contragents_manualinput (timecode, kodcontragentm, nameshort, namefull, fromService, fromID, byUserID, byUserLastname, kfsim) VALUES ('$timecode', '$newKodcontragentm', '$senderName', '$senderName', 'mailnew out', '$__newKod', '$userID', '$userLastname', '$percent')");
                if ($req) {
                    $db->update(
                        __MAIL_OUTGOING_TABLENAME,
                        array(
                            'outbox_docRecipient_kodzakazm' => $newKodcontragentm,
                        ),
                        array(
                            'id' => $id,
                        )
                    );
                }
            }
        } else {
            if ($row[__MAIL_OUTGOING_TABLENAME]['outbox_docRecipient_kodzakazm'] !== null) {
                $db->update(
                    __MAIL_OUTGOING_TABLENAME,
                    array(
                        'outbox_docRecipient_kodzakazm' => null,
                    ),
                    array(
                        'id' => $id,
                    )
                );
            }
        }
        #
        # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        # ИЗМЕНЕНИЕ ЗАПИСИ
        #
    } elseif ('UPD' == $action) {
        $UID = date('Y') . "-" . $outbox_docID;
        $db->update(__MAIL_OUTGOING_TABLENAME, array(
            'outbox_docIDSTR'           => $outbox_docID,
            'outbox_docTypeSTR'         => $docTypeSTR,
            'outbox_UID'                => $UID,
            'outbox_docSenderID'        => $senderID,
            'outbox_docSenderSTR'       => $senderSTR,
            'outbox_docContractorID'    => $contractorID,
            'outbox_docContractorDEPT'  => $contractorDEPTKOD,
            'outbox_docContractorSTR'   => $contractorSTR,
            'outbox_docContractorEMAIL' => $contractorEMAIL,
            'outbox_docUpdatedByID'     => $_SESSION['id'],
            'outbox_docUpdatedBySTR'    => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
            'outbox_docUpdatedWhen'     => date('Y-m-d H:i:s'),
            'outbox_controlIspolStatus' => $checkStatus,
        ), array(
            'id' => $id,
        ));
    }
    #
    #
    ###### UPD 18.10.23
    ###### Сохраняем в специальную таблицу sp_contragents_manualinput 
    ###### вручную введенные названия контрагентов
    if ($row[__MAIL_OUTGOING_TABLENAME]['outbox_docRecipient_kodzakaz'] === '000000000000000') {
        $userID = $_SESSION['id'];
        $userLastname = $_SESSION['lastname'];
        $newKodcontragentm = newKodcontragentm();
        $senderName = $row[__MAIL_OUTGOING_TABLENAME]['outbox_docRecipient'];

        $req_manualInputs = $db->sql("SELECT nameshort FROM sp_contragents_manualinput WHERE koddel<>'99'")->fetchAll();
        $saveManualInput = '';
        foreach ($req_manualInputs as $key => $value) {
            $string1 = $req_manualInputs[$key]['nameshort'];
            $string2 = $senderName;
            similar_text($string1, $string2, $percent);
            if ($percent > 90) {
                $saveManualInput = '';
                break;
            } else {
                $saveManualInput = 'save';
            }
        }
        if ($saveManualInput == 'save') {
            $timecode = date('Y-m-d H:i:s');
            $req = $db->sql("INSERT INTO sp_contragents_manualinput (timecode, kodcontragentm, nameshort, namefull, fromService, fromID, byUserID, byUserLastname, kfsim) VALUES ('$timecode', '$newKodcontragentm', '$senderName', '$senderName', 'mailnew out', '$__newKod', '$userID', '$userLastname', '$percent')");
            if ($req) {
                $db->update(
                    __MAIL_OUTGOING_TABLENAME,
                    array(
                        'outbox_docRecipient_kodzakazm' => $newKodcontragentm,
                    ),
                    array(
                        'id' => $id,
                    )
                );
            }
        }
    } else {
        if ($row[__MAIL_OUTGOING_TABLENAME]['outbox_docRecipient_kodzakazm'] !== null) {
            $db->update(
                __MAIL_OUTGOING_TABLENAME,
                array(
                    'outbox_docRecipient_kodzakazm' => null,
                ),
                array(
                    'id' => $id,
                )
            );
        }
    }
    # ----- ----- ----- ----- -----
    #
    if (!empty($inbox_rowID_rel)) {
        #
        # UPD 20.12.22
        #
        # ТИП ДОКУМЕНТА ОТВЕТ
        # >>> 1. Если чекбокс 'enbl_inbox_docType_change' активен, то проивзодится жесткая связка
        # с входящим документом (тип меняется на ЗАПРОС) и текущим исходящим (ОТВЕТ).
        # Прописываются взаимные кросс-линки 'rel' между входящими и исходящим документами (Оо).
        # >>> 2. Если чекбокс 'enbl_inbox_docType_change' не активен, то прописываются те же кросс-линки,
        # при этом смены типа документа на другой стороне не происходит.
        #
        $_REQ_Incoming = $db->sql("SELECT inbox_docType, inbox_docType_prev, inbox_docID, inbox_UID, koddocmail, inbox_docFileID, outbox_rowID_rel, outbox_rowIDadd_rel FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id=" . $inbox_rowID_rel)->fetchAll();
        #
        if (2 == $outbox_docType) {
            #
            if (isset($_REQ_Incoming) && !empty($_REQ_Incoming)) {
                $db->update(__MAIL_OUTGOING_TABLENAME, array(
                    'inbox_docID_rel'      => $_REQ_Incoming[0]['inbox_docID'],
                    'inbox_UID_rel'        => $_REQ_Incoming[0]['inbox_UID'],
                    'inbox_koddocmail_rel' => $_REQ_Incoming[0]['koddocmail'],
                    'inbox_fileID_rel'     => $_REQ_Incoming[0]['inbox_docFileID'],
                ), array(
                    'id' => $id,
                ));
                #
                if ($_REQ_Incoming[0]['inbox_docType'] >= 0) {

                    # ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
                    # UPD 20230808-01 >>>>>
                    # Добавил контроль, не был ли на этот документ уже ранее дан ответ (проверяем поле на пустоту)
                    #
                    if ("" == $_REQ_Incoming[0]['outbox_rowID_rel']) {
                        $db->update(__MAIL_INCOMING_TABLENAME, array(
                            'outbox_rowID_rel'      => $id,
                            'outbox_docID_rel'      => $outbox_docID,
                            'outbox_UID_rel'        => $outbox_UID,
                            'outbox_koddocmail_rel' => $outbox_koddocmail,
                            'outbox_fileID_rel'     => $outbox_docFileID,
                        ), array(
                            'id' => $inbox_rowID_rel,
                        ));
                        if (json_encode($values['enbl_inbox_docType_change'], JSON_NUMERIC_CHECK) == '[1]') {
                            $db->update(__MAIL_INCOMING_TABLENAME, array(
                                'inbox_docType'      => 3,
                                'inbox_docTypeSTR'   => 'Зап',
                                'inbox_docType_prev' => $_REQ_Incoming[0]['inbox_docType'],
                                'inbox_docType_lock' => '1',
                            ), array(
                                'id' => $inbox_rowID_rel,
                            ));
                        }
                    } else {
                        if ($_REQ_Incoming[0]['outbox_rowID_rel'] != $id) {
                            $rowIDadd_rel = "";
                            if ("" == $_REQ_Incoming[0]['outbox_rowIDadd_rel'] || NULL == $_REQ_Incoming[0]['outbox_rowIDadd_rel']) {
                                $rowIDadd_rel = $id;
                            } else {
                                $rowIDadd_rel = (strpos($_REQ_Incoming[0]['outbox_rowIDadd_rel'], $id) !== false) ? $_REQ_Incoming[0]['outbox_rowIDadd_rel'] : $_REQ_Incoming[0]['outbox_rowIDadd_rel'] . "," . $id;
                            }
                            $db->update(__MAIL_INCOMING_TABLENAME, array(
                                'outbox_rowIDadd_rel' => $rowIDadd_rel,
                            ), array(
                                'id' => $inbox_rowID_rel,
                            ));
                        }
                    }
                    # <<<<< UPD 20230808-01
                    # ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
                }
            } else {
                $db->update(__MAIL_OUTGOING_TABLENAME, array(
                    'inbox_rowID_rel'      => null,
                    'inbox_docID_rel'      => null,
                    'inbox_UID_rel'        => null,
                    'inbox_koddocmail_rel' => null,
                    'inbox_fileID_rel'     => null,
                ), array(
                    'id' => $id,
                ));
                #
                $db->update(__MAIL_INCOMING_TABLENAME, array(
                    'outbox_docID_rel'      => null,
                    'outbox_rowID_rel'      => null,
                    'outbox_UID_rel'        => null,
                    'outbox_koddocmail_rel' => null,
                    'outbox_fileID_rel'     => null,
                ), array(
                    'id' => $inbox_rowID_rel,
                ));
            }
        } elseif (3 != $outbox_docType) {
            $db->update(__MAIL_OUTGOING_TABLENAME, array(
                'inbox_rowID_rel'      => null,
                'inbox_docID_rel'      => null,
                'inbox_UID_rel'        => null,
                'inbox_koddocmail_rel' => null,
                'inbox_fileID_rel'     => null,
            ), array(
                'id' => $id,
            ));
            # ----- ----- ----- ----- -----
            # Тип документа
            $docTYPEprev = $_REQ_Incoming[0]['inbox_docType_prev'];
            if (NULL != $docTYPEprev && "" != $docTYPEprev) {
                $__docTypeDATAprev = $db->sql("SELECT * FROM mailbox_sp_doctypes_incoming WHERE type_id = " . $docTYPEprev)->fetchAll();
                $docTypeSTRprev    = !empty($__docTypeDATAprev[0]['type_name_short']) ? $__docTypeDATAprev[0]['type_name_short'] : "---";
                $db->update(__MAIL_INCOMING_TABLENAME, array(
                    'inbox_docType'    => $docTYPEprev,
                    'inbox_docTypeSTR' => $docTypeSTRprev,
                ), array(
                    'id' => $inbox_rowID_rel,
                ));
            }
            #
            $db->update(__MAIL_INCOMING_TABLENAME, array(
                'outbox_docID_rel'      => null,
                'outbox_rowID_rel'      => null,
                'outbox_UID_rel'        => null,
                'outbox_koddocmail_rel' => null,
                'outbox_fileID_rel'     => null,
            ), array(
                'id' => $inbox_rowID_rel,
            ));
        }
    } else {
        $db->update(__MAIL_OUTGOING_TABLENAME, array(
            'inbox_rowID_rel'      => null,
            'inbox_docID_rel'      => null,
            'inbox_UID_rel'        => null,
            'inbox_koddocmail_rel' => null,
            'inbox_fileID_rel'     => null,
        ), array(
            'id' => $id,
        ));
    }
    # ----- ----- ----- ----- -----
    #
    // Формируем список ID пользователей исполнивших документ
    $_checkoutUserIDs   = "";
    $_checkoutUserDates = "";
    $_QRY_CheckoutIDs   = $db->sql("SELECT userid, timestamp FROM " . __MAIL_OUTGOING_PREFIX . "_logCheckouts WHERE koddocmail='{$outbox_koddocmail}' AND ispolStatus='1'")->fetchAll();
    foreach ($_QRY_CheckoutIDs as $key => $value) {
        $_checkoutUserIDs .= $_QRY_CheckoutIDs[$key]['userid'] . ",";
        $_checkoutUserDates .= $_QRY_CheckoutIDs[$key]['timestamp'] . ",";
    }
    $_checkoutUserIDs   = rtrim($_checkoutUserIDs, ",");
    $_checkoutUserDates = rtrim($_checkoutUserDates, ",");
    $_checkoutUserIDs   = ltrim($_checkoutUserIDs, ",");
    $_checkoutUserDates = ltrim($_checkoutUserDates, ",");
    $db->update(__MAIL_OUTGOING_TABLENAME, array(
        'outbox_controlIspolCheckoutID'      => $_checkoutUserIDs,
        'outbox_controlIspolCheckoutDates'   => $_checkoutUserDates,
        'outbox_controlIspolCheckoutComment' => null,
    ), array(
        'id' => $id,
    ));
    # ----- ----- ----- ----- -----
    #
    $__sID      = $db->sql("SELECT koddocmail, outbox_docID, outbox_docFileID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $__year     = date('Y');
    $__fileID   = $__sID[0]['outbox_docFileID'];
    $file_id    = $__year . "00" . $__sID[0]['outbox_docID'];
    $koddocmail = $__sID[0]['koddocmail'];
    $db->update(__MAIL_OUTGOING_FILES_TABLENAME, array(
        'flag'       => 'CHU',
        'koddocmail' => $koddocmail,
        'file_id'    => $file_id,
    ), array(
        'id' => $__fileID,
    ));
    $db->update(__MAIL_OUTGOING_FILES_TABLENAME, array(
        'flag' => 'CHU',
    ), array(
        'koddocmail' => $koddocmail,
        'mainfile'   => '0',
    ));
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
# UPD 15.07.23
# Пишем комментарий исполнителя в общий чат с комментариями
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
#
function updateLogComments($db, $action, $id, $values) {
    $reqDB       = $db->sql("SELECT koddocmail, outbox_docContractorComment, outbox_docComment FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $_timestamp  = date("Y-m-d H:i:s");
    $_koddocmail = $reqDB[0]['koddocmail'];
    $_commentID  = $_koddocmail . ".K." . time();
    $_userid     = $_SESSION['id'];
    $_username   = $_SESSION['lastname'] . " " . $_SESSION['firstname'];
    if ('CRT' == $action) {
        $contractorCommentForm     = $values[__MAIL_OUTGOING_TABLENAME]['outbox_docContractorComment'];
        $contractorCheckoutComment = $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolCheckoutComment'];
        $docCommentForm            = $values[__MAIL_OUTGOING_TABLENAME]['outbox_docComment'];
        if (!empty($contractorCheckoutComment)) {
            $db->insert(__MAIL_OUTGOING_PREFIX . '_logComments', array(
                'koddel'          => "",
                'timestamp'       => $_timestamp,
                'action'          => 'FORM',
                'koddocmail'      => $_koddocmail,
                'commentID'       => $_commentID,
                'userid'          => $_userid,
                'username'        => $_username,
                'prevcommentText' => null,
                'commentText'     => $contractorCheckoutComment,
                'commentAdd'      => 'Через форму документа добавлен комментарий исполнителя (к отметке)',
            ));
            $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolCheckoutComment'] = "";
        }
        if (!empty($docCommentForm)) {
            $db->insert(__MAIL_OUTGOING_PREFIX . '_logComments', array(
                'koddel'          => "",
                'timestamp'       => $_timestamp,
                'action'          => 'FORM',
                'koddocmail'      => $_koddocmail,
                'commentID'       => $_commentID,
                'userid'          => $_userid,
                'username'        => $_username,
                'prevcommentText' => null,
                'commentText'     => $docCommentForm,
                'commentAdd'      => 'В форме документа обновлена дополнительная информация',
            ));
        }
        if (!empty($contractorCommentForm)) {
            $db->insert(__MAIL_OUTGOING_PREFIX . '_logComments', array(
                'koddel'          => "",
                'timestamp'       => $_timestamp,
                'action'          => 'FORM',
                'koddocmail'      => $_koddocmail,
                'commentID'       => $_commentID,
                'userid'          => $_userid,
                'username'        => $_username,
                'prevcommentText' => null,
                'commentText'     => $contractorCommentForm,
                'commentAdd'      => 'В форме документа обновлен первичный комментарий исполнителя',
            ));
        }
    }
    if ('UPD' == $action) {
        $contractorCommentForm     = $values[__MAIL_OUTGOING_TABLENAME]['outbox_docContractorComment'];
        $contractorCheckoutComment = $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolCheckoutComment'];
        $docCommentForm            = $values[__MAIL_OUTGOING_TABLENAME]['outbox_docComment'];
        if (!empty($contractorCheckoutComment)) {
            $db->insert(__MAIL_OUTGOING_PREFIX . '_logComments', array(
                'koddel'          => "",
                'timestamp'       => $_timestamp,
                'action'          => 'FORM',
                'koddocmail'      => $_koddocmail,
                'commentID'       => $_commentID,
                'userid'          => $_userid,
                'username'        => $_username,
                'prevcommentText' => null,
                'commentText'     => $contractorCheckoutComment,
                'commentAdd'      => 'Через форму документа добавлен комментарий ответственного',
            ));
            $values[__MAIL_OUTGOING_PREFIX]['outbox_controlIspolCheckoutComment'] = "";
        }
        if ($docCommentForm != $reqDB[0]['outbox_docComment']) {
            $db->insert(__MAIL_OUTGOING_PREFIX . '_logComments', array(
                'koddel'          => "",
                'timestamp'       => $_timestamp,
                'action'          => 'FORM',
                'koddocmail'      => $_koddocmail,
                'commentID'       => $_commentID,
                'userid'          => $_userid,
                'username'        => $_username,
                'prevcommentText' => null,
                'commentText'     => $docCommentForm,
                'commentAdd'      => 'В форме документа обновлена дополнительная информация',
            ));
        }
        if ($contractorCommentForm != $reqDB[0]['outbox_docContractorComment']) {
            $db->insert(__MAIL_OUTGOING_PREFIX . '_logComments', array(
                'koddel'          => "",
                'timestamp'       => $_timestamp,
                'action'          => 'FORM',
                'koddocmail'      => $_koddocmail,
                'commentID'       => $_commentID,
                'userid'          => $_userid,
                'username'        => $_username,
                'prevcommentText' => null,
                'commentText'     => $contractorCommentForm,
                'commentAdd'      => 'В форме документа обновлен первичный комментарий исполнителя',
            ));
        }
    }
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
# UPD 19.07.23
# Обновляем количество комментариев к документу
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
#
function updateCountComments($db, $id) {
    $reqDB       = $db->sql("SELECT koddocmail FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $_koddocmail = $reqDB[0]['koddocmail'];
    $_reqDB1     = $db->sql("SELECT COUNT(*) as CommCounts FROM " . __MAIL_OUTGOING_PREFIX . "_logComments WHERE action IN ('COMM','FORM') AND koddocmail = '{$_koddocmail}' AND koddel NOT IN ('deleted', 'NULL')")->fetchAll();
    $counts      = $_reqDB1[0]['CommCounts'];
    $_reqDB2     = $db->sql("UPDATE " . __MAIL_OUTGOING_TABLENAME . " SET cntComments = '{$counts}' WHERE koddocmail = '{$_koddocmail}'");
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
#
function updateFileIDadd($db, $action, $id, $values, $row) {
    if ('UPD' == $action) {
        $_koddocmail          = $row[__MAIL_OUTGOING_TABLENAME]['koddocmail'];
        $_outbox_docFileIDadd = "";
        $_REQ_UploadedFiles   = mysqlQuery("SELECT id FROM " . __MAIL_OUTGOING_FILES_TABLENAME . " WHERE koddocmail='{$_koddocmail}' AND mainfile='0'");
        while ($_ROW_UploadedFiles = mysqli_fetch_assoc($_REQ_UploadedFiles)) {
            $_outbox_docFileIDadd .= $_ROW_UploadedFiles['id'] . ",";
        }
        //
        //
        $db->update(__MAIL_OUTGOING_TABLENAME, array(
            'outbox_docFileIDadd' => $_outbox_docFileIDadd,
        ), array(
            'id' => $id,
        ));
    }
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function delAttachment($db, $id) {
    $__rowFileID     = $db->sql("SELECT outbox_docFileID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $row2delete      = $__rowFileID[0]['outbox_docFileID'];
    $addFiles2delete = $__rowFileID[0]['outbox_docFileIDadd'];
    if (!is_null($row2delete) && !empty($row2delete)) {
        // Удаление оригинального файла ($__tmp2) и сим-ссылки ($__tmp1) с диска
        $__file = $db->sql("SELECT file_truelocation, file_syspath FROM " . __MAIL_OUTGOING_FILES_TABLENAME . " WHERE id=" . $row2delete)->fetchAll();
        $__tmp1 = unlink($__file[0]['file_syspath']);
        $__tmp2 = unlink($__file[0]['file_truelocation']);
        // Удаление записи в таблице файлов
        if ($__tmp1 && $__tmp2) {
            $query = $db->sql("DELETE FROM " . __MAIL_OUTGOING_FILES_TABLENAME . " WHERE id=" . $row2delete);
            PORTAL_SYSLOG('99922000', '000000D', $row2delete, null, null, null);
        }
    }
    $addFiles2delete = "" != $addFiles2delete ? substr($addFiles2delete, 0, -1) : "";
    if (!is_null($addFiles2delete) && !empty($addFiles2delete)) {
        $arrAddFiles2delete = explode(",", $addFiles2delete);
        foreach ($arrAddFiles2delete as $key => $value) {
            // Удаление оригинального файла ($__tmp2) и сим-ссылки ($__tmp1) с диска
            $__file1 = $db->sql("SELECT file_truelocation, file_syspath FROM " . __MAIL_OUTGOING_FILES_TABLENAME . " WHERE id=" . $value)->fetchAll();
            $__tmp11 = unlink($__file1[0]['file_syspath']);
            $__tmp21 = unlink($__file1[0]['file_truelocation']);
            // Удаление записи в таблице файлов
            if ($__tmp11 && $__tmp21) {
                $query = $db->sql("DELETE FROM " . __MAIL_OUTGOING_FILES_TABLENAME . " WHERE id=" . $value);
            }
        }
    }
    //
    $arrFromDB = [
        'outbox_docID'                     => $__rowFileID[0]['outbox_docID'],
        'outbox_docDate'                   => $__rowFileID[0]['outbox_docDate'],
        'outbox_docType'                   => $__rowFileID[0]['outbox_docType'],
        'outbox_docAbout'                  => $__rowFileID[0]['outbox_docAbout'],
        'outbox_docRecipient_kodzakaz'     => $__rowFileID[0]['outbox_docRecipient_kodzakaz'],
        'outbox_docSourceID'               => $__rowFileID[0]['outbox_docSourceID'],
        'outbox_docSourceDate'             => $__rowFileID[0]['outbox_docSourceDate'],
        // 'outbox_koddocmail_rel' => $__rowFileID[0]['outbox_koddocmail_rel'],
        // 'outbox_docID_rel' => $__rowFileID[0]['outbox_docID_rel'],
        'outbox_docFileID'                 => $__rowFileID[0]['outbox_docFileID'],
        'outbox_docFileIDadd'              => $__rowFileID[0]['outbox_docFileIDadd'],
        'outbox_docSender_kodzayvtel'      => $__rowFileID[0]['outbox_docSender_kodzayvtel'],
        'outbox_docContractor_kodispolout' => $__rowFileID[0]['outbox_docContractor_kodispolout'],
        'outbox_controlIspolActive'        => $__rowFileID[0]['outbox_controlIspolActive'],
        'outbox_docDateDeadline'           => $__rowFileID[0]['outbox_docDateDeadline'],
        'outbox_controlIspolMailReminder1' => $__rowFileID[0]['outbox_controlIspolMailReminder1'],
        'outbox_controlIspolMailReminder2' => $__rowFileID[0]['outbox_controlIspolMailReminder2'],
        'outbox_controlIspolCheckout'      => $__rowFileID[0]['outbox_controlIspolCheckout'],
    ];
    $settings = json_decode(json_encode($arrFromDB), JSON_FORCE_OBJECT);
    //
    $reqDB1      = $db->sql("SELECT kodispolout FROM mailbox_sp_users WHERE id='" . $_SESSION['id'] . "'")->fetchAll();
    $kodispolout = (!empty($reqDB1) && "" != $reqDB1[0]['kodispolout']) ? $reqDB1[0]['kodispolout'] : null;
    //
    $db->insert(__MAIL_OUTGOING_PREFIX . '_logChanges', array(
        'timestamp'    => date('Y-m-d H:i:s'),
        'action'       => 'DEL',
        'koddocmail'   => $reqDB1[0]['koddocmail'],
        'userid'       => $_SESSION['id'],
        'kodispol'     => $kodispolout,
        'oldSettings'  => null,
        'newSettings'  => json_encode($settings),
        'changes'      => null,
        'changesTitle' => null,
        'changesText'  => null,
        'changesCount' => null,
    ));
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function updateSyslogAfterupload($db, $id) {
    // $_REQ1 = $db->sql("SELECT koddocmail FROM " . __MAIL_OUTGOING_FILES_TABLENAME . " WHERE id=" . $id)->fetchAll();
    // $_koddocmail    = !empty($_REQ1[0]['koddocmail']) ? $_REQ1[0]['koddocmail'] : "";
    // $_REQ2 = $db->sql("SELECT outbox_docID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE koddocmail=" . $_koddocmail)->fetchAll();
    // $_docID         = !empty($_REQ2[0]['outbox_docID']) ? $_REQ2[0]['outbox_docID'] : "";
    PORTAL_SYSLOG('99922000', '000000F', $id, null, null, null);
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function updateSyslogAfterremove($db, $id) {
    PORTAL_SYSLOG('99922000', '0000003', $id, null, null, null);
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function backupRemovedRecords($db, $id) {
    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
    // ОПЕРАЦИИ ПРИ УДАЛЕНИИ ОСНОВНОЙ ЗАПИСИ ВО ВХОДЯЩЕЙ ПОЧТЕ mailbox_incoming
    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
    // > Основное поле-маркер удаленной записи - KODDEL (практически во всх таблицах)
    /*
    Маркируем как удаленные:
    - mailbox_incoming_logChanges
    - mailbox_incoming_logComments
    - mailbox_incoming_logCheckouts
    - mailbox_incoming_logControl
    - mailbox_incoming_logMailing
    - mailbox_incoming_files
     */
    $_REQ_Koddocmail = $db->sql("SELECT koddocmail FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $_koddocmail     = $_REQ_Koddocmail[0]['koddocmail'];

    $_REQ_UpdateKoddel_1 = $db->sql("UPDATE " . __MAIL_OUTGOING_PREFIX . "_logChanges SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
    $_REQ_UpdateKoddel_2 = $db->sql("UPDATE " . __MAIL_OUTGOING_PREFIX . "_logComments SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
    $_REQ_UpdateKoddel_3 = $db->sql("UPDATE " . __MAIL_OUTGOING_PREFIX . "_logCheckouts SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
    $_REQ_UpdateKoddel_4 = $db->sql("UPDATE " . __MAIL_OUTGOING_PREFIX . "_logControl SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
    // $_REQ_UpdateKoddel_5 = $db->sql("UPDATE " . __MAIL_OUTGOING_PREFIX . "_logMailing SET koddel='deleted' WHERE send_koddocmail='" . $_koddocmail . "'");
    // Маркируем таблицу прикрепленных файлов
    $_REQ_UpdateKoddel_Files = $db->sql("UPDATE " . __MAIL_OUTGOING_FILES_TABLENAME . " SET koddel='deleted' WHERE koddocmail='" . $_koddocmail . "'");
    // Копируем запись в таблицу для удаленных записей
    $_REQ_InsertDeleted = $db->sql("INSERT INTO " . __MAIL_OUTGOING_PREFIX . "_deletedRecords SELECT * FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE koddocmail='" . $_koddocmail . "'");
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
# ФУНКЦИЯ: ОТПРАВКА EMAIL ИСПОЛНИТЕЛЯ ДОКУМЕНТА С ФИКСАЦИЕЙ ВРЕМЕННОЙ МЕТКИ И ОТПРАВИВШЕГО
# ---
# inbox_docContractorID - ID ответственного из ".__MAIL_INCOMING_PREFIX."
# email - email из users, где id = inbox_docContractorID
# inbox_emailSentByID - ID отправившего
# inbox_emailSentWhen - временная метка отправки
#
#
#
function sendemailtoispolYouareispolnitel($db, $action, $id, $values, $row) {

    $query1          = $db->sql("SELECT koddocmail, outbox_docID, outbox_docContractorID, outbox_docContractorMULTI, outbox_docContractor_kodispolout, outbox_docDateDeadline, outbox_docRecipient, outbox_docRecipientName, outbox_docSourceID, outbox_docSourceDate, outbox_docAbout, outbox_docTypeSTR, outbox_docFileID, outbox_docFileIDadd, toSendEmail, outbox_controlIspolActive, outbox_controlIspolStatus FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();
    $__koddocmail    = $query1[0]['koddocmail'];
    $__docID         = $query1[0]['outbox_docID'];
    $__docFileID     = $query1[0]['outbox_docFileID'];
    $__docFileIDadd  = $query1[0]['outbox_docFileIDadd'];
    $__contrID       = $query1[0]['outbox_docContractorID'];
    $__contrKOD      = $query1[0]['outbox_docContractor_kodispolout'];
    $__deadline      = date_create($query1[0]['outbox_docDateDeadline']);
    $__ispolMULTI    = $query1[0]['outbox_docContractorMULTI'];
    $__recipientORG  = $query1[0]['outbox_docRecipient'];
    $__recipientNAME = $query1[0]['outbox_docRecipientName'];
    $__sourceID      = $query1[0]['outbox_docSourceID'];
    $__sourceDate    = date_create($query1[0]['outbox_docSourceDate']);
    $__docABOUT      = $query1[0]['outbox_docAbout'];
    $__docTYPE       = $query1[0]['outbox_docTypeSTR'];
    $__toSendEmail   = $query1[0]['toSendEmail'];
    $__controlActive = $query1[0]['outbox_controlIspolActive'];
    $__controlStatus = $query1[0]['outbox_controlIspolStatus'];

    $__fileAddLinks = "---";

    if ($query1 && "" != $__contrKOD && null != $__contrKOD) {

        $arrispol     = explode(",", $__contrKOD);
        $arrFileIDAdd = explode(",", $__docFileIDadd);
        foreach ($arrispol as $value) {
            $__contractorDATA1 = $db->sql("SELECT id, emailaddress, namezayvfio FROM mailbox_sp_users WHERE kodispolout=" . $value)->fetchAll();
            $__contractorDATA2 = $db->sql("SELECT firstname, middlename, lastname FROM users WHERE id=" . $__contractorDATA1[0]['id'])->fetchAll();
            $__fileName        = "";
            $__fileURL         = "";
            $__fileLink        = "---";

            if ("" != $__docFileID) {
                $__contractorDATA3 = $db->sql("SELECT file_originalname, file_url FROM " . __MAIL_OUTGOING_FILES_TABLENAME . " WHERE id=" . $__docFileID)->fetchAll();
                $__fileName        = (!empty($__contractorDATA3) && "" != $__contractorDATA3[0]['file_originalname']) ? "Основной файл" : "---";
                $__fileURL         = !empty($__contractorDATA3) ? $__contractorDATA3[0]['file_url'] : "";
                $__fileLink        = ("" != $__fileURL) ? '<a href="' . $__fileURL . '" title="">' . $__fileName . '</a>' : "---";
            }
            if ("" != $__docFileIDadd) {
                $__fileAddLinks = "";
                foreach ($arrFileIDAdd as &$value) {
                    if ("" != $value) {
                        $__QRY_FileIDadd = $db->sql("SELECT file_originalname, file_url FROM mailbox_outgoing_files WHERE id=" . $value)->fetchAll();
                        $__fileAddName   = (!empty($__QRY_FileIDadd) && "" != $__QRY_FileIDadd[0]['file_originalname']) ? $__QRY_FileIDadd[0]['file_originalname'] : "---";
                        $__fileAddURL    = !empty($__QRY_FileIDadd) ? $__QRY_FileIDadd[0]['file_url'] : "";
                        $__fileAddLink   = ("" != $__fileAddURL) ? '<a href="' . $__fileAddURL . '" title="">' . $__fileAddName . '</a>' : "---";
                        $__fileAddLinks .= $__fileAddLink . ", ";
                    }
                }
                $__fileAddLinks = rtrim($__fileAddLinks, ', ');
                $__fileAddLinks = ltrim($__fileAddLinks, ', ');
            }
            if ($__contractorDATA1 && $__contractorDATA2) {
                $__email = $__contractorDATA1[0]['emailaddress'];
                $__IO    = !empty($__contractorDATA2) ? $__contractorDATA2[0]['firstname'] . " " . $__contractorDATA2[0]['middlename'] : '';
                $__FIO   = $__contractorDATA1[0]['namezayvfio'];
                //
                //
                if (json_encode($values['toSendEmail'], JSON_NUMERIC_CHECK) == '[1]') {
                    //
                    // Записываем в БД в таблицу ".__MAIL_OUTGOING_PREFIX.", кто поставил галочку "Отправка на email" в окне редактирования и когда
                    $db->update(__MAIL_OUTGOING_TABLENAME, array(
                        'outbox_emailSentByID'  => $_SESSION['id'],
                        'outbox_emailSentBySTR' => $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
                        'outbox_emailSentWhen'  => date('Y-m-d H:i:s'),
                    ), array(
                        'id' => $id,
                    ));
                    # ----- ----- ----- ----- -----
                    #
                    # БЛОК ОТПРАВКИ СООБЩЕНИЯ
                    #
                    #
                    $mail    = new PHPMailer;
                    $message = "";
                    #
                    #
                    # SERVER SETTINGS
                    #
                    #
                    # Enable verbose debug output
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    # Disable verbose debug output
                    # $mail->SMTPDebug = 0;
                    # Send using SMTP
                    $mail->isSMTP();
                    # Set the SMTP server to send through
                    $mail->Host = 'mail.atgs.ru';
                    # Enable SMTP authentication
                    $mail->SMTPAuth = true;
                    # SMTP connection will not close after each email sent, reduces SMTP overhead
                    $mail->SMTPKeepAlive = true;
                    # SMTP username
                    $mail->Username = 'portal@atgs.ru';
                    # SMTP password
                    $mail->Password = 'iu3Li,quohch';
                    # Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    # TCP port
                    $mail->Port = 587;
                    #
                    $mail->setLanguage('ru', __DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/libs/PHPMailer/language/');
                    $mail->CharSet = "utf-8";
                    #
                    # From
                    $from_name  = "АТГС.Портал / Корпоративные сервисы";
                    $from_email = "portal@atgs.ru";
                    $from_name  = "=?utf-8?B?" . base64_encode($from_name) . "?=";
                    $mail->setFrom($from_email, $from_name);
                    # Mail address
                    // $email_to = $__email;
                    $email_to    = 'chugunov@atgs.ru';
                    $email_admin = 'chugunov@atgs.ru';
                    #
                    # Тема сообения
                    $subjectTxt = "Почта АТГС [Исходящие] : Вы назначены ответственным по документу №" . $__docID;
                    $subject    = "=?utf-8?B?" . base64_encode($subjectTxt) . "?=";
                    #
                    $mail->addAddress($email_to);
                    $mail->addCC($email_admin);
                    $mail->addReplyTo('notreply@atgs.ru', 'Do not reply');
                    #
                    # Message body
                    $_msgTitle    = "Исходящий документ";
                    $_msgText     = '<span style="font-size:28px">' . $__IO . ', </span><br>вы назначены ответственным по исходящему документу №' . $__docID;
                    $_docDeadline = validateDate($query1[0]['outbox_docDateDeadline'], "Y-m-d") ? date('d.m.Y', strtotime($query1[0]['outbox_docDateDeadline'])) : "---";
                    $_sourceDate  = validateDate($query1[0]['outbox_docSourceDate'], "Y-m-d") ? date('d.m.Y', strtotime($query1[0]['outbox_docSourceDate'])) : "---";

                    $message .= makeMailMsg_NotifyIspol_Type2($__koddocmail, $_msgTitle, $__docID, $__IO, $__fileLink, $__fileAddLinks, $__docTYPE, $__recipientORG, $__docABOUT, $__sourceID, $_sourceDate, $__controlActive, $__controlStatus, $_docDeadline);
                    #
                    # Content
                    $mail->isHTML(true); // Set email format to HTML
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    $mail->AltBody = 'Ваш почтовый клиент не принимает сообщений в формате HTML. Вариант рассылки в формате PLAIN TEXT будет реализован позже.';
                    #
                    # Send the message, check for errors
                    #
                    # Открыли файл для записи данных в конец файла
                    #
                    $filename = __DIR_ROOT . __SERVICENAME_MAILNEW . "/logs/PHPMailer_errors.log";
                    if (is_writable($filename)) {

                        if (!$handle = fopen($filename, 'a')) {
                            echo "<span style='color:red; text-align:center'><i>Не могу открыть лог-файл для записи отчета об отправке.</i></span>";
                            exit;
                        }

                        if (!$mail->send()) {
                            $err  = $mail->ErrorInfo . PHP_EOL;
                            $text = date('Y-m-d h:i:s') . " : ошибка рассылки на ( $email_to ) : " . $err;
                            // Записываем $somecontent в наш открытый файл.
                            if (fwrite($handle, $text) === false) {
                                echo "<span style='color:red; text-align:center'><i>Не могу произвести запись в лог файл.</i></span>";
                                exit;
                            }
                            echo "<span style='color:red; text-align:center'><i>Ошибка при отправке сообщения : $err.</i></span>";
                            fclose($handle);
                        } else {
                            $text = date('Y-m-d h:i:s') . " : сообщение на ( $email_to ) успешно отправлено" . PHP_EOL;
                            // Записываем $somecontent в наш открытый файл.
                            if (fwrite($handle, $text) === false) {
                                echo "<span style='color:red; text-align:center'><i>Не могу произвести запись в лог-файл.</i></span>";
                                exit;
                            }
                            echo "<span style='color:green; text-align:center'><i>Сообщение успешно отправлено. Запись в лог-файл произведена.</i></span>";
                            fclose($handle);
                            //
                            // Записываем в БД в таблицу ".__MAIL_OUTGOING_PREFIX.", кто поставил галочку "Отправка на email" в окне редактирования и когда
                            $db->update(
                                __MAIL_OUTGOING_TABLENAME,
                                array(
                                    'toSendEmail' => "1",
                                ),
                                array(
                                    'id' => $id,
                                )
                            );
                            //
                            $timestamp = date('Y-m-d H:i:s');
                            //
                            // Проверочная запись сформированного сообщения
                            // $db->insert('mailbox_test_EmailSending', array(
                            //     'email_when'      => $timestamp,
                            //     'email_header'    => "",
                            //     'email_name'      => $__FIO,
                            //     'email_address'   => $email_to,
                            //     'email_subject'   => $subject,
                            //     'email_text'      => $message,
                            //     'email_whoSendID' => $_SESSION['id'],
                            //     'email_docID'     => $__docID,
                            //     'email_docRow'    => $id,
                            // ));
                            //
                            $msgMail = "Отправлено email-уведомление на <span class='text-success'>" . $email_to . "</span> о назначении ответственного ( <span class='text-success'>" . $__FIO . "</span> )";
                            $db->insert(__MAIL_OUTGOING_PREFIX . '_logChanges', array(
                                'koddel'        => "",
                                'recordType'    => "0",
                                'recordNum'     => "1",
                                'recordID'      => null,
                                'timestamp'     => $timestamp,
                                'action'        => "MAIL_NOTIFY",
                                'koddocmail'    => $__koddocmail,
                                'userid'        => $_SESSION['id'],
                                'kodispol'      => $__contrKOD,
                                'oldSettings'   => null,
                                'newSettings'   => null,
                                'changes'       => $__contrKOD,
                                'changesOldVal' => null,
                                'changesNewVal' => null,
                                'changesTitle'  => "Отправлено email-уведомление",
                                'changesStr'    => $msgMail,
                                'changesText'   => $msgMail,
                                'changesCount'  => null,
                            ));
                        }
                    } else {
                        echo "<span style='color:red; text-align:center'><i>Лог-файл недоступен для записи.</i></span>";
                    }
                }
            }
        }
    }
}
#
#
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
# ФУНКЦИЯ: ОТПРАВКА EMAIL ИСПОЛНИТЕЛЯ ДОКУМЕНТА С ФИКСАЦИЕЙ ВРЕМЕННОЙ МЕТКИ И ОТПРАВИВШЕГО
# ---
# inbox_docContractorID - ID ответственного из ".__MAIL_OUTGOING_PREFIX."
# email - email из users, где id = inbox_docContractorID
# inbox_emailSentByID - ID отправившего
# inbox_emailSentWhen - временная метка отправки
#
#
#
function sendemailtoispolFullcheckout($db, $action, $id, $values, $row) {

    $_sqlReq_1 = $db->sql("SELECT * FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id=" . $id)->fetchAll();

    $__koddocmail      = $_sqlReq_1[0]['koddocmail'];
    $__docID           = $_sqlReq_1[0]['outbox_docID'];
    $__docFileID       = $_sqlReq_1[0]['outbox_docFileID'];
    $__docFileIDadd    = $_sqlReq_1[0]['outbox_docFileIDadd'];
    $__contrID         = $_sqlReq_1[0]['outbox_docContractorID'];
    $__contrKOD        = $_sqlReq_1[0]['outbox_docContractor_kodispolout'];
    $__deadline        = date_create($row[__MAIL_OUTGOING_TABLENAME]['outbox_docDateDeadline']);
    $__ispolMULTI      = $_sqlReq_1[0]['outbox_docContractorMULTI'];
    $__recipientORG    = $_sqlReq_1[0]['outbox_docRecipient'];
    $__recipientNAME   = $_sqlReq_1[0]['outbox_docRecipientName'];
    $__sourceID        = $_sqlReq_1[0]['outbox_docSourceID'];
    $__sourceDate      = date_create($row[__MAIL_OUTGOING_TABLENAME]['outbox_docSourceDate']);
    $__docABOUT        = $_sqlReq_1[0]['outbox_docAbout'];
    $__docTYPE         = $_sqlReq_1[0]['outbox_docTypeSTR'];
    $__toSendEmail     = $_sqlReq_1[0]['toSendEmail'];
    $__controlActive   = $_sqlReq_1[0]['outbox_controlIspolActive'];
    $__controlCheckout = $_sqlReq_1[0]['outbox_controlIspolCheckout'];
    $__controlStatus   = $_sqlReq_1[0]['outbox_controlIspolStatus'];

    $__fileAddLinks = "---";
    //
    // ОТЛАДКА >>>>>
    $msgMain  = $__koddocmail;
    $msgText1 = "__contrID: " . $__contrID;
    $msgText2 = "outbox_docContractor_kodispoloutс: " . $__contrKOD;
    $msgText3 = "outbox_controlIspolMailNotifyCheckout: " . $__controlCheckout;
    $msgText4 = "";
    $msgText5 = "";
    outputTempTest("sendEmailToIspol_FullCheckout", $msgMain, $msgText1, $msgText2, $msgText3, $msgText4, $msgText5);
    // <<<<< ОТЛАДКА
    //
    if ("1" == $__controlCheckout && ("1" == $_sqlReq_1[0]['outbox_controlIspolMailNotifyCheckout'] or "1" == $values[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolMailNotifyCheckout'])) {
        $arrispol     = explode(",", $__contrKOD);
        $arrFileIDAdd = explode(",", $__docFileIDadd);
        foreach ($arrispol as $value) {
            $__contractorDATA1 = $db->sql("SELECT id, emailaddress, namezayvfio FROM mailbox_sp_users WHERE kodispolout=" . $value)->fetchAll();
            $__contractorDATA2 = $db->sql("SELECT firstname, middlename, lastname FROM users WHERE id=" . $__contractorDATA1[0]['id'])->fetchAll();
            $__fileName        = "";
            $__fileURL         = "";
            $__fileLink        = "---";

            if ("" != $__docFileID) {
                $__contractorDATA3 = $db->sql("SELECT file_originalname, file_url FROM " . __MAIL_OUTGOING_FILES_TABLENAME . " WHERE id=" . $__docFileID)->fetchAll();
                $__fileName        = (!empty($__contractorDATA3) && "" != $__contractorDATA3[0]['file_originalname']) ? "Основной файл" : "---";
                $__fileURL         = !empty($__contractorDATA3) ? $__contractorDATA3[0]['file_url'] : "";
                $__fileLink        = ("" != $__fileURL) ? '<a href="' . $__fileURL . '" title="">' . $__fileName . '</a>' : "---";
            }
            if ("" != $__docFileIDadd) {
                $__fileAddLinks = "";
                foreach ($arrFileIDAdd as &$value) {
                    if ("" != $value) {
                        $__QRY_FileIDadd = $db_handle->runQuery("SELECT file_originalname, file_url FROM mailbox_outgoing_files WHERE id=" . $value);
                        $__fileAddName   = (!empty($__QRY_FileIDadd) && "" != $__QRY_FileIDadd[0]['file_originalname']) ? $__QRY_FileIDadd[0]['file_originalname'] : "---";
                        $__fileAddURL    = !empty($__QRY_FileIDadd) ? $__QRY_FileIDadd[0]['file_url'] : "";
                        $__fileAddLink   = ("" != $__fileAddURL) ? '<a href="' . $__fileAddURL . '" title="">' . $__fileAddName . '</a>' : "---";
                        $__fileAddLinks .= $__fileAddLink . ", ";
                    }
                }
                $__fileAddLinks = rtrim($__fileAddLinks, ', ');
                $__fileAddLinks = ltrim($__fileAddLinks, ', ');
            }
            if ($__contractorDATA1 && $__contractorDATA2) {
                $__email = $__contractorDATA1[0]['emailaddress'];
                $__IO    = !empty($__contractorDATA2) ? $__contractorDATA2[0]['firstname'] . " " . $__contractorDATA2[0]['middlename'] : '';
                $__FIO   = $__contractorDATA1[0]['namezayvfio'];
                //
                //
                //
                // Записываем в БД в таблицу ".__MAIL_OUTGOING_PREFIX.", кто поставил галочку "Отправка на email" в окне редактирования и когда
                $db->update(__MAIL_OUTGOING_TABLENAME, array(
                    'outbox_emailSentByID'  => $_SESSION['id'],
                    'outbox_emailSentBySTR' => "X" . $_SESSION['lastname'] . " " . mb_substr($_SESSION['firstname'], 0, 1) . ". " . mb_substr($_SESSION['middlename'], 0, 1) . ".",
                    'outbox_emailSentWhen'  => date('Y-m-d H:i:s'),
                ), array(
                    'id' => $id,
                ));
                # ----- ----- ----- ----- -----
                #
                # БЛОК ОТПРАВКИ СООБЩЕНИЯ
                #
                #
                $mail    = new PHPMailer;
                $message = "";
                #
                #
                # SERVER SETTINGS
                #
                #
                # Enable verbose debug output
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                # Disable verbose debug output
                # $mail->SMTPDebug = 0;
                # Send using SMTP
                $mail->isSMTP();
                # Set the SMTP server to send through
                $mail->Host = 'mail.atgs.ru';
                # Enable SMTP authentication
                $mail->SMTPAuth = true;
                # SMTP connection will not close after each email sent, reduces SMTP overhead
                $mail->SMTPKeepAlive = true;
                # SMTP username
                $mail->Username = 'portal@atgs.ru';
                # SMTP password
                $mail->Password = 'iu3Li,quohch';
                # Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                # TCP port
                $mail->Port = 587;
                #
                $mail->setLanguage('ru', __DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/libs_PHPMailer/language/');
                $mail->CharSet = "utf-8";
                #
                # From
                $from_name  = "АТГС.Портал / Корпоративные сервисы";
                $from_email = "portal@atgs.ru";
                $from_name  = "=?utf-8?B?" . base64_encode($from_name) . "?=";
                $mail->setFrom($from_email, $from_name);
                # Mail address
                // $email_to = $__email;
                $email_to    = 'chugunov@atgs.ru';
                $email_admin = 'chugunov@atgs.ru';
                #
                # Тема сообения
                $subjectTxt = "Почта АТГС [Исходящие] : Документ №" . $__docID . " исполнен";
                $subject    = "=?utf-8?B?" . base64_encode($subjectTxt) . "?=";
                #
                $mail->addAddress($email_to);
                $mail->addCC($email_admin);
                $mail->addReplyTo('notreply@atgs.ru', 'Do not reply');
                #
                # Message body
                $_msgTitle    = "Исходящий документ";
                $_msgText     = '<span style="font-size:28px">' . $__IO . ', </span><br>документ №' . $__docID . " исполнен";
                $_docDeadline = validateDate($_sqlReq_1[0]['outbox_docDateDeadline'], "Y-m-d") ? date('d.m.Y', strtotime($_sqlReq_1[0]['outbox_docDateDeadline'])) : "---";
                $_sourceDate  = validateDate($_sqlReq_1[0]['outbox_docSourceDate'], "Y-m-d") ? date('d.m.Y', strtotime($_sqlReq_1[0]['outbox_docSourceDate'])) : "---";

                $message .= makeMailMsg_Notify_FullCheckout('outgoing', $__koddocmail, $_msgTitle, $__docID, $__IO, $__fileLink, $__fileAddLinks, $__docTYPE, $__recipientORG, $__docABOUT, $__sourceID, $_sourceDate, $__controlActive, $__controlStatus, $_docDeadline);
                #
                # Content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = $subject;
                $mail->Body    = $message;
                $mail->AltBody = 'Ваш почтовый клиент не принимает сообщений в формате HTML. Вариант рассылки в формате PLAIN TEXT будет реализован позже.';
                #
                # Send the message, check for errors
                #
                # Открыли файл для записи данных в конец файла
                #
                $filename = __DIR_ROOT . __SERVICENAME_MAILNEW . "/logs/PHPMailer_errors.log";
                if (is_writable($filename)) {

                    if (!$handle = fopen($filename, 'a')) {
                        echo "<span style='color:red; text-align:center'><i>Не могу открыть лог-файл для записи отчета об отправке.</i></span>";
                        exit;
                    }

                    if (!$mail->send()) {
                        $err  = $mail->ErrorInfo . PHP_EOL;
                        $text = date('Y-m-d h:i:s') . " : ошибка рассылки на ( $email_to ) : " . $err;
                        // Записываем $somecontent в наш открытый файл.
                        if (fwrite($handle, $text) === false) {
                            echo "<span style='color:red; text-align:center'><i>Не могу произвести запись в лог файл.</i></span>";
                            exit;
                        }
                        echo "<span style='color:red; text-align:center'><i>Ошибка при отправке сообщения : $err.</i></span>";
                        fclose($handle);
                    } else {
                        $text = date('Y-m-d h:i:s') . " : сообщение на ( $email_to ) успешно отправлено" . PHP_EOL;
                        // Записываем $somecontent в наш открытый файл.
                        if (fwrite($handle, $text) === false) {
                            echo "<span style='color:red; text-align:center'><i>Не могу произвести запись в лог-файл.</i></span>";
                            exit;
                        }
                        echo "<span style='color:green; text-align:center'><i>Сообщение успешно отправлено. Запись в лог-файл произведена.</i></span>";
                        fclose($handle);
                        //
                        // Записываем в БД в таблицу ".__MAIL_OUTGOING_PREFIX.", кто поставил галочку "Отправка на email" в окне редактирования и когда
                        $db->update(
                            __MAIL_OUTGOING_TABLENAME,
                            array(
                                'toSendEmail' => "1",
                            ),
                            array(
                                'id' => $id,
                            )
                        );
                        //
                        $timestamp = date('Y-m-d H:i:s');
                        //
                        // Проверочная запись сформированного сообщения
                        $db->insert('mailbox_test_EmailSending', array(
                            'email_when'      => $timestamp,
                            'email_header'    => "",
                            'email_name'      => $__FIO,
                            'email_address'   => $email_to,
                            'email_subject'   => $subject,
                            'email_text'      => $message,
                            'email_whoSendID' => $_SESSION['id'],
                            'email_docID'     => $__docID,
                            'email_docRow'    => $id,
                        ));
                        //
                        $msgMail = "Отправлено email-уведомление на <span class='text-success'>" . $email_to . "</span> о назначении ответственного ( <span class='text-success'>" . $__FIO . "</span> )";
                        $db->insert(__MAIL_OUTGOING_PREFIX . '_logChanges', array(
                            'koddel'        => "",
                            'recordType'    => "0",
                            'recordNum'     => "1",
                            'recordID'      => null,
                            'timestamp'     => $timestamp,
                            'action'        => "MAIL_NOTIFY",
                            'koddocmail'    => $__koddocmail,
                            'userid'        => $_SESSION['id'],
                            'kodispol'      => $__contrKOD,
                            'oldSettings'   => null,
                            'newSettings'   => null,
                            'changes'       => $__contrKOD,
                            'changesOldVal' => null,
                            'changesNewVal' => null,
                            'changesTitle'  => "Отправлено email-уведомление",
                            'changesStr'    => $msgMail,
                            'changesText'   => $msgMail,
                            'changesCount'  => null,
                        ));
                        $_SQLReq_NotifyCheckout_Update = $db->sql("UPDATE " . __MAIL_OUTGOING_TABLENAME . " SET outbox_controlIspolMailNotifyCheckout = 0 WHERE koddocmail = '{$__koddocmail}'");
                    }
                } else {
                    echo "<span style='color:red; text-align:center'><i>Лог-файл недоступен для записи.</i></span>";
                }
            }
        }
    }
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#####
# ФУНКЦИЯ
# Назначение: обновление общего статуса исполнения документа (".__MAIL_INCOMING_PREFIX.".inbox_controlIspolCheckout)
#####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
#
function updateIspolCheckout($db, $id, $values, $row) {
    // Берем из сессии ID пользователя
    $userid = $_SESSION['id'];
    // Текущая временная метка
    $datenow = date("Y-m-d H:i:s");
    // Разбираем в массив строку с исполнителями напрямую из БД ($row) и подсчитываем количество
    $arrispol = explode(",", $row[__MAIL_OUTGOING_TABLENAME]['outbox_docContractor_kodispolout']);
    $cntIspol = count($arrispol);
    //
    $koddocmail = $row[__MAIL_OUTGOING_TABLENAME]['koddocmail'];
    // SQL: (reqSQL_1) СЧитаем количество отметок об исполнении по текущему документу (koddocmail)
    $_reqSQL_1    = $db->sql("SELECT COUNT(ispolStatus) as ispolCounts FROM " . __MAIL_OUTGOING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND ispolStatus='1'")->fetchAll();
    $cntCheckouts = $_reqSQL_1[0]['ispolCounts'];
    //
    // ОТЛАДКА >>>>>
    $msgMain  = $koddocmail;
    $msgText1 = "cntIspol = " . $cntIspol . " // cntCheckouts = " . $cntCheckouts;
    $msgText2 = "";
    $msgText3 = "";
    $msgText4 = "";
    $msgText5 = "";
    outputTempTest("updateIspolCheckout", $msgMain, $msgText1, $msgText2, $msgText3, $msgText4, $msgText5);
    // <<<<< ОТЛАДКА
    //

    if (($cntIspol == $cntCheckouts) && ("1" != $row[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolCheckout'])) {
        $_reqDB3 = $db->sql("UPDATE " . __MAIL_OUTGOING_TABLENAME . " SET outbox_controlIspolCheckout='1', outbox_controlIspolCheckoutWhen='{$datenow}' WHERE id='{$id}'");

        # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
        # 12.04.2023
        # Статус исполнения документа
        # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
        $ctl     = $row[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolActive'];
        $chkout  = 1;
        $DL      = date('Y-m-d', strtotime($row[__MAIL_OUTGOING_TABLENAME]['outbox_docDateDeadline']));
        $datenow = date('Y-m-d', time());
        $useDL   = $row[__MAIL_OUTGOING_TABLENAME]['outbox_controlIspolUseDeadline'];

        if ('1' == $ctl) {
            if ('1' == $chkout) {
                $checkStatus = 1;
            } else {
                if ($useDL && ($datenow > $DL)) {
                    $checkStatus = 3;
                } else {
                    $checkStatus = 2;
                }
            }
        } else {
            $checkStatus = 0;
        }
        $_sqlReq_1 = $db->sql("UPDATE " . __MAIL_OUTGOING_TABLENAME . " SET outbox_controlIspolStatus='{$checkStatus}' WHERE id='{$id}'");
        # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
        # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
        sendemailtoispolFullcheckout($db, 'Email', $id, $values, $row);
    }
    if ($cntIspol != $cntCheckouts) {
        $_reqDB3 = $db->sql("UPDATE " . __MAIL_OUTGOING_TABLENAME . " SET outbox_controlIspolCheckout='0', outbox_controlIspolCheckoutWhen=NULL WHERE id='{$id}'");
    }
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
function newUserMessage($db, $action, $id, $values) {
    $_QRY = $db->sql("SELECT *  FROM mailbox_outgoing WHERE id=" . $id)->fetchAll();
    if ("changed" == $_SESSION['outbox_checkChanges_is']) {
        if ((!empty($_QRY[0]['outbox_docContractorID']) && "1" != $_QRY[0]['outbox_docContractorID']) && (!empty($_QRY[0]['outbox_docContractor_kodispolout']) && "000000000000000" != $_QRY[0]['outbox_docContractor_kodispolout'])) {
            $msg_maintext    = '';
            $msg_subtext1    = '';
            $msg_subtext2    = '';
            $msg_subtext3    = '';
            $msg_specialtext = '';
            $comment         = '';
            $servicename     = "почта";
            $parent_id       = $_QRY[0]['koddocmail'];
            if ("1" == $_QRY[0]['outbox_docContractorMULTI']) {
                $for_singleuser = "";
                $for_groupuser  = trim($_QRY[0]['outbox_docContractorID']);
                $for_groupuser .= ",1011"; // Пока для проверки
            } else {
                $for_singleuser = trim($_QRY[0]['outbox_docContractorID']);
                $for_singleuser .= ",1011"; // Пока для проверки
                $for_groupuser = "";
            }

            $msg_maintext .= trim($_QRY[0]["outbox_docAbout"]);
            $msg_subtext1 .= 'Документ : ' . trim('№ 1-1/' . $_QRY[0]["outbox_docID"] . ' от ' . date("d.m.Y H:i", strtotime($_QRY[0]["outbox_docDate"])));
            $msg_subtext2 .= 'Контрагент : ' . trim($_QRY[0]["outbox_docRecipient"]);
            $msg_subtext3 .= 'Исполнитель : ' . trim(str_replace(',', ' , ', $_QRY[0]["outbox_docContractorSTR"]));
            //
            $msg_specialtext .= '';
            //
            $msg_link1 = trim('http://' . $_SERVER["HTTP_HOST"] . '/mailnew/index.php?type=out&mode=profile&uid=' . $parent_id);
            $msg_link2 = '';
            //
            $query  = mysqlQuery("SELECT MAX(msg_id) as lastKod FROM portal_push_messages ORDER BY id DESC");
            $row    = mysqli_fetch_assoc($query);
            $newKod = $row['lastKod'];
            $newKod++;

            $msg_id_new = $newKod;
            $msg_title  = ("CRT" == $action) ? "Новый исходящий документ" : "Изменения по исходящему документу";
            $msg_type   = ("CRT" == $action) ? "info" : "warning";
            $msg_active = "1";
            $msg_status = "0";
            $comment .= "CheckChanges is " . $_SESSION['outbox_checkChanges_is'] . "\r\n";
            $comment .= "Data in Form" . "\r\n";
            $comment .= $_SESSION['outbox_checkChanges_dataForm'];
            $comment .= "---\r\n";
            $comment .= "Data in DB" . "\r\n";
            $comment .= $_SESSION['outbox_checkChanges_dataDB'];
            $_QRY_INS = $db->sql("INSERT INTO portal_push_messages (msg_timestamp, msg_id, parent_id, servicename, msg_type, msg_active, msg_status, msg_title, msg_maintext, msg_subtext1, msg_subtext2, msg_subtext3, msg_specialtext, msg_link1, msg_link2, for_singleuser, for_groupuser, comment) VALUES (NOW(), '$msg_id_new', '$parent_id', '$servicename', '$msg_type', '$msg_active', '$msg_status', '$msg_title', '$msg_maintext', '$msg_subtext1', '$msg_subtext2', '$msg_subtext3', '$msg_specialtext', '$msg_link1', '$msg_link2', '$for_singleuser', '$for_groupuser', '$comment')");
        }
    }
}
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function dateDiff($date_earlier, $date_later, $output_format) {
    // $date_later - поздняя дата (ГГГГ-ММ-ДД)
    // $date_earlier - ранняя дата (ГГГГ-ММ-ДД)
    // $output_format - в чем выводить разницу (years, month, days, hours, mins, secs)

    // Declare and define two dates
    $date1 = strtotime($date_earlier . " 10:00:00");
    $date2 = strtotime($date_later . " 10:00:00");

    // Formulate the Difference between two dates
    $diff = abs($date2 - $date1);

    // To get the year divide the resultant date into
    // total seconds in a year (365*60*60*24)
    $years = floor($diff / (365 * 60 * 60 * 24));

    // To get the month, subtract it with years and
    // divide the resultant date into
    // total seconds in a month (30*60*60*24)
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));

    // To get the day, subtract it with years and
    // months and divide the resultant date into
    // total seconds in a days (60*60*24)
    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

    // To get the hour, subtract it with years,
    // months & seconds and divide the resultant
    // date into total seconds in a hours (60*60)
    $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));

    // To get the minutes, subtract it with years,
    // months, seconds and hours and divide the
    // resultant date into total seconds i.e. 60
    $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);

    // To get the minutes, subtract it with years,
    // months, seconds, hours and minutes
    $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));

    switch ($output_format) {
        case 'years':
            $output = $years;
            break;
        case 'months':
            $output = $months;
            break;
        case 'days':
            $output = $days;
            break;
        case 'hours':
            $output = $hours;
            break;
        case 'mins':
            $output = $minutes;
            break;
        case 'secs':
            $output = $seconds;
            break;
        default:
            $output = $seconds;
    }
    return $output;
}

#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
function statsMailOutgoing($db, $action, $year) {
    $_QRY_docsTotal = $db->sql("SELECT COUNT(*) AS docsTotal FROM mailbox_outgoing WHERE YEAR(outbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $docsTotal      = $_QRY_docsTotal[0]['docsTotal'];

    $_QRY_docsNoispol = $db->sql("SELECT COUNT(*) AS docsNoispol FROM mailbox_outgoing WHERE (outbox_docContractorID='1' OR outbox_docContractorID IS NULL OR outbox_docContractorID='')  AND YEAR(outbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $docsNoispol      = $_QRY_docsNoispol[0]['docsNoispol'];

    $_QRY_docsNoattach = $db->sql("SELECT COUNT(*) AS docsNoattach FROM mailbox_outgoing WHERE (outbox_docFileID='' OR outbox_docFileID IS NULL) AND (outbox_docFileIDadd='' OR outbox_docFileIDadd IS NULL) AND YEAR(outbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $docsNoattach      = $_QRY_docsNoattach[0]['docsNoattach'];

    // $_QRY_ctrlOn = $db->sql("SELECT COUNT(*) AS ctrlOn FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND outbox_controlIspolStatus NOT IN (0,1) AND YEAR(outbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $_QRY_ctrlOn = $db->sql("SELECT COUNT(*) AS ctrlOn FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND YEAR(outbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $ctrlOn      = $_QRY_ctrlOn[0]['ctrlOn'];

    $_QRY_ctrlNotexec = $db->sql("SELECT COUNT(*) AS ctrlNotexec FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND outbox_controlIspolStatus IN (2,3) AND outbox_controlIspolCheckout!='1' AND YEAR(outbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $ctrlNotexec      = $_QRY_ctrlNotexec[0]['ctrlNotexec'];

    $_QRY_ctrlDLon = $db->sql("SELECT COUNT(*) AS ctrlDLon FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND outbox_controlIspolUseDeadline='1' AND TIMESTAMPDIFF(HOUR, NOW(), outbox_docDateDeadline) >= 72 AND outbox_controlIspolStatus IN (2,3) AND outbox_controlIspolCheckout!='1' AND YEAR(outbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $ctrlDLon      = $_QRY_ctrlDLon[0]['ctrlDLon'];

    $_QRY_ctrlDL3days = $db->sql("SELECT COUNT(*) AS ctrlDL3days FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND outbox_controlIspolUseDeadline='1' AND TIMESTAMPDIFF (HOUR, NOW(), outbox_docDateDeadline) >= 24 AND TIMESTAMPDIFF (HOUR, NOW(), outbox_docDateDeadline) < 72 AND outbox_controlIspolStatus IN (2,3) AND outbox_controlIspolCheckout!='1' AND YEAR(outbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $ctrlDL3days      = $_QRY_ctrlDL3days[0]['ctrlDL3days'];

    $_QRY_ctrlDL1day = $db->sql("SELECT COUNT(*) AS ctrlDL1day FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND outbox_controlIspolUseDeadline='1' AND TIMESTAMPDIFF(HOUR, NOW(), outbox_docDateDeadline) < 24 AND TIMESTAMPDIFF(HOUR, NOW(), outbox_docDateDeadline) >= 0 AND outbox_controlIspolStatus IN (2,3) AND outbox_controlIspolCheckout!='1' AND YEAR(outbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $ctrlDL1day      = $_QRY_ctrlDL1day[0]['ctrlDL1day'];

    $_QRY_ctrlDLexpired = $db->sql("SELECT COUNT(*) AS ctrlDLexpired FROM mailbox_outgoing WHERE outbox_controlIspolActive='1' AND outbox_controlIspolUseDeadline='1' AND TIMESTAMPDIFF (HOUR, NOW(), outbox_docDateDeadline) < 0 AND outbox_controlIspolStatus IN (2,3) AND outbox_controlIspolCheckout!='1' AND YEAR(outbox_docDate)='{$year}' AND koddel!='99'")->fetchAll();
    $ctrlDLexpired      = $_QRY_ctrlDLexpired[0]['ctrlDLexpired'];

    $_QRY_UpdateStats = $db->sql("UPDATE mailbox_outgoing_stats SET docs_total='{$docsTotal}', docs_noispol='{$docsNoispol}', docs_noattach='{$docsNoattach}', control_on='{$ctrlOn}', control_notexec='{$ctrlNotexec}', control_DLon='{$ctrlDLon}', control_DL3days='{$ctrlDL3days}', control_DL1day='{$ctrlDL1day}', control_DLexpired='{$ctrlDLexpired}' WHERE statYear='{$year}'");
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
#
function syncLinkFields($db, $action, $id, $values, $row) {
    if ('UPD' == $action || 'CRT' == $action) {
        $_koddocmail              = $row[__MAIL_OUTGOING_TABLENAME]['koddocmail'];
        $_incomingLinkRowIDsArray = explode(',', $row[__MAIL_OUTGOING_TABLENAME]['inbox_rowIDs_links']);
        $_outgoingLinkRowIDsArray = explode(',', $row[__MAIL_OUTGOING_TABLENAME]['outbox_rowIDs_links']);
        // Синхронизируем дополнительные связи с входящими документами Почты
        if (!empty($_incomingLinkRowIDsArray)) {
            foreach ($_incomingLinkRowIDsArray as $value) {
                $reqLinkedIncoming = $db->sql("SELECT outbox_rowIDs_links FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id='{$value}'")->fetchAll();
                if ($reqLinkedIncoming) {
                    $linkIncoming = $reqLinkedIncoming[0]['outbox_rowIDs_links'];
                    if (!empty($linkIncoming)) {
                        if (!strpos($linkIncoming, $id)) {
                            $linkIncoming .= "," . $id;
                            // Записываем в БД в таблицу ".__MAIL_OUTGOING_PREFIX.", кто поставил галочку "Отправка на email" в окне редактирования и когда
                            $db->update(__MAIL_INCOMING_TABLENAME, array('outbox_rowIDs_links' => $linkIncoming), array('id' => $value));
                        } else {
                            // $query = mysqlQuery("SELECT * FROM " . __MAIL_INCOMING_TABLENAME . " WHERE outbox_rowIDs_links != '' AND outbox_rowIDs_links IS NOT NULL AND");
                        }
                    } else {
                        $linkIncoming .= $id;
                    }
                }
            }
        }
        // Синхронизируем дополнительные связи с исходящими документами Почты
        if (!empty($_outgoingLinkRowIDsArray)) {
            foreach ($_outgoingLinkRowIDsArray as $value) {
                $reqLinkedOutgoing = $db->sql("SELECT outbox_rowIDs_links FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id='{$value}'")->fetchAll();
                if ($reqLinkedOutgoing) {
                    $linkOutgoing = $reqLinkedOutgoing[0]['outbox_rowIDs_links'];
                    if (!empty($linkOutgoing)) {
                        if (!strpos($linkOutgoing, $id)) {
                            $linkOutgoing .= "," . $id;
                            // Записываем в БД в таблицу ".__MAIL_OUTGOING_PREFIX.", кто поставил галочку "Отправка на email" в окне редактирования и когда
                            $db->update(__MAIL_OUTGOING_TABLENAME, array('outbox_rowIDs_links' => $linkOutgoing), array('id' => $value));
                        }
                    } else {
                        $linkOutgoing .= $id;
                    }
                }
            }
        }
    }
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#
function syncRelFields($db, $action, $id, $values, $row) {
    if ('UPD' == $action || 'CRT' == $action) {
        $sourceRowIDRelV = $values[__MAIL_OUTGOING_TABLENAME]['inbox_rowID_rel'];
        $sourceRowIDRel = $row[__MAIL_OUTGOING_TABLENAME]['inbox_rowID_rel'];
        $sourceRowIDaddRel = $row[__MAIL_OUTGOING_TABLENAME]['inbox_rowIDadd_rel'];

        if (empty($sourceRowIDRelV)) {
            $reqRel = $db->sql("SELECT outbox_rowID_rel, outbox_docID_rel, outbox_koddocmail_rel, outbox_rowIDadd_rel FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id='{$sourceRowIDRel}'")->fetchAll();
            $relativeRowIDRel = $reqRel[0]['outbox_rowID_rel'];
            $relativeDocIDRel = $reqRel[0]['outbox_docID_rel'];
            $relativeKodRel = $reqRel[0]['outbox_koddocmail_rel'];
            $relativeRowIDaddRel = $reqRel[0]['outbox_rowIDadd_rel'];
            $relativeRowIDaddRelArray = explode(',', $relativeRowIDaddRel);

            $replaceStr = str_replace($relativeRowIDaddRelArray[0], '', $relativeRowIDaddRel);
            $replaceStr = str_replace(',,', '', $replaceStr);
            $replaceStr = rtrim($replaceStr, ',');
            $replaceStr = ltrim($replaceStr, ',');

            $selector1 = $relativeRowIDaddRelArray[0];
            $reqRel1 = $db->sql("SELECT koddocmail, outbox_docID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE id='{$selector1}'")->fetchAll();

            $db->update(__MAIL_INCOMING_TABLENAME, array(
                'outbox_rowID_rel'          => $relativeRowIDaddRelArray[0],
                'outbox_docID_rel'          => $reqRel1[0]['outbox_docID'],
                'outbox_koddocmail_rel'     => $reqRel1[0]['koddocmail'],
                'outbox_rowIDadd_rel'       => $replaceStr,
            ), array(
                'id' => $sourceRowIDRel,
            ));

            $db->insert('mailbox_testTable', array(
                'id_1'    => $selector1,
                'date'    => date("Y-m-d H:i:s"),
                'field_1' => 'syncRelFields 1',
                'field_2' => 'outbox_rowID_rel > ' . $relativeRowIDaddRelArray[0],
                'field_3' => 'outbox_docID_rel > ' . $reqRel1[0]['outbox_docID'],
                'field_4' => 'outbox_koddocmail_rel > ' . $reqRel1[0]['koddocmail'],
                'field_5' => 'outbox_rowIDadd_rel > ' . $replaceStr,
            ));
        }
    }
}
#
#
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
######
###### UPD 25.07.23
###### Отмечаем (при выбрпанном соответствующем чекбоксе)
###### выполненным документ в противоположной почте, на которую редактируемый документ является ответным
######
######
function updateRelCheckout($db, $action, $id, $values, $row) {
    if ($values['set_inbox_fullCheckout']) {
        if ("CRT" == $action || "UPD" == $action) {
            $relRowid = $row[__MAIL_OUTGOING_TABLENAME]['inbox_rowID_rel'];
            $req1     = $db->sql("SELECT koddocmail FROM " . __MAIL_INCOMING_TABLENAME . " WHERE id='{$relRowid}'")->fetchAll();
            if (!empty($req1)) {
                $db->update(
                    __MAIL_INCOMING_TABLENAME . "_logCheckouts",
                    array(
                        'ispolStatus' => "1",
                    ),
                    array(
                        'koddocmail' => $req1[0]['koddocmail'],
                    )
                );
                //
                $db->update(
                    __MAIL_INCOMING_TABLENAME,
                    array(
                        'inbox_controlIspolStatus'       => "1",
                        'inbox_controlIspolCheckout'     => "1",
                        'inbox_controlIspolCheckoutWhen' => date("Y-m-d H:i:s"),
                    ),
                    array(
                        'koddocmail' => $req1[0]['koddocmail'],
                    )
                );
                # ----- ----- ----- ----- -----
                #
                // Формируем список ID пользователей исполнивших документ
                if ("" != $req1[0]['koddocmail']) {
                    $koddocmail         = $req1[0]['koddocmail'];
                    $_checkoutUserIDs   = "";
                    $_checkoutUserDates = "";
                    $_QRY_CheckoutIDs   = $db->sql("SELECT userid, timestamp FROM " . __MAIL_INCOMING_PREFIX . "_logCheckouts WHERE koddocmail='{$koddocmail}' AND ispolStatus='1'")->fetchAll();
                    foreach ($_QRY_CheckoutIDs as $key => $value) {
                        $_checkoutUserIDs .= $_QRY_CheckoutIDs[$key]['userid'] . ",";
                        $_checkoutUserDates .= $_QRY_CheckoutIDs[$key]['timestamp'] . ",";
                    }
                    $_checkoutUserIDs   = rtrim($_checkoutUserIDs, ",");
                    $_checkoutUserDates = rtrim($_checkoutUserDates, ",");
                    $_checkoutUserIDs   = ltrim($_checkoutUserIDs, ",");
                    $_checkoutUserDates = ltrim($_checkoutUserDates, ",");
                    $db->update(__MAIL_INCOMING_TABLENAME, array(
                        'inbox_controlIspolCheckoutID'    => $_checkoutUserIDs,
                        'inbox_controlIspolCheckoutDates' => $_checkoutUserDates,
                    ), array(
                        'koddocmail' => $koddocmail,
                    ));
                }
            }
        }
    }
}
######
######
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
#
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
/*
 * Example PHP implementation used for the index.html example
 */

// DataTables PHP library
require __DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/libs/Datatables/Editor-PHP-1.9.7/lib/DataTables.php';

// Alias Editor classes so they are easy to use
use DataTables\Editor\Options;
use DataTables\Editor\Upload;
use DataTables\Editor\Validate;
use DataTables\Editor\ValidateOptions;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
// Build our Editor instance and process the data coming from _POST
Editor::inst($db, __MAIL_OUTGOING_TABLENAME)
    ->fields(
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.ID'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.koddocmail'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docType')
            ->options(
                Options::inst()
                    ->table('mailbox_sp_doctypes_outgoing')
                    ->value('type_id')
                    ->label(array('status', 'type_id', 'type_name_full', 'type_name_short'))
                    ->order('type_id asc')
                    ->render(function ($row) {
                        return $row['type_name_full'];
                    })
                    ->where(function ($q) {
                        $q->where('status', '1', '=');
                    })
            )
            ->validator(Validate::notEmpty(ValidateOptions::inst()
                ->message('Тип письма обязателен'))),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docType_lock'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docType_prev'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docTypeSTR'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docID'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_UID'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docIDSTR'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_rowIDs_links')
            ->setFormatter(Format::ifEmpty(NULL)),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docIDs_links')
            ->options(
                Options::inst()
                    ->table(__MAIL_OUTGOING_TABLENAME)
                    ->value('id')
                    ->label(array('id', 'outbox_docID', 'outbox_docRecipient', 'outbox_docDate', 'outbox_docAbout'))
                    ->render(function ($row) {
                        $date    = date_create($row['outbox_docDate']);
                        $docDate = date_format($date, "d.m.Y");
                        $tmp     = $docDate . ' / №' . $row['outbox_docID'] . ' / ' . $row['outbox_docRecipient'] . ' / ' . $row['outbox_docAbout'];
                        $output  = (mb_strlen($tmp, 'UTF-8') > 160) ? mb_substr($tmp, 0, 120, 'UTF-8') . " ..." : $tmp;
                        return $output;
                    })
                    ->where(function ($q) use ($__startDate, $__endDate) {
                        $q->where('outbox_docDate', '( SELECT outbox_docDate FROM ' . __MAIL_OUTGOING_TABLENAME . ' WHERE outbox_docDate >= ' . $__startDate . ' AND outbox_docDate <= ' . $__endDate . ' )', 'IN', false);
                    })
                    ->order('outbox_docDate DESC')
            )
            ->validator(Validate::dbValues())
            ->setFormatter(Format::ifEmpty(NULL)),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.dognet_rowIDs_links')
            ->setFormatter(Format::ifEmpty(NULL)),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.dognet_docIDs_links')
            ->options(
                Options::inst()
                    ->table('dognet_docbase')
                    ->value('id')
                    ->label(array('id', 'koddel', 'docnumber', 'docnameshot'))
                    ->render(function ($row) {
                        $tmp    = '3-4/' . $row['docnumber'] . ' / ' . $row['docnameshot'];
                        $output = (mb_strlen($tmp, 'UTF-8') > 160) ? mb_substr($tmp, 0, 120, 'UTF-8') . " ..." : $tmp;
                        return $output;
                    })
                    ->where(function ($q) {
                        $q->where('koddel', '99', '!=');
                    })
                    ->order('docNumber DESC')
            )
            ->validator(Validate::dbValues())
            ->setFormatter(Format::ifEmpty(NULL)),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.sp_rowIDs_links')
            ->setFormatter(Format::ifEmpty(NULL)),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.sp_docIDs_links')
            ->options(
                Options::inst()
                    ->table('sp_contragents')
                    ->value('id')
                    ->label(array('id', 'koddel', 'namefull', 'nameshort'))
                    ->render(function ($row) {
                        $tmp    = $row['namefull'];
                        $output = (mb_strlen($tmp, 'UTF-8') > 160) ? mb_substr($tmp, 0, 120, 'UTF-8') . " ..." : $tmp;
                        return $output;
                    })
                    ->where(function ($q) {
                        $q->where('koddel', '99', '!=');
                        $q->where('useinmail', '1', '=');
                    })
                    ->order('nameshort ASC')
            )
            ->validator(Validate::dbValues())
            ->setFormatter(Format::ifEmpty(NULL)),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.inbox_rowIDs_links')
            ->setFormatter(Format::ifEmpty(NULL)),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.inbox_docIDs_links')
            ->options(
                Options::inst()
                    ->table(__MAIL_INCOMING_TABLENAME)
                    ->value('id')
                    ->label(array('id', 'inbox_docID', 'inbox_docSender', 'inbox_docDate', 'inbox_docAbout'))
                    ->render(function ($row) {
                        $date    = date_create($row['inbox_docDate']);
                        $docDate = date_format($date, "d.m.Y");
                        $tmp     = $docDate . ' / №' . $row['inbox_docID'] . ' / ' . $row['inbox_docSender'] . ' / ' . $row['inbox_docAbout'];
                        $output  = (mb_strlen($tmp, 'UTF-8') > 160) ? mb_substr($tmp, 0, 120, 'UTF-8') . " ..." : $tmp;
                        return $output;
                    })
                    ->where(function ($q) use ($__startDate, $__endDate) {
                        $q->where('inbox_docDate', '( SELECT inbox_docDate FROM ' . __MAIL_INCOMING_TABLENAME . ' WHERE inbox_docDate >= ' . $__startDate . ' AND inbox_docDate <= ' . $__endDate . ' )', 'IN', false);
                    })
                    ->order('inbox_docDate DESC')
            )
            ->validator(Validate::dbValues())
            ->setFormatter(Format::ifEmpty(NULL)),
        //
        //
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        //
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.inbox_rowIDadd_rel')
            ->setFormatter(Format::ifEmpty(NULL)),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.inbox_rowIDList_rel')
            ->options(
                Options::inst()
                    ->table(__MAIL_INCOMING_TABLENAME)
                    ->value('id')
                    ->label(array('id', 'inbox_docID', 'inbox_docSender', 'inbox_docDate', 'inbox_docAbout'))
                    ->render(function ($row) {
                        $date    = date_create($row['inbox_docDate']);
                        $docDate = date_format($date, "d.m.Y");
                        return $docDate . ' / №' . $row['inbox_docID'] . ' / ' . $row['inbox_docSender'] . ' / ' . $row['inbox_docAbout'];
                    })
                    ->where(function ($q) use ($__startDate, $__endDate) {
                        $q->where('inbox_docDate', '( SELECT inbox_docDate FROM ' . __MAIL_INCOMING_TABLENAME . ' WHERE inbox_docDate >= ' . $__startDate . ' AND inbox_docDate <= ' . $__endDate . ' AND inbox_docType <> "2" )', 'IN', false);
                    })
                    ->order('inbox_docDate DESC')
            )
            ->validator(Validate::dbValues())
            ->setFormatter(Format::ifEmpty(NULL)),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.inbox_rowID_rel')
            ->options(
                Options::inst()
                    ->table(__MAIL_INCOMING_TABLENAME)
                    ->value('id')
                    ->label(array('id', 'inbox_docID', 'inbox_docSender', 'inbox_docDate', 'inbox_docAbout', 'inbox_docSourceID'))
                    ->render(function ($row) {
                        $date     = date_create($row['inbox_docDate']);
                        $docDate  = date_format($date, "d.m.Y");
                        $sourceID = !empty($row['inbox_docSourceID']) ? $row['inbox_docSourceID'] : "---";
                        return $docDate . ' / № (АТГС) ' . $row['inbox_docID'] . ' / № (Орг) ' . $sourceID . ' / ' . $row['inbox_docSender'] . ' / ' . $row['inbox_docAbout'];
                    })
                    ->where(function ($q) use ($__startDate, $__endDate) {
                        $q->where('inbox_docDate', '( SELECT inbox_docDate FROM ' . __MAIL_INCOMING_TABLENAME . ' WHERE inbox_docDate >= ' . $__startDate . ' AND inbox_docDate <= ' . $__endDate . ' AND inbox_docType <> "2")', 'IN', false);
                    })
                    ->order('inbox_docDate DESC')
            )
            ->validator(Validate::dbValues())
            ->setFormatter(Format::ifEmpty(NULL)),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.inbox_docID_rel'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.inbox_koddocmail_rel'),
        //
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        //
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docDate')
            ->validator(Validate::dateFormat(
                'd.m.Y H:i:s',
                ValidateOptions::inst()
                    ->allowEmpty(false)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d H:i:s',
                'd.m.Y H:i:s'
            ))
            ->setFormatter(Format::datetime(
                'd.m.Y H:i:s',
                'Y-m-d H:i:s'
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docAbout')
            ->validator(Validate::notEmpty(
                ValidateOptions::inst()
                    ->message('Краткое описание обязательно')
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docRecipient_kodzakaz')
            ->options(
                Options::inst()
                    ->table('sp_contragents')
                    ->value('kodcontragent')
                    ->label(array('kodcontragent', 'namefull', 'nameshort', 'zakfio'))
                    ->render(function ($row) {
                        return ("" !== $row['namefull']) ? $row['namefull'] . " (полное название)" : ("" !== $row['nameshort']) ? $row['nameshort'] . " (краткое название)" : "---";
                    })
                    ->where(function ($q) {
                        $q->where('koddel', '99', '!=');
                    })
            )
            ->validator(Validate::notEmpty(
                ValidateOptions::inst()
                    ->message('Получатель письма обязателен')
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docRecipient'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docRecipientName'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docSender_kodzayvtel')
            ->options(
                Options::inst()
                    ->table('mailbox_sp_users')
                    ->value('kodzayvtel')
                    ->label(array('status_zayvtel', 'kodzayvtel', 'namezayvfio'))
                    ->render(function ($row) {
                        return ($row['namezayvfio']);
                    })
                    ->where(function ($q) {
                        $q->where('status_zayvtel', '1', '=');
                    })
            )
            ->validator(Validate::notEmpty(
                ValidateOptions::inst()
                    ->message('Отправитель письма обязателен')
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docSenderID'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docSenderSTR'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docSourceID'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docSourceDate')
            ->validator(Validate::dateFormat(
                'd.m.Y',
                ValidateOptions::inst()
                    ->allowEmpty(true)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d',
                'd.m.Y'
            ))
            ->setFormatter(Format::datetime(
                'd.m.Y',
                'Y-m-d'
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docFileID')
            ->setFormatter(Format::ifEmpty(null))
            ->upload(
                Upload::inst(
                    function ($file, $id) use ($varFileArray, $db) {
                        $__pref    = date('Y') . $_SESSION['id'] . date('mdHis');
                        $__name    = $__pref . "-" . $file['name'];
                        $__nameTmp = $file['tmp_name'];
                        $__ext     = explode('.', $__name);
                        $__ext     = strtolower(end($__ext));

                        $md5       = md5(uniqid());
                        $__nameMD5 = "{$md5}.{$__ext}";

                        // >>> 
                        // Редакция от 09.01.24
                        // Пытаемся сохранить оригинальное название файла, заменив пробелы на _ и удалив спецсимволы
                        $newpattern = "[^a-zа-яё0-9,-_\s\(\)\[\]\.]";
                        $file_newOriginalName = mb_eregi_replace($newpattern, '', $file['name']);
                        $file_newOriginalName = mb_ereg_replace('[\s]', '_', $file_newOriginalName);
                        // $__url = str_replace($_SERVER['DOCUMENT_ROOT'], 'http://' . $_SERVER['HTTP_HOST'], $varFileArray['syspath'] . $__name);
                        $__url = str_replace($_SERVER['DOCUMENT_ROOT'], 'http://' . $_SERVER['HTTP_HOST'], $varFileArray['syspath'] . $__pref . "-" . $file_newOriginalName);
                        move_uploaded_file($__nameTmp, $varFileArray['docpath'] . "{$__name}");
                        // symlink($varFileArray['docpath'] . "{$__name}", $varFileArray['syspath'] . $__name);
                        symlink($varFileArray['docpath'] . "{$__name}", $varFileArray['syspath'] . $__pref . "-" . $file_newOriginalName);
                        // <<<

                        $db->update(
                            __MAIL_OUTGOING_FILES_TABLENAME, // Database table to update
                            [
                                'mainfile'          => '1',
                                'flag'              => 'PREUPL',
                                'file_year'         => $varFileArray['year'],
                                'file_id'           => '',
                                'file_name'         => $__name,
                                'file_originalname' => $file['name'],
                                'file_symname'      => $__pref . "-" . $file_newOriginalName,
                                // Правка от 05/06/2019
                                //                             'file_truelocation'    =>    $varFileArray['docpath']."{$__name}.{$__ext}",
                                'file_truelocation' => $varFileArray['docpath'] . "{$__name}",
                                // ---
                                'file_syspath'      => $varFileArray['syspath'] . $__pref . "-" . $file_newOriginalName,
                                'file_webpath'      => $varFileArray['webpath'] . $__pref . "-" . $file_newOriginalName,
                                'file_url'          => $__url,
                            ],
                            ['id' => $id]
                        );
                        return $id;
                    }
                )
                    ->db(
                        __MAIL_OUTGOING_FILES_TABLENAME,
                        'id',
                        array(
                            'file_extension'    => Upload::DB_EXTN,
                            'file_size'         => Upload::DB_FILE_SIZE,
                            'file_webpath'      => '',
                            'file_truelocation' => '',
                            'file_originalname' => '',
                            'koddocmail'        => '',
                        )
                    )
                    ->validator(Validate::fileSize(35000000, 'Размер документа не должен превышать 35МБ'))
                    ->validator(Validate::fileExtensions(array('png', 'jpg', 'pdf'), "Загрузите документ"))
            ),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docFileIDadd'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.inbox_fileID_rel')
            ->options(
                Options::inst()
                    ->table(__MAIL_INCOMING_FILES_TABLENAME)
                    ->value('id')
                    ->label(array('file_webpath', 'file_name'))
            ),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_FILES_TABLENAME . '.file_webpath'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_FILES_TABLENAME . '.file_url'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_FILES_TABLENAME . '.file_name'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docContractor_kodispolout')
            ->options(
                Options::inst()
                    ->table('mailbox_sp_users')
                    ->value('kodispolout')
                    ->label(array('status_ispolout', 'kodispolout', 'namezayvfio'))
                    ->render(function ($row) {
                        return ($row['namezayvfio']);
                    })
                    ->where(function ($q) {
                        $q->where('status_ispolout', '1', '=');
                    })
            )
            ->validator(Validate::notEmpty(
                ValidateOptions::inst()
                    ->message('Определите исполнителя или выберите "---"')
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docContractorID'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docContractorDEPT'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docContractorSTR'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docContractorEMAIL'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docDateDeadline')
            ->validator(Validate::dateFormat(
                'd.m.Y',
                ValidateOptions::inst()
                    ->allowEmpty(true)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d',
                'd.m.Y'
            ))
            ->setFormatter(Format::datetime(
                'd.m.Y',
                'Y-m-d'
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docContractorComment'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docComment'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docCreatedByID')->set(Field::SET_CREATE),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docCreatedBySTR')->set(Field::SET_CREATE),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docCreatedWhen')
            ->set(Field::SET_CREATE)
            ->validator(Validate::dateFormat(
                'd.m.Y H:i:s',
                ValidateOptions::inst()
                    ->allowEmpty(false)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d H:i:s',
                'd.m.Y H:i:s'
            ))
            ->setFormatter(Format::datetime(
                'd.m.Y H:i:s',
                'Y-m-d H:i:s'
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docUpdatedByID')->set(Field::SET_EDIT),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docUpdatedBySTR')->set(Field::SET_EDIT),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_docUpdatedWhen')
            ->set(Field::SET_EDIT)
            ->validator(Validate::dateFormat(
                'd.m.Y H:i:s',
                ValidateOptions::inst()
                    ->allowEmpty(false)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d H:i:s',
                'd.m.Y H:i:s'
            ))
            ->setFormatter(Format::datetime(
                'd.m.Y H:i:s',
                'Y-m-d H:i:s'
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_emailSentByID')->set(Field::SET_CREATE),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_emailSentBySTR')->set(Field::SET_CREATE),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_emailSentWhen')
            ->set(Field::SET_CREATE)
            ->validator(Validate::dateFormat(
                'd.m.Y H:i:s',
                ValidateOptions::inst()
                    ->allowEmpty(false)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d H:i:s',
                'd.m.Y H:i:s'
            ))
            ->setFormatter(Format::datetime(
                'd.m.Y H:i:s',
                'Y-m-d H:i:s'
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolActive'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolCheckout'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolCheckoutComment'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolCheckoutID'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolCheckoutDates'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolCheckoutWhen')
            ->set(Field::SET_CREATE)
            ->validator(Validate::dateFormat(
                'd.m.Y H:i:s',
                ValidateOptions::inst()
                    ->allowEmpty(true)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d H:i:s',
                'd.m.Y H:i:s'
            )),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolStatus'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolWarning'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolAlarm'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolDays'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolMailReminder1'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolMailReminder2'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolMailNotifyDL'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolMailNotifyCheckout'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolMailSpecialNotifyCheckout'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolMailSpecialNotifyDL'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolUseDeadline'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.outbox_controlIspolStatusDeadline'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.cntComments'),
        Field::inst(__MAIL_OUTGOING_TABLENAME . '.docmailext'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.id'),
        Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.file_webpath'),
        Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.file_originalname'),
    )
    #
    #    ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    #    ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    #    ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    #
    ->on('preUpload', function ($editor, $data) {
    })
    ->on('postUpload', function ($editor, $id, $files, $data) {
        updateSyslogAfterupload($editor->db(), $id);
    })
    ->on('preEdit', function ($editor, $id, $values) {
        checkChanges($editor->db(), $id, $values);
        fixLog($editor->db(), 'UPD', $id, $values);
        updateLogCheckouts($editor->db(), $id, $values);
        clearRelFields($editor->db(), $id, $values);
        updateLogComments($editor->db(), 'UPD', $id, $values);
        updateCountComments($editor->db(), $id);
    })
    ->on('preCreate', function ($editor, $values) {
        // updateLogComments($editor->db(), 'CRT', null, $values);
    })
    ->on('preGet', function ($editor, $id) use ($__startDate, $__endDate) {
        $editor->where(function ($q) use ($__startDate, $__endDate) {
            $q->where(__MAIL_OUTGOING_TABLENAME . '.outbox_docDate', '( SELECT outbox_docDate FROM ' . __MAIL_OUTGOING_TABLENAME . ' WHERE outbox_docDate >= ' . $__startDate . ' AND outbox_docDate <= ' . $__endDate . ' )', 'IN', false);
        });
    })
    ->on('postCreate', function ($editor, $id, $values, $row) {
        updateFields($editor->db(), 'CRT', $id, $values, $row);
        fixCreate($editor->db(), 'CRT', $id, $values);
        fixLog($editor->db(), 'CRT', $id, $values);
        sendemailtoispolYouareispolnitel($editor->db(), 'Email', $id, $values, $row);
        newUserMessage($editor->db(), 'CRT', $id, $values);
        statsMailOutgoing($editor->db(), 'CRT', date("Y"));
        syncLinkFields($editor->db(), 'CRT', $id, $values, $row);
        updateLogComments($editor->db(), 'CRT', $id, $values);
        updateCountComments($editor->db(), $id);
        syncRelFields($editor->db(), 'CRT', $id, $values, $row);
    })
    ->on('postEdit', function ($editor, $id, $values, $row) {
        fixUpdate($editor->db(), 'UPD', $id, $values);
        updateFields($editor->db(), 'UPD', $id, $values, $row);
        updateRelCheckout($editor->db(), 'UPD', $id, $values, $row);
        updateIspolStatus($editor->db(), $id, $values);
        updateIspolCheckout($editor->db(), $id, $values, $row);
        updateFileIDadd($editor->db(), 'UPD', $id, $values, $row);
        sendemailtoispolYouareispolnitel($editor->db(), 'Email', $id, $values, $row);
        newUserMessage($editor->db(), 'UPD', $id, $values);
        statsMailOutgoing($editor->db(), 'UPD', date("Y"));
        syncLinkFields($editor->db(), 'UPD', $id, $values, $row);
        syncRelFields($editor->db(), 'UPD', $id, $values, $row);
    })
    ->on('preRemove', function ($editor, $id, $values) {
        fixLog($editor->db(), 'DEL', $id, $values);
        backupRemovedRecords($editor->db(), $id);
        delAttachment($editor->db(), $id);
        updateSyslogAfterremove($editor->db(), $id);
    })
    ->on('postRemove', function ($editor) {
        statsMailOutgoing($editor->db(), 'DEL', date("Y"));
    })
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    ->leftJoin('sp_contragents', 'sp_contragents.kodcontragent', '=', __MAIL_OUTGOING_TABLENAME . '.outbox_docRecipient_kodzakaz')
    ->leftJoin(__MAIL_OUTGOING_FILES_TABLENAME, __MAIL_OUTGOING_FILES_TABLENAME . '.id', '=', __MAIL_OUTGOING_TABLENAME . '.outbox_docFileID')
    ->leftJoin(__MAIL_INCOMING_FILES_TABLENAME, __MAIL_INCOMING_FILES_TABLENAME . '.id', '=', __MAIL_OUTGOING_TABLENAME . '.inbox_fileID_rel')
    ->process($_POST)
    ->json();
