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

// Код документа в таблице mailbox_outgoing
$_koddocmail = isset($_POST['koddocmail']) ? $_POST['koddocmail'] : "";

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$data = array();
$error = $success = $output = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
        return 0;
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $_koddocmail != "") {

            $_QRY_Main = mysqli_fetch_assoc(mysqlQuery("SELECT dognet_docIDs_links, dognet_rowIDs_links FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE koddocmail='{$_koddocmail}'"));
            $dognetlinks = $_QRY_Main['dognet_rowIDs_links'];
            if (!empty($dognetlinks)) {
                $_QRY_Dognet = mysqlQuery("SELECT ID, koddoc, docnumber, kodobject, kodzakaz, docnameshot FROM dognet_docbase WHERE ID IN ('{$dognetlinks}') ORDER BY docnumber DESC");

                if ($_QRY_Dognet->num_rows > 0) {
                    $output .= '<div class="display-block linkedDog hidden">';
                    $output .= '<div class="itemlist alert alert-secondary alert-dismissible alert-fadein fade show" role="alert">';
                    $output .= '<p class=""><span class="title">Договоры, связанные с письмом в Профиле</span></p>';

                    $output .= '<table class="table">';
                    $output .= '<thead>';
                    $output .= '<tr>';
                    $output .= '<th>Договор</th><th>Заказчик</th><th>Объект</th><th>Название</th>';
                    $output .= '</tr>';
                    $output .= '</thead>';
                    $output .= '<tbody>';
                    while ($_ROW_Dognet = mysqli_fetch_assoc($_QRY_Dognet)) {
                        $kodobject = !empty($_ROW_Dognet['kodobject']) ? $_ROW_Dognet['kodobject'] : "";
                        $kodzakaz = !empty($_ROW_Dognet['kodzakaz']) ? $_ROW_Dognet['kodzakaz'] : "";

                        $_QRY_Object = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM sp_objects WHERE kodobject='{$kodobject}'"));
                        $_QRY_Zakaz = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM sp_contragents WHERE kodcontragent='{$kodzakaz}'"));

                        $rowid = $_ROW_Dognet["ID"];
                        $koddoc = $_ROW_Dognet["koddoc"];
                        $docnumber = $_ROW_Dognet["docnumber"];
                        $objectname = $_QRY_Object["nameobjectlong"];
                        $zakazchikname = $_QRY_Zakaz["namefull"];
                        $docnameshot = $_ROW_Dognet["docnameshot"];

                        $output .= '<tr class="contrInfo"><td><span class="docnumber"><a href="/dognet/dognet-docview.php?docview_type=details&uniqueID=' . $koddoc . '" target="_blanc">3-4/' . $docnumber . '</a></span></td><td><span class="docnameshot">' . $zakazchikname . '</span></td><td><span class="objectname">' . $objectname . '</span></td><td><span class="zakazchikname">' . $docnameshot . '</span></td></tr>';
                        $output .= '<tr class="contrButtons"><td colspan="4" class="text-end px-0 pb-2">';
                        $output .= '<button id="btn-linkedDoc-files" onClick="ajaxRequest_byLinkedDocFiles(' . $koddoc . ', \'byDoc\', \'byLinkedDocFiles\')" koddoc="' . $koddoc . '" type="button" class="btn btn-small-link">Файлы</button>';
                        $output .= '<button id="btn-linkedDoc-sums" onClick="ajaxRequest_byLinkedDocSums(' . $koddoc . ', \'byDoc\', \'byLinkedDocSums\')" koddoc="' . $koddoc . '" type="button" class="btn btn-small-link">Выполнение</button>';
                        $output .= '<button id="btn-linkedDoc-linkedInc" onClick="ajaxRequest_byLinkedDoc2Inc(' . $rowid . ', ' . $koddoc . ', \'byDoc\', \'byLinkedDoc2Inc\')" koddoc="' . $koddoc . '" type="button" class="btn btn-small-link">Входящие</button>';
                        $output .= '<button id="btn-linkedDoc-linkedOut" onClick="ajaxRequest_byLinkedDoc2Out(' . $rowid . ', ' . $koddoc . ', \'byDoc\', \'byLinkedDoc2Out\')" koddoc="' . $koddoc . '" type="button" class="btn btn-small-link">Исходящие</button>';
                        $output .= '</td></tr>';
                    }
                    $output .= '</tbody>';
                    $output .= '</table>';
                    $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                    $output .= '</thead>';
                    $output .= '</div>';
                } else {
                    $output .= '<div class="hidden">';
                    $output .= '<div class="itemlist alert alert-light alert-dismissible alert-fadein fade show" role="alert">';
                    $output .= '<div class="item"><span class="nodata text-danger text-center">Связанных договоров не найдено</span></div>';
                    $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                    $output .= '</div>';
                    $output .= '</div>';
                }
            } else {
                $output .= '<div class="hidden">';
                $output .= '<div class="itemlist alert alert-light alert-dismissible alert-fadein fade show" role="alert">';
                $output .= '<div class="item"><span class="nodata text-danger text-center">Связанных договоров не найдено</span></div>';
                $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                $output .= '</div>';
                $output .= '</div>';
            }
        } else {
            $output .= '<div class="hidden">';
            $output .= '<div class="itemlist alert alert-light alert-dismissible alert-fadein fade show" role="alert">';
            $output .= '<div class="item"><span class="nodata text-danger text-center">Что-то пошло не так...</span></div>';
            $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            $output .= '</div>';
            $output .= '</div>';
        }
    }
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
