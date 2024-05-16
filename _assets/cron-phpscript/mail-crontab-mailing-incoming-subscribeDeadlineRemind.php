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
$datenow     = date("Y-m-d");
$datetime1   = date("Y-m-d H:i:s", strtotime('-1 month', strtotime($datetimenow)));
$datetime2   = date("Y-m-d H:i:s", strtotime($datetimenow));
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
#

$messageConstBlockTop = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" style="font-family:arial, helvetica neue, helvetica, sans-serif"><head><meta charset="UTF-8"><meta content="width=device-width, initial-scale=1" name="viewport"><meta name="x-apple-disable-message-reformatting"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta content="telephone=no" name="format-detection"><title>АТГС.Портал / Корпоративные сервисы</title><xml><o:OfficeDocumentSettings><o:AllowPNG></o:AllowPNG><o:PixelsPerInch></o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]--><style type="text/css">#outlook a{padding:0}.es-button{mso-style-priority:100!important;text-decoration:none!important}a[x-apple-data-detectors]{color:inherit!important;text-decoration:none!important;font-size:inherit!important;font-family:inherit!important;font-weight:inherit!important;line-height:inherit!important}.es-desk-hidden{display:none;float:left;overflow:hidden;width:0;max-height:0;line-height:0;mso-hide:all}.es-button-border:hover a.es-button, .es-button-border:hover button.es-button{background:#56d66b!important}.es-button-border:hover{border-color:#42d159 #42d159 #42d159 #42d159!important;background:#56d66b!important}td .es-button-border:hover a.es-button-1{background:#4b545c!important}td .es-button-border-2:hover{background:#4b545c!important;border-style:solid solid solid solid!important;border-color:#42d159 #42d159 #42d159 #42d159!important}@media only screen and (max-width:600px){p, ul li, ol li, a{ line-height:150%!important} h1, h2, h3, h1 a, h2 a, h3 a{ line-height:120%} h1{ font-size:30px!important; text-align:left} h2{ font-size:24px!important; text-align:left} h3{ font-size:20px!important; text-align:left} .es-header-body h1 a, .es-content-body h1 a, .es-footer-body h1 a{ font-size:30px!important; text-align:left} .es-header-body h2 a, .es-content-body h2 a, .es-footer-body h2 a{ font-size:24px!important; text-align:left} .es-header-body h3 a, .es-content-body h3 a, .es-footer-body h3 a{ font-size:20px!important; text-align:left} .es-menu td a{ font-size:14px!important} .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a{ font-size:14px!important} .es-content-body p, .es-content-body ul li, .es-content-body ol li, .es-content-body a{ font-size:14px!important} .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a{ font-size:14px!important} .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a{ font-size:12px!important} *[class="gmail-fix"]{ display:none!important} .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3{ text-align:center!important} .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3{ text-align:right!important} .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3{ text-align:left!important} .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img{ display:inline!important} .es-button-border{ display:inline-block!important} a.es-button, button.es-button{ font-size:18px!important; display:inline-block!important} .es-adaptive table, .es-left, .es-right{ width:100%!important} .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header{ width:100%!important; max-width:600px!important} .es-adapt-td{ display:block!important; width:100%!important} .adapt-img{ width:100%!important; height:auto!important} .es-m-p0{ padding:0!important} .es-m-p0r{ padding-right:0!important} .es-m-p0l{ padding-left:0!important} .es-m-p0t{ padding-top:0!important} .es-m-p0b{ padding-bottom:0!important} .es-m-p20b{ padding-bottom:20px!important} .es-mobile-hidden, .es-hidden{ display:none!important} tr.es-desk-hidden, td.es-desk-hidden, table.es-desk-hidden{ width:auto!important; overflow:visible!important; float:none!important; max-height:inherit!important; line-height:inherit!important} tr.es-desk-hidden{ display:table-row!important} table.es-desk-hidden{ display:table!important} td.es-desk-menu-hidden{ display:table-cell!important} .es-menu td{ width:1%!important} table.es-table-not-adapt, .esd-block-html table{ width:auto!important} table.es-social{ display:inline-block!important} table.es-social td{ display:inline-block!important} .es-desk-hidden{ display:table-row!important; width:auto!important; overflow:visible!important; max-height:inherit!important} .es-m-p5{ padding:5px!important} .es-m-p5t{ padding-top:5px!important} .es-m-p5b{ padding-bottom:5px!important} .es-m-p5r{ padding-right:5px!important} .es-m-p5l{ padding-left:5px!important} .es-m-p10{ padding:10px!important} .es-m-p10t{ padding-top:10px!important} .es-m-p10b{ padding-bottom:10px!important} .es-m-p10r{ padding-right:10px!important} .es-m-p10l{ padding-left:10px!important} .es-m-p15{ padding:15px!important} .es-m-p15t{ padding-top:15px!important} .es-m-p15b{ padding-bottom:15px!important} .es-m-p15r{ padding-right:15px!important} .es-m-p15l{ padding-left:15px!important} .es-m-p20{ padding:20px!important} .es-m-p20t{ padding-top:20px!important} .es-m-p20r{ padding-right:20px!important} .es-m-p20l{ padding-left:20px!important} .es-m-p25{ padding:25px!important} .es-m-p25t{ padding-top:25px!important} .es-m-p25b{ padding-bottom:25px!important} .es-m-p25r{ padding-right:25px!important} .es-m-p25l{ padding-left:25px!important} .es-m-p30{ padding:30px!important} .es-m-p30t{ padding-top:30px!important} .es-m-p30b{ padding-bottom:30px!important} .es-m-p30r{ padding-right:30px!important} .es-m-p30l{ padding-left:30px!important} .es-m-p35{ padding:35px!important} .es-m-p35t{ padding-top:35px!important} .es-m-p35b{ padding-bottom:35px!important} .es-m-p35r{ padding-right:35px!important} .es-m-p35l{ padding-left:35px!important} .es-m-p40{ padding:40px!important} .es-m-p40t{ padding-top:40px!important} .es-m-p40b{ padding-bottom:40px!important} .es-m-p40r{ padding-right:40px!important} .es-m-p40l{ padding-left:40px!important} .h-auto{ height:auto!important}}</style></head><body style="width:100%;font-family:arial, helvetica neue, helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0"><div class="es-wrapper-color" style="background-color:#F6F6F6"><!--[if gte mso 9]><v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t"><v:fill type="tile" color="#f6f6f6"></v:fill></v:background><![endif]--><table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6"><tr><td valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-header" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"><tr><td align="center" style="padding:0;Margin:0"><table bgcolor="#ffffff" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px"><tr><td class="es-m-p5t es-m-p5b es-m-p0r es-m-p0l" align="left" bgcolor="#343a40" style="padding:10px;Margin:0;background-color:#343a40"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0;width:580px"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-m-txt-c" valign="top" align="right" style="Margin:0;padding-top:10px;padding-bottom:10px;padding-left:10px;padding-right:20px;width:30px;font-size:0px"><img src="http://atgs.ru/ext/img/newicons/portal.service-newmail.favicon.ico" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="72"></td><td align="left" style="padding:0;Margin:0"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-m-txt-l" align="left" style="padding:0;Margin:0;padding-left:5px;padding-right:10px"><h1 style="Margin:0;line-height:32px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:32px;font-style:normal;font-weight:normal;color:#ffffff"><strong>Почта АТГС</strong></h1><h1 style="Margin:0;line-height:16px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:16px;font-style:normal;font-weight:normal;color:#ffffff">Входящая и исходящая корпоративная корреспонденция</h1></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td align="left" bgcolor="#111" style="padding:0;Margin:0;background-color:#111111"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr class="es-mobile-hidden"><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0"><table class="es-menu" width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr class="links"><td style="Margin:0;padding-left:5px;padding-right:5px;padding-top:15px;padding-bottom:15px;border:0" width="33.33%" valign="top" bgcolor="transparent" align="center"><a target="_blank" href="http://192.168.1.89/mailnew/index.php?type=main" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:14px;font-weight:normal">ГЛАВНАЯ</a></td><td style="Margin:0;padding-left:5px;padding-right:5px;padding-top:15px;padding-bottom:15px;border:0" width="33.33%" valign="top" bgcolor="transparent" align="center"><a target="_blank" href="http://192.168.1.89/mailnew/index.php?type=in&mode=thisyear" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:14px;font-weight:normal">ВХОДЯЩИЕ</a></td><td style="Margin:0;padding-left:5px;padding-right:5px;padding-top:15px;padding-bottom:15px;border:0" width="33.33%" valign="top" bgcolor="transparent" align="center"><a target="_blank" href="http://192.168.1.89/mailnew/index.php?type=out&mode=thisyear" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:14px;font-weight:normal">ИСХОДЯЩИЕ</a></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table>';
#
#
# ----- ----- ----- ----- -----
#
#
$messageConstBlockBottom = '
<table cellpadding="0" cellspacing="0" class="es-footer" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"><tr><td align="center" style="padding:0;Margin:0"><table class="es-footer-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#000000;width:600px" cellspacing="0" cellpadding="0" bgcolor="#000000" align="center"><tr><td align="left" bgcolor="#343a40" style="padding:0;Margin:0;padding-top:10px;padding-left:10px;padding-right:10px;background-color:#343a40"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td valign="top" align="center" style="padding:0;Margin:0;width:580px"><table style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-position:center top" width="100%" cellspacing="0" cellpadding="0" role="presentation"><tr><td class="es-m-txt-c" align="center" style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px;font-size:0px"><img src="https://atgs.ru/ext/img/portal-main-logo.svg" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="43"></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0;width:580px"><table style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-position:center top" width="100%" cellspacing="0" cellpadding="0" role="presentation"><tr><td align="center" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:17px;color:#ffffff;font-size:14px"><strong>АТГС.Портал</strong></p></td></tr><tr class="es-mobile-hidden"><td align="center" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:12px;color:#999999;font-size:11px">Корпоративные веб-сервисы АО "АтлантикТрансгазСистема" (с 2019 года)</p></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0;width:580px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-m-txt-c" align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;font-size:0px"><table class="es-table-not-adapt es-social" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td valign="top" align="center" style="padding:0;Margin:0;padding-right:10px"><a href="https://t.me/+Q7qZuzU5pevZ74YQ" target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Телеграм-канал АТГС.Портал" src="http://atgs.ru/ext/img/newicons/Telegram_Color_Logo.png" alt="Telegram" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td><td valign="top" align="center" style="padding:0;Margin:0"><a href="https://vk.com/atgsportal" target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="VK сообщество АТГС.Портал" src="http://atgs.ru/ext/img/newicons/VK_Color_Compact_Logo.png" alt="VK" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td></tr></table></td></tr></table></td></tr></table></td></tr><tr class="es-mobile-hidden"><td style="Margin:0;padding-left:5px;padding-right:5px;padding-top:10px;padding-bottom:10px;background-color:#111111" bgcolor="#111" align="left"><table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td valign="top" align="center" style="padding:0;Margin:0;width:590px"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" class="es-menu" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr class="links"><td align="center" valign="top" width="16.67%" id="esd-menu-id-0" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/portalnew" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Портал</a></td><td align="center" valign="top" width="16.67%" id="esd-menu-id-1" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/mailnew/index.php?type=main" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Почта</a></td><td align="center" valign="top" width="16.67%" id="esd-menu-id-2" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/dognet" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Договор</a></td><td align="center" valign="top" width="16.67%" id="esd-menu-id-2" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/ism" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">ИСМ</a></td><td align="center" valign="top" width="16.67%" id="esd-menu-id-2" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/hr" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Кадры</a></td><td align="center" valign="top" width="16.67%" id="esd-menu-id-2" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/eda" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Еда</a></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"><tr><td class="es-info-area" align="center" style="padding:0;Margin:0"><table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#ffffff;width:600px" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"><tr><td style="Margin:0;padding-left:10px;padding-right:10px;padding-top:20px;padding-bottom:20px;background-color:#f1f1f1" bgcolor="#f1f1f1" align="left"><table class="es-left" cellspacing="0" cellpadding="0" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p20b" align="left" style="padding:0;Margin:0;width:245px"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-infoblock es-m-txt-c" valign="top" align="right" style="padding:0;Margin:0;padding-right:10px;line-height:0px;font-size:0px;color:#CCCCCC;width:30px"><img src="http://atgs.ru/ext/img/newicons/YC.avatar.logo-1-round.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="37" height="37"></td><td align="left" style="padding:0;Margin:0"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-infoblock es-m-txt-l" align="left" style="padding:0;Margin:0;line-height:14px;font-size:12px;color:#CCCCCC"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:14px;color:#999999;font-size:12px"><span style="color:#111">Ярослав Чугунов</span><br><span style="font-size:11px">Разработчик и администратор</span></p></td></tr></table></td></tr></table></td><td class="es-hidden" style="padding:0;Margin:0;width:5px"></td></tr></table><table class="es-left" cellspacing="0" cellpadding="0" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p20b" align="left" style="padding:0;Margin:0;width:157px"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-infoblock es-m-txt-c" valign="top" align="right" style="padding:0;Margin:0;padding-right:10px;line-height:0px;font-size:0px;color:#CCCCCC;width:30px"><img src="https://guocsp.stripocdn.email/content/guids/CABINET_b07b88d99bde76e46a2396d11f306432/images/26531551864324009.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="37" height="37"></td><td align="left" style="padding:0;Margin:0"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-infoblock es-m-txt-l" align="left" style="padding:0;Margin:0;line-height:14px;font-size:12px;color:#CCCCCC"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:14px;color:#111111;font-size:12px">+7 926 1124469</p></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:168px"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-infoblock es-m-txt-c" valign="top" align="right" style="padding:0;Margin:0;padding-right:10px;line-height:0px;font-size:0px;color:#CCCCCC;width:30px"><img src="https://guocsp.stripocdn.email/content/guids/CABINET_b07b88d99bde76e46a2396d11f306432/images/4801551865294269.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="37" height="37"></td><td align="left" style="padding:0;Margin:0"><table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-infoblock es-m-txt-l" align="left" style="padding:0;Margin:0;line-height:14px;font-size:12px;color:#CCCCCC"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:14px;color:#CCCCCC;font-size:12px"><a target="_blank" href="mailto:chugunov@atgs.ru" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#111111;font-size:12px">chugunov@atgs.ru</a></p></td></tr></table></td></tr></table></td></tr></table></tr><tr><td align="left" style="padding:0;Margin:0;padding-left:10px;padding-right:10px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td valign="top" align="center" style="padding:0;Margin:0;width:580px"><table style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-position:center top" width="100%" cellspacing="0" cellpadding="0" role="presentation"><tr><td class="es-infoblock" align="center" style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px;line-height:14px;font-size:12px;color:#CCCCCC"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:12px;color:#999999;font-size:10px"><em>Данное сообщение отправлено роботом. Не используйте адрес его отправителя для обратной связи.</em></p></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></div></body></html>';
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
// Выорка по разнице (TIMESTAMPDIFF) между текущей датой и дедлайном
$_SQLReq_Select1 = "
SELECT * FROM mailbox_incoming
WHERE inbox_docDate BETWEEN '$datetime1' AND '$datetime2'
AND (inbox_controlIspolActive = '1' AND inbox_controlIspolCheckout <> '1')
AND ( ((TIMESTAMPDIFF(SECOND, '$datetimenow', inbox_docDateDeadline) <= 0)) )
AND inbox_docContractorID != ''
";
$sqlClear_select1 = str_replace(array('\'', '"'), '`', $_SQLReq_Select1);
echo '<br>' . $sqlClear_select1 . '<br>' . REMINDER_1 . '<br>' . REMINDER_2;
$_SQLReq_Insert1 = "";
$_QRY_SELECT     = mysqlQuery($_SQLReq_Select1);
//
if (isset($_QRY_SELECT)) {
     while ($_ROW_SELECT = mysqli_fetch_assoc($_QRY_SELECT)) {
          //
          $__docID       = $_ROW_SELECT['inbox_docID'];
          $__docFileID   = $_ROW_SELECT['inbox_docFileID'];
          $__docFileIDadd = $_ROW_SELECT['inbox_docFileIDadd'];
          $__contrID     = $_ROW_SELECT['inbox_docContractorID'];
          // $__contrKOD    = $_ROW_SELECT['inbox_docContractor_kodzayvispol'];
          $__contrKOD    = $_ROW_SELECT['inbox_controlIspolMailUserListNotifyDL'];
          $__deadline    = $_ROW_SELECT['inbox_docDateDeadline'];
          $__ispolMULTI  = $_ROW_SELECT['inbox_docContractorMULTI'];
          $__senderORG   = $_ROW_SELECT['inbox_docSender'];
          $__senderNAME  = $_ROW_SELECT['inbox_docSenderName'];
          $__sourceID    = $_ROW_SELECT['inbox_docSourceID'];
          $__sourceDate  = date_create($_ROW_SELECT['inbox_docSourceDate']);
          $__docABOUT    = $_ROW_SELECT['inbox_docAbout'];
          $__docTYPE     = $_ROW_SELECT['inbox_docTypeSTR'];
          $__toSendEmail = $_ROW_SELECT['toSendEmail'];
          //
          $__koddocmail  = $_ROW_SELECT['koddocmail'];
          $__ispolIDs    = $_ROW_SELECT['inbox_docContractorID'];
          $__ispolNames  = $_ROW_SELECT['inbox_docContractorSTR'];
          $__ispolEmails = $_ROW_SELECT['inbox_docContractorEMAIL'];
          //
          $__controlActive = $_ROW_SELECT['inbox_controlIspolActive'];
          $__controlStatus = $_ROW_SELECT['inbox_controlIspolStatus'];
          //        
          $__reminder1 = $_ROW_SELECT['inbox_controlIspolMailReminder1'];
          $__reminder2 = $_ROW_SELECT['inbox_controlIspolMailReminder2'];
          $__notifyDL = $_ROW_SELECT['inbox_controlIspolMailNotifyDL'];
          //
          $arrIspolIDs    = explode(",", $__ispolIDs);
          $arrIspolNames  = explode(",", $__ispolNames);
          $arrIspolEmails = explode(",", $__ispolEmails);
          $arrispol       = explode(",", $__contrKOD);
          $arrFileIDAdd   = explode(",", $__docFileIDadd);
          //
          $diff = date_diff(date_create($datetimenow), date_create($__deadline));
          $deadline = date_create($__deadline);
          $_docDeadlineRemains = $diff->format('%a (полные дни)');
          // $_docDeadlineRemains = round($diff / (60 * 60 * 24));
          $_docDeadlineDate        = date_format($deadline, 'd.m.Y H:i');
          $date1                   = new DateTime($datetimenow);
          $date2                   = $deadline;
          // $_intDiffSeconds         = intval($diff->format('%h'));
          //
          $_docTitle = "Входящий документ";
          //
          $_docDeadline = validateDate($_ROW_SELECT['inbox_docDateDeadline'], "Y-m-d") ? date('d.m.Y', strtotime($_ROW_SELECT['inbox_docDateDeadline'])) : "---";
          $_sourceDate = validateDate($_ROW_SELECT['inbox_docSourceDate'], "Y-m-d") ? date('d.m.Y', strtotime($_ROW_SELECT['inbox_docSourceDate'])) : "---";

          $controlActiveStr = $__controlActive ? '<span style="color:green">Контроль активен</span>' : '<span style="color:#999999">Контроль не активен</span>';
          // Статус исполнения 
          switch ($__controlStatus) {
               case 0:
                    $controlStatusStr = '<span style="color:#999999">Режим контроля исполнения не активен</span>';
                    break;
               case 1:
                    $controlStatusStr = '<span style="color:green">Режим контроля исполнения активен и документ выполнен полностью (всеми исполнителями)</span>';
                    break;
               case 2:
                    $controlStatusStr = '<span style="color:#FF8C00">Режим контроля исполнения активен, но документ выполнен не полностью (не всеми исполнителями)</span>';
                    break;
               case 3:
                    $controlStatusStr = '<span style="color:#A94442">Контроль активен и по документу истёк срок исполнения</span>';
                    break;
               default:
                    $controlStatusStr = "---";
          }

          $_intDiffSeconds = strtotime($__deadline) - strtotime($datetimenow);

          $_docDeadlineRemainsSrok = intval($diff->format('%a')) < 1 ? "менее суток" : $diff->format('%a ') . getNumEnding(intval($diff->format('%a')), array('день', 'дня', 'дней'));
          $_docDeadlineRemainsText = ($date2 < $date1) ? '<span style="color: #ffffff; background-color: #E2574C;">Выполнение просрочено на ' . $diff->format('%a ') . getNumEnding(intval($diff->format('%a')), array('день', 'дня', 'дней')) . ' (полных)</span>' : '<span style="color: #ffffff; background-color: #000000;">На выполнение осталось ' . $_docDeadlineRemainsSrok . ' (полных)</span>';
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
          $messageContent_docID = '<tr><td align="center" style="padding:0;Margin:0"><table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px"><tr><td align="left" style="padding:0;Margin:0;padding-top:0px;padding-bottom:5px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-m-txt-c" align="center" bgcolor="" style="padding:10px;Margin:0"><h2 style="Margin:0;line-height:38px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:32px;font-style:normal;font-weight:normal;color:#000000;text-align:center"><strong>' . $_docTitle . '</strong></h2><h2 style="Margin:0;line-height:24px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:20px;font-style:normal;font-weight:normal;color:#000000;text-align:center"><strong><span style="color:#000000">№ 1-2/' . $__docID . '</span></strong></h2></td></tr><tr><td align="center" class="es-m-txt-c" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:21px;color:#cccccc;font-size:14px">DocID ' . $__koddocmail . '</p></td></tr></table></td></tr></table></td></tr>';
          # ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
          if ("" != $__contrKOD && null != $__contrKOD) {
               foreach ($arrispol as $value) {
                    $__contractorDATA1 = $db_handle->runQuery("SELECT id, emailaddress, namezayvfio FROM mailbox_sp_users WHERE kodispol=" . $value);
                    $__contractorDATA2 = $db_handle->runQuery("SELECT firstname, middlename, lastname FROM users WHERE id=" . $__contractorDATA1[0]['id']);
                    $__contractorID    = $__contractorDATA1[0]['id'];
                    $__fileName        = "";
                    $__fileURL         = "";
                    $__fileLink        = "---";
                    // Определяем триггер
                    if ($_intDiffSeconds > 0) {
                         $_trigg1 = $__reminder1 * ($_intDiffSeconds <= REMINDER_1 ? 1 : 0);
                         $_trigg2 = $__reminder2 * ($_intDiffSeconds <= REMINDER_2 ? 1 : 0);
                         $_trigg = $_trigg1 + 2 * $_trigg2;
                    } else {
                         $_trigg1 = -1;
                         $_trigg2 = -1;
                         $_trigg = 4;
                    }
                    //
                    //
                    //
                    $_msgTitleStr = "Контроль исполнения (по подписке)";
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
                              $_triggStr = "менее 3-x дней";
                              $_triggTitleStr = "осталось менее трёх дней";
                              $_triggTitleStyle = "background-color:#FF8C00;";
                              $_msgTextStr = "по данному документу установлен дедлайн и до истечения срока на его исполнение";
                              $_triggTextStr = "осталось менее трёх дней";
                              $_msgDopTextStr = "";
                              $_letterSubjText = "Срок исполнения истекает";
                              break;
                         case 2:
                              $_triggStr = "менее 1-го дня";
                              $_triggTitleStr = "осталось менее одного дня";
                              $_triggTitleStyle = "background-color:#800000;";
                              $_msgTextStr = "по данному документу установлен дедлайн и до истечения срока на его исполнение";
                              $_triggTextStr = "осталось менее одного дня";
                              $_msgDopTextStr = "";
                              $_letterSubjText = "Срок исполнения почти истёк";
                              break;
                         case 3:
                              $_triggStr = "менее 1-го дня, менее 3-х дней";
                              $_triggTitleStr = "осталось менее одного дня";
                              $_triggTitleStyle = "background-color:#800000;";
                              $_msgTextStr = "по данному документу установлен дедлайн и до истечения срока на его исполнение";
                              $_triggTextStr = "осталось менее одного дня";
                              $_msgDopTextStr = "";
                              $_letterSubjText = "Срок исполнения почти истёк";
                              break;
                         case 4:
                              $_triggStr = "срок истёк";
                              $_triggTitleStr = "срок истёк";
                              $_triggTitleStyle = "background-color:#FF0000;";
                              $_msgTextStr = "по данному документу установлен дедлайн и на его исполнение";
                              $_triggTextStr = "срок истёк";
                              $_msgDopTextStr = "Вы получили это сообщение, потому что подписались на уведомление об истечении срока по данному документу.";
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
                    #
                    # Тема сообения
                    $subjectTxt = "Почта АТГС [Входящие] : КИ (по подписке) : Документ № 1-2/" . $__docID . " : " . $_letterSubjText;
                    $subject    = "=?utf-8?B?" . base64_encode($subjectTxt) . "?=";
                    #
                    # ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
                    if ("" != $__docFileID) {
                         $__contractorDATA3 = $db_handle->runQuery("SELECT file_originalname, file_url FROM mailbox_incoming_files WHERE id=" . $__docFileID);
                         $__fileName        = (!empty($__contractorDATA3) && "" != $__contractorDATA3[0]['file_originalname']) ? $__contractorDATA3[0]['file_originalname'] : "---";
                         $__fileURL         = !empty($__contractorDATA3) ? $__contractorDATA3[0]['file_url'] : "";
                         $__fileLink        = ("" != $__fileURL) ? '<a href="' . $__fileURL . '" title="" style="text-decoration:none !important">' . $__fileName . '</a>' : "---";
                    }
                    $detailsAddLinks = "";
                    if ("" != $__docFileIDadd) {
                         foreach ($arrFileIDAdd as &$value) {
                              if ($value != "") {
                                   $__QRY_FileIDadd = $db_handle->runQuery("SELECT file_originalname, file_url FROM mailbox_incoming_files WHERE id=" . $value);
                                   $__fileAddName = (!empty($__QRY_FileIDadd) && "" != $__QRY_FileIDadd[0]['file_originalname']) ? $__QRY_FileIDadd[0]['file_originalname'] : "---";
                                   $__fileAddURL = !empty($__QRY_FileIDadd) ? $__QRY_FileIDadd[0]['file_url'] : "";
                                   $__fileAddLink = ("" != $__fileAddURL) ? '<a href="' . $__fileAddURL . '" title="" style="text-decoration:none !important">' . $__fileAddName . '</a>' : "---";
                                   $detailsAddLinks .= $__fileAddLink . ", ";
                              }
                         }
                         $detailsAddLinks = rtrim($detailsAddLinks, ', ');
                    } else {
                         $detailsAddLinks .= "Нет дополнительных файлов";
                    }
                    if ($__contractorDATA1 && $__contractorDATA2) {
                         $__email = $__contractorDATA1[0]['emailaddress'];
                         $__IO    = !empty($__contractorDATA2) ? $__contractorDATA2[0]['firstname'] . " " . $__contractorDATA2[0]['middlename'] : '';
                         $__FIO   = $__contractorDATA1[0]['namezayvfio'];
                         #
                         # Часть сообщения
                         $_msgText = '<span style="font-size:28px">' . $__IO . ', </span><br><span style="color: #E2574C">истекает срок исполнения</span> по входящему документу №' . $__docID . ', по которому Вы являетесь ответственным';
                         #
                         #
                         # ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
                         #
                         #
                         $messageContent_ispolText = '<tr><td align="left" style="Margin:0;padding-bottom:5px;padding-top:10px;padding-left:10px;padding-right:10px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:580px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" class="es-m-txt-c" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:29px;color:#111111;font-size:24px" class="p_description">' . $_msgTitleStr . '</p></td></tr><tr><td align="center" class="es-m-txt-c" style="padding:0;Margin:0;padding-bottom:25px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:38px;color:#ffffff;font-size:32px" class="p_description"><b><span style="' . $_triggTitleStyle . 'text-transform:uppercase;">' . $_triggTitleStr . '</span></b></p></td></tr><tr><td align="center" class="es-m-txt-c" style="padding:0;Margin:0"><h1 style="Margin:0;line-height:34px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:28px;font-style:normal;font-weight:normal;color:#111111;text-align:center"><strong>' . $__IO . ',</strong></h1></td></tr><tr><td align="center" class="es-m-txt-c" style="padding:0;Margin:0;padding-bottom:15px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:22px;color:#999999;font-size:18px" class="p_description">' . $_msgTextStr . ' ' . $_triggTextStr . '. ' . $_msgDopTextStr . '</p></td></tr></table></td></tr></table></td></tr>';
                         #
                         #
                         # ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
                         #
                         # 
                         #
                         $messageContent_docInfo = '<tr><td align="left" bgcolor="#111" style="padding:10px;Margin:0;background-color:#111111"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:580px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><h3 style="Margin:0;line-height:22px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:18px;font-style:normal;font-weight:normal;color:#ffffff"><strong>Информация о документе</strong></h3></td></tr></table></td></tr></table></td></tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-bottom:5px;padding-top:20px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td align="left" style="padding:0;Margin:0;width:304px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><h4 style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:14px">Описание документа</h4></td></tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:10px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:246px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:17px;color:#333333;font-size:14px">' . $__docABOUT . '</p></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0px"><table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0;border-bottom:1px solid #cccccc;background:none;height:1px;width:100%;margin:0px"></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td align="left" style="padding:0;Margin:0;width:304px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><h4 style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:14px">Основной файл</h4></td></tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:10px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:246px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:17px;color:#333333;font-size:14px">' . $__fileLink . '</p></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0;background-position:center center"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0px"><table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0;border-bottom:1px solid #cccccc;background:none;height:1px;width:100%;margin:0px"></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td align="left" style="padding:0;Margin:0;width:304px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><h4 style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:14px">Дополнительные файлы</h4></td></tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:10px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:246px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:17px;color:#333333;font-size:14px">' . $detailsAddLinks . '</p></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0px"><table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0;border-bottom:1px solid #cccccc;background:none;height:1px;width:100%;margin:0px"></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td align="left" style="padding:0;Margin:0;width:304px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><h4 style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:14px">Тип документа</h4></td></tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:10px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:246px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:17px;color:#333333;font-size:14px">' . $__docTYPE . '</p></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0px"><table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0;border-bottom:1px solid #cccccc;background:none;height:1px;width:100%;margin:0px"></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td align="left" style="padding:0;Margin:0;width:304px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><h4 style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:14px">Организация-отправитель</h4></td></tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:10px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:246px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:17px;color:#333333;font-size:14px">' . $__senderORG . '</p></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0px"><table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0;border-bottom:1px solid #cccccc;background:none;height:1px;width:100%;margin:0px"></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td align="left" style="padding:0;Margin:0;width:304px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><h4 style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:14px">Исходящий номер и дата</h4></td></tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:10px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:246px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:17px;color:#333333;font-size:14px">' . $__sourceID . ', ' . $_sourceDate . '</p></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0px"><table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0;border-bottom:1px solid #cccccc;background:none;height:1px;width:100%;margin:0px"></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td align="left" style="padding:0;Margin:0;width:304px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><h4 style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:14px">Контроль исполнения</h4></td></tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:10px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:246px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:17px;color:#333333;font-size:14px">' . $controlActiveStr . '</p></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0px"><table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0;border-bottom:1px solid #cccccc;background:none;height:1px;width:100%;margin:0px"></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td align="left" style="padding:0;Margin:0;width:304px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><h4 style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:14px">Дедлайн</h4></td></tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:10px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:246px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:17px;color:#333333;font-size:14px">' . $_docDeadline . '</p></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0px"><table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0;border-bottom:1px solid #cccccc;background:none;height:1px;width:100%;margin:0px"></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td align="left" style="padding:0;Margin:0;width:304px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><h4 style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:14px">Статус исполнения</h4></td></tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:10px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:246px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:17px;color:#333333;font-size:14px">' . $controlStatusStr . '</p></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0px"><table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td style="padding:0;Margin:0;border-bottom:1px solid #cccccc;background:none;height:1px;width:100%;margin:0px"></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td align="left" style="padding:0;Margin:0;width:304px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><h4 style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:14px">Список исполнителей</h4></td></tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:10px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td align="left" style="padding:0;Margin:0;width:246px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:17px;color:#333333;font-size:14px">' . $__ispolNames . '</p></td></tr></table></td></tr></table></td></tr></table></td></tr><tr><td align="left" style="Margin:0;padding-bottom:10px;padding-left:10px;padding-right:10px;padding-top:30px;background-color:#fafafa"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:580px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr class="es-mobile-hidden"><td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;font-size:0px"><img src="http://qrcoder.ru/code/?http%3A%2F%2F192.168.1.89%2Fmailnew%2Findex.php%3Ftype%3Din%26mode%3Dprofile%26uid%3D' . $__koddocmail . '&4&0" alt width="150" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></td></tr></table></td></tr><tr><td align="center" valign="top" style="padding:0;Margin:0;width:580px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" class="es-m-txt-c" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px"><!--[if mso]><a href="http://192.168.1.89/mailnew/index.php?type=in&mode=profile&uid=' . $__koddocmail . '" target="_blank" hidden><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" esdevVmlButton href="http://192.168.1.89/mailnew/index.php?type=in&mode=profile&uid=' . $__koddocmail . '" style="height:36px; v-text-anchor:middle; width:155px" arcsize="14%" stroke="f"  fillcolor="#343a40"><w:anchorlock></w:anchorlock><center style="color:#ffffff; font-family:arial, "helvetica neue", helvetica, sans-serif; font-size:12px; font-weight:400; line-height:12px;  mso-text-raise:1px">Профиль документа</center></v:roundrect></a><![endif]--><!--[if !mso]><!-- --><span class="msohide es-button-border-2 es-button-border" style="border-style:solid;border-color:#2cb543;background:#343a40;border-width:0px;display:inline-block;border-radius:5px;width:auto;mso-border-alt:10px;mso-hide:all"><a href="http://192.168.1.89/mailnew/index.php?type=in&mode=profile&uid=' . $__koddocmail . '" class="es-button es-button-1" target="_blank" style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;color:#FFFFFF;font-size:14px;display:inline-block;background:#343a40;border-radius:5px;font-family:arial, helvetica neue, helvetica, sans-serif;font-weight:normal;font-style:normal;line-height:17px;width:auto;text-align:center;padding:10px;border-color:#343a40">Профиль документа</a></span><!--<![endif]--></td></tr></table></td></tr></table></td></tr></table></td></tr></table>';
                         #
                         #
                         # ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
                         #
                         # 
                         #
                         # Mail address
                         $email_to = $__email;
                         // $email_to = 'chugunov@atgs.ru';
                         $email_admin = 'chugunov@atgs.ru';
                         $mail->addAddress($email_to);
                         $mail->addCC($email_admin);
                         #
                         # Content
                         $mail->isHTML(true); // Set email format to HTML
                         $mail->Subject = $subject;
                         $mail->Body    = $messageConstBlockTop . $messageContent_docID . $messageContent_ispolText . $messageContent_docInfo . $messageConstBlockBottom;
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
                         $msg_body = $messageConstBlockTop . $messageContent_docID . $messageContent_ispolText . $messageContent_docInfo . $messageConstBlockBottom;
                         $user_ID = $__contractorID;
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
                                   $_SQLReq_MailIncomingLog_Insert = "INSERT INTO mailbox_incoming_logControl (timestamp, action, koddocmail, controlTrigger, userid, userkod, usermail, description, sqlrequest, comment) VALUES ('{$datetimenow}', 'MAIL_REMIND', '{$__koddocmail}', '{$_trigg}', '{$__contractorID}', '{$value}', '{$email_to}', 'Напоминание о необходимости исполнения по входящему документу', '{$sqlClear_select1}', 'Reminder1:{$__reminder1},Reminder2:{$__reminder2}')";
                                   $_QRY_MailIncomingLog_Insert = mysqlQuery($_SQLReq_MailIncomingLog_Insert);
                                   //
                                   //
                                   $_reqINS = mysqlQuery("INSERT INTO mailbox_incoming_logChanges (timestamp, action, koddocmail, userid, kodispol, oldSettings, newSettings, changes, changesText, changesCount) VALUES ('{$datetimenow}', 'MAIL_REMIND', '{$__koddocmail}', '999999', '{$__contractorID}', null, null, '{$_trigg}', 'Ответственному ( <span class=text-success>{$__FIO}</span> ) отправлено автоматическое email-уведомление на <span class=text-success>{$email_to}</span> об истечении срока исполнения ({$_triggStr})', null)");
                                   //
                                   //
                                   //
                                   if ($_intDiffSeconds <= REMINDER_1) {
                                        if (1 == $__reminder1) {
                                             $_SQLReq_IncomingDoc_Update = "UPDATE mailbox_incoming SET inbox_controlIspolMailReminder1 = 0 WHERE koddocmail = '{$__koddocmail}'";
                                             $_QRY_IncomingDoc_Update = mysqlQuery($_SQLReq_IncomingDoc_Update);
                                        }
                                   }
                                   if ($_intDiffSeconds <= REMINDER_2) {
                                        if (1 == $__reminder2) {
                                             $_SQLReq_IncomingDoc_Update = "UPDATE mailbox_incoming SET inbox_controlIspolMailReminder2 = 0 WHERE koddocmail = '{$__koddocmail}'";
                                             $_QRY_IncomingDoc_Update = mysqlQuery($_SQLReq_IncomingDoc_Update);
                                        }
                                   }
                                   if ($_intDiffSeconds <= 0) {
                                        if (1 == $__notifyDL) {
                                             $_SQLReq_IncomingDoc_Update = "UPDATE mailbox_incoming SET inbox_controlIspolMailNotifyDL = 0 WHERE koddocmail = '{$__koddocmail}'";
                                             $_QRY_IncomingDoc_Update = mysqlQuery($_SQLReq_IncomingDoc_Update);
                                        }
                                   }
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
                         $_SQLReq_Insert1 = "INSERT INTO mailbox_IspolAutoReminder_log (send_datetime, send_koddocmail, send_ispolIDs, send_ispolNames, send_ispolEmails, send_comment) VALUES ('{$datetimenow}', '{$__koddocmail}', '{$__ispolIDs}', '{$__ispolNames}', '{$__ispolEmails}', 'Crontab have completed')";
                         $_QRY_INSERT     = mysqlQuery($_SQLReq_Insert1);
                         if ($_QRY_INSERT) {
                              echo "<br>Record to table 'mailbox_IspolAutoReminder_log' is inserted and mailing is perfomed at " . date("Y-m-d H:m:i") . " : success<br>";
                         } else {
                              echo "<br>Crontab is not performed at " . date("Y-m-d H:m:i") . " ...<br>";
                         }
                    }
               }
               $__clearSubscribersList = $db_handle->runQuery("UPDATE mailbox_incoming SET inbox_controlIspolMailUserListNotifyDL=NULL WHERE koddocmail=" . $__koddocmail);
          }
     }
}
unset($_IS_CRONTAB);
