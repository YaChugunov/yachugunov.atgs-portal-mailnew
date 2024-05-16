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

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$data = array();
$error = $success = $output1 = $output2 = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
        return 0;
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $_koddocmail != "") {
            $_QRY_FilesList1 = mysqlQuery("SELECT * FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE mainfile=1 AND koddocmail='" . $_koddocmail . "'");
            while ($_ROW_FilesList1 = mysqli_fetch_assoc($_QRY_FilesList1)) {
                if ($_ROW_FilesList1['file_url'] == "") {
                    $error = 'Битая ссылка на файл';
                } elseif ($_ROW_FilesList1['file_originalname'] == "") {
                    $error = 'Ошибка в имени файла';
                } else {
                    $_fileurl = $_ROW_FilesList1['file_url'];
                    $_file_originalname = $_ROW_FilesList1['file_originalname'];
                    $_comment = ($_ROW_FilesList1['comment'] != "") ? $_ROW_FilesList1['comment'] : "Комментария к файлу нет";
                    // 
                    $success = '<div class="docMain mainfile item mb-2">';
                    $success .= '<span class="filelink"><a href="' . $_fileurl . '" target="_blank" title="' . $_file_originalname . '">' . $_file_originalname . '</a></span>';
                    $success .= '<br>';
                    $success .= '<span class="comment">' . $_comment . '</span>';
                    $success .= '</div>';
                }
                if (!empty($success)) {
                    $output1 .= $success;
                    $output1 .= '|||';
                }
                //
                if (!empty($error)) {
                    $output1 .= $error;
                    $output1 .= '|||';
                }
            }
            $output1 = ($output1 != "") ? substr($output1, 0, -3) : $output1;
            #
            #
            $_QRY_FilesList2 = mysqlQuery("SELECT * FROM " . __MAIL_INCOMING_FILES_TABLENAME . " WHERE mainfile=0 AND koddocmail='" . $_koddocmail . "'");
            while ($_ROW_FilesList2 = mysqli_fetch_assoc($_QRY_FilesList2)) {
                if ($_ROW_FilesList2['file_url'] == "") {
                    $error = 'Битая ссылка на файл';
                } elseif ($_ROW_FilesList2['file_originalname'] == "") {
                    $error = 'Ошибка в имени файла';
                } else {
                    $_fileurl = $_ROW_FilesList2['file_url'];
                    $_file_originalname = $_ROW_FilesList2['file_originalname'];
                    $_comment = ($_ROW_FilesList2['comment'] != "") ? $_ROW_FilesList2['comment'] : '<span class="text-muted">Комментария к файлу нет</span>';
                    // 
                    $success = '<div class="docMain addfiles item mb-2">';
                    $success .= '<span class="filelink"><a href="' . $_fileurl . '" target="_blank" title="' . $_file_originalname . '">' . $_file_originalname . '</a></span>';
                    $success .= '<br>';
                    $success .= '<span class="comment">' . $_comment . '</span>';
                    $success .= '</div>';
                }
                if (!empty($success)) {
                    $output2 .= $success;
                    $output2 .= '|||';
                }
                //
                if (!empty($error)) {
                    $output2 .= $error;
                    $output2 .= '|||';
                }
            }
            $output2 = ($output2 != "") ? substr($output2, 0, -3) : $output2;
        } else {
            $output2 = '<span style="color: red">Что-то пошло не так...</span>';
        }
    }
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output1 . "///-///" . $output2;