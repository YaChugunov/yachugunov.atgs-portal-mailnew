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
$_str = isset($_POST['str']) ? $_POST['str'] : "";
$_koddoc = isset($_POST['koddoc']) ? $_POST['koddoc'] : "";

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$data = array();
$error = $success = $output = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
        return 0;
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $_str != "") {

            $_QRY_Dog = mysqli_fetch_assoc(mysqlQuery("SELECT docnumber FROM dognet_docbase WHERE koddoc='{$_koddoc}' AND koddel<>'99'"));
            $docnumber = !empty($_QRY_Dog['docnumber']) ? $_QRY_Dog['docnumber'] : "----";

            $_QRY_Outgoing = mysqlQuery("SELECT koddocmail, outbox_docIDSTR, outbox_docDate, outbox_docSourceID, outbox_docSourceDate, outbox_docAbout, outbox_docSenderSTR, outbox_docContractorSTR FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE ID LIKE '%{$_str}%' ORDER BY outbox_docDate DESC");
            if ($_QRY_Outgoing->num_rows > 0) {
                $output .= '<div class="display-subblock mail linkedOut hidden">';
                $output .= '<div class="itemlist alert alert-light alert-dismissible fade show" role="alert">';
                $output .= '<p class="small-title"><strong>Исходящие письма, связанные с договором 3-4/' . $docnumber . '</strong></p>';

                $output .= '<table class="byZak-lastOut table compact display">';
                $output .= '<thead>';
                $output .= '<tr>';
                $output .= '<th>Исх. № (АТГС)</th><th>Дата (АТГС)</th><th>Отправитель</th><th>Исполнитель</th><th>Вх. № (контр)</th><th>Дата (контр)</th><th>Описание документа</th>';
                $output .= '</tr>';
                $output .= '</thead>';
                $output .= '<tbody>';
                while ($_ROW_Outgoing = mysqli_fetch_assoc($_QRY_Outgoing)) {

                    $koddocmail = $_ROW_Outgoing["koddocmail"];
                    $docid = !empty($_ROW_Outgoing["outbox_docIDSTR"]) ? $_ROW_Outgoing["outbox_docIDSTR"] : '<span class="empty">---</span>';
                    $docdate = !empty($_ROW_Outgoing["outbox_docDate"]) && $_ROW_Outgoing["outbox_docDate"] != "0000-00-00 00:00:00" ? date("d.m.Y H:m", strtotime($_ROW_Outgoing["outbox_docDate"])) : '<span class="empty">Нет данных</span>';
                    $sender = !empty($_ROW_Outgoing["outbox_docSenderSTR"]) ? $_ROW_Outgoing["outbox_docSenderSTR"] : '<span class="empty">Нет данных</span>';
                    $ispol = !empty($_ROW_Outgoing["outbox_docContractorSTR"]) ? $_ROW_Outgoing["outbox_docContractorSTR"] : '<span class="empty">Нет данных</span>';
                    $sourceid = !empty($_ROW_Outgoing["outbox_docSourceID"]) ? $_ROW_Outgoing["outbox_docSourceID"] : '<span class="empty">Нет данных</span>';
                    $sourcedate = !empty($_ROW_Outgoing["outbox_docSourceDate"] && $_ROW_Outgoing["outbox_docSourceDate"] != "0000-00-00 00:00:00") ? date("d.m.Y", strtotime($_ROW_Outgoing["outbox_docSourceDate"])) : '<span class="empty">Нет данных</span>';
                    $docabout = !empty($_ROW_Outgoing["outbox_docAbout"]) ? $_ROW_Outgoing["outbox_docAbout"] : '<span class="empty">Нет данных</span>';

                    $output .= '<tr class=""><td><span class="docid"><a href="/mailnew/index.php?type=out&mode=profile&uid=' . $koddocmail . '">1-1/' . $docid . '</a></span></td><td><span class="docdate">' . $docdate . '</span></td><td><span class="docsender">' . $sender . '</span></td><td><span class="docispol">' . $ispol . '</span></td><td><span class="docsourceid">' . $sourceid . '</span></td><td><span class="docsourcedate">' . $sourcedate . '</span></td><td><span class="docabout">' . $docabout . '</span></td></tr>';
                }
                $output .= '</tbody>';
                $output .= '</table>';

                $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                $output .= '</div>';
                $output .= '</div>';
            } else {
                $output .= '<div class="linkedOut hidden">';
                $output .= '<div class="itemlist alert alert-light alert-dismissible fade show" role="alert">';
                $output .= '<div class="item"><span class="nodata text-danger text-center">Связанных документов в исходящих не найдено</span></div>';
                $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                $output .= '</div>';
                $output .= '</div>';
            }
        } else {
            $output .= '<div class="hidden">';
            $output .= '<div class="itemlist alert alert-light alert-dismissible fade show" role="alert">';
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
