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
$_koddoc = isset($_POST['koddoc']) ? $_POST['koddoc'] : "";

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

$data = array();
$error = $success = $output = "";

if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization($_SESSION['login'], $_SESSION['password']) == -1) {
        return 0;
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $_koddoc != "") {

            $_QRY_Dog = mysqlQuery("SELECT docnumber, kodshab FROM dognet_docbase WHERE koddoc='{$_koddoc}' AND koddel<>'99'");
            $_ROW_Dog = mysqli_fetch_assoc($_QRY_Dog);
            if ($_ROW_Dog['kodshab'] == 1 || $_ROW_Dog['kodshab'] == 3) {

                $_QRY_Stage = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(summastage) as sum FROM dognet_dockalplan WHERE koddoc='{$_koddoc}' AND koddel<>'99'"));

                $_QRY_Chf = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(chetfsumma) as sum FROM dognet_kalplanchf WHERE kodkalplan IN ( SELECT kodkalplan FROM dognet_dockalplan WHERE koddoc='{$_koddoc}' and koddel<>'99' ) AND koddel<>'99'"));

                $_QRY_Av = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(summaavans) as sum FROM dognet_docavans WHERE koddoc IN ( SELECT kodkalplan FROM dognet_dockalplan WHERE koddoc='{$_koddoc}' AND koddel<>'99' ) AND koddel<>'99'"));

                $_QRY_Oplav = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(summaoplav) as sum FROM dognet_chfavans WHERE kodkalplan IN ( SELECT kodkalplan FROM dognet_dockalplan WHERE koddoc='{$_koddoc}' and koddel<>'99' ) AND kodchfact IN ( SELECT kodchfact FROM dognet_kalplanchf WHERE kodkalplan IN ( SELECT kodkalplan FROM dognet_dockalplan WHERE koddoc='{$_koddoc}' AND koddel<>'99' )) AND koddel<>'99'"));

                $_QRY_Oplchf = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(summaopl) as sum FROM dognet_oplatachf WHERE kodkalplan IN ( SELECT kodkalplan FROM dognet_dockalplan WHERE koddoc='{$_koddoc}' and koddel<>'99' ) AND kodchfact IN ( SELECT kodchfact FROM dognet_kalplanchf WHERE kodkalplan IN ( SELECT kodkalplan FROM dognet_dockalplan WHERE koddoc='{$_koddoc}' AND koddel<>'99' )) AND koddel<>'99'"));
            } elseif ($_ROW_Dog['kodshab'] == 0 || $_ROW_Dog['kodshab'] == 2 || $_ROW_Dog['kodshab'] == 4) {

                $_QRY_Stage = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(docsumma) as sum FROM dognet_docbase WHERE koddoc='{$_koddoc}' AND koddel<>'99'"));

                $_QRY_Chf = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(chetfsumma) as sum FROM dognet_kalplanchf WHERE kodkalplan='{$_koddoc}' AND koddel<>'99'"));

                $_QRY_Av = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(summaavans) as sum FROM dognet_docavans WHERE koddoc='{$_koddoc}' AND koddel<>'99'"));

                $_QRY_Oplav = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(summaoplav) as sum FROM dognet_chfavans WHERE kodkalplan='{$_koddoc}' AND kodchfact IN ( SELECT kodchfact FROM dognet_kalplanchf WHERE kodkalplan='{$_koddoc}' AND koddel<>'99' ) AND koddel<>'99'"));

                $_QRY_Oplchf = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(summaopl) as sum FROM dognet_oplatachf WHERE kodkalplan='{$_koddoc}' AND kodchfact IN ( SELECT kodchfact FROM dognet_kalplanchf WHERE kodkalplan='{$_koddoc}' AND koddel<>'99' ) AND koddel<>'99'"));
            }

            if ($_QRY_Dog) {

                $sumstage = !empty($_QRY_Stage['sum']) ? number_format($_QRY_Stage['sum'], 2, ',', ' ') . " р." : '0,00 р.';
                $sumchf = !empty($_QRY_Chf['sum']) ? number_format($_QRY_Chf['sum'], 2, ',', ' ') . " р." : '0,00 р.';
                $sumav = !empty($_QRY_Av['sum']) ? number_format($_QRY_Av['sum'], 2, ',', ' ') . " р." : '0,00 р.';
                $sumoplav = !empty($_QRY_Oplav['sum']) ? number_format($_QRY_Oplav['sum'], 2, ',', ' ') . " р." : '0,00 р.';
                $sumoplchf = !empty($_QRY_Oplchf['sum']) ? number_format($_QRY_Oplchf['sum'], 2, ',', ' ') . " р." : '0,00 р.';

                $zadolchf = $_QRY_Chf['sum'] - ($_QRY_Oplav['sum'] + $_QRY_Oplchf['sum']);
                $zadolchf = !empty($zadolchf) ? number_format($zadolchf, 2, ',', ' ') . " р." : '0,00 р.';

                $zadoldog = $_QRY_Stage['sum'] - $_QRY_Chf['sum'];
                $zadoldog = !empty($zadoldog) ? number_format($zadoldog, 2, ',', ' ') . " р." : '0,00 р.';

                $ostav = $_QRY_Av['sum'] - $_QRY_Oplav['sum'];
                $ostav = !empty($ostav) ? number_format($ostav, 2, ',', ' ') . " р." : '0,00 р.';

                $output .= '<div class="display-subblock sumDog hidden">';
                $output .= '<div class="itemlist alert alert-light alert-dismissible fade show" role="alert">';
                $output .= '<p><strong>Выполнение по договору 3-4/' . $_ROW_Dog['docnumber'] . '</strong></p>';

                $output .= '<table class="table">';
                $output .= '<thead>';
                $output .= '<tr>';
                $output .= '<th>Сумма договора (остаток)</th><th>Выполнение (задолженность)</th><th>Оплата</th><th>Авансы (остаток)</th><th>Зачтено</th>';
                $output .= '</tr>';
                $output .= '</thead>';
                $output .= '<tbody>';
                $output .= '<tr class=""><td><span class="docid">' . $sumstage . ' (' . $zadoldog . ')</span></td><td><span class="docid">' . $sumchf . ' (' . $zadolchf . ')</span></td><td><span class="docabout">' . $sumoplchf . '</span></td><td><span class="docsourceid">' . $sumav . ' (' . $ostav . ')</span></td><td><span class="docsourcedate">' . $sumoplav . '</span></td></tr>';
                $output .= '</tbody>';
                $output .= '</table>';

                $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                $output .= '</div>';
                $output .= '</div>';
            } else {
                $output .= '<div class="sumDog hidden">';
                $output .= '<div class="itemlist alert alert-light alert-dismissible fade show" role="alert">';
                $output .= '<div class="item"><span class="nodata text-danger text-center">Связанных документов во входящих не найдено</span></div>';
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
