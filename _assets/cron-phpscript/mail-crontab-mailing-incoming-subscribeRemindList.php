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

define("REMINDER_1", 86400 * 3);
define("REMINDER_2", 86400);

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
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция проверки строки на правильность даты
//
function validateDate($date, $format = 'Y-m-d') {
     $d = DateTime::createFromFormat($format, $date);
     return $d && $d->format($format) === $date;
}


# Подключаем библиотеки
// require "/var/www/html/atgs-portal.local/www/mailnew/_assets/libs/PHPMailer/src/Exception.php";
// require "/var/www/html/atgs-portal.local/www/mailnew/_assets/libs/PHPMailer/src/PHPMailer.php";
// require "/var/www/html/atgs-portal.local/www/mailnew/_assets/libs/PHPMailer/src/SMTP.php";

# Import PHPMailer classes into the global namespace
# These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
$datetimenow = date("Y-m-d H:i:s");
$datenow = date("Y-m-d");
$datetime1 = date("Y-m-d H:i:s", strtotime('-1 month', strtotime($datetimenow)));
$datetime0 = date("Y-m-d H:i:s", strtotime(strtotime($datetimenow)));
$datetimeDL2 = date("Y-m-d 00:00:00", strtotime('+7 day', strtotime($datetimenow)));
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
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
$mail->setLanguage('ru', "/var/www/html/atgs-portal.local/www/mailnew/_assets/libs/PHPMailer/language/");
$mail->CharSet = "utf-8";
#
# From
$from_name  = "АТГС.Портал / Корпоративные сервисы";
$from_email = "portal@atgs.ru";
$from_name  = "=?utf-8?B?" . base64_encode($from_name) . "?=";
$mail->setFrom($from_email, $from_name);
#
# Заголовок письма
$_msgTitle = "Срок исполнения";
// $email_admin = 'chugunov@atgs.ru';
$mail->addReplyTo('notreply@atgs.ru', 'Do not reply');
#
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
#
# Выборка по пользователя, которые подписались на еженедельную рассылку
#
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
#
$_SQLReq_Subscribers = "SELECT * FROM users WHERE id IN (SELECT userid FROM mailbox_userSettingsUI WHERE incoming_subscribeWeekReminder='1')";
$_SQLReq_Subscribers_Clr = str_replace(array('\'', '"'), '`', $_SQLReq_Subscribers);
$_QRY_Subscribers = mysqlQuery($_SQLReq_Subscribers);
if (isset($_QRY_Subscribers)) {
     while ($_ROW_Subscribers = mysqli_fetch_assoc($_QRY_Subscribers)) {

          $_docContractorID = $_ROW_Subscribers['id'];

          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          # Основной открывающий блок >>>>>
          $messageContent_Start = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" style="font-family:arial, helvetica neue, helvetica, sans-serif"><head><meta charset="UTF-8"><meta content="width=device-width, initial-scale=1" name="viewport"><meta name="x-apple-disable-message-reformatting"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta content="telephone=no" name="format-detection"><title>АТГС.Портал / Корпоративные сервисы</title><style type="text/css">#outlook a{ padding: 0} .es-button{ mso-style-priority: 100 !important; text-decoration: none !important} a[x-apple-data-detectors]{ color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important} .es-desk-hidden{ display: none; float: left; overflow: hidden; width: 0; max-height: 0; line-height: 0; mso-hide: all} .es-button-border:hover a.es-button, .es-button-border:hover button.es-button{ background: #56d66b !important} .es-button-border:hover{ border-color: #42d159 #42d159 #42d159 #42d159 !important; background: #56d66b !important} td .es-button-border:hover a.es-button-1{ background: #4b545c !important} td .es-button-border-2:hover{ background: #4b545c !important; border-style: solid solid solid solid !important; border-color: #42d159 #42d159 #42d159 #42d159 !important} @media only screen and (max-width:600px){ p, ul li, ol li, a{ line-height: 150% !important} h1, h2, h3, h1 a, h2 a, h3 a{ line-height: 120%} h1{ font-size: 30px !important; text-align: left} h2{ font-size: 24px !important; text-align: left} h3{ font-size: 20px !important; text-align: left} .es-header-body h1 a, .es-content-body h1 a, .es-footer-body h1 a{ font-size: 30px !important; text-align: left} .es-header-body h2 a, .es-content-body h2 a, .es-footer-body h2 a{ font-size: 24px !important; text-align: left} .es-header-body h3 a, .es-content-body h3 a, .es-footer-body h3 a{ font-size: 20px !important; text-align: left} .es-menu td a{ font-size: 14px !important} .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a{ font-size: 14px !important} .es-content-body p, .es-content-body ul li, .es-content-body ol li, .es-content-body a{ font-size: 14px !important} .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a{ font-size: 14px !important} .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a{ font-size: 12px !important} *[class="gmail-fix"]{ display: none !important} .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3{ text-align: center !important} .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3{ text-align: right !important} .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3{ text-align: left !important} .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img{ display: inline !important} .es-button-border{ display: inline-block !important} a.es-button, button.es-button{ font-size: 18px !important; display: inline-block !important} .es-adaptive table, .es-left, .es-right{ width: 100% !important} .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header{ width: 100% !important; max-width: 600px !important} .es-adapt-td{ display: block !important; width: 100% !important} .adapt-img{ width: 100% !important; height: auto !important} .es-m-p0{ padding: 0 !important} .es-m-p0r{ padding-right: 0 !important} .es-m-p0l{ padding-left: 0 !important} .es-m-p0t{ padding-top: 0 !important} .es-m-p0b{ padding-bottom: 0 !important} .es-m-p20b{ padding-bottom: 20px !important} .es-mobile-hidden, .es-hidden{ display: none !important} tr.es-desk-hidden, td.es-desk-hidden, table.es-desk-hidden{ width: auto !important; overflow: visible !important; float: none !important; max-height: inherit !important; line-height: inherit !important} tr.es-desk-hidden{ display: table-row !important} table.es-desk-hidden{ display: table !important} td.es-desk-menu-hidden{ display: table-cell !important} .es-menu td{ width: 1% !important} table.es-table-not-adapt, .esd-block-html table{ width: auto !important} table.es-social{ display: inline-block !important} table.es-social td{ display: inline-block !important} .es-desk-hidden{ display: table-row !important; width: auto !important; overflow: visible !important; max-height: inherit !important} .es-m-p5{ padding: 5px !important} .es-m-p5t{ padding-top: 5px !important} .es-m-p5b{ padding-bottom: 5px !important} .es-m-p5r{ padding-right: 5px !important} .es-m-p5l{ padding-left: 5px !important} .es-m-p10{ padding: 10px !important} .es-m-p10t{ padding-top: 10px !important} .es-m-p10b{ padding-bottom: 10px !important} .es-m-p10r{ padding-right: 10px !important} .es-m-p10l{ padding-left: 10px !important} .es-m-p15{ padding: 15px !important} .es-m-p15t{ padding-top: 15px !important} .es-m-p15b{ padding-bottom: 15px !important} .es-m-p15r{ padding-right: 15px !important} .es-m-p15l{ padding-left: 15px !important} .es-m-p20{ padding: 20px !important} .es-m-p20t{ padding-top: 20px !important} .es-m-p20r{ padding-right: 20px !important} .es-m-p20l{ padding-left: 20px !important} .es-m-p25{ padding: 25px !important} .es-m-p25t{ padding-top: 25px !important} .es-m-p25b{ padding-bottom: 25px !important} .es-m-p25r{ padding-right: 25px !important} .es-m-p25l{ padding-left: 25px !important} .es-m-p30{ padding: 30px !important} .es-m-p30t{ padding-top: 30px !important} .es-m-p30b{ padding-bottom: 30px !important} .es-m-p30r{ padding-right: 30px !important} .es-m-p30l{ padding-left: 30px !important} .es-m-p35{ padding: 35px !important} .es-m-p35t{ padding-top: 35px !important} .es-m-p35b{ padding-bottom: 35px !important} .es-m-p35r{ padding-right: 35px !important} .es-m-p35l{ padding-left: 35px !important} .es-m-p40{ padding: 40px !important} .es-m-p40t{ padding-top: 40px !important} .es-m-p40b{ padding-bottom: 40px !important} .es-m-p40r{ padding-right: 40px !important} .es-m-p40l{ padding-left: 40px !important} .h-auto{ height: auto !important}} </style></head><body style="width:100%;font-family:arial, helvetica neue, helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0"><div class="es-wrapper-color" style="background-color:#F6F6F6"><table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6">';
          # Основной открывающий блок <<<<<
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          #
          #
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          # Блок с заголовком и верхнем меню >>>>> 
          $messageContent_Top_FullBlock = '<tr><td valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-header" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"><tr><td align="center" style="padding:0;Margin:0"><table bgcolor="#ffffff" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px"><tr><td class="es-m-p5t es-m-p5b es-m-p0r es-m-p0l" align="left" bgcolor="#343a40" style="padding:10px;Margin:0;background-color:#343a40"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0;width:580px"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-m-txt-c" valign="top" align="right" style="Margin:0;padding-top:10px;padding-bottom:10px;padding-left:10px;padding-right:20px;width:30px;font-size:0px"><img src="http://atgs.ru/ext/img/newicons/portal.service-newmail.favicon.ico" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="72"></td><td align="left" style="padding:0;Margin:0"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-m-txt-l" align="left" style="padding:0;Margin:0;padding-left:5px;padding-right:10px"><h1 style="Margin:0;line-height:32px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:32px;font-style:normal;font-weight:normal;color:#ffffff"><strong>Почта АТГС</strong></h1><h1 style="Margin:0;line-height:16px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:16px;font-style:normal;font-weight:normal;color:#ffffff">Входящая и исходящая корпоративная корреспонденция</h1></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td align="left" bgcolor="#111" style="padding:0;Margin:0;background-color:#111111"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr class="es-mobile-hidden"><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0"><table class="es-menu" width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr class="links"><td style="Margin:0;padding-left:5px;padding-right:5px;padding-top:15px;padding-bottom:15px;border:0" width="33.33%" valign="top" bgcolor="transparent" align="center"><a target="_blank" href="http://192.168.1.89/mailnew/index.php?type=main" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:14px;font-weight:normal">ГЛАВНАЯ</a></td><td style="Margin:0;padding-left:5px;padding-right:5px;padding-top:15px;padding-bottom:15px;border:0" width="33.33%" valign="top" bgcolor="transparent" align="center"><a target="_blank" href="http://192.168.1.89/mailnew/index.php?type=in&mode=thisyear" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:14px;font-weight:normal">ВХОДЯЩИЕ</a></td><td style="Margin:0;padding-left:5px;padding-right:5px;padding-top:15px;padding-bottom:15px;border:0" width="33.33%" valign="top" bgcolor="transparent" align="center"><a target="_blank" href="http://192.168.1.89/mailnew/index.php?type=out&mode=thisyear" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:14px;font-weight:normal">ИСХОДЯЩИЕ</a></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr>';
          # Блок с заголовком и верхнем меню <<<<< 
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          #
          #
          # Тема сообения
          $subjectTxt = "Почта АТГС [Входящие] : КИ : Список невыполненных документов с уже истёкшим или наступающим сроком выполнения в ближайшую неделю";
          $subject    = "=?utf-8?B?" . base64_encode($subjectTxt) . "?=";
          #
          # Выводим текст к пользователю перед выводом списка документов
          #
          $__contrData = $db_handle->runQuery("SELECT id, emailaddress, namezayvfio FROM mailbox_sp_users WHERE userid='{$_docContractorID}'");
          $__IO = $_ROW_Subscribers['firstname'] . " " . $_ROW_Subscribers['middlename'];
          $__email = $__contrData[0]['emailaddress'];
          $__FIO   = $__contrData[0]['namezayvfio'];
          #
          #
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          # Центральный открывающий блок >>>>>
          $messageContent_Body_Opening = '<tr><td align="center" style="padding:0;Margin:0"><table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">';
          # Центральный открывающий блок <<<<<
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          #
          #
          # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
          # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
          # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
          #
          # ОСНОВНАЯ ИНОФРМАТИВНАЯ ЧАСТЬ ПИСЬМА
          #
          # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
          # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
          # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
          #
          #
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          # Обращение к пользователю >>>>>
          $messageContent_Body_IspolText = '<tr><td align="left" style="Margin:0;padding-bottom:5px;padding-top:10px;padding-left:10px;padding-right:10px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:580px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" class="es-m-txt-c" style="padding:0;Margin:0;padding-top:15px"><h1 style="Margin:0;line-height:34px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:28px;font-style:normal;font-weight:normal;color:#111111;text-align:center"><strong>' . $__IO . ',</strong></h1></td></tr><tr><td align="center" class="es-m-txt-c" style="padding:0;Margin:0;padding-bottom:15px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:22px;color:#999999;font-size:18px" class="p_description">ниже приведен список документов (не старше месяца), <u>в которых вы являетесь ответственным</u>, которые стоят на контроле и по которым истёк срок исполнения, а также по которым наступит или истечёт срок исполнения в ближайшую неделю</p></td></tr></table></td></tr></table></td></tr>';
          # Обращение к пользователю <<<<<
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          #
          #
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          # Открывающий блок списка >>>>>
          $messageContent_Body_ListOpening = '<tr><td valign="top" style="padding:0;Margin:0"><table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"><tr><td align="center" style="padding:0;Margin:0"><table class="es-content-body" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">';
          # Открывающий блок списка <<<<<
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          #
          #
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          # Заголовок списка >>>>>
          $messageContent_Body_ListHeader = '<tr><td align="left" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" bgcolor="#111111" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#111111"><tr><td align="left" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:10px;padding-bottom:10px"><h3 style="Margin:0;line-height:22px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:18px;font-style:normal;font-weight:normal;color:#ffffff"><strong>Список документов</strong></h3></td></tr></table></td></tr></table></td></tr>';
          # Заголовок списка <<<<<
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          #
          #
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          # Заголовок таблицы списка >>>>>
          $messageContent_Body_ListTableHeader = '<tr><td align="left" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:10px;padding-bottom:10px"><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p20b" align="center" style="padding:0;Margin:0;width:35px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px"><strong>№</strong></p></td></tr></table></td><td class="es-hidden" style="padding:0;Margin:0;width:10px"></td></tr></table><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p20b" align="center" style="padding:0;Margin:0;width:295px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px"><strong>Описание / Организация / Файл</strong></p></td></tr></table></td><td class="es-hidden" style="padding:0;Margin:0;width:10px"></td></tr></table><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td align="center" class="es-m-p20b" style="padding:0;Margin:0;width:115px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px"><strong>Срок</strong></p></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:115px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px"><strong>Отв-ый(е)</strong></p></td></tr></table></td></tr></table></tr>';
          # Заголовок таблицы списка <<<<<
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
          #
          #
          #
          #
          # Выорка по разнице (TIMESTAMPDIFF) между текущей датой и дедлайном
          #
          $_SQLReq_Docs = "
          SELECT * FROM mailbox_incoming 
          WHERE (((inbox_docDate BETWEEN '$datetime1' AND '$datetime0') AND TIMESTAMPDIFF(SECOND, '$datetimenow', inbox_docDateDeadline) <= 0 ) OR (inbox_docDateDeadline BETWEEN '$datetime0' AND '$datetimeDL2')) 
          AND inbox_controlIspolActive='1' AND inbox_controlIspolUseDeadline='1' AND inbox_controlIspolCheckout<>'1' AND inbox_docContractorID!='' AND inbox_docContractorID LIKE '%{$_docContractorID}%'";
          $_SQLReq_Docs_Clr = str_replace(array('\'', '"'), '`', $_SQLReq_Docs);
          echo '<br>' . $_SQLReq_Docs_Clr . '<br>' . REMINDER_1 . '<br>' . REMINDER_2;
          $_SQLReq_Insert1 = "";
          $messageContent_docInfo = "";
          $_QRY_Docs = mysqlQuery($_SQLReq_Docs);
          #
          #
          $messageContent_Body_ListTableRow = '';
          if (isset($_QRY_Docs)) {
               while ($_ROW_Docs = mysqli_fetch_assoc($_QRY_Docs)) {
                    #
                    $__docID = $_ROW_Docs['inbox_docID'];
                    $__docFileID = $_ROW_Docs['inbox_docFileID'];
                    $__docFileIDadd = $_ROW_Docs['inbox_docFileIDadd'];
                    $__contrID = $_ROW_Docs['inbox_docContractorID'];
                    $__contrKOD = $_ROW_Docs['inbox_docContractor_kodzayvispol'];
                    $__deadline = $_ROW_Docs['inbox_docDateDeadline'];
                    $__koddocmail  = $_ROW_Docs['koddocmail'];
                    # Тема документа и номер
                    $_docAbout = !empty($_ROW_Docs['inbox_docAbout']) ? $_ROW_Docs['inbox_docAbout'] : "---";
                    $_docID = !empty($_ROW_Docs['inbox_docIDSTR']) ? '<a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#0178FA;font-size:12px" href="http://192.168.1.89/mailnew/index.php?type=in&mode=profile&uid=' . $__koddocmail . '">' . $_ROW_Docs['inbox_docIDSTR'] . '</a>' : "---";
                    # Определяем организацию-контрагента
                    $kodzakaz = $_ROW_Docs['inbox_docSender_kodzakaz'];
                    $__contragentDATA = $db_handle->runQuery("SELECT namefull, nameshort FROM sp_contragents WHERE kodcontragent='{$kodzakaz}'");
                    $_docContragentName = (!empty($__contragentDATA[0]['namefull'])) ? $__contragentDATA[0]['namefull'] : (!empty($__contragentDATA[0]['nameshort'])) ? $__contragentDATA[0]['nameshort'] : "---";
                    $_docContragentName = wordwrap($_docContragentName, 12, "\n", false);
                    # Определяем дедлайн
                    $_docDeadline = validateDate($_ROW_Docs['inbox_docDateDeadline'], "Y-m-d") ? date('d.m.Y', strtotime($_ROW_Docs['inbox_docDateDeadline'])) : "---";
                    # Ответственные по документу
                    $_docIspolStr = !empty($_ROW_Docs['inbox_docContractorSTR']) ? str_replace(",", ", ", $_ROW_Docs['inbox_docContractorSTR']) : "---";
                    # Формируем ссылку на основной прикрепленный файл
                    $_fileName        = "";
                    $_fileURL         = "";
                    $_fileLink        = "";
                    if ("" != $__docFileID) {
                         $__fileDATA = $db_handle->runQuery("SELECT file_originalname, file_url FROM mailbox_incoming_files WHERE id=" . $__docFileID);
                         $_fileName        = (!empty($__fileDATA) && "" != $__fileDATA[0]['file_originalname']) ? $__fileDATA[0]['file_originalname'] : "---";
                         $_fileURL         = !empty($__fileDATA) ? $__fileDATA[0]['file_url'] : "";
                         $_fileLink        = ("" != $_fileURL) ? '<a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#0178FA;font-size:12px" href="' . $_fileURL . '" title="Основной файл">' . $_fileName . '</a>' : "---";
                    }

                    $_intDiffSeconds = strtotime($__deadline) - strtotime($datetimenow);

                    # 
                    # Определяем триггер
                    if ($_intDiffSeconds > 0) {
                         $_trigg1 = $_intDiffSeconds <= REMINDER_1 ? 1 : 0;
                         $_trigg2 = $_intDiffSeconds <= REMINDER_2 ? 1 : 0;
                         $_trigg = $_trigg1 + 2 * $_trigg2;
                    } else {
                         $_trigg1 = -1;
                         $_trigg2 = -1;
                         $_trigg = 4;
                    }
                    $_msgTitleStr = "Контроль исполнения";
                    switch ($_trigg) {
                         case 0:
                              $_triggStr = "";
                              $_triggTitleStr = "";
                              $_triggTitleStyle = "background-color:transparent;";
                              $_msgTextStr = "";
                              $_triggTextStr = "";
                              $_msgDopTextStr = "";
                              $_letterSubjText = "";
                              break;
                         case 1:
                              $_triggStr = '<span style="color:#FFC107">Менее 3-x дней</span>';
                              $_triggTitleStr = "осталось менее трёх дней";
                              $_triggTitleStyle = "background-color:#FF8C00;";
                              $_msgTextStr = "по данному документу установлен дедлайн и до истечения срока на его исполнение";
                              $_triggTextStr = "осталось менее трёх дней";
                              $_msgDopTextStr = "";
                              $_letterSubjText = "Срок исполнения истекает";
                              break;
                         case 2:
                              $_triggStr = '<span style="color:#FFC107 ">Менее 1-го дня</span>';
                              $_triggTitleStr = "осталось менее одного дня";
                              $_triggTitleStyle = "background-color:#800000;";
                              $_msgTextStr = "по данному документу установлен дедлайн и до истечения срока на его исполнение";
                              $_triggTextStr = "осталось менее одного дня";
                              $_msgDopTextStr = "";
                              $_letterSubjText = "Срок исполнения почти истёк";
                              break;
                         case 3:
                              $_triggStr = '<span style="color:#FFC107">Менее 1-го дня</span>';
                              $_triggTitleStr = "осталось менее одного дня";
                              $_triggTitleStyle = "background-color:#800000;";
                              $_msgTextStr = "по данному документу установлен дедлайн и до истечения срока на его исполнение";
                              $_triggTextStr = "осталось менее одного дня";
                              $_msgDopTextStr = "";
                              $_letterSubjText = "Срок исполнения почти истёк";
                              break;
                         case 4:
                              $_triggStr = '<span style="color:#DC3545 ">Срок истёк</span>';
                              $_triggTitleStr = "срок истёк";
                              $_triggTitleStyle = "background-color:#FF0000;";
                              $_msgTextStr = "по данному документу установлен дедлайн и на его исполнение";
                              $_triggTextStr = "срок истёк";
                              $_msgDopTextStr = "";
                              $_letterSubjText = "Срок исполнения истёк";
                              break;
                         default:
                              $_triggStr = "";
                              $_triggTitleStr = "";
                              $_triggTitleStyle = "background-color:transparent;";
                              $_msgTextStr = "";
                              $_triggTextStr = "";
                              $_msgDopTextStr = "";
                    }

                    # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
                    # Строка талицы списка >>>>>
                    $messageContent_Body_ListTableRow .= '<tr><td align="left" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:10px;padding-bottom:10px"><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p20b" align="center" style="padding:0;Margin:0;width:35px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:16px;color:#a9a9a9;font-size:12px">' . $_docID . '</p></td></tr></table></td><td class="es-hidden" style="padding:0;Margin:0;width:10px"></td></tr></table><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p20b" align="center" style="padding:0;Margin:0;width:295px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0;padding-bottom:5px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:16px;color:#333333;font-size:12px">' . $_docAbout . '</p></td></tr><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:16px;color:#333333;font-size:12px">' . $_docContragentName . '</p></td></tr><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:16px;color:#a9a9a9;font-size:12px">' . $_fileLink . '</p></td></tr></table></td><td class="es-hidden" style="padding:0;Margin:0;width:10px"></td></tr></table><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td align="center" class="es-m-p20b" style="padding:0;Margin:0;width:115px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0;padding-bottom:5px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:16px;color:#333333;font-size:12px">' . $_docDeadline . '</p></td></tr><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:16px;font-size:12px">' . $_triggStr . '</p></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:115px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:16px;color:#333333;font-size:12px">' . $_docIspolStr . '</p></td></tr></table></td></tr></table></tr>';
                    # Строка талицы списка <<<<<
                    # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
                    $messageContent_Body_ListTableRow .= '<tr><td align="left" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="padding:5px;Margin:0;font-size:0"><table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0;border-bottom:1px solid #cccccc;background:unset;height:1px;width:100%;margin:0px"></td></tr></table></td></tr></table></td></tr></table></td></tr>';
               }
               # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
               # Текст под талицей списка >>>>>
               $messageContent_Body_UnderTableMessage = '<tr><td align="left" style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px;padding-left:15px;padding-right:15px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:12px;color:#999999;font-size:11px">Важно помнить, что документ с истекшим сроком исполнения не обязательно является не выполненым. Возможно по нему просто забыли выставить отметку об исполнении, либо это сделали не все ответственные.</p></td></tr></table></td></tr></table></td></tr>';
               # Текст под талицей списка <<<<<
               # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
               #
               #
               # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
               # Закрывающий блок списка >>>>>
               $messageContent_Body_ListClosing = '</table></td></tr></table></td></tr>';
               # Закрывающий блок списка <<<<<
               # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
               #
               #
               # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
               # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
               # ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
               #
               #
               # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
               # Нижний блок >>>>>
               # Состоит из двух полных TABLE
               $messageContent_Body_BottomBlock = '<table cellpadding="0" cellspacing="0" class="es-footer" align="center"style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"><tr><td align="center" style="padding:0;Margin:0"><table class="es-footer-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#000000;width:600px" cellspacing="0" cellpadding="0" bgcolor="#000000" align="center"><tr><td align="left" bgcolor="#343a40" style="padding:0;Margin:0;padding-top:10px;padding-left:10px;padding-right:10px;background-color:#343a40"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td valign="top" align="center" style="padding:0;Margin:0;width:580px"><table style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-position:center top" width="100%" cellspacing="0" cellpadding="0" role="presentation"><tr><td class="es-m-txt-c" align="center" style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px;font-size:0px"><img src="https://atgs.ru/ext/img/portal-main-logo.svg" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="43"></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0;width:580px"><table style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-position:center top" width="100%" cellspacing="0" cellpadding="0" role="presentation"><tr><td align="center" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:17px;color:#ffffff;font-size:14px"><strong>АТГС.Портал</strong></p></td></tr><tr class="es-mobile-hidden"><td align="center" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:12px;color:#999999;font-size:11px">Корпоративные веб-сервисы АО "АтлантикТрансгазСистема" (с 2019 года)</p></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0;width:580px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-m-txt-c" align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;font-size:0px"><table class="es-table-not-adapt es-social" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td valign="top" align="center" style="padding:0;Margin:0;padding-right:10px"><a href="https://t.me/+Q7qZuzU5pevZ74YQ" target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Телеграм-канал АТГС.Портал" src="http://atgs.ru/ext/img/newicons/Telegram_Color_Logo.png" alt="Telegram" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td><td valign="top" align="center" style="padding:0;Margin:0"><a href="https://vk.com/atgsportal" target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="VK сообщество АТГС.Портал" src="http://atgs.ru/ext/img/newicons/VK_Color_Compact_Logo.png" alt="VK" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td></tr></table></td></tr></table></td></tr></table></td></tr><tr class="es-mobile-hidden"><td style="Margin:0;padding-left:5px;padding-right:5px;padding-top:10px;padding-bottom:10px;background-color:#111111" bgcolor="#111" align="left"><table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td valign="top" align="center" style="padding:0;Margin:0;width:590px"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" class="es-menu" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr class="links"><td align="center" valign="top" width="16.67%" id="esd-menu-id-0" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/portalnew" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Портал</a></td><td align="center" valign="top" width="16.67%" id="esd-menu-id-1" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/mailnew/index.php?type=main" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Почта</a></td><td align="center" valign="top" width="16.67%" id="esd-menu-id-2" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/dognet" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Договор</a></td><td align="center" valign="top" width="16.67%" id="esd-menu-id-2" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/ism" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">ИСМ</a></td><td align="center" valign="top" width="16.67%" id="esd-menu-id-2" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/hr" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Кадры</a></td><td align="center" valign="top" width="16.67%" id="esd-menu-id-2" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/eda" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Еда</a></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" class="es-content" align="center"style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"><tr><td class="es-info-area" align="center" style="padding:0;Margin:0"><table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#ffffff;width:600px" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"><tr><td style="Margin:0;padding-left:10px;padding-right:10px;padding-top:20px;padding-bottom:20px;background-color:#f1f1f1" bgcolor="#f1f1f1" align="left"><table class="es-left" cellspacing="0" cellpadding="0" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p20b" align="left" style="padding:0;Margin:0;width:245px"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-infoblock es-m-txt-c" valign="top" align="right" style="padding:0;Margin:0;padding-right:10px;line-height:0px;font-size:0px;color:#CCCCCC;width:30px"><img src="http://atgs.ru/ext/img/newicons/YC.avatar.logo-1-round.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="37" height="37"></td><td align="left" style="padding:0;Margin:0"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-infoblock es-m-txt-l" align="left" style="padding:0;Margin:0;line-height:14px;font-size:12px;color:#CCCCCC"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:14px;color:#999999;font-size:12px"><span style="color:#111">Ярослав Чугунов</span><br><span style="font-size:11px">Разработчик и администратор</span></p></td></tr></table></td></tr></table></td><td class="es-hidden" style="padding:0;Margin:0;width:5px"></td></tr></table><table class="es-left" cellspacing="0" cellpadding="0" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p20b" align="left" style="padding:0;Margin:0;width:157px"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-infoblock es-m-txt-c" valign="top" align="right" style="padding:0;Margin:0;padding-right:10px;line-height:0px;font-size:0px;color:#CCCCCC;width:30px"><img src="https://guocsp.stripocdn.email/content/guids/CABINET_b07b88d99bde76e46a2396d11f306432/images/26531551864324009.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="37" height="37"></td><td align="left" style="padding:0;Margin:0"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-infoblock es-m-txt-l" align="left" style="padding:0;Margin:0;line-height:14px;font-size:12px;color:#CCCCCC"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:14px;color:#111111;font-size:12px">+7 926 1124469</p></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:168px"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-infoblock es-m-txt-c" valign="top" align="right" style="padding:0;Margin:0;padding-right:10px;line-height:0px;font-size:0px;color:#CCCCCC;width:30px"><img src="https://guocsp.stripocdn.email/content/guids/CABINET_b07b88d99bde76e46a2396d11f306432/images/4801551865294269.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="37" height="37"></td><td align="left" style="padding:0;Margin:0"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-infoblock es-m-txt-l" align="left" style="padding:0;Margin:0;line-height:14px;font-size:12px;color:#CCCCCC"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:14px;color:#CCCCCC;font-size:12px"><a target="_blank" href="mailto:chugunov@atgs.ru" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#111111;font-size:12px">chugunov@atgs.ru</a></p></td></tr></table></td></tr></table></td></tr></table></tr><tr><td align="left" style="padding:0;Margin:0;padding-left:10px;padding-right:10px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td valign="top" align="center" style="padding:0;Margin:0;width:580px"><table style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-position:center top" width="100%" cellspacing="0" cellpadding="0" role="presentation"><tr><td class="es-infoblock" align="center" style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px;line-height:14px;font-size:12px;color:#CCCCCC"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:12px;color:#999999;font-size:10px"><em>Данное сообщение отправлено роботом. Не используйте адрес его отправителя для обратной связи.</em></p></td></tr></table></td></tr></table></td></tr></table></td></tr></table>';
               # Нижний блок <<<<<
               # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
               #
               #
               # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
               # Центральный закрывающий блок >>>>>
               $messageContent_Body_Closing = '</table></td></tr>';
               # Центральный закрывающий блок <<<<<
               # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
               #
               #
               # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
               # Основной закрывающий блок >>>>>
               $messageContent_End = '</table></div></body></html>';
               # Основной закрывающий блок <<<<<
               # ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
               #
               #
               #
               #
               #
               # 
               #
               # Mail address
               // $email_to = $__email;
               $email_to = 'chugunov@atgs.ru';
               $email_admin = 'chugunov@atgs.ru';
               $mail->addAddress($email_to);
               // $mail->addCC($email_admin);
               #
               # Content
               $mail->isHTML(true); // Set email format to HTML
               $mail->Subject = $subject;
               $mail->Body = $messageContent_Start . $messageContent_Top_FullBlock . $messageContent_Body_Opening . $messageContent_Body_IspolText . $messageContent_Body_ListOpening . $messageContent_Body_ListHeader . $messageContent_Body_ListTableHeader . $messageContent_Body_ListTableRow . $messageContent_Body_UnderTableMessage . $messageContent_Body_ListClosing . $messageContent_Body_BottomBlock . $messageContent_Body_Closing . $messageContent_End;
               $mail->AltBody = 'Ваш почтовый клиент не принимает сообщений в формате HTML. Вариант рассылки в формате PLAIN TEXT будет реализован позже.';
               #
               # Send the message, check for errors
               #
               # Открыли файл для записи данных в конец файла
               $filename = "/var/www/html/atgs-portal.local/www/mailnew/logs/PHPMailer_errors.log";
               #
               $koddel = "";
               $timestamp = $datetimenow;
               $msg_type = "MAIL_REMINDER";
               $msg_mode = "CRONTAB";
               $msg_status = "";
               $msg_log = "";
               $msg_comment = "";

               $msg_body = $messageContent_Start . $messageContent_Top_FullBlock . $messageContent_Body_Opening . $messageContent_Body_IspolText . $messageContent_Body_ListOpening . $messageContent_Body_ListHeader . $messageContent_Body_ListTableHeader . $messageContent_Body_ListTableRow . $messageContent_Body_UnderTableMessage . $messageContent_Body_ListClosing . $messageContent_Body_BottomBlock . $messageContent_Body_Closing . $messageContent_End;

               $user_ID = $_docContractorID;
               $user_kod = $__contrKOD;
               $user_name = $__FIO;
               $user_email = $__email;
               $initiator_script = $_SERVER['PHP_SELF'];
               if (is_writable($filename)) {
                    if (!$handle = fopen($filename, 'a')) {
                         $text = "Не могу открыть лог-файл для записи отчета об отправке";
                         echo "<span style='color:red; text-align:center'><i>" . $text . "</i></span>";
                         $msg_log .= "[bad: $text]";
                         $msg_status = "FOPEN_ERR";
                         fixMailingLog($koddel, $timestamp, $msg_type, $msg_mode, $msg_status, $msg_log, $msg_comment, $msg_body, $user_ID, $user_kod, $user_name, $user_email, $initiator_script);
                         exit;
                    }
                    if (!$mail->send()) {
                         $err  = $mail->ErrorInfo . PHP_EOL;
                         $text = date('Y-m-d h:i:s') . " : ошибка рассылки на ( $email_to ) : " . $err;
                         $msg_log .= "[bad: $text]";
                         // Записываем $somecontent в наш открытый файл.
                         if (fwrite($handle, $text) === false) {
                              $text = "Не могу произвести запись в лог файл";
                              echo "<span style='color:red; text-align:center'><i>$text</i></span>";
                              $msg_log .= "[bad: $text]";
                              $msg_status = "FLOG_ERR";
                              fixMailingLog($koddel, $timestamp, $msg_type, $msg_mode, $msg_status, $msg_log, $msg_comment, $msg_body, $user_ID, $user_kod, $user_name, $user_email, $initiator_script);
                              exit;
                         }
                         echo "<span style='color:red; text-align:center'><i>Ошибка при отправке сообщения : $err</i></span>";
                         $msg_log .= "[bad: Ошибка при отправке сообщения : $err]";
                         $msg_status = "MSEND_ERR";
                         fixMailingLog($koddel, $timestamp, $msg_type, $msg_mode, $msg_status, $msg_log, $msg_comment, $msg_body, $user_ID, $user_kod, $user_name, $user_email, $initiator_script);
                         fclose($handle);
                    } else {
                         $text = date('Y-m-d h:i:s') . " : сообщение на ( $email_to ) успешно отправлено" . PHP_EOL;
                         $msg_log .= "[good: $text]";
                         $msg_status = "MSEND_OK";
                         // Записываем $somecontent в наш открытый файл.
                         if (fwrite($handle, $text) === false) {
                              $text = "Не могу произвести запись в лог файл";
                              echo "<span style='color:red; text-align:center'><i>$text</i></span>";
                              $msg_log .= "[bad: $text]";
                              $msg_status = "MSEND_OK (FLOG_ERR)";
                              fixMailingLog($koddel, $timestamp, $msg_type, $msg_mode, $msg_status, $msg_log, $msg_comment, $msg_body, $user_ID, $user_kod, $user_name, $user_email, $initiator_script);
                              exit;
                         }
                         $text = "Сообщение успешно отправлено, запись в лог-файл произведена";
                         echo "<span style='color:green; text-align:center'><i>$text</i></span>";
                         $msg_log .= "[good: $text]";
                         $msg_status = "MSEND_OK (FLOG_OK)";
                         fclose($handle);
                         // Вставляем запись в log рассылок входящей почты
                         $_SQLReq_MailIncomingLog_Insert = "INSERT INTO mailbox_incoming_logControl (timestamp, action, koddocmail, controlTrigger, userid, userkod, usermail, description, sqlrequest, comment) VALUES ('{$datetimenow}', 'MAIL_REMIND', '{$__koddocmail}', '{$_trigg}', '{$_docContractorID}', '', '{$email_to}', 'Напоминание о необходимости исполнения по входящему документу', '{$_SQLReq_Docs_Clr}', '')";
                         $_QRY_MailIncomingLog_Insert = mysqlQuery($_SQLReq_MailIncomingLog_Insert);
                         //
                         //
                         $_reqINS = mysqlQuery("INSERT INTO mailbox_incoming_logChanges (timestamp, action, koddocmail, userid, kodispol, oldSettings, newSettings, changes, changesText, changesCount) VALUES ('{$datetimenow}', 'MAIL_REMIND', '{$__koddocmail}', '999999', '{$_docContractorID}', null, null, '{$_trigg}', 'Ответственному ( <span class=text-success>{$__FIO}</span> ) отправлено автоматическое email-уведомление на <span class=text-success>{$email_to}</span> об истечении срока исполнения ({$_triggStr})', null)");

                         $msg_comment .= "[desc: " . $_letterSubjText . " (" . $_triggStr . ")]";
                         echo '<br><br><br><br><br>' . $_trigg1 . ' : ' . $_trigg2 . ' : ' . $_intDiffSeconds . '<br><br><br><br><br>';
                    }
               } else {
                    $text = "Лог-файл недоступен для записи";
                    echo "<span style='color:red; text-align:center'><i>$text</i></span>";
                    $msg_log .= "[bad: $text]";
               }
               fixMailingLog($koddel, $timestamp, $msg_type, $msg_mode, $msg_status, $msg_log, $msg_comment, $msg_body, $user_ID, $user_kod, $user_name, $user_email, $initiator_script);
               #
               # :::
               // Clear all addresses and attachments for next loop
               $mail->ClearAllRecipients();
               $mail->ClearAddresses();
               #
               #
               # Вставляем запись в log рассылок входящей почты
               $_SQLReq_Insert1 = "INSERT INTO mailbox_IspolAutoReminder_log (send_datetime, send_koddocmail, send_ispolIDs, send_ispolNames, send_ispolEmails, send_comment) VALUES ('{$datetimenow}', '{$__koddocmail}', '', '', '', 'Crontab have completed')";
               $_QRY_INSERT = mysqlQuery($_SQLReq_Insert1);
               if ($_QRY_INSERT) {
                    echo "<br>Record to table 'mailbox_IspolAutoReminder_log' is inserted and mailing is perfomed at " . date("Y-m-d H:m:i") . " : success<br>";
               } else {
                    echo "<br>Crontab is not performed at " . date("Y-m-d H:m:i") . " ...<br>";
               }
          }
     }
}
unset($_IS_CRONTAB);
