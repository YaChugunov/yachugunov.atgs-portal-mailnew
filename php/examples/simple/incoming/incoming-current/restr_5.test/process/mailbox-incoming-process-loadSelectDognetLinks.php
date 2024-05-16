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
$__startDate     = $_SESSION['in_startTableDate'];
$__startDate_rel = $_SESSION['in_startTableDate'];
$__endDate       = $_SESSION['in_endTableDate'];
$__endDate_rel   = $_SESSION['in_endTableDate'];
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
if ((__MAIL_TESTMODE_ON == true || __MAIL_TESTMODE_ON == 1) && __MAIL_TESTMODE_TYPE < 3) {
    define('__MAIL_RESTR5', '/restr_5.test');
} else {
    define('__MAIL_RESTR5', '/restr_5');
}
# Import PHPMailer classes into the global namespace
# These must be at the top of your script, not inside a function
use DataTables\Editor;
use DataTables\Editor\Field;
use DataTables\Editor\Format;

######
######
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
######
######
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
######
######
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
Editor::inst($db, __MAIL_INCOMING_TABLENAME)
    ->fields(
        Field::inst(__MAIL_INCOMING_TABLENAME . '.dognet_docIDs_links')
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
                    ->order('docnumber DESC')
            )
            ->validator(Validate::dbValues())
            ->setFormatter(Format::ifEmpty(null)),
    )
    #
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    #
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    ->process($_POST)
    ->json();