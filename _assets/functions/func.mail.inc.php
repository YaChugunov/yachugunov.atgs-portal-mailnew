<?php
# Import PHPMailer classes into the global namespace
# These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
#
# Подключаем библиотеки
require "/var/www/html/atgs-portal.local/www/mailnew/_assets/libs/PHPMailer/src/Exception.php";
require "/var/www/html/atgs-portal.local/www/mailnew/_assets/libs/PHPMailer/src/PHPMailer.php";
require "/var/www/html/atgs-portal.local/www/mailnew/_assets/libs/PHPMailer/src/SMTP.php";
#
# 
# ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
# ##### КОНСТАНТЫ
# ##### ##### ##### ##### ##### ##### ##### ##### ##### #####

if (!isset($_IS_CRONTAB)) {
  $_reqDB_UIsettings = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM mailbox_userSettingsUI WHERE ID = '{$_SESSION['id']}'"));
  define('__UI_PERSONAL_USE', isset($_reqDB_UIsettings['use_personalSettings']) ? $_reqDB_UIsettings['use_personalSettings'] : '0');
  define('__UI_PERSONAL_PUSH', isset($_reqDB_UIsettings['use_pushMessages']) ? $_reqDB_UIsettings['use_pushMessages'] : '0');
  define('__UI_PERSONAL_INC_DASHBOARDSHOW', isset($_reqDB_UIsettings['incoming_showDashboard']) ? $_reqDB_UIsettings['incoming_showDashboard'] : '0');
  define('__UI_PERSONAL_OUT_DASHBOARDSHOW', isset($_reqDB_UIsettings['outgoing_showDashboard']) ? $_reqDB_UIsettings['outgoing_showDashboard'] : '0');
  define('__UI_PERSONAL_INC_LEGENDSHOW', isset($_reqDB_UIsettings['mailbox_showLegend']) ? $_reqDB_UIsettings['mailbox_showLegend'] : '0');
  define('__UI_PERSONAL_OUT_LEGENDSHOW', isset($_reqDB_UIsettings['mailbox_showLegend']) ? $_reqDB_UIsettings['mailbox_showLegend'] : '0');
}
#
# 
# ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
# ##### КЛАССЫ
# ##### ##### ##### ##### ##### ##### ##### ##### ##### #####





# 
# 
# ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
# ##### ФУНКЦИИ
# ##### ##### ##### ##### ##### ##### ##### ##### ##### #####

function makeMailMsg_NotifyIspol_Type1($koddocmail, $title, $docID, $mainText, $detailsLink, $detailsType, $detailsOrg, $detailsDesc, $detailsSourceID) {
  $output = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
<!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<meta content="width=device-width" name="viewport"/>
<!--[if !mso]><!-->
<meta content="IE=edge" http-equiv="X-UA-Compatible"/>
<!--<![endif]-->
<title></title>
<!--[if !mso]><!-->
<!--<![endif]-->
<style type="text/css">
		body {
			margin: 0;
			padding: 0;
		}

		table,
		td,
		tr {
			vertical-align: top;
			border-collapse: collapse;
		}

		* {
			line-height: inherit;
		}

		a[x-apple-data-detectors=true] {
			color: inherit !important;
			text-decoration: none !important;
		}
	</style>
<style id="media-query" type="text/css">
		@media (max-width: 620px) {

			.block-grid,
			.col {
				min-width: 320px !important;
				max-width: 100% !important;
				display: block !important;
			}

			.block-grid {
				width: 100% !important;
			}

			.col {
				width: 100% !important;
			}

			.col_cont {
				margin: 0 auto;
			}

			img.fullwidth,
			img.fullwidthOnMobile {
				max-width: 100% !important;
			}

			.no-stack .col {
				min-width: 0 !important;
				display: table-cell !important;
			}

			.no-stack.two-up .col {
				width: 50% !important;
			}

			.no-stack .col.num2 {
				width: 16.6% !important;
			}

			.no-stack .col.num3 {
				width: 25% !important;
			}

			.no-stack .col.num4 {
				width: 33% !important;
			}

			.no-stack .col.num5 {
				width: 41.6% !important;
			}

			.no-stack .col.num6 {
				width: 50% !important;
			}

			.no-stack .col.num7 {
				width: 58.3% !important;
			}

			.no-stack .col.num8 {
				width: 66.6% !important;
			}

			.no-stack .col.num9 {
				width: 75% !important;
			}

			.no-stack .col.num10 {
				width: 83.3% !important;
			}

			.video-block {
				max-width: none !important;
			}

			.mobile_hide {
				min-height: 0px;
				max-height: 0px;
				max-width: 0px;
				display: none;
				overflow: hidden;
				font-size: 0px;
			}

			.desktop_hide {
				display: block !important;
				max-height: none !important;
			}
		}
	</style>
</head>
<body class="clean-body" style="margin: 0; padding: 0; -webkit-text-size-adjust: 100%; background-color: #fff;">
<!--[if IE]><div class="ie-browser"><![endif]-->
<table bgcolor="#fff" cellpadding="0" cellspacing="0" class="nl-container" role="presentation" style="table-layout: fixed; vertical-align: top; min-width: 320px; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; width: 100%;" valign="top" width="100%">
<tbody>
<tr style="vertical-align: top;" valign="top">
<td style="word-break: break-word; vertical-align: top;" valign="top">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color:#fff"><![endif]-->
<div style="background-color:transparent;">
<div class="block-grid mixed-two-up" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="150" style="background-color:transparent;width:150px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
<div class="col num3" style="display: table-cell; vertical-align: top; max-width: 320px; min-width: 150px; width: 150px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<div class="mobile_hide">
<div align="left" class="img-container left fixedwidth fullwidthOnMobile" style="padding-right: 0px;padding-left: 0px;">
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr style="line-height:0px"><td style="padding-right: 0px;padding-left: 0px;" align="left"><![endif]--><img alt="Alternate text" border="0" class="left fixedwidth fullwidthOnMobile" src="http://atgs.ru/ext/img/59986fe-e033-481a-9f16-935182b7541e.png" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; width: 100%; max-width: 112px; display: block;" title="Alternate text" width="112"/>
<!--[if mso]></td></tr></table><![endif]-->
</div>
</div>
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="450" style="background-color:transparent;width:450px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
<div class="col num9" style="display: table-cell; vertical-align: top; min-width: 320px; max-width: 450px; width: 450px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 25px; padding-bottom: 15px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:25px;padding-right:10px;padding-bottom:15px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 36px; line-height: 1.2; word-break: break-word; text-align: left; mso-line-height-alt: 34px; margin: 0;"><span style="color: #ffb400; font-size: 36px; text-transform: uppercase"><span style=""><strong>' . $title . '</strong></span></span></p>
<p style="font-size: 20px; line-height: 1.2; word-break: break-word; text-align: left; mso-line-height-alt: 24px; margin: 0;"><span style="color: #ffffff; font-size: 20px; background-color: #000000;"><span style=""><strong>Документ во входящих № ' . $docID . '</strong></span></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:transparent;width:600px; border-top: 1px solid transparent; border-left: 1px solid transparent; border-bottom: 1px solid transparent; border-right: 1px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:15px;"><![endif]-->
<div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 598px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:1px solid transparent; border-left:1px solid transparent; border-bottom:1px solid transparent; border-right:1px solid transparent; padding-top:5px; padding-bottom:15px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 14px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 17px; margin: 0;"><strong><span style="color: #000000; font-size: 18px;"><span style="">' . $mainText . '</span></span></strong></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid mixed-two-up" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="200" style="background-color:transparent;width:200px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num4" style="display: table-cell; vertical-align: top; max-width: 320px; min-width: 200px; width: 200px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="font-size: 12px; color: #333333;">ССЫЛКА</span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="400" style="background-color:transparent;width:400px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num8" style="display: table-cell; vertical-align: top; min-width: 320px; max-width: 400px; width: 400px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="color: #00a6dc;"><span style="font-size: 12px;">' . $detailsLink . '</span></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid mixed-two-up" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="200" style="background-color:transparent;width:200px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num4" style="display: table-cell; vertical-align: top; max-width: 320px; min-width: 200px; width: 200px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="font-size: 12px; color: #333333;">ТИП ДОКУМЕНТА</span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="400" style="background-color:transparent;width:400px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num8" style="display: table-cell; vertical-align: top; min-width: 320px; max-width: 400px; width: 400px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="color: #000000;"><span style="font-size: 12px;">' . $detailsType . '</span></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid mixed-two-up" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="200" style="background-color:transparent;width:200px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num4" style="display: table-cell; vertical-align: top; max-width: 320px; min-width: 200px; width: 200px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="font-size: 12px; color: #333333;">ОТПРАВИТЕЛЬ</span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="400" style="background-color:transparent;width:400px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num8" style="display: table-cell; vertical-align: top; min-width: 320px; max-width: 400px; width: 400px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="color: #000000;"><span style="font-size: 12px;">' . $detailsOrg . '</span></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid mixed-two-up" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="200" style="background-color:transparent;width:200px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num4" style="display: table-cell; vertical-align: top; max-width: 320px; min-width: 200px; width: 200px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="font-size: 12px; color: #333333;">ОПИСАНИЕ</span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="400" style="background-color:transparent;width:400px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num8" style="display: table-cell; vertical-align: top; min-width: 320px; max-width: 400px; width: 400px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; text-align: left; mso-line-height-alt: 14px; margin: 0;"><strong><span style="color: #000000; font-size: 12px;">' . $detailsDesc . '</span></strong></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid mixed-two-up" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="200" style="background-color:transparent;width:200px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num4" style="display: table-cell; vertical-align: top; max-width: 320px; min-width: 200px; width: 200px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;"><span style="font-size: 12px; color: #333333;">ИСХОДЯЩИЙ НОМЕР</span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="400" style="background-color:transparent;width:400px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
<div class="col num8" style="display: table-cell; vertical-align: top; min-width: 320px; max-width: 400px; width: 400px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 5px; padding-bottom: 5px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 12px; line-height: 1.2; word-break: break-word; text-align: left; mso-line-height-alt: 14px; margin: 0;"><span style="font-size: 12px;"><span style="color: #000000;">' . $detailsSourceID . '</span></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid" style="min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: transparent;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:transparent;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
<div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
<div class="col_cont" style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<table border="0" cellpadding="0" cellspacing="0" class="divider" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top" width="100%">
<tbody>
<tr style="vertical-align: top;" valign="top">
<td class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 15px; padding-right: 10px; padding-bottom: 5px; padding-left: 10px;" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 1px solid #BBBBBB; width: 100%;" valign="top" width="100%">
<tbody>
<tr style="vertical-align: top;" valign="top">
<td style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top"><span></span></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 0px; padding-bottom: 0px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;line-height:1.2;padding-top:0px;padding-right:10px;padding-bottom:0px;padding-left:10px;">
<div style="line-height: 1.2; font-size: 12px; color: #555555; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
<p style="font-size: 9px; line-height: 1.2; word-break: break-word; text-align: left; mso-line-height-alt: 11px; margin: 0;"><span style="font-size: 9px; color: #999999;"><em><span style="">Служба уведомлений <span style="color: #333333;">АТГС.Портал</span></span></em></span></p>
<p style="font-size: 9px; line-height: 1.2; word-break: break-word; text-align: left; mso-line-height-alt: 11px; margin: 0;"><span style="font-size: 9px; color: #999999;"><em><span style="">Данное сообщение отправлено роботом. Не используйте адрес его отправителя для обратной связи.</span></em></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
</td>
</tr>
</tbody>
</table>
<!--[if (IE)]></div><![endif]-->
</body>
</html>
';
  return $output;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
function makeMailMsg_NotifyIspol_Type2($koddocmail, $title, $docID, $mainText, $detailsLink, $detailsAddLinks, $detailsType, $detailsOrg, $detailsDesc, $detailsSourceID, $detailsSourceDate, $controlActive, $controlStatus, $controlDeadline, $detailsIspol) {
  $html = file_get_contents("/var/www/html/atgs-portal.local/www/mailnew/_assets/templates-mail/mail-incoming-notify-IspolAssigned-tpl-2.html");
  $search  = array('###koddocmail###', '###title###', '###docID###', '###mainText###', '###detailsLink###', '###detailsAddLinks###', '###detailsType###', '###detailsOrg###', '###detailsDesc###', '###detailsSourceID###', '###detailsSourceDate###', '###controlActive###', '###controlStatus###', '###controlDeadline###', '###detailsIspolList###');
  // Контроль исполнения 
  $controlActiveStr = $controlActive ? '<span style="color:green">Контроль активен</span>' : '<span style="color:#999999">Контроль не активен</span>';
  // Статус исполнения 
  switch ($controlStatus) {
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
      $controlStatusStr = '<span style="color:#A94442">Контроль активен и по документу просрочен дедлайн</span>';
      break;
    default:
      $controlStatusStr = "---";
  }
  $replace = array($koddocmail, $title, $docID, $mainText, $detailsLink, $detailsAddLinks, $detailsType, $detailsOrg, $detailsDesc, $detailsSourceID, $detailsSourceDate, $controlActiveStr, $controlStatusStr, $controlDeadline, $detailsIspol);
  $output = str_replace($search, $replace, $html);
  return $output;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
function makeMailMsg_Notify_FullCheckout($mailtype, $koddocmail, $title, $docID, $mainText, $detailsLink, $detailsAddLinks, $detailsType, $detailsOrg, $detailsDesc, $detailsSourceID, $detailsSourceDate, $controlActive, $controlStatus, $controlDeadline, $detailsIspol) {
  $html = file_get_contents("/var/www/html/atgs-portal.local/www/mailnew/_assets/templates-mail/mail-$mailtype-notify-FullCheckout.html");
  $search  = array('###koddocmail###', '###title###', '###docID###', '###mainText###', '###detailsLink###', '###detailsAddLinks###', '###detailsType###', '###detailsOrg###', '###detailsDesc###', '###detailsSourceID###', '###detailsSourceDate###', '###controlActive###', '###controlStatus###', '###controlDeadline###', '###detailsIspolList###');
  // Контроль исполнения 
  $controlActiveStr = $controlActive ? '<span style="color:green">Контроль активен</span>' : '<span style="color:#999999">Контроль не активен</span>';
  // Статус исполнения 
  switch ($controlStatus) {
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
      $controlStatusStr = '<span style="color:#A94442">Контроль активен и по документу просрочен дедлайн</span>';
      break;
    default:
      $controlStatusStr = "---";
  }
  $replace = array($koddocmail, $title, $docID, $mainText, $detailsLink, $detailsAddLinks, $detailsType, $detailsOrg, $detailsDesc, $detailsSourceID, $detailsSourceDate, $controlActiveStr, $controlStatusStr, $controlDeadline, $detailsIspol);
  $output = str_replace($search, $replace, $html);
  return $output;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
function makeMailMsg_Notify_subscribeFullCheckout($mailtype, $koddocmail, $title, $docID, $mainText, $detailsLink, $detailsAddLinks, $detailsType, $detailsOrg, $detailsDesc, $detailsSourceID, $detailsSourceDate, $controlActive, $controlStatus, $controlDeadline, $detailsIspol) {
  $html = file_get_contents("/var/www/html/atgs-portal.local/www/mailnew/_assets/templates-mail/mail-$mailtype-notify-subscribeFullCheckout.html");
  $search  = array('###koddocmail###', '###title###', '###docID###', '###mainText###', '###detailsLink###', '###detailsAddLinks###', '###detailsType###', '###detailsOrg###', '###detailsDesc###', '###detailsSourceID###', '###detailsSourceDate###', '###controlActive###', '###controlStatus###', '###controlDeadline###', '###detailsIspolList###');
  // Контроль исполнения 
  $controlActiveStr = $controlActive ? '<span style="color:green">Контроль активен</span>' : '<span style="color:#999999">Контроль не активен</span>';
  // Статус исполнения 
  switch ($controlStatus) {
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
      $controlStatusStr = '<span style="color:#A94442">Контроль активен и по документу просрочен дедлайн</span>';
      break;
    default:
      $controlStatusStr = "---";
  }
  $replace = array($koddocmail, $title, $docID, $mainText, $detailsLink, $detailsAddLinks, $detailsType, $detailsOrg, $detailsDesc, $detailsSourceID, $detailsSourceDate, $controlActiveStr, $controlStatusStr, $controlDeadline, $detailsIspol);
  $output = str_replace($search, $replace, $html);
  return $output;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
function makeMailMsg_IspolReminder_Type1($koddocmail, $title, $docID, $mainText, $detailsLink, $detailsType, $detailsOrg, $detailsDesc, $detailsSourceID) {
  $outputHeader = '
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" style="font-family:arial, helvetica neue, helvetica, sans-serif">
     <head>
      <meta charset="UTF-8">
      <meta content="width=device-width, initial-scale=1" name="viewport">
      <meta name="x-apple-disable-message-reformatting">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta content="telephone=no" name="format-detection">
      <title>New message</title><!--[if (mso 16)]>
        <style type="text/css">
        a {text-decoration: none;}
        </style>
        <![endif]--><!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--><!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
        <o:AllowPNG></o:AllowPNG>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
      <style type="text/css">
    #outlook a {
        padding:0;
    }
    .es-button {
        mso-style-priority:100!important;
        text-decoration:none!important;
    }
    a[x-apple-data-detectors] {
        color:inherit!important;
        text-decoration:none!important;
        font-size:inherit!important;
        font-family:inherit!important;
        font-weight:inherit!important;
        line-height:inherit!important;
    }
    .es-desk-hidden {
        display:none;
        float:left;
        overflow:hidden;
        width:0;
        max-height:0;
        line-height:0;
        mso-hide:all;
    }
    .es-button-border:hover a.es-button, .es-button-border:hover button.es-button {
        background:#56d66b!important;
    }
    .es-button-border:hover {
        border-color:#42d159 #42d159 #42d159 #42d159!important;
        background:#56d66b!important;
    }
    td .es-button-border:hover a.es-button-1 {
        background:#4b545c!important;
    }
    td .es-button-border-2:hover {
        background:#4b545c!important;
        border-style:solid solid solid solid!important;
        border-color:#42d159 #42d159 #42d159 #42d159!important;
    }
    [data-ogsb] .es-button.es-button-3 {
        padding:10px!important;
    }
    @media only screen and (max-width:600px) {p, ul li, ol li, a { line-height:150%!important } h1, h2, h3, h1 a, h2 a, h3 a { line-height:120% } h1 { font-size:30px!important; text-align:left } h2 { font-size:24px!important; text-align:left } h3 { font-size:20px!important; text-align:left } .es-header-body h1 a, .es-content-body h1 a, .es-footer-body h1 a { font-size:30px!important; text-align:left } .es-header-body h2 a, .es-content-body h2 a, .es-footer-body h2 a { font-size:24px!important; text-align:left } .es-header-body h3 a, .es-content-body h3 a, .es-footer-body h3 a { font-size:20px!important; text-align:left } .es-menu td a { font-size:14px!important } .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size:14px!important } .es-content-body p, .es-content-body ul li, .es-content-body ol li, .es-content-body a { font-size:14px!important } .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size:14px!important } .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size:12px!important } *[class="gmail-fix"] { display:none!important } .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align:center!important } .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align:right!important } .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align:left!important } .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display:inline!important } .es-button-border { display:inline-block!important } a.es-button, button.es-button { font-size:18px!important; display:inline-block!important } .es-adaptive table, .es-left, .es-right { width:100%!important } .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width:100%!important; max-width:600px!important } .es-adapt-td { display:block!important; width:100%!important } .adapt-img { width:100%!important; height:auto!important } .es-m-p0 { padding:0!important } .es-m-p0r { padding-right:0!important } .es-m-p0l { padding-left:0!important } .es-m-p0t { padding-top:0!important } .es-m-p0b { padding-bottom:0!important } .es-m-p20b { padding-bottom:20px!important } .es-mobile-hidden, .es-hidden { display:none!important } tr.es-desk-hidden, td.es-desk-hidden, table.es-desk-hidden { width:auto!important; overflow:visible!important; float:none!important; max-height:inherit!important; line-height:inherit!important } tr.es-desk-hidden { display:table-row!important } table.es-desk-hidden { display:table!important } td.es-desk-menu-hidden { display:table-cell!important } .es-menu td { width:1%!important } table.es-table-not-adapt, .esd-block-html table { width:auto!important } table.es-social { display:inline-block!important } table.es-social td { display:inline-block!important } .es-desk-hidden { display:table-row!important; width:auto!important; overflow:visible!important; max-height:inherit!important } .es-m-p5 { padding:5px!important } .es-m-p5t { padding-top:5px!important } .es-m-p5b { padding-bottom:5px!important } .es-m-p5r { padding-right:5px!important } .es-m-p5l { padding-left:5px!important } .es-m-p10 { padding:10px!important } .es-m-p10t { padding-top:10px!important } .es-m-p10b { padding-bottom:10px!important } .es-m-p10r { padding-right:10px!important } .es-m-p10l { padding-left:10px!important } .es-m-p15 { padding:15px!important } .es-m-p15t { padding-top:15px!important } .es-m-p15b { padding-bottom:15px!important } .es-m-p15r { padding-right:15px!important } .es-m-p15l { padding-left:15px!important } .es-m-p20 { padding:20px!important } .es-m-p20t { padding-top:20px!important } .es-m-p20r { padding-right:20px!important } .es-m-p20l { padding-left:20px!important } .es-m-p25 { padding:25px!important } .es-m-p25t { padding-top:25px!important } .es-m-p25b { padding-bottom:25px!important } .es-m-p25r { padding-right:25px!important } .es-m-p25l { padding-left:25px!important } .es-m-p30 { padding:30px!important } .es-m-p30t { padding-top:30px!important } .es-m-p30b { padding-bottom:30px!important } .es-m-p30r { padding-right:30px!important } .es-m-p30l { padding-left:30px!important } .es-m-p35 { padding:35px!important } .es-m-p35t { padding-top:35px!important } .es-m-p35b { padding-bottom:35px!important } .es-m-p35r { padding-right:35px!important } .es-m-p35l { padding-left:35px!important } .es-m-p40 { padding:40px!important } .es-m-p40t { padding-top:40px!important } .es-m-p40b { padding-bottom:40px!important } .es-m-p40r { padding-right:40px!important } .es-m-p40l { padding-left:40px!important } .h-auto { height:auto!important } }
    </style>
     </head>
     <body style="width:100%;font-family:arial, helvetica neue, helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0">
      <div class="es-wrapper-color" style="background-color:#F6F6F6"><!--[if gte mso 9]>
                <v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
                    <v:fill type="tile" color="#f6f6f6"></v:fill>
                </v:background>
            <![endif]-->
       <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6">
         <tr>
          <td valign="top" style="padding:0;Margin:0">
           <table cellpadding="0" cellspacing="0" class="es-header" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top">
             <tr>
              <td align="center" style="padding:0;Margin:0">
               <table bgcolor="#ffffff" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                 <tr>
                  <td class="es-m-p5t es-m-p5b es-m-p0r es-m-p0l" align="left" bgcolor="#343a40" style="padding:10px;Margin:0;background-color:#343a40">
                   <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                     <tr>
                      <td align="left" style="padding:0;Margin:0;width:580px">
                       <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                         <tr>
                          <td class="es-m-txt-c" valign="top" align="right" style="Margin:0;padding-top:10px;padding-bottom:10px;padding-left:10px;padding-right:20px;width:30px;font-size:0px"><img src="http://atgs.ru/ext/img/newicons/portal.service-newmail.favicon.ico" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="72"></td>
                          <td align="left" style="padding:0;Margin:0">
                           <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                             <tr>
                              <td class="es-m-txt-l" align="left" style="padding:0;Margin:0;padding-left:5px;padding-right:10px"><h1 style="Margin:0;line-height:32px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:32px;font-style:normal;font-weight:normal;color:#ffffff"><strong>Почта АТГС</strong></h1><h1 style="Margin:0;line-height:32px;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;font-size:32px;font-style:normal;font-weight:normal;color:#ffffff"><span style="font-size:16px;line-height:16px">Входящая и исходящая корпоративная корреспонденция</span></h1></td>
                             </tr>
                           </table></td>
                         </tr>
                       </table></td>
                     </tr>
                   </table></td>
                 </tr>
                 <tr>
                  <td align="left" bgcolor="#111" style="padding:0;Margin:0;background-color:#111111">
                   <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                     <tr class="es-mobile-hidden">
                      <td align="center" valign="top" style="padding:0;Margin:0;width:600px">
                       <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                         <tr>
                          <td style="padding:0;Margin:0">
                           <table class="es-menu" width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                             <tr class="links">
                              <td style="Margin:0;padding-left:5px;padding-right:5px;padding-top:15px;padding-bottom:15px;border:0" width="33.33%" valign="top" bgcolor="transparent" align="center"><a target="_blank" href="" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:14px;font-weight:normal">ГЛАВНАЯ</a></td>
                              <td style="Margin:0;padding-left:5px;padding-right:5px;padding-top:15px;padding-bottom:15px;border:0" width="33.33%" valign="top" bgcolor="transparent" align="center"><a target="_blank" href="" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:14px;font-weight:normal">ВХОДЯЩИЕ</a></td>
                              <td style="Margin:0;padding-left:5px;padding-right:5px;padding-top:15px;padding-bottom:15px;border:0" width="33.33%" valign="top" bgcolor="transparent" align="center"><a target="_blank" href="" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:14px;font-weight:normal">ИСХОДЯЩИЕ</a></td>
                             </tr>
                           </table></td>
                         </tr>
                       </table></td>
                     </tr>
                   </table></td>
                 </tr>
               </table></td>
             </tr>
           </table>
    ';

  $outputBody = '

    ';

  $outputFooter = '
                            <table cellpadding="0" cellspacing="0" class="es-footer" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top">
                            <tr>
                            <td align="center" style="padding:0;Margin:0">
                            <table class="es-footer-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#000000;width:600px" cellspacing="0" cellpadding="0" bgcolor="#000000" align="center">
                                <tr>
                                <td align="left" bgcolor="#343a40" style="padding:0;Margin:0;padding-top:10px;padding-left:10px;padding-right:10px;background-color:#343a40">
                                <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                    <td valign="top" align="center" style="padding:0;Margin:0;width:580px">
                                    <table style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-position:center top" width="100%" cellspacing="0" cellpadding="0" role="presentation">
                                        <tr>
                                        <td class="es-m-txt-c" align="center" style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px;font-size:0px"><img src="https://atgs.ru/ext/img/portal-main-logo.svg" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="43"></td>
                                        </tr>
                                    </table></td>
                                    </tr>
                                    <tr>
                                    <td align="left" style="padding:0;Margin:0;width:580px">
                                    <table style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-position:center top" width="100%" cellspacing="0" cellpadding="0" role="presentation">
                                        <tr>
                                        <td align="center" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:14px;color:#cccccc;font-size:12px"><strong><span style="color:#ffffff">АТГС.Портал</span></strong></p></td>
                                        </tr>
                                        <tr class="es-mobile-hidden">
                                        <td align="center" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:14px;color:#cccccc;font-size:12px">Корпоративные веб-сервисы АО "АтлантикТрансгазСистема" (с 2019 года)</p></td>
                                        </tr>
                                    </table></td>
                                    </tr>
                                    <tr>
                                    <td align="left" style="padding:0;Margin:0;width:580px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                        <td class="es-m-txt-c" align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;font-size:0px">
                                        <table class="es-table-not-adapt es-social" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                            <td valign="top" align="center" style="padding:0;Margin:0;padding-right:10px"><a href="https://t.me/+Q7qZuzU5pevZ74YQ" target="_blank"><img title="Телеграм-канал АТГС.Портал" src="http://atgs.ru/ext/img/newicons/Telegram_Color_Logo.png" alt="Telegram" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td>
                                            <td valign="top" align="center" style="padding:0;Margin:0"><a href="https://vk.com/atgsportal" target="_blank"><img title="VK сообщество АТГС.Портал" src="http://atgs.ru/ext/img/newicons/VK_Color_Compact_Logo.png" alt="VK" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td>
                                            </tr>
                                        </table></td>
                                        </tr>
                                    </table></td>
                                    </tr>
                                </table></td>
                                </tr>
                                <tr class="es-mobile-hidden">
                                <td style="Margin:0;padding-left:5px;padding-right:5px;padding-top:10px;padding-bottom:10px;background-color:#111111" bgcolor="#111" align="left">
                                <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                    <td valign="top" align="center" style="padding:0;Margin:0;width:590px">
                                    <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                        <td style="padding:0;Margin:0">
                                        <table cellpadding="0" cellspacing="0" width="100%" class="es-menu" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr class="links">
                                            <td align="center" valign="top" width="16.67%" id="esd-menu-id-0" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/portal" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Портал</a></td>
                                            <td align="center" valign="top" width="16.67%" id="esd-menu-id-1" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/mailnew" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Почта</a></td>
                                            <td align="center" valign="top" width="16.67%" id="esd-menu-id-2" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/dognet" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Договор</a></td>
                                            <td align="center" valign="top" width="16.67%" id="esd-menu-id-2" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/ism" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">ИСМ</a></td>
                                            <td align="center" valign="top" width="16.67%" id="esd-menu-id-2" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/hr" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Кадры</a></td>
                                            <td align="center" valign="top" width="16.67%" id="esd-menu-id-2" style="Margin:0;padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;border:0"><a target="_blank" href="http://192.168.1.89/eda" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;display:block;font-family:arial, helvetica neue, helvetica, sans-serif;color:#aaaaaa;font-size:12px">Еда</a></td>
                                            </tr>
                                        </table></td>
                                        </tr>
                                    </table></td>
                                    </tr>
                                </table></td>
                                </tr>
                            </table></td>
                            </tr>
                        </table>
                        <table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                            <tr>
                            <td class="es-info-area" align="center" style="padding:0;Margin:0">
                            <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#ffffff;width:600px" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center">
                                <tr>
                                <td style="Margin:0;padding-left:10px;padding-right:10px;padding-top:20px;padding-bottom:20px;background-color:#f1f1f1" bgcolor="#f1f1f1" align="left"><!--[if mso]><table style="width:580px" cellpadding="0" cellspacing="0"><tr><td style="width:250px" valign="top"><![endif]-->
                                <table class="es-left" cellspacing="0" cellpadding="0" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                    <tr>
                                    <td class="es-m-p20b" align="left" style="padding:0;Margin:0;width:245px">
                                    <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                        <td class="es-infoblock es-m-txt-c" valign="top" align="right" style="padding:0;Margin:0;padding-right:10px;line-height:0px;font-size:0px;color:#CCCCCC;width:30px"><img src="http://atgs.ru/ext/img/newicons/YC.avatar.logo-1-round.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="37" height="37"></td>
                                        <td align="left" style="padding:0;Margin:0">
                                        <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                            <td class="es-infoblock es-m-txt-l" align="left" style="padding:0;Margin:0;line-height:14px;font-size:12px;color:#333333"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:14px;color:#333333;font-size:12px"><strong>Ярослав Чугунов</strong><br><span style="color:#999999">Разработчик и администратор</span></p></td>
                                            </tr>
                                        </table></td>
                                        </tr>
                                    </table></td>
                                    <td class="es-hidden" style="padding:0;Margin:0;width:5px"></td>
                                    </tr>
                                </table><!--[if mso]></td><td style="width:157px" valign="top"><![endif]-->
                                <table class="es-left" cellspacing="0" cellpadding="0" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                    <tr>
                                    <td class="es-m-p20b" align="left" style="padding:0;Margin:0;width:157px">
                                    <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                        <td class="es-infoblock es-m-txt-c" valign="top" align="right" style="padding:0;Margin:0;padding-right:10px;line-height:0px;font-size:0px;color:#CCCCCC;width:30px"><img src="https://guocsp.stripocdn.email/content/guids/CABINET_b07b88d99bde76e46a2396d11f306432/images/26531551864324009.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="37" height="37"></td>
                                        <td align="left" style="padding:0;Margin:0">
                                        <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                            <td class="es-infoblock es-m-txt-l" align="left" style="padding:0;Margin:0;line-height:14px;font-size:12px;color:#333333"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:14px;color:#333333;font-size:12px">+7 926 1124469</p></td>
                                            </tr>
                                        </table></td>
                                        </tr>
                                    </table></td>
                                    </tr>
                                </table><!--[if mso]></td><td style="width:5px"></td><td style="width:168px" valign="top"><![endif]-->
                                <table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
                                    <tr>
                                    <td align="left" style="padding:0;Margin:0;width:168px">
                                    <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                        <td class="es-infoblock es-m-txt-c" valign="top" align="right" style="padding:0;Margin:0;padding-right:10px;line-height:0px;font-size:0px;color:#CCCCCC;width:30px"><img src="https://guocsp.stripocdn.email/content/guids/CABINET_b07b88d99bde76e46a2396d11f306432/images/4801551865294269.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="37" height="37"></td>
                                        <td align="left" style="padding:0;Margin:0">
                                        <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                            <td class="es-infoblock es-m-txt-l" align="left" style="padding:0;Margin:0;line-height:14px;font-size:12px;color:#333333"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:14px;color:#333333;font-size:12px"><a target="_blank" href="mailto:chugunov@atgs.ru" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#333333;font-size:12px">chugunov@atgs.ru</a></p></td>
                                            </tr>
                                        </table></td>
                                        </tr>
                                    </table></td>
                                    </tr>
                                </table><!--[if mso]></td></tr></table><![endif]--></td>
                                </tr>
                                <tr>
                                <td align="left" style="padding:0;Margin:0;padding-left:10px;padding-right:10px">
                                <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                    <td valign="top" align="center" style="padding:0;Margin:0;width:580px">
                                    <table style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-position:center top" width="100%" cellspacing="0" cellpadding="0" role="presentation">
                                        <tr>
                                        <td class="es-infoblock" align="center" style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px;line-height:14px;font-size:12px;color:#333333"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:12px;color:#333333;font-size:10px"><em>Данное сообщение отправлено роботом. Не используйте адрес его отправителя для обратной связи.</em></p></td>
                                        </tr>
                                    </table></td>
                                    </tr>
                                </table></td>
                                </tr>
                            </table></td>
                            </tr>
                        </table></td>
                    </tr>
                </table>
            </div>
        </body>
    </html>    
';
  return $outputHeader . $outputBody . $outputFooter;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
function makeMailMsg_IspolReminder_Type2($koddocmail, $title, $docID, $mainText, $detailsLink, $detailsType, $detailsOrg, $detailsDesc, $detailsSourceID) {
  $output = '
';
  return $output;
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
function checkDocmail_onFullCheckout($db, $koddocmail) {
  $output = "";
  if (isset($db)) {
    $_reqSQL_1 = $db->sql("SELECT inbox_docContractor_kodzayvispol as ispolSet FROM mailbox_incoming WHERE koddocmail='{$koddocmail}'")->fetchAll();
    $arrIspolSet1 = explode(",", $_reqSQL_1[0]['ispolSet']);
  } else {
    $_reqSQL_1 = mysqli_fetch_assoc(mysqlQuery("SELECT inbox_docContractor_kodzayvispol as ispolSet FROM mailbox_incoming WHERE koddocmail='{$koddocmail}'"));
    $arrIspolSet1 = explode(",", $_reqSQL_1['ispolSet']);
  }
  return $output;
}


# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция проверки строки на правильность даты
//
function fixMailingLog($koddel, $timestamp, $msg_type, $msg_mode, $msg_status, $msg_log, $msg_comment, $msg_body, $user_ID, $user_kod, $user_name, $user_email, $initiator_script) {
  #
  $recordID = "NM.LOG-IN-" . date("YmdHis", strtotime($timestamp));
  #
  $_reqINS = mysqlQuery("INSERT INTO mailbox_incoming_logMailing (recordID, koddel, timestamp, msg_type, msg_mode, msg_status, msg_log, msg_comment, msg_body, user_ID, user_kod, user_name, user_email, initiator_script) VALUES ('{$recordID}', '{$koddel}', '{$timestamp}', '{$msg_type}', '{$msg_mode}', '{$msg_status}', '{$msg_log}', '{$msg_comment}', '{$msg_body}', '{$user_ID}', '{$user_kod}', '{$user_name}', '{$user_email}', '{$initiator_script}')");
  #
  #
}


# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция сравнения двух строк
//
function compareStrings($s1, $s2) {
  //one is empty, so no result
  if (strlen($s1) == 0 || strlen($s2) == 0) {
    return 0;
  }
  //replace none alphanumeric charactors
  //i left - in case its used to combine words
  $s1clean = preg_replace("/[^A-Za-z0-9-]/", ' ', $s1);
  $s2clean = preg_replace("/[^A-Za-z0-9-]/", ' ', $s2);
  //remove double spaces
  while (strpos($s1clean, "  ") !== false) {
    $s1clean = str_replace("  ", " ", $s1clean);
  }
  while (strpos($s2clean, "  ") !== false) {
    $s2clean = str_replace("  ", " ", $s2clean);
  }
  //create arrays
  $ar1 = explode(" ", $s1clean);
  $ar2 = explode(" ", $s2clean);
  $l1 = count($ar1);
  $l2 = count($ar2);
  //flip the arrays if needed so ar1 is always largest.
  if ($l2 > $l1) {
    $t = $ar2;
    $ar2 = $ar1;
    $ar1 = $t;
  }
  //flip array 2, to make the words the keys
  $ar2 = array_flip($ar2);
  $maxwords = max($l1, $l2);
  $matches = 0;
  //find matching words
  foreach ($ar1 as $word) {
    if (array_key_exists($word, $ar2))
      $matches++;
  }
  return ($matches / $maxwords) * 100;
}
