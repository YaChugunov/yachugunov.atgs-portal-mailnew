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
$error = $success = $output = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
        return 0;
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $_koddocmail != "") {
            $_QRY_Main = mysqli_fetch_assoc(mysqlQuery("SELECT outbox_docIDSTR, outbox_docRecipient_kodzakaz, outbox_docSourceID, outbox_docSourceDate FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE koddocmail='{$_koddocmail}'"));
            $kodzakaz = $_QRY_Main['outbox_docRecipient_kodzakaz'];
            $_QRY_ContrAgent = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM sp_contragents WHERE kodcontragent='{$kodzakaz}'"));
            $outZak_name = !empty($_QRY_ContrAgent['namefull']) ? $_QRY_ContrAgent['namefull'] : $_QRY_ContrAgent['nameshort'];
            $outZak_address = $_QRY_ContrAgent['address_legal'];
            $outZak_dolg = $_QRY_ContrAgent['director_post'];
            $outZak_fio = $_QRY_ContrAgent['director_fio'] != "" ? $_QRY_ContrAgent['director_fio'] : $_QRY_ContrAgent['zakfio'];
            $outZak_inn = $_QRY_ContrAgent['inn'];
            $output = $kodzakaz . "///" . $outZak_name . "///" . $outZak_address . "///" . $outZak_dolg . "///" . $outZak_fio . "///" . $outZak_inn;
            $output = !empty($output) ? $output : "Данные о контрагенте не найдены";
        } else {
            $output = '<span style="color: red">Что-то пошло не так...</span>';
        }
    }
}
unset($_POST);
// Вывод сообщений о результате загрузки.
echo $output;
