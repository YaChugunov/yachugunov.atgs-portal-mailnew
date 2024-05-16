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
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

// Код документа в таблице mailbox_incoming
$_koddocmail = isset($_POST['koddocmail']) ? $_POST['koddocmail'] : "";
$_QRY_docMain = mysqlQuery("SELECT outbox_rowID_rel, outbox_rowIDadd_rel, outbox_rowIDs_links, inbox_rowIDs_links, dognet_rowIDs_links FROM " . __MAIL_INCOMING_TABLENAME . " WHERE koddocmail='" . $_koddocmail . "'");

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$data = array();
$error = $success = $output = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
        return 0;
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $_koddocmail != "") {
            $_QRY_FilesList = mysqlQuery("SELECT * FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE mainfile=1 AND koddocmail='" . $_koddocmail . "'");
            while ($_ROW_FilesList = mysqli_fetch_assoc($_QRY_FilesList)) {
                if ($_ROW_FilesList['file_url'] == "") {
                    $error = 'Битая ссылка на файл';
                } elseif ($_ROW_FilesList['file_originalname'] == "") {
                    $error = 'Ошибка в имени файла';
                } else {
                    $_fileurl = $_ROW_FilesList['file_url'];
                    $_file_originalname = $_ROW_FilesList['file_originalname'];
                    $_comment = ($_ROW_FilesList['comment'] != "") ? $_ROW_FilesList['comment'] : "Комментария к файлу нет";
                    // 
                    $success = '<div class="docMain mainfile item mb-2">';
                    $success .= '<span class="filelink"><a href="' . $_fileurl . '" target="_blank" title="' . $_file_originalname . '">' . $_file_originalname . '</a></span>';
                    $success .= '<br>';
                    $success .= '<span class="comment">' . $_comment . '</span>';
                    $success .= '</div>';
                }
                if (!empty($success)) {
                    $output .= $success;
                    $output .= '|||';
                }
                //
                if (!empty($error)) {
                    $output .= $error;
                    $output .= '|||';
                }
            }
            $output = ($output != "") ? substr($output, 0, -3) : $output;
        } else {
            $output = '<span style="color: red">Что-то пошло не так...</span>';
        }
    }
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
