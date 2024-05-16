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
$__startDate = $_SESSION['inArch_startTableDate'];
$__endDate = $_SESSION['inArch_endTableDate'];
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && __MAIL_TESTMODE_TYPE < 3) {
    define('__MAIL_RESTR5', '/restr_5.test');
} else {
    define('__MAIL_RESTR5', '/restr_5');
}
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
#
/* 
	ПУТИ ДЛЯ СОХРАНЕНИЯ ЗАГРУЖАЕМЫХ ФАЙЛОВ
	> Массив директорий и вспомогательных параметров
*/
if ("POST" == $_SERVER["REQUEST_METHOD"]) {
    $d = dir(__MAIL_INCOMING_STORAGE_SERVERPATH . __MAIL_INCOMING_STORAGE_SUBFOLDER . "/");
    $docpath = $d->path;
    $webpath = __SERVICENAME_MAILNEW . __MAIL_INCOMING_STORAGE_WORKPATH . __MAIL_INCOMING_STORAGE_SUBFOLDER . "/";
    $syspath = __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_STORAGE_WORKPATH . __MAIL_INCOMING_STORAGE_SUBFOLDER . "/";
    $varFileArray = [
        "year"    => date("Y"),
        "fID"     => "",
        "docpath" => $docpath,
        "webpath" => $webpath,
        "syspath" => $syspath,
    ];
}
#
#
#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
#
#
#
/*
 * Example PHP implementation used for the index.html example
 */
// DataTables PHP library
require(__DIR_ROOT . __SERVICENAME_MAILNEW . '/_assets/libs/Datatables/Editor-PHP-1.9.7/lib/DataTables.php');

// Alias Editor classes so they are easy to use
use
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\Mjoin,
    DataTables\Editor\Options,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;
// Build our Editor instance and process the data coming from _POST
Editor::inst($db, __MAIL_INCOMING_TABLENAME)
    ->fields(
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.ID'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.koddocmail'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docType'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docType_lock'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docType_prev'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docTypeSTR'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docID'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_UID'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docIDSTR'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_rowIDs_links'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.dognet_rowIDs_links'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_rowIDs_links'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_rowIDadd_rel'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_rowID_rel'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_docID_rel'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_koddocmail_rel'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docDate')
            ->validator(Validate::dateFormat(
                'd.m.Y H:i:s',
                ValidateOptions::inst()
                    ->allowEmpty(false)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d H:i:s',
                'd.m.Y H:i:s'
            ))
            ->setFormatter(Format::datetime(
                'd.m.Y H:i:s',
                'Y-m-d H:i:s'
            )),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docAbout')
            ->validator(Validate::notEmpty(
                ValidateOptions::inst()
                    ->message('Краткое описание обязательно')
            )),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docSender_kodzakaz'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docSender'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docSenderName'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docRecipient_kodzayvtel'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docRecipientID'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docRecipientSTR'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docSourceID'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docSourceDate')
            ->validator(Validate::dateFormat(
                'd.m.Y',
                ValidateOptions::inst()
                    ->allowEmpty(true)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d',
                'd.m.Y'
            ))
            ->setFormatter(Format::datetime(
                'd.m.Y',
                'Y-m-d'
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docFileID')
            ->setFormatter(Format::ifEmpty(null))
            ->upload(
                Upload::inst(
                    function ($file, $id) use ($varFileArray, $db) {
                        $__pref    = date('Y') . $_SESSION['id'] . date('mdHis');
                        $__name    = $__pref . "-" . $file['name'];
                        $__nameTmp = $file['tmp_name'];
                        $__ext     = explode('.', $__name);
                        $__ext     = strtolower(end($__ext));

                        $md5       = md5(uniqid());
                        $__nameMD5 = "{$md5}.{$__ext}";

                        $__url = str_replace($_SERVER['DOCUMENT_ROOT'], 'http://' . $_SERVER['HTTP_HOST'], $varFileArray['syspath'] . $__nameMD5);

                        move_uploaded_file($__nameTmp, $varFileArray['docpath'] . "{$__name}");
                        symlink($varFileArray['docpath'] . "{$__name}", $varFileArray['syspath'] . $__nameMD5);

                        $db->update(
                            __MAIL_INCOMING_FILES_TABLENAME, // Database table to update
                            [
                                'mainfile'          => '1',
                                'flag'              => 'PREUPL',
                                'file_year'         => $varFileArray['year'],
                                'file_id'           => '',
                                'file_name'         => $__name,
                                'file_originalname' => $file['name'],
                                'file_symname'      => $__nameMD5,
                                // Правка от 05/06/2019
                                //        'file_truelocation' => $varFileArray['docpath']."{$__name}.{$__ext}",
                                'file_truelocation' => $varFileArray['docpath'] . "{$__name}",
                                // ---
                                'file_syspath'      => $varFileArray['syspath'] . $__nameMD5,
                                'file_webpath'      => $varFileArray['webpath'] . $__nameMD5,
                                'file_url'          => $__url,

                            ],
                            ['id' => $id]
                        );
                        return $id;
                    }
                )
                    ->db(
                        __MAIL_INCOMING_FILES_TABLENAME,
                        'id',
                        array(
                            'file_extension'    => Upload::DB_EXTN,
                            'file_size'         => Upload::DB_FILE_SIZE,
                            'file_webpath'      => '',
                            'file_truelocation' => '',
                            'file_originalname' => '',
                            'koddocmail'        => '',
                        )
                    )
                    ->validator(Validate::fileSize(35000000, 'Размер документа не должен превышать 20МБ'))
                    ->validator(Validate::fileExtensions(array('png', 'jpg', 'pdf'), "Загрузите документ"))
            ),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docFileIDadd'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.outbox_fileID_rel')
            ->options(
                Options::inst()
                    ->table(__MAIL_OUTGOING_FILES_TABLENAME)
                    ->value('id')
                    ->label(array('file_webpath', 'file_name'))
            ),
        Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.file_webpath'),
        Field::inst(__MAIL_OUTGOING_FILES_TABLENAME . '.file_name'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractor_kodzayvispol'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractorMULTI'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractorID'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractorDEPT'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractorSTR'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractorEMAIL'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docDateDeadline')
            ->validator(Validate::dateFormat(
                'd.m.Y',
                ValidateOptions::inst()
                    ->allowEmpty(true)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d',
                'd.m.Y'
            ))
            ->setFormatter(Format::datetime(
                'd.m.Y',
                'Y-m-d'
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docContractorComment'),
        //   Field::inst( 'inbox_noticeEmail' ),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docComment'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docCreatedByID')->set(Field::SET_CREATE),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docCreatedBySTR')->set(Field::SET_CREATE),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docCreatedWhen')
            ->set(Field::SET_CREATE)
            ->validator(Validate::dateFormat(
                'd.m.Y H:i:s',
                ValidateOptions::inst()
                    ->allowEmpty(false)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d H:i:s',
                'd.m.Y H:i:s'
            ))
            ->setFormatter(Format::datetime(
                'd.m.Y H:i:s',
                'Y-m-d H:i:s'
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docUpdatedByID')->set(Field::SET_EDIT),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docUpdatedBySTR')->set(Field::SET_EDIT),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_docUpdatedWhen')
            ->set(Field::SET_EDIT)
            ->validator(Validate::dateFormat(
                'd.m.Y H:i:s',
                ValidateOptions::inst()
                    ->allowEmpty(false)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d H:i:s',
                'd.m.Y H:i:s'
            ))
            ->setFormatter(Format::datetime(
                'd.m.Y H:i:s',
                'Y-m-d H:i:s'
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.toSendEmail'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_emailSentByID')->set(Field::SET_CREATE),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_emailSentBySTR')->set(Field::SET_CREATE),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_emailSentWhen')
            ->set(Field::SET_CREATE)
            ->validator(Validate::dateFormat(
                'd.m.Y H:i:s',
                ValidateOptions::inst()
                    ->allowEmpty(false)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d H:i:s',
                'd.m.Y H:i:s'
            ))
            ->setFormatter(Format::datetime(
                'd.m.Y H:i:s',
                'Y-m-d H:i:s'
            )),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolActive'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolCheckout'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolCheckoutComment'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolCheckoutID'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolCheckoutDates'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolCheckoutWhen')
            ->set(Field::SET_CREATE)
            ->validator(Validate::dateFormat(
                'd.m.Y H:i:s',
                ValidateOptions::inst()
                    ->allowEmpty(true)
            ))
            ->getFormatter(Format::datetime(
                'Y-m-d H:i:s',
                'd.m.Y H:i:s'
            )),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolStatus'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolWarning'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolAlarm'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolDays'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailReminder1'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailReminder2'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailNotifyDL'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailNotifyCheckout'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailSpecialNotifyCheckout'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailSpecialNotifyDL'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailUserListNotifyCheckout'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolMailUserListNotifyDL'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolUseDeadline'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.inbox_controlIspolStatusDeadline'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.cntComments'),
        Field::inst(__MAIL_INCOMING_TABLENAME . '.docmailext'),
        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        Field::inst(__MAIL_INCOMING_FILES_TABLENAME . '.id'),
        Field::inst(__MAIL_INCOMING_FILES_TABLENAME . '.file_webpath'),
        Field::inst(__MAIL_INCOMING_FILES_TABLENAME . '.file_originalname'),
    )

    ->on('preGet', function ($editor, $id) use ($__startDate, $__endDate) {
        $editor->where(function ($q) use ($__startDate, $__endDate) {
            $q->where(__MAIL_INCOMING_TABLENAME . '.inbox_docDate', '( SELECT inbox_docDate FROM ' . __MAIL_INCOMING_TABLENAME . ' WHERE inbox_docDate >= ' . $__startDate . ' AND inbox_docDate <= ' . $__endDate . ' )', 'IN', false);
        });
    })


    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

    ->leftJoin('sp_contragents', 'sp_contragents.kodcontragent', '=', __MAIL_INCOMING_TABLENAME . '.inbox_docSender_kodzakaz')
    ->leftJoin(__MAIL_INCOMING_FILES_TABLENAME, __MAIL_INCOMING_FILES_TABLENAME . '.id', '=', __MAIL_INCOMING_TABLENAME . '.inbox_docFileID')
    ->leftJoin(__MAIL_OUTGOING_FILES_TABLENAME, __MAIL_OUTGOING_FILES_TABLENAME . '.id', '=', __MAIL_INCOMING_TABLENAME . '.outbox_fileID_rel')
    ->tryCatch(false)
    ->debug(true)
    ->process($_POST)
    ->json();