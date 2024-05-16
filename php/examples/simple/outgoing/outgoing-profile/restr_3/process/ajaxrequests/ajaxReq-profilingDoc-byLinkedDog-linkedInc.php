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

            $_QRY_Incoming = mysqlQuery("SELECT koddocmail, inbox_docIDSTR, inbox_docDate, inbox_docSourceID, inbox_docSourceDate, inbox_docAbout, inbox_docRecipientSTR, inbox_docContractorSTR FROM " . __MAIL_INCOMING_TABLENAME . " WHERE dognet_rowIDs_links LIKE '%{$_str}%' ORDER BY inbox_docDate DESC");
            if ($_QRY_Incoming->num_rows > 0) {
                $output .= '<div class="display-subblock mail linkedInc hidden">';
                $output .= '<div class="itemlist alert alert-light alert-dismissible alert-fadein fade show" role="alert">';
                $output .= '<p class="small-title"><strong>Входящие письма, связанные с договором 3-4/' . $docnumber . '</strong></p>';

                $output .= '<table class="byZak-lastIn table compact display">';
                $output .= '<thead>';
                $output .= '<tr>';
                $output .= '<th>Вх. № (АТГС)</th><th>Дата (АТГС)</th><th>Получатель</th><th>Ответственный</th><th>Исх. № (контр)</th><th>Дата (контр)</th><th>Описание документа</th>';
                $output .= '</tr>';
                $output .= '</thead>';
                $output .= '<tbody>';
                while ($_ROW_Incoming = mysqli_fetch_assoc($_QRY_Incoming)) {

                    $koddocmail = $_ROW_Incoming["koddocmail"];
                    $docid = !empty($_ROW_Incoming["inbox_docIDSTR"]) ? $_ROW_Incoming["inbox_docIDSTR"] : '<span class="empty">---</span>';
                    $docdate = !empty($_ROW_Incoming["inbox_docDate"]) && $_ROW_Incoming["inbox_docDate"] != "0000-00-00 00:00:00" ? date("d.m.Y H:m", strtotime($_ROW_Incoming["inbox_docDate"])) : '<span class="empty">Нет данных</span>';
                    $recipient = !empty($_ROW_Incoming["inbox_docRecipientSTR"]) ? $_ROW_Incoming["inbox_docRecipientSTR"] : '<span class="empty">Нет данных</span>';
                    $ispol = !empty($_ROW_Incoming["inbox_docContractorSTR"]) ? str_replace(",", ", ", $_ROW_Incoming["inbox_docContractorSTR"]) : '<span class="empty">Нет данных</span>';
                    $sourceid = !empty($_ROW_Incoming["inbox_docSourceID"]) ? $_ROW_Incoming["inbox_docSourceID"] : '<span class="empty">Нет данных</span>';
                    $sourcedate = !empty($_ROW_Incoming["inbox_docSourceDate"] && $_ROW_Incoming["inbox_docSourceDate"] != "0000-00-00 00:00:00") ? date("d.m.Y", strtotime($_ROW_Incoming["inbox_docSourceDate"])) : '<span class="empty">Нет данных</span>';
                    $docabout = !empty($_ROW_Incoming["inbox_docAbout"]) ? $_ROW_Incoming["inbox_docAbout"] : '<span class="empty">Нет данных</span>';

                    $output .= '<tr class=""><td><span class="docid"><a href="/mailnew/index.php?type=in&mode=profile&uid=' . $koddocmail . '">1-2/' . $docid . '</a></span></td><td><span class="docdate">' . $docdate . '</span></td><td><span class="docrecipient">' . $recipient . '</span></td><td><span class="docispol">' . $ispol . '</span></td><td><span class="docsourceid">' . $sourceid . '</span></td><td><span class="docsourcedate">' . $sourcedate . '</span></td><td><span class="docabout">' . $docabout . '</span></td></tr>';
                }
                $output .= '</tbody>';
                $output .= '</table>';

                $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                $output .= '</div>';
                $output .= '</div>';
            } else {
                $output .= '<div class="linkedInc hidden">';
                $output .= '<div class="itemlist alert alert-light alert-dismissible alert-fadein fade show" role="alert">';
                $output .= '<div class="item"><span class="nodata text-danger text-center">Связанных документов во входящих не найдено</span></div>';
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
