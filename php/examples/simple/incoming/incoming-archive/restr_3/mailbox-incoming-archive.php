<?php
date_default_timezone_set('Europe/Moscow');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$ispolStatus_msg_0 = '<span class="errtext text-primary">В текущей конфигурации сервиса эта отметка будет недоступна, если вы не являетесь исполнителем по документу и(или) по нему не включен режим КИ (контроль исполнения).</span>';
$ispolStatus_msg_1 = '<span class="errtext text-primary">В текущей конфигурации сервиса управлять напоминаниями и уведомлениями исполнителю(ям) могут только сами исполнители. Кроме того, также должен быть активен режим КИ (контроль исполнения) и установлен дедлайн.</span>';
$ispolStatus_msg_2 = '<span class="inftext text-primary">Текущее состояние отметки по документу, сохраненное в БД</span>';
$ispolStatus_msg_3 = '<span class="inftext text-primary">Сначала сделайте отметку об исполнении документа</span>';
$ispolStatusOtherOn_msg_0 = '<span class="inftext text-primary">Вы можете отметить документ как исполненный за остальных ответственных</span>';
$ispolStatusOtherOff_msg_0 = '<span class="inftext text-primary">Вы можете снять отметку с докумена как исполненного за других ответственных</span>';
$tab3_ispolList_msg_0 = '<span class="errtext text-primary">Выберите ответственного(ых) либо сделайте отметку ниже < Без ответственных ></span>';
$deadline_default_days = 14;
$popoverLinkToKnow = "<span class='knoweledge-base-link'><a href='#nolink>Подробнее в разделе Помощь</a></span>";

$_QRY_SystemSettings = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM mailbox_systemSettings WHERE typeMailbox = 'incoming'"));
define("DL_WARN_SEC", $_QRY_SystemSettings['DLwarning_inSecs']);
define("DL_WARN_DAY", $_QRY_SystemSettings['DLwarning_inDays']);
define("DL_ALRM_SEC", $_QRY_SystemSettings['DLalarm_inSecs']);
define("DL_ALRM_DAY", $_QRY_SystemSettings['DLalarm_inDays']);
define("DL_DEFL_SEC", $_QRY_SystemSettings['DLdefault_inSecs']);
define("DL_DEFL_DAY", $_QRY_SystemSettings['DLdefault_inDays']);
define("CONTROL_H", $_QRY_SystemSettings['controlHour']);
define("CRON_H", $_QRY_SystemSettings['cronHour']);
define("REMINDER_WARN", 86400 * $_QRY_SystemSettings['DLwarning_inDays']);
define("REMINDER_ALRM", 86400 * $_QRY_SystemSettings['DLalarm_inDays']);
// ----- ----- ----- ----- ----- 
$_QRY_UserSettings = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM mailbox_userSettingsUI WHERE userid = '{$_SESSION['id']}'"));
$_SESSION['incoming_setControlIspolOnStart'] = $_QRY_UserSettings ? $_QRY_UserSettings['incoming_setControlIspolOnStart'] : '0';
$_SESSION['incoming_setDeadlineOnStart'] = $_QRY_UserSettings ? $_QRY_UserSettings['incoming_setDeadlineOnStart'] : '0';
// ----- ----- ----- ----- ----- 
if (!isset($_SESSION['incoming_pageLength']) && empty($_SESSION['incoming_pageLength'])) {
    $_SESSION['incoming_pageLength'] = !empty($_QRY_UserSettings['incoming_pageLengthDefault']) ? $_QRY_UserSettings['incoming_pageLengthDefault'] : '15';
}
// ----- ----- ----- ----- ----- 
if (!isset($_SESSION['incoming_pageCurrent']) || $_SESSION['incoming_pageCurrent'] == "") {
    $_SESSION['incoming_pageCurrent'] = 'nodraw';
}
// ----- ----- ----- ----- ----- 
if (!isset($_SESSION['incoming_selectedRowID']) || $_SESSION['incoming_selectedRowID'] == "") {
    $_SESSION['incoming_selectedRowID'] = '';
}
if (isset($_GET['mode'])) {
    switch ($_GET['mode']) {
        case 'thisyear':
            $__subsubtitle                 = 'Текущий год <span class="text-danger">/ ' . date("Y") . '</span>';
            $startTableDate                = '"' . date('Y') . '-01-01 00:00:01"';
            $_SESSION['in_startTableDate'] = $startTableDate;
            $endTableDate                  = '"' . date('Y') . '-12-31 23:59:59"';
            $_SESSION['in_endTableDate']   = $endTableDate;
            if (isset($_GET['rel'])) {
                $__relID = $_GET['rel'];
            } else {
                $__relID = "norel";
            }
            break;
        case 'archive':
            if (isset($_GET['year']) && $_GET['year'] >= 2007 && $_GET['year'] <= date('Y')) {
                $__subsubtitle                 = 'Архив <span class="text-danger">/ ' . $_GET['year'] . '</span>';
                $startTableDate                = '"' . $_GET["year"] . '-01-01 00:00:01"';
                $_SESSION['inArch_startTableDate'] = $startTableDate;
                $endTableDate                  = '"' . $_GET["year"] . '-12-31 23:59:59"';
                $_SESSION['inArch_endTableDate']   = $endTableDate;
            } else {
                $__subsubtitle                 = "Весь архив";
                $startTableDate                = '"2007-01-01 00:00:01"';
                $_SESSION['inArch_startTableDate'] = $startTableDate;
                $endTableDate                  = '"' . date('Y') . '-12-31 23:59:59"';
                $_SESSION['inArch_endTableDate']   = $endTableDate;
            }
            if (isset($_GET['rel'])) {
                $__relID = $_GET['rel'];
            } else {
                $__relID = "norel";
            }
            break;
        default:
            if (isset($_GET['year']) && $_GET['year'] >= 2007 && $_GET['year'] <= date('Y')) {
                $__subsubtitle                 = 'Архив <span class="text-danger">/ ' . $_GET['year'] . '</span>';
                $startTableDate                = '"' . $_GET["year"] . '-01-01 00:00:01"';
                $_SESSION['inArch_startTableDate'] = $startTableDate;
                $endTableDate                  = '"' . $_GET["year"] . '-12-31 23:59:59"';
                $_SESSION['inArch_endTableDate']   = $endTableDate;
            } else {
                $__subsubtitle                 = "Весь архив";
                $startTableDate                = '"2007-01-01 00:00:01"';
                $_SESSION['inArch_startTableDate'] = $startTableDate;
                $endTableDate                  = '"' . date('Y') . '-12-31 23:59:59"';
                $_SESSION['inArch_endTableDate']   = $endTableDate;
            }
            if (isset($_GET['rel'])) {
                $__relID = $_GET['rel'];
            } else {
                $__relID = "norel";
            }
    }
} else {
    if (isset($_GET['year']) && $_GET['year'] >= 2007 && $_GET['year'] <= date('Y')) {
        $__subsubtitle                 = 'Архив <span class="text-danger">/ ' . $_GET['year'] . '</span>';
        $startTableDate                = '"' . $_GET["year"] . '-01-01 00:00:01"';
        $_SESSION['inArch_startTableDate'] = $startTableDate;
        $endTableDate                  = '"' . $_GET["year"] . '-12-31 23:59:59"';
        $_SESSION['inArch_endTableDate']   = $endTableDate;
    } else {
        $__subsubtitle                 = "Весь архив";
        $startTableDate                = '"2007-01-01 00:00:01"';
        $_SESSION['inArch_startTableDate'] = $startTableDate;
        $endTableDate                  = '"' . date('Y') . '-12-31 23:59:59"';
        $_SESSION['inArch_endTableDate']   = $endTableDate;
    }
    if (isset($_GET['rel'])) {
        $__relID = $_GET['rel'];
    } else {
        $__relID = "norel";
    }
}
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
//  require_once('../_assets/drivers/bd_remote.php');
//  require_once(realpath('../_assets/functions/funcSecure.inc.php'));
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения нового номера документа/записи (docID) для таблицы записей
//
function newDocID() {
    $query    = mysqlQuery("SELECT MAX(inbox_docID) as lastDocID FROM " . __MAIL_INCOMING_TABLENAME . " WHERE YEAR(inbox_docDate)=YEAR(NOW()) ORDER BY id DESC");
    $row      = mysqli_fetch_assoc($query);
    $newDocID = $row['lastDocID'];
    $newDocID++;
    return $newDocID;
}
$__newDocID    = newDocID();
$__newDocIDSTR = $__newDocID;

$_QRY_ISPOL = mysqli_fetch_array(mysqlQuery("SELECT status_ispol, namezayvfio, showispolonly FROM mailbox_sp_users WHERE ID='" . $_SESSION['id'] . "'"));

?>

<script type="text/javascript" language="javascript" class="init">
    // 
    (function(factory) {
        if (typeof define === 'function' && define.amd) {
            // AMD
            define(['jquery', 'datatables.net', 'datatables.net-editor'], factory);
        } else if (typeof exports === 'object') {
            // Node / CommonJS
            module.exports = function($, dt) {
                if (!$) {
                    $ = require('jquery');
                }
                factory($, dt || $.fn.dataTable || require('datatables'));
            };
        } else if (jQuery) {
            // Browser standard
            factory(jQuery, jQuery.fn.dataTable);
        }
    }(function($, DataTable) {
        'use strict';
        if (!DataTable.ext.editorFields) {
            DataTable.ext.editorFields = {};
        }
        var _fieldTypes = DataTable.Editor ?
            DataTable.Editor.fieldTypes :
            DataTable.ext.editorFields;
        _fieldTypes.select2 = {
            _addOptions: function(conf, opts) {
                var elOpts = conf._input[0].options;
                elOpts.length = 0;
                if (opts) {
                    DataTable.Editor.pairs(opts, conf.optionsPair, function(val, label, i) {
                        elOpts[i] = new Option(label, val);
                    });
                }
            },
            create: function(conf) {
                conf._input = $('<select/>')
                    .attr($.extend({
                        id: DataTable.Editor.safeId(conf.id)
                    }, conf.attr || {}));

                var options = $.extend({
                    width: '100%'
                }, conf.opts);
                _fieldTypes.select2._addOptions(conf, conf.options || conf.ipOpts);
                conf._input.select2(options);
                var open;
                conf._input
                    .on('select2:open', function() {
                        open = true;
                    })
                    .on('select2:close', function() {
                        open = false;
                    });
                // On open, need to have the instance update now that it is in the DOM
                this.one('open.select2-' + DataTable.Editor.safeId(conf.id), function() {
                    conf._input.select2(options);
                    if (open) {
                        conf._input.select2('open');
                    }
                });
                return conf._input[0];
            },
            get: function(conf) {
                var val = conf._input.val();
                val = conf._input.prop('multiple') && val === null ? [] :
                    val;
                return conf.separator ?
                    val.join(conf.separator) :
                    val;
            },
            set: function(conf, val) {
                if (conf.separator && !Array.isArray(val)) {
                    val = val.split(conf.separator);
                }
                // Clear out any existing tags
                if (conf.opts && conf.opts.tags) {
                    conf._input.val([]);
                }
                // The value isn't present in the current options list, so we need to add it
                // in order to be able to select it. Note that this makes the set action async!
                // It doesn't appear to be possible to add an option to select2, then change
                // its label and update the display
                var needAjax = false;
                if (conf.opts && conf.opts.ajax) {
                    if (Array.isArray(val)) {
                        for (var i = 0, ien = val.length; i < ien; i++) {
                            if (conf._input.find('option[value="' + val[i] + '"]').length === 0) {
                                needAjax = true;
                                break;
                            }
                        }
                    } else {
                        if (conf._input.find('option[value="' + val + '"]').length === 0) {
                            needAjax = true;
                        }
                    }
                }
                if (needAjax && val) {
                    $.ajax($.extend({
                        beforeSend: function(jqXhr, settings) {
                            // Add an initial data request to the server, but don't
                            // override `data` since the dev might be using that
                            var initData = conf.urlDataType === undefined || conf
                                .urlDataType === 'json' ?
                                'initialValue=true&value=' + JSON.stringify(val) :
                                $.param({
                                    initialValue: true,
                                    value: val
                                });
                            if (typeof conf.opts.ajax.url === 'function') {
                                settings.url = conf.opts.ajax.url();
                            }
                            if (settings.type === 'GET') {
                                settings.url += settings.url.indexOf('?') === -1 ?
                                    '?' + initData :
                                    '&' + initData;
                            } else {
                                settings.data = settings.data ?
                                    settings.data + '&' + initData :
                                    initData;
                            }
                        },
                        success: function(json) {
                            var addOption = function(option) {
                                if (conf._input.find('option[value="' + option.id +
                                        '"]').length === 0) {
                                    $('<option/>')
                                        .attr('value', option.id)
                                        .text(option.text)
                                        .appendTo(conf._input);
                                }
                            }
                            if (Array.isArray(json)) {
                                for (var i = 0, ien = json.length; i < ien; i++) {
                                    addOption(json[i]);
                                }
                            } else if (json.results && Array.isArray(json.results)) {
                                for (var i = 0, ien = json.results.length; i < ien; i++) {
                                    addOption(json.results[i]);
                                }
                            } else {
                                addOption(json);
                            }
                            conf._input
                                .val(val)
                                .trigger('change', {
                                    editor: true
                                });
                        }
                    }, conf.opts.ajax));
                } else {
                    conf._input
                        .val(val)
                        .trigger('change', {
                            editor: true
                        });
                }
            },
            enable: function(conf) {
                $(conf._input).removeAttr('disabled');
            },
            disable: function(conf) {
                $(conf._input).attr('disabled', 'disabled');
            },
            // Non-standard Editor methods - custom to this plug-in
            inst: function(conf) {
                var args = Array.prototype.slice.call(arguments);
                args.shift();
                return conf._input.select2.apply(conf._input, args);
            },
            update: function(conf, data) {
                var val = _fieldTypes.select2.get(conf);
                _fieldTypes.select2._addOptions(conf, data);
                // Restore null value if it was, to let the placeholder show
                if (val === null) {
                    _fieldTypes.select2.set(conf, null);
                }
                $(conf._input).trigger('change', {
                    editor: true
                });
            },
            focus: function(conf) {
                if (conf._input.is(':visible') && conf.onFocus === 'focus') {
                    conf._input.select2('open');
                }
            },
            owns: function(conf, node) {
                if ($(node).closest('.select2-container').length || $(node).closest('.select2').length || $(
                        node).hasClass('select2-selection__choice__remove')) {
                    return true;
                }
                return false;
            },
            canReturnSubmit: function() {
                return false;
            }
        };
    }));
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    function getAllUrlParams(url) {
        // извлекаем строку из URL или объекта window
        var queryString = url ? url.split('?')[1] : window.location.search.slice(1);
        // объект для хранения параметров
        var obj = {};
        // если есть строка запроса
        if (queryString) {
            // данные после знака # будут опущены
            queryString = queryString.split('#')[0];
            // разделяем параметры
            var arr = queryString.split('&');
            for (var i = 0; i < arr.length; i++) {
                // разделяем параметр на ключ => значение
                var a = arr[i].split('=');

                // обработка данных вида: list[]=thing1&list[]=thing2
                var paramNum = undefined;
                var paramName = a[0].replace(/\[\d*\]/, function(v) {
                    paramNum = v.slice(1, -1);
                    return '';
                });

                // передача значения параметра ('true' если значение не задано)
                var paramValue = typeof(a[1]) === 'undefined' ? true : a[1];

                // преобразование регистра
                paramName = paramName.toLowerCase();
                paramValue = paramValue.toLowerCase();

                // если ключ параметра уже задан
                if (obj[paramName]) {
                    // преобразуем текущее значение в массив
                    if (typeof obj[paramName] === 'string') {
                        obj[paramName] = [obj[paramName]];
                    }
                    // если не задан индекс...
                    if (typeof paramNum === 'undefined') {
                        // помещаем значение в конец массива
                        obj[paramName].push(paramValue);
                    }
                    // если индекс задан...
                    else {
                        // размещаем элемент по заданному индексу
                        obj[paramName][paramNum] = paramValue;
                    }
                }
                // если параметр не задан, делаем это вручную
                else {
                    obj[paramName] = paramValue;
                }
            }
        }
        return obj;
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // Функция проверки даты на валидность
    //
    function isValidDate(str) {
        var d = moment(str, 'DD.MM.YYYY');
        if (d == null || !d.isValid()) return false;
        console.log('isValidDate', str, d, str.indexOf(d.format('DD.DM.YYYY')) >= 0)
        return str.indexOf(d.format('DD.DM.YYYY')) >= 0 ||
            str.indexOf(d.format('D.M.YY')) >= 0 ||
            str.indexOf(d.format('DD.M.YY')) >= 0 ||
            str.indexOf(d.format('D.MM.YY')) >= 0 ||
            str.indexOf(d.format('D.M.YYYY')) >= 0 ||
            str.indexOf(d.format('DD.M.YYYY')) >= 0 ||
            str.indexOf(d.format('D.MM.YYYY')) >= 0;
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // Функция проверки переменной на значение
    //
    function checkVal(val) {
        if (typeof val !== "undefined" && val !== "" && val !== null) {
            return 1;
        } else {
            return 0;
        }
    }
    // MARK: Section name    
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // Функция проверки массива на наличие в ней заданной строковой переменной как элемента массива
    //
    function checkArrOnString(stringAsArray, stringOnSearch) {
        if (typeof stringAsArray !== "undefined" && stringAsArray !== "" && stringAsArray !== null) {
            let arr = stringAsArray.split(",");
            console.log('function checkArrOnString >>>', stringAsArray.split(","), stringOnSearch, arr.indexOf(
                stringOnSearch));
            return arr.indexOf(stringOnSearch);
        } else {
            return -2;
        }
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ФУНКЦИЯ ДЛЯ УДАЛЕНИЯ ОПРЕДЕЛЁННЫХ GET ПАРАМЕТРОВ
    // 
    function removeURLParameter(url, parameter) {
        //prefer to use l.search if you have a location/link object
        var urlparts = url.split('?');
        if (urlparts.length >= 2) {

            var prefix = encodeURIComponent(parameter) + '=';
            var pars = urlparts[1].split(/[&;]/g);

            //reverse iteration as may be destructive
            for (var i = pars.length; i-- > 0;) {
                //idiom for string.startsWith
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                    pars.splice(i, 1);
                }
            }

            if (pars.length > 0) {
                url = urlparts[0] + '?' + pars.join('&');
            } else {
                url = urlparts[0];
            }

            return url;
        } else {
            return url;
        }
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    function clearInputFile(f) {
        if (f.value) {
            try {
                f.value = ''; //for IE11, latest Chrome/Firefox/Opera...
            } catch (err) {}
            if (f.value) { //for IE5 ~ IE10
                var form = document.createElement('form'),
                    ref = f.nextSibling;
                form.appendChild(f);
                form.reset();
                ref.parentNode.insertBefore(f, ref);
            }
        }
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    var reqField_uploadAttachedFiles = {
        uploadAttachedFiles: function(response) {
            console.log('Ajax upload response:', response);
            response.forEach(function(msg) {
                $('#uploadFiles-result > table').append(msg);
            });
        }
    };
    //
    function ajaxRequest_uploadAttachedFiles(formData, responseHandler) {

        // Fire off the request_addItem to /form.php
        request_uploadAttachedFiles = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-uploadAttachedFiles.php',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            dataType: 'json',
            success: reqField_uploadAttachedFiles[responseHandler]
        });
        // Callback handler that will be called on success
        request_uploadAttachedFiles.done(function(response, textStatus, jqXHR) {});
        // Callback handler that will be called on failure
        request_uploadAttachedFiles.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_uploadAttachedFiles.always(function() {

        });
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    var reqField_listAttachedFiles = {
        listAttachedFiles: function(response) {
            console.log('listAttachedFiles:', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                $('#listFiles-result').html('');
                res_attached = '';
                res_output = '';
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                x1 = res.split('|||');
                var x2 = [];
                res_container_beg = '<table class="table table-striped table-listattachments-inform"><tbody>';
                for (i = 0; i < x1.length; i++) {
                    x2 = (x1[i] != "") ? x1[i].split('///') : "";
                    res_icon = x2[0];
                    res_filename = x2[1];
                    res_remove = x2[2];
                    if (res_filename !== "" && res_filename !== null && typeof res_filename !== "undefined") {
                        res_attached += '<tr><td><span class="attached-filelink">' + res_icon +
                            '</span></td><td><span class="attached-filelink">' + res_filename +
                            '</span></td><td><span class="attached-filelink">' + res_remove +
                            '</span></td></tr>';
                    }
                }
                res_container_end = '</tbody></table>';
                console.log('Ajax list response:', response, x1);
                if (res_attached !== "" && res_attached !== null && typeof res_attached !== "undefined") {
                    res_output = res_container_beg + res_attached + res_container_end;
                    $('#listFiles-result').html(res_output);
                } else {
                    $('#listFiles-result').html("Нет дополнительных файлов");
                }
            } else {
                $('#listFiles-result').html('<span style="color:red">Нет дополнительных файлов</span>');
            }
        }
    };
    //
    function ajaxRequest_listAttachedFiles(koddocmail, responseHandler) {

        // Fire off the request_addItem to /form.php
        request_listAttachedFiles = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-listAttachedFiles.php',
            cache: false,
            data: {
                koddocmail: koddocmail
            },
            success: reqField_listAttachedFiles[responseHandler]
        });
        // Callback handler that will be called on success
        request_listAttachedFiles.done(function(response, textStatus, jqXHR) {
            var res_attached = "";
            if (response !== "") {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                x1 = res.split('|||');
                var x2 = [];
                for (i = 0; i < x1.length; i++) {
                    x2 = (x1[i] != "") ? x1[i].split('///') : "";
                    res_filename = x2[0];
                    res_remove = x2[1];
                    if (res_filename !== "" && res_filename !== null && typeof res_filename !== "undefined") {
                        res_attached += '<span class="attached-filelink">' + res_filename + '</span>';
                        res_attached += '<br>';
                    }
                }
            }
            console.log('request_listAttachedFiles (res_attached):', res_attached)
            if (res_attached !== "" && res_attached !== "undefined") {
                $('#listFiles-result-' + koddocmail + '').html(res_attached);
            } else {
                $('#listFiles-result-' + koddocmail + '').html("Нет дополнительных файлов");
            }
        });
        // Callback handler that will be called on failure
        request_listAttachedFiles.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_listAttachedFiles.always(function() {

        });
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    var reqField_deleteAttachedFile = {
        deleteAttachedFile: function(response) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
            x = res.split('/');
            res_status = x[0];
            res_koddocmail = x[1];
            res_mainfile = x[2];
            if (res_status === '1') {
                console.log('Deleted! / ' + res);
                if (res_koddocmail != "") {
                    if (res_mainfile != "1") {
                        $('#listFiles-result').html('');
                        $('#uploadFiles-result > table').html('');
                        ajaxRequest_listAttachedFiles(res_koddocmail, 'listAttachedFiles');
                    } else {
                        $('#lnkDocFileID').html('');
                    }
                }
            } else {
                console.log('Error!');
            }
        }
    };
    //
    function ajaxRequest_deleteAttachedFile(rowid, responseHandler) {

        // Fire off the request_addItem to /form.php
        request_deleteAttachedFile = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-deleteAttachedFile.php',
            cache: false,
            data: {
                rowID: rowid
            },
            success: reqField_deleteAttachedFile[responseHandler]
        });
        // Callback handler that will be called on success
        request_deleteAttachedFile.done(function(response, textStatus, jqXHR) {
            console.log('request_deleteAttachedFile', response)
        });
        // Callback handler that will be called on failure
        request_deleteAttachedFile.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_deleteAttachedFile.always(function() {

        });
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    var reqField_getLastDeadlineMail = {
        getLastDeadlineMail: function(response) {
            if (response != '0') {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                x = res.split('/');
                res_trigger = x[0];
                res_timestamp = x[1];
                console.log('Ajax getLastDeadlineMail response:', res_trigger, res_timestamp);
                if (res_trigger != "") {
                    switch (res_trigger) {
                        case '0':
                            $('#controlIspolMailReminder1-Msg').html('')
                            $('#controlIspolMailReminder2-Msg').html('')
                            break;
                        case '1':
                            $('#controlIspolMailReminder1-Msg').html('Отправлено напоминание ' + res_timestamp)
                            $('#controlIspolMailReminder2-Msg').html('')
                            break;
                        case '2':
                            $('#controlIspolMailReminder1-Msg').html('')
                            $('#controlIspolMailReminder2-Msg').html('Отправлено напоминание ' + res_timestamp);
                            break;
                        case '3':
                            $('#controlIspolMailReminder1-Msg').html('Отправлено напоминание ' + res_timestamp)
                            $('#controlIspolMailReminder2-Msg').html('Отправлено напоминание ' + res_timestamp);
                            break;
                        default:
                            $('#controlIspolMailReminder1-Msg').html('')
                            $('#controlIspolMailReminder2-Msg').html('');
                    }
                }
            }
        }
    };
    //
    function ajaxRequest_getLastDeadlineMail(koddocmail, userid, responseHandler) {
        // Fire off the request_addItem to /form.php
        request_getLastDeadlineMail = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getLastDeadlineMail.php',
            cache: false,
            data: {
                koddocmail: koddocmail,
                userid: userid
            },
            success: reqField_getLastDeadlineMail[responseHandler]
        });
        // Callback handler that will be called on success
        request_getLastDeadlineMail.done(function(response, textStatus, jqXHR) {
            console.log('request_getLastDeadlineMail', response)
        });
        // Callback handler that will be called on failure
        request_getLastDeadlineMail.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_getLastDeadlineMail.always(function() {});
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    var reqField_getLastNotify = {
        getLastNotify: function(response) {}
    };
    //
    function ajaxRequest_getLastNotify(action, koddocmail, parameter, responseHandler) {

        // Fire off the request_addItem to /form.php
        request_getLastNotify = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getLastNotify.php',
            cache: false,
            data: {
                koddocmail: koddocmail,
                parameter: parameter,
                action: action
            },
            success: reqField_getLastNotify[responseHandler]
        });
        // Callback handler that will be called on success
        request_getLastNotify.done(function(response, textStatus, jqXHR) {
            if (response != "0" && response != "-1" && response != "-2") {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                switch (parameter) {
                    case 'Reminder1:1':
                        $('#lastNotify-reminder1').html(res);
                        break;
                    case 'Reminder2:1':
                        $('#lastNotify-reminder2').html(res);
                        break;
                    default:
                        $('#lastNotify-reminder1').html('');
                        $('#lastNotify-reminder2').html('');
                }
            }
            console.log('request_getLastNotify', response)
        });
        // Callback handler that will be called on failure
        request_getLastNotify.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_getLastNotify.always(function() {

        });
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    var reqField_getLastAction = {
        getLastAction: function(response) {}
    };
    //
    function ajaxRequest_getLastAction(koddocmail, parameter, responseHandler) {

        // Fire off the request_addItem to /form.php
        request_getLastAction = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getLastAction.php',
            cache: false,
            data: {
                koddocmail: koddocmail,
                parameter: parameter
            },
            success: reqField_getLastAction[responseHandler]
        });
        // Callback handler that will be called on success
        request_getLastAction.done(function(response, textStatus, jqXHR) {
            if (response != "0" && response != "-1" && response != "-2") {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                x = res.split('///');
                res_changes = x[0];
                res_timestamp = x[1];
                switch (parameter) {
                    case 'inbox_docDateDeadline':
                        $('#lastUpate-docDateDeadline').html(res_timestamp);
                        break;
                    case 'inbox_controlIspolActive':
                        $('#lastUpate-ispolActive').html(res_timestamp);
                        break;
                    case 'inbox_controlIspolMailReminder1':
                        $('#lastUpate-reminder1').html(res_timestamp);
                        break;
                        0
                    case 'inbox_controlIspolMailReminder2':
                        $('#lastUpate-reminder2').html(res_timestamp);
                        break;
                    case 'inbox_controlIspolCheckout':
                        $('#lastUpate-ispolCheckout').html(res_timestamp);
                        break;
                    default:
                        $('#lastUpate-docDateDeadline').html('');
                        $('#lastUpate-ispolActive').html('');
                        $('#lastUpate-reminder1').html('');
                        $('#lastUpate-reminder2').html('');
                        $('#lastUpate-ispolCheckout').html('');
                }
            }
            console.log('request_getLastAction', response)
        });
        // Callback handler that will be called on failure
        request_getLastAction.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_getLastAction.always(function() {

        });
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    var reqField_getLinkedRelFiles = {
        getLinkedRelFiles: function(response) {}
    };
    //
    function ajaxRequest_getLinkedRelFiles(koddocmail, responseHandler) {
        // Fire off the request_addItem to /form.php
        request_getLinkedRelFiles = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getLinkedRelFiles.php',
            cache: false,
            data: {
                koddocmail: koddocmail
            },
            success: reqField_getLinkedRelFiles[responseHandler]
        });
        // Callback handler that will be called on success
        request_getLinkedRelFiles.done(function(response, textStatus, jqXHR) {
            if (response != "0" && response != "-1" && response != "-2") {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                x1 = res.split("///");
                if (x1.length > 0) {
                    $('#wrap-linkedOutgoing-showFiles').css("display", "block");
                    for (i = 0; i < x1.length - 1; i++) {
                        console.log('request_getLinkedRelFiles X', i, x1[i])
                        $('#outputArea-linkedOutgoing-showFiles').html(x1[i]);
                    }
                }
            } else if (response === "0") {
                $('#wrap-linkedOutgoing-showFiles').css("display", "block");
                $('#outputArea-linkedOutgoing-showFiles').html(
                    '<span class="text-danger">Нет прикрепленных файлов</span>');
            } else {
                $('#wrap-linkedOutgoing-showFiles').css("display", "none");
            }
        });
        // Callback handler that will be called on failure
        request_getLinkedRelFiles.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_getLinkedRelFiles.always(function() {

        });
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    var reqField_countDocComments = {
        countDocComments: function(response) {}
    };
    //
    function ajaxRequest_countDocComments(koddocmail, responseHandler) {

        // Fire off the request_addItem to /form.php
        request_countDocComments = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-countDocComments.php',
            cache: false,
            data: {
                koddocmail: koddocmail
            },
            success: reqField_countDocComments[responseHandler]
        });
        // Callback handler that will be called on success
        request_countDocComments.done(function(response, textStatus, jqXHR) {
            res_reqField_countDocComments = '';
            if (response != "-1" && response != "-2" && response != "-3") {
                res_reqField_countDocComments = response.replace(new RegExp("\\r?\\n", "g"), "");
            }
            console.log('DocComments has counted!', res_reqField_countDocComments)
        });
        // Callback handler that will be called on failure
        request_countDocComments.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_countDocComments.always(function() {});
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    var reqField_getDocTypeLock = {
        getUserCheckout: function(response) {}
    };
    //
    function ajaxRequest_getDocTypeLock(koddocmail, responseHandler) {

        // Fire off the request_addItem to /form.php
        request_getDocTypeLock = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getDocTypeLock.php',
            cache: false,
            data: {
                koddocmail: koddocmail
            },
            success: reqField_getDocTypeLock[responseHandler]
        });
        // Callback handler that will be called on success
        request_getDocTypeLock.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
            if (response) {
                if (res == '1') {
                    $('select[id="DTE_Field_mailbox_incoming-inbox_docType"]').prop('disabled', 'disabled');
                } else {
                    $('select[id="DTE_Field_mailbox_incoming-inbox_docType"]').prop('disabled', false);
                }
            }
            console.log('getDocTypeLock is recieved!', res);
        });
        // Callback handler that will be called on failure
        request_getDocTypeLock.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_getDocTypeLock.always(function() {});
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    function ajaxRequest_checkOnCurrYearAsync(koddocmail) {
        var result = false;
        $.ajax({
            async: false,
            cache: false,
            type: "post",
            url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-checkRecordOnCurrentYear.php",
            data: {
                koddocmail: koddocmail
            },
            success: function(response) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                if (res == '1') {
                    result = true;
                } else {
                    result = false;
                }
            }
        });
        return result;
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    var table_incoming;

    $(document).ready(function() {
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        table_incoming = $('#inbox').DataTable({
            dom: "<'row '<'col-sm-6'B><'col-sm-6 d-inline-flex justify-content-end'fl>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-6'i><'col-sm-6'p>>",
            // dom: "<'row'<'col-sm-5'B><'col-sm-4'><'col-sm-3'l>>" +
            //   "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-4'i><'col-sm-8'p>>",
            language: {
                url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-mail.json"
            },
            ajax: {
                url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-incoming-process.php",
                type: "POST"
            },
            serverSide: true,
            createdRow: function(row, data, index) {
                if (data.<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docType == 3) {
                    if (data.<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_docID_rel == '' ||
                        data
                        .<?php echo __MAIL_INCOMING_TABLENAME; ?>
                        .outbox_docID_rel == null) {
                        $(row).css('background-color', 'rgb(226, 201, 201)');
                    } else {
                        $(row).css('background-color', 'rgb(201, 226, 201)');
                    }
                }
                $('[data-toggle="tooltip"]', row).tooltip({
                    html: true,
                    placement: 'top',
                    trigger: "hover",
                    customClass: 'tooltip-incoming'
                });
            },
            preDrawCallback: function(settings) {},
            drawCallback: function(settings) {},
            initComplete: function(settings, json) {},
            columns: [{
                    class: "details-control", // (0)
                    searchable: false,
                    orderable: false,
                    data: null,
                    defaultContent: "<i class='fa-solid fa-ellipsis-vertical'></i>"
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docTypeSTR", // (1)
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docIDSTR", // (2)
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDate" // (3)
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docAbout", // (4)
                    defaultContent: ""
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender" // (5)
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docRecipientSTR" // (6)
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractorSTR" // (7)
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline" // (8)
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolDays" // (9)
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID", // (10)
                    render: function(id) {
                        let ext = id ? table_incoming.file(
                                '<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>', id)
                            .file_extension : "";
                        let ext_ico = '<i class="fa-solid fa-file fa-lg"></i>';
                        if (ext === "pdf") {
                            ext_ico = '<i class="fa-solid fa-file-pdf fa-lg"></i>';
                        }
                        return id ?
                            '<span data-toggle="tooltip" title="Открыть или сохранить основной прикрепленный файл к документу" class=""><a class="" target="_blank" href="' +
                            table_incoming.file(
                                '<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>', id)
                            .file_webpath +
                            '">' + ext_ico + '</a></span>' :
                            '-';
                    },
                    defaultContent: "",
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileIDadd", // (11)
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>.file_webpath", // (12)
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel", // (13)
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_rowIDs_links", // (14)
                    className: "text-center"
                },
                {
                    class: "", // (15)
                    searchable: false,
                    orderable: false,
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolCheckoutWhen",
                    defaultContent: '<span class=""><i class="fa-regular fa-rectangle-list"></i></span>'
                },
                {
                    class: "", // (16)
                    searchable: false,
                    orderable: false,
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.cntComments",
                    defaultContent: ""
                },
                {
                    data: "<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>.file_originalname", // (17)
                    searchable: false,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive", // (18)
                    searchable: true,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolCheckout", // (19)
                    searchable: true,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractorDEPT", // (20)
                    searchable: true,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail", // (21)
                    searchable: true,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolCheckoutID", // (22)
                    searchable: true,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSourceID", // (23)
                    searchable: true,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolStatusDeadline", // (24)
                    searchable: true,
                    visible: false
                }
            ],
            columnDefs: [{
                    orderable: false,
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return row.<?php echo __MAIL_INCOMING_TABLENAME; ?>.docmailext ==
                            'Mailnew' ?
                            '<i class="fa-solid fa-ellipsis-vertical"></i>' :
                            '<i class="fa-solid fa-ellipsis-vertical"></i>';
                    },
                },
                {
                    orderable: false,
                    searchable: true,
                    render: function(data) {
                        if (data === '---') {
                            return '<div style="font-size:0.9em; padding:0 2px; width:32px"><span class="">' +
                                data + '</span></div>';
                        } else if (data === 'Общ') {
                            return '<div class="doctype-label doctype-0"><span class="">' +
                                data +
                                '</span></div>';
                        } else if (data === 'Инф') {
                            return '<div class="doctype-label doctype-1"><span class="">' +
                                data +
                                '</span></div>';
                        } else if (data === 'Отв') {
                            return '<div class="doctype-label doctype-3"><span class="">' +
                                data +
                                '</span></div>';
                        } else if (data === 'Зап') {
                            return '<div class="doctype-label doctype-2"><span class="">' +
                                data +
                                '</span></div>';
                        } else {
                            return '<div class="doctype-label doctype-none"><span class="">???</span></div>';
                        }
                    },
                    defaultContent: "---",
                    targets: 1
                },
                {
                    orderable: true,
                    targets: 2
                },
                {
                    orderable: true,
                    searchable: true,
                    render: function(data) {
                        let fullDate = moment(data, 'DD.MM.YYYY hh:mm:ss').format(
                            'DD.MM.YYYY H:mm');
                        let shortDate = moment(data, 'DD.MM.YYYY hh:mm:ss').format(
                            'DD.MM.YYYY');;
                        return '<span class="" title="' +
                            fullDate +
                            '" style="text-decoration:none" data-toggle="tooltip">' +
                            shortDate +
                            '</span>';
                    },
                    targets: 3
                },
                {
                    orderable: false,
                    searchable: true,
                    targets: 4,
                    render: function(data, type, row, meta) {
                        let fullStr = data;
                        let shortStr = data.substr(0, 28) + " ...";
                        let sourceID = row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_docSourceID !== "" ? row
                            .<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSourceID :
                            "no ID";
                        let sourceDate = row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_docSourceDate !== "" &&
                            row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_docSourceDate !==
                            null && typeof row
                            .<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_docSourceDate !==
                            "undefined" ? moment(row
                                .<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .inbox_docSourceDate,
                                'DD.MM.YYYY HH:mm:ss')
                            .format(
                                'DD.MM.YYYY') : "no date";
                        let sourceInfo = (sourceID == "no ID" || sourceDate == "no date") ?
                            '<span><i style="color:#E0E0E0" class="fa-regular fa-lightbulb fa-lg" aria-hidden="true"></i></span>' :
                            '<span data-toggle="tooltip" data-placement="top" title="Внутренний исходящий номер отправителя - ' +
                            sourceID + ' от ' +
                            sourceDate +
                            '"><i class="fa-solid fa-lightbulb fa-lg"></i></span>';
                        let tags =
                            '<span style="padding-left:5px; padding-right:5px" data-toggle="tooltip" data-placement="top" title="Теги по документу (функция скоро будет добавлена)"><i style="color:#E0E0E0" class="fa-solid fa-tags fa-lg" aria-hidden="true"></i></span>';
                        if (fullStr.length > 28) {
                            return '<span style="float:left"><span data-toggle="tooltip" data-placement="top" title="' +
                                data.replace(/["']/g, '') + '">' + shortStr +
                                '</span></span><span style="float:right">' + tags +
                                sourceInfo +
                                '</span>';
                        } else {
                            return '<span style="float:left">' + fullStr +
                                '</span><span style="float:right">' + tags + sourceInfo +
                                '</span>';
                        }
                    }
                },
                {
                    orderable: false,
                    searchable: true,
                    targets: 5,
                    render: function(data, type, row, meta) {
                        let fullStr = data;
                        let shortStr = data.substr(0, 26) + " ..."
                        if (fullStr.length > 26) {
                            return '<span class="about" data-toggle="tooltip" title="' +
                                fullStr +
                                '">' + shortStr + '</span>';
                        } else {
                            return fullStr;
                        }
                    }
                },
                {
                    orderable: false,
                    searchable: true,
                    targets: 6
                },
                {
                    orderable: false,
                    searchable: true,
                    targets: 7,
                    render: function(data, type, row, meta) {
                        koddocmail = row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .koddocmail;
                        ispolactive = row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_controlIspolActive;
                        multi = row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_docContractorMULTI;
                        checkUserInIspol = '';
                        checkUserInCheckout = '';
                        arrContractorID = [];
                        arrCheckoutID = [];
                        if (checkVal(row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .inbox_docContractorID)) {
                            arrContractorID = row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .inbox_docContractorID.split(',');
                            checkUserInIspol = arrContractorID.includes(
                                '<?php echo $_SESSION['id']; ?>');
                        }
                        if (checkVal(row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .inbox_controlIspolCheckoutID)) {
                            arrCheckoutID = row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .inbox_controlIspolCheckoutID.split(',');
                            checkUserInCheckout = arrCheckoutID.includes(
                                '<?php echo $_SESSION['id']; ?>');
                        }
                        //
                        if (checkUserInIspol == "1") {
                            if (ispolactive == '1') {
                                userInIspol = (checkUserInCheckout) ?
                                    '<span data-toggle="tooltip" title="Вы в числе ответственных по документу исполнили документ при активном режиме контроля исполнения" class="text-success pr-1" style="position:relative; top:-2px"><i class="fa-solid fa-circle fa-2xs fa-beat-fade" aria-hidden="true"></i></span>' :
                                    '<span data-toggle="tooltip" title="Вы в числе ответственных по документу не исполнили документ при активном режиме контроля исполнения" class="text-danger pr-1" style="position:relative; top:-2px"><i class="fa-solid fa-circle fa-2xs fa-beat-fade" aria-hidden="true"></i></span>';
                            } else {
                                userInIspol =
                                    '<span data-toggle="tooltip" title="Вы в числе ответственных по документу при неактивном режиме контроля исполнения" class="pr-1" style="position:relative; top:-2px"><i class="fa-solid fa-circle fa-2xs" aria-hidden="true"></i></span>';
                            }
                        } else {
                            userInIspol = '';
                        }
                        //
                        if (multi == "1") {
                            res = data.replace(new RegExp("\\r?\\n", "g"), "");
                            x = res.split(',');
                            contractor_1 = x[0];
                            if (ispolactive == "1") {
                                return '<div class="d-flex"><div class="">' + userInIspol +
                                    '</div><div class="mr-auto">' + contractor_1 +
                                    '</div><div class=""><span data-toggle="tooltip" title="Ответственные - ' +
                                    data +
                                    '"><span id="link-logCheckouts" data-toggle="modal" data-type="logCheckouts" data-id="' +
                                    koddocmail +
                                    '" data-target="#modal-logCheckouts"><i class="fa-solid fa-users" aria-hidden="true"></i></span></span></div></div>';
                            } else {
                                return '<div class="d-flex"><div class="">' + userInIspol +
                                    '</div><div class="mr-auto">' + contractor_1 +
                                    '</div><div class=""><span data-toggle="tooltip" title="Ответственные - ' +
                                    data +
                                    '"><span id="link-noControlIspolActive" data-toggle="modal" data-type="noControlIspolActive" data-id="' +
                                    koddocmail +
                                    '" data-target="#modal-noControlIspolActive"><i class="fa-solid fa-users" aria-hidden="true"></i></span></span></div></div>';
                            }
                        } else {
                            res = data.replace(new RegExp("\\r?\\n", "g"), "");
                            x = res.split(',');
                            contractor_2 = x[0];
                            if (data !== "---") {
                                if (ispolactive == "1") {
                                    return '<div class="d-flex"><div class="">' +
                                        userInIspol +
                                        '</div><div class="mr-auto">' + contractor_2 +
                                        '</div><div class=""><span data-toggle="tooltip" title="Ответственный - ' +
                                        data +
                                        '"><span id="link-logCheckouts" data-toggle="modal" data-type="logCheckouts" data-id="' +
                                        koddocmail +
                                        '" data-target="#modal-logCheckouts"><i class="fa-solid fa-user" aria-hidden="true"></i></span></span></div></div>';
                                } else {
                                    return '<div class="d-flex"><div class="">' +
                                        userInIspol +
                                        '</div><div class="mr-auto">' + contractor_2 +
                                        '</div><div class=""><span data-toggle="tooltip" title="Ответственный - ' +
                                        data +
                                        '"><span id="link-noControlIspolActive" data-toggle="modal" data-type="noControlIspolActive" data-id="' +
                                        koddocmail +
                                        '" data-target="#modal-noControlIspolActive"><i class="fa-solid fa-user" aria-hidden="true"></i></span></span></div></div>';
                                }
                            } else {
                                return '<span style="float:left">' + data + '</span>';
                            }
                        }
                    }
                },
                {
                    orderable: false,
                    searchable: true,
                    targets: 8,
                    render: function(data, type, row, meta) {
                        var deadlineTooltip = moment(row
                            .<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_docDateDeadline,
                            'DD.MM.YYYY').format('DD.MM.YYYY');
                        if (row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_controlIspolUseDeadline ===
                            '1') {
                            return '<span data-toggle="tooltip" title="Дедлайн: ' +
                                deadlineTooltip +
                                '"><span><i class="fa-regular fa-clock fa-lg"></i></span></span>';
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    orderable: false,
                    targets: 9,
                    render: function(data, type, row, meta) {
                        moment.locale('en');
                        var controlDiffWarning = <?php echo REMINDER_WARN; ?>;
                        var controlDiffAlarm = <?php echo REMINDER_ALRM; ?>;
                        var checkoutWhen = row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_controlIspolCheckoutWhen;
                        // Текущая дата
                        var date1 = moment().format('YYYY-MM-DD HH:mm');
                        // Дедлайн по документу
                        var date2 = moment(row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_docDateDeadline, 'DD.MM.YYYY').format();
                        var deadlineTooltip = moment(row
                            .<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_docDateDeadline,
                            'DD.MM.YYYY').format('DD.MM.YYYY');
                        // Дата создания записи
                        var date3 = moment(row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .inbox_docCreatedWhen,
                                'DD.MM.YYYY HH:mm:ss')
                            .format();
                        var diffDays = moment(date2).diff(moment(date1), 'days');
                        // var diffDays = 0;
                        diffDays = (diffDays > 9) ? "9+" : diffDays;
                        var diffSecs = moment(date2).diff(moment(date1), 'seconds');
                        // var diffSecs = 0;
                        if (row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_controlIspolActive === '1') {
                            if (row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .inbox_controlIspolUseDeadline ===
                                '1') {
                                if (row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                    .inbox_controlIspolCheckout ===
                                    '1') {
                                    return '<span data-toggle="tooltip" title="Документ исполнен полностью - ' +
                                        checkoutWhen +
                                        '.<br>Дедлайн - ' +
                                        deadlineTooltip +
                                        '."><span class="text-success"><i class="fa-solid fa-circle-check fa-lg"></i></span></span>';
                                } else {
                                    if (diffSecs > controlDiffAlarm && diffSecs <=
                                        controlDiffWarning) {
                                        return '<div class="control"><div class="spanWarning"><div class="spanText"><span class="" data-toggle="tooltip" data-html="true" data-placement="auto" title="Контроль исполнения активен.<br>Дедлайн - ' +
                                            deadlineTooltip +
                                            '.<br>До дедлайна менее 3-х суток.">' +
                                            diffDays +
                                            '</span></div></div></div>';
                                    } else if (diffSecs > 0 && diffSecs <=
                                        controlDiffAlarm) {
                                        return '<div class="control"><div class="spanAlarm"><div class="spanText"><span data-toggle="tooltip" title="Контроль исполнения активен.<br>Дедлайн - ' +
                                            deadlineTooltip +
                                            '.<br>До наступления дедлайна менее суток.">' +
                                            diffDays + '</span></div></div></div>';
                                    } else if (diffSecs <= 0) {
                                        return '<span data-toggle="tooltip" title="Контроль исполнения активен.<br>Дедлайн - ' +
                                            deadlineTooltip +
                                            ' - просрочен!"><span class="text-danger"><i class="fa-solid fa-fire fa-beat-fade fa-lg"></i></span></span>';
                                    } else {
                                        return '<div class="control"><div class="spanOk"><div class="spanText"><span data-toggle="tooltip" title="Контроль исполнения активен.<br>Дедлайн - ' +
                                            deadlineTooltip +
                                            '.<br>До наступления дедлайна (полных дней) - ' +
                                            diffDays +
                                            '.">' + diffDays +
                                            '</span></div></div></div>';
                                    }
                                }
                            } else {
                                if (row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                    .inbox_controlIspolCheckout ===
                                    '1') {
                                    return '<span data-toggle="tooltip" title="Документ исполнен полностью - ' +
                                        checkoutWhen +
                                        '. Контроль исполнения не был активен. Дедлайна не было."><span class="text-success"><i class="fa-solid fa-circle-check fa-lg"></i></span></span>';
                                } else {
                                    return '<span data-toggle="tooltip" title="Документ не исполнен.<br>Контроль исполнения активен.<br>Дедлайна нет."><span class="text-info"><i class="fa-solid fa-circle-exclamation fa-lg"></i></span></span>';
                                }
                            }
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    orderable: false,
                    targets: 10
                },
                {
                    orderable: false,
                    render: function(data, type, row, meta) {
                        if (data !== null && typeof data !== 'undefined' && data !==
                            "") {
                            return '<div id="link-listAddFiles" data-toggle="modal" data-type="listAddFiles" data-id="' +
                                row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .koddocmail +
                                '" data-target="#modal-listAddFiles"><span data-toggle="tooltip" title="Открыть список дополнительных прикрепленных файлов к документу" class=""><i class="fa-regular fa-file-lines fa-lg"></i></span></div>';
                        } else {
                            return "-";
                        }
                    },
                    targets: 11
                },
                {
                    orderable: false,
                    render: function(data, type, row, meta) {
                        if (data != null && row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .outbox_rowID_rel !=
                            null &&
                            row
                            .<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .outbox_rowID_rel != 0) {
                            return '<div class=""><span data-toggle="tooltip" title="Номер документа, связанный с текущим как ответный, либо являющийся запросом для текущего. При клике вы перейдете к этому документу в разделе исходящих." class=""><a class="text-dark" href="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/index.php?type=out&mode=archive&rel=' +
                                row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .outbox_koddocmail_rel +
                                '"><div class="docnum">' + row
                                .<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .outbox_docID_rel + '</div></a></span></div>';
                        } else if (data == null && row
                            .<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .outbox_rowID_rel !=
                            0 &&
                            row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .outbox_rowID_rel != null) {
                            return '<div class=""><span data-toggle="tooltip" title="Номер документа, связанный с текущим как ответный, либо являющийся запросом для текущего. При клике вы перейдете к этому документу в разделе исходящих." class=""><a class="text-dark" href="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/index.php?type=out&mode=archive&rel=' +
                                row
                                .<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .outbox_koddocmail_rel + '"><div class="docnum">' + row
                                .<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .outbox_docID_rel + '</div></a></span></div>';
                        } else if (data != null && (row
                                .<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .outbox_rowID_rel ==
                                null || row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .outbox_rowID_rel == 0)) {
                            return '<div class=""><a class="text-dark" href="' + data +
                                '" target="_blank"><div class=""><i class="fa-solid fa-file fa-lg"></i></div></a></div>';
                        } else if (data == 0) {
                            return '-';
                        } else {
                            return '-';
                        }
                    },
                    targets: 12
                },
                {
                    orderable: false,
                    render: function(data, type, row, meta) {
                        if (data !== null && typeof data !== 'undefined' && data !== "") {
                            return '<div id="link-listLinkDocs" data-toggle="modal" data-type="listLinkDocs" data-id="' +
                                row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .koddocmail +
                                '" data-target="#modal-listLinkDocs"><span data-toggle="tooltip" title="Список дополнительных документов, для которых текущий документ является ответным" class=""><i class="fa-regular fa-file-lines fa-lg"></i></span></div>';
                        } else {
                            return "-";
                        }
                    },
                    targets: 13
                },
                {
                    orderable: false,
                    render: function(data, type, row, meta) {
                        inboxLinks = data;
                        outboxLinks = row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .outbox_rowIDs_links;
                        dognetLinks = row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .dognet_rowIDs_links;
                        if ((inboxLinks !== null && typeof inboxLinks !== 'undefined' &&
                                inboxLinks !== "") ||
                            (outboxLinks !== null && typeof outboxLinks !==
                                'undefined' &&
                                outboxLinks !== "") ||
                            (dognetLinks !== null && typeof dognetLinks !==
                                'undefined' &&
                                dognetLinks !== "")) {
                            return '<div id="link-listAdditionalLinkDocs" data-toggle="modal" data-type="listAdditionalLinkDocs" data-id="' +
                                row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .koddocmail +
                                '" data-target="#modal-listAdditionalLinkDocs"><span data-toggle="tooltip" title="Посмотреть все связи по документу" class=""><i class="fa fa-chain fa-lg" aria-hidden="true"></i></span></div>';
                        } else {
                            return "-";
                        }
                    },
                    targets: 14
                },
                {
                    orderable: false,
                    targets: 15,
                    render: function(data, type, row, meta) {
                        return '<div id="link-logChanges" data-toggle="modal" data-type="logChanges" data-id="' +
                            row.<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail +
                            '" data-target="#modal-logChanges"><span data-toggle="tooltip" title="Открыть журнал действий пользователй по документу" class=""><i class="fa-regular fa-rectangle-list fa-lg"></i></span></div>';
                    }
                },
                {
                    orderable: false,
                    targets: 16,
                    render: function(data, type, row, meta) {
                        if (data && data > 0) {
                            return '<div id="link-logComments" data-toggle="modal" data-type="logComments" data-id="' +
                                row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .koddocmail +
                                '" data-target="#modal-logComments"><span data-toggle="tooltip" title="В чате по документу есть сообщения, открыть чат по документу" class=""><i class="fa-solid fa-comments fa-lg"></i></span></div>';
                        } else {
                            return '<div id="link-logComments" data-toggle="modal" data-type="logComments" data-id="' +
                                row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .koddocmail +
                                '" data-target="#modal-logComments"><span data-toggle="tooltip" title="В чате по документу нет сообщений, открыть чат по документу" class="" style="color:#E0E0E0"><i class="fa-regular fa-comments fa-lg"></i></span></div>';
                        }
                    }
                }
            ],
            order: [
                [3, "desc"]
            ],
            select: 'single',
            processing: true,
            paging: true,
            searching: true,
            pageLength: <?php echo $_SESSION['incoming_pageLength']; ?>,
            lengthChange: true,
            lengthMenu: [
                [15, 30, 50, -1],
                [15, 30, 50, "Все"]
            ],
            buttons: [{
                text: '<i class="fa-solid fa-rotate"></i>',
                action: function(e, dt, node, config) {
                    $('#mail-incoming-filters-block *').filter('input, select').each(function(
                        index, element) {
                        var itemID = $(element).attr('id');
                        var tagName = $(element).prop('tagName').toLowerCase();
                        if (tagName === 'select') {
                            if (!$(element).prop('disabled')) {
                                $('#' + itemID + ' option').prop('selected', false);
                                $('#' + itemID).val('');
                            }
                        }
                        if (tagName === 'input') {
                            if (!$(element).prop('disabled')) {
                                $('#' + itemID).val('');
                            }
                        }
                    });

                    // var ispolname =
                    //     '<?php echo $_QRY_ISPOL["namezayvfio"] != "" ? $_QRY_ISPOL["namezayvfio"] : ""; ?>';
                    // var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispol"]; ?>';
                    // var showIspolOnly =
                    //     '<?php echo $_QRY_ISPOL["showispolonly"]; ?>';
                    // if (ispolStatus === "1" && ispolname != "" && showIspolOnly ===
                    //     "1") {
                    //     if ($("#chkOnlyIspolMe").prop('checked') === true) {
                    //         table_incoming
                    //             .columns(7)
                    //             .search(ispolname)
                    //             .draw();
                    //         $("#filterIspol").val(ispolname);
                    //     } else {
                    //         table_incoming.columns().search('').draw();
                    //     }
                    // } else {
                    //     table_incoming.columns().search('').draw();
                    // }
                    $('#columnSearch_btnClear').click();
                    table_incoming.rows('.selected').deselect();
                },
                className: 'btn-dark refreshButton'
            }]
        });
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        table_incoming.on('draw', function() {
            // do something with the ID of the selected items
            console.log('TOOOOOOLTIPSSSSS');
        });
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        // Array to track the ids of the details displayed rows
        var detailRows = [];
        $('#inbox tbody').on('click', 'td.details-control', function() {
            var tr = $(this).parents('tr');
            var row = table_incoming.row(tr);
            var idx = $.inArray(tr.attr('id'), detailRows);
            if (row.child.isShown()) {
                tr.removeClass('details');
                row.child.hide();

                // Remove from the 'open' array
                detailRows.splice(idx, 1);
            } else {
                tr.addClass('details');
                rowData = table_incoming.row(row);
                d = row.data();
                var docFileID = d.<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID;
                d.mainfile = docFileID != "" && docFileID != null ?
                    '<span class="attached-filelink"><span class="" style="color:#00bf00; font-size:0.8rem; margin-right: 5px;"><i class="fa-solid fa-paperclip"></i></span><a target="_blank" href="<?php echo __ROOT; ?>' +
                    d.<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>.file_webpath + '">' + d
                    .<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>.file_originalname + '</a></span>' :
                    'Файл не прикреплен';
                //

                d.senderName = d.<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender !== "" ? d
                    .<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender : "---";
                //
                d.ispolActive = d.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                    .inbox_controlIspolActive == 1 ?
                    '<span class="" style=""><i class="fa-regular fa-square-check"></i></span>' :
                    '<span class="" style=""><i class="fa-regular fa-square"></i></span>';
                d.deadlineDate = isValidDate(d.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                        .inbox_docDateDeadline) === true ?
                    '<span class="" style="">' + d.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                    .inbox_docDateDeadline + '</span>' :
                    '<span class="" style="">---</span>';
                d.reminder1 = d.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                    .inbox_controlIspolMailReminder1 ==
                    1 ?
                    '<span class="" style=""><i class="fa-regular fa-square-check"></i></span>' :
                    '<span class="" style=""><i class="fa-regular fa-square"></i></span>';
                d.reminder2 = d.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                    .inbox_controlIspolMailReminder2 ==
                    1 ?
                    '<span class="" style=""><i class="fa-regular fa-square-check"></i></span>' :
                    '<span class="" style=""><i class="fa-regular fa-square"></i></span>';
                d.ispolCheckout = d.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                    .inbox_controlIspolCheckout ==
                    1 ?
                    '<span class="" style=""><i class="fa-regular fa-square-check"></i></span>' :
                    '<span class="" style=""><i class="fa-regular fa-square"></i></span>';
                d.ispolCheckoutWhen = d.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                    .inbox_controlIspolCheckoutWhen ===
                    null ? "" : row.data().<?php echo __MAIL_INCOMING_TABLENAME; ?>
                    .inbox_controlIspolCheckoutWhen;
                switch (d.<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolStatus) {
                    case '0':
                        ispolStatus_str = 'Контроль исполнения не активен';
                        break;
                    case '1':
                        ispolStatus_str = 'Документ исполнен';
                        break;
                    case '2':
                        ispolStatus_str = 'Контроль исполнения активен, дедлайн есть, документ не исполнен';
                        break;
                    case '3':
                        ispolStatus_str =
                            'Контроль исполнения активен, дедлайн есть и просрочен, документ не исполнен';
                        break;
                    default:
                        ispolStatus_str = '---';
                        break;
                }
                d.ispolStatus = '<b>' + ispolStatus_str + '</b>';
                //
                ajaxRequest_getLastAction(d.<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail,
                    'inbox_docDateDeadline', 'getLastAction');
                ajaxRequest_getLastAction(d.<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail,
                    'inbox_controlIspolActive', 'getLastAction');
                ajaxRequest_getLastAction(d.<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail,
                    'inbox_controlIspolMailReminder1', 'getLastAction');
                ajaxRequest_getLastAction(d.<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail,
                    'inbox_controlIspolMailReminder2', 'getLastAction');
                ajaxRequest_getLastAction(d.<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail,
                    'inbox_controlIspolCheckout', 'getLastAction');
                // 
                ajaxRequest_getLastNotify('MAIL_REMIND', d.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                    .koddocmail,
                    'Reminder1:1', 'getLastNotify');
                ajaxRequest_getLastNotify('MAIL_REMIND', d.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                    .koddocmail,
                    'Reminder2:1', 'getLastNotify');
                rowData.child(<?php include 'templates/mailbox-incoming-details.tpl'; ?>).show();
                ajaxRequest_listAttachedFiles(d.<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail,
                    'listAttachedFiles');
                // 
                // Add to the 'open' array
                if (idx === -1) {
                    detailRows.push(tr.attr('id'));
                }
                // Определяем просрочен ли дедлайн или нет
                moment.locale('en');
                var date1 = moment().format('YYYY-MM-DD HH:mm:ss');
                var date2 = moment(row.data().<?php echo __MAIL_INCOMING_TABLENAME; ?>
                        .inbox_docDateDeadline,
                        'DD.MM.YYYY')
                    .format();
                var diffDays = moment(date2).diff(moment(date1), 'days');
                if (diffDays < 7) {
                    var listItemII = document.getElementById("inbox_details");
                    $("td.tdDeadline")
                        .closest("tr", listItemII)
                        .addClass('text-danger')
                        .css('font-style', 'italic')
                        .css('font-weight', '700');
                }
            }
        });
        // On each draw, loop over the `detailRows` array and show any child rows
        table_incoming.on('draw', function() {
            $.each(detailRows, function(i, id) {
                $('#' + id + ' td.details-control').trigger('click');
            });
        });
        //
        //
        $('#columnSearch_btnApply').on('click', function() {
            console.log("Ответственный: " + $("#filterIspol").val());
            table_incoming.ajax.reload();
            table_incoming
                .columns(1)
                .search($("#filterType").val())
                .draw();
            table_incoming
                .columns(2)
                .search($("#filterNumber").val())
                .draw();
            table_incoming
                .columns(4)
                .search($("#filterAbout").val())
                .draw();
            table_incoming
                .columns(5)
                .search($("#filterSender").val())
                .draw();
            table_incoming
                .columns(7)
                .search($("#filterIspol").val())
                .draw();
            table_incoming
                .columns(6)
                .search($("#filterRecipient").val())
                .draw();
            table_incoming
                .columns(18)
                .search($("#filterInControl").val())
                .draw();
            table_incoming
                .columns(19)
                .search($("#filterCheckout").val())
                .draw();
            table_incoming
                .columns(20)
                .search($("#filterDept").val())
                .draw();
            table_incoming
                .columns(21)
                .search($("#filterKOD").val())
                .draw();
            table_incoming
                .columns(23)
                .search($("#filterSourceID").val())
                .draw();
            table_incoming
                .columns(24)
                .search($("#filterIspolDL").val())
                .draw();
        });
        //
        //
        $('#columnSearch_btnClear, button.columnSearch_btnClear').on('click', function() {
            // Очищаем блок фильтров, отслеживая заблокированные элементы
            $('#mail-incoming-filters-block *').filter('input, select').each(function(index, element) {
                var itemID = $(element).attr('id');
                var tagName = $(element).prop('tagName').toLowerCase();
                if (tagName === 'select') {
                    if (!$(element).prop('disabled')) {
                        $('#' + itemID + ' option').prop('selected', false);
                        $('#' + itemID).val('');
                    }
                }
                if (tagName === 'input') {
                    if (!$(element).prop('disabled')) {
                        $('#' + itemID).val('');
                    }
                }
            });
            table_incoming.columns().search('').draw();

            window.location.replace(removeURLParameter(document.location.href, 'rel'));

            // $("#chkOnlyIspolMe").prop('checked', false)
            // ajaxRequest_saveToDB_showIspolOnly('showIspolOnly');
        });
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        if (getAllUrlParams().record_id) {
            table_incoming.on('draw', function() {
                var indexes = table_incoming.rows().eq(0).filter(
                    function(rowIdx) {
                        return table_incoming.cell(rowIdx, 2).data() === getAllUrlParams()
                            .record_id ?
                            true : false;
                    });
                table_incoming.rows(indexes).select();
                table_incoming.rows({
                    selected: false
                }).nodes().to$().css({
                    "display": "none"
                });
                table_incoming.rows(indexes).deselect();
                // 			alert ( table_incoming.column( 2 ).data().indexOf(getAllUrlParams().doc_id) );
                // 			alert ( table_incoming.row( {page: 'all', selected:true} ).ids() );
                // 			table_incoming.ajax.reload();
            });
            table_incoming.ajax.reload(null, false);
        }
        //
        //
        table_incoming.one('preDraw', function() {
            var relID = "<?php echo $__relID; ?>";
            if (relID == "norel") {
                var ispolname =
                    '<?php echo $_QRY_ISPOL["namezayvfio"] != "" ? $_QRY_ISPOL["namezayvfio"] : ""; ?>';
                var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispol"]; ?>';
                var showIspolOnly = ($("#chkOnlyIspolMe").prop('checked') === true) ? '1' : '0';
                if (ispolStatus === "1" && ispolname != "" && showIspolOnly === "1") {
                    table_incoming
                        .columns(7)
                        .search(ispolname)
                        .draw();
                    $("#filterIspol").val(ispolname);
                    $("#filterIspol").prop('disabled', true);
                }
            } else {
                table_incoming
                    .columns(21)
                    .search(relID)
                    .draw();
                $("#filterKOD").val(relID);
                $('p.mail-incoming-filters-button i').show();
                $('button.columnSearch_btnClear.btnTop').show();
            }
        });
        //
        //
        table_incoming.on('draw', function() {
            if (($('#filterKOD').val() != "") || ($('#filterIspol').val() != "") || ($('#filterType')
                    .val() != "") || ($('#filterNumber')
                    .val() != "") || ($('#filterAbout').val() != "") || ($('#filterSender').val() != "") ||
                ($(
                    '#filterInControl').val() != "") || ($('#filterCheckout').val() != "") || ($(
                    '#filterDept').val() != "") || ($('#filterRecipient').val() != "")) {
                $('p.mail-incoming-filters-button i').show();
                $('button.columnSearch_btnClear.btnTop').show();
                console.log('Filters In');
            } else {
                $('p.mail-incoming-filters-button i').hide();
                $('button.columnSearch_btnClear.btnTop').hide();
                console.log('Filters Out');
            }

            var notEmpty;
            $("#mail-incoming-filters-block .form-control").each(function() {
                var element = $(this);
                if (element.val() != "") {
                    notEmpty = true;
                    console.log(element, element.val());
                    element.addClass('filter-active');
                } else {
                    element.removeClass('filter-active');
                }
            });
        });
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        $("#chkOnlyIspolMe").click(function() {
            var ispolname =
                '<?php echo $_QRY_ISPOL["namezayvfio"] != "" ? $_QRY_ISPOL["namezayvfio"] : ""; ?>';
            var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispol"]; ?>';
            if (ispolStatus === "1" && ispolname != "") {
                if ($("#chkOnlyIspolMe").prop('checked') === true) {
                    table_incoming
                        .columns(7)
                        .search(ispolname)
                        .draw();
                    $("#filterIspol").val(ispolname);
                    $("#filterIspol").prop('disabled', true);
                } else {
                    table_incoming.columns().search('').draw();
                    $("#filterIspol").val('');
                    $("#filterIspol").prop('disabled', false);
                }
            } else {
                table_incoming.columns().search('').draw();
                $("#filterIspol").val('');
            }
        });
        //
        var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispol"]; ?>';
        if (ispolStatus === "1") {
            $("#divOnlyIspolMe").css('display', 'block');
        } else {
            $("#divOnlyIspolMe").css('display', 'none');
        }
        //
        //
        $("#chkOnlyInControl").click(function() {});
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        $(document).on("click", ".remove-file", function() {
            var rowid = $(this).attr('rowid');
            var koddocmail = $(this).attr('koddocmail');
            console.log(rowid, 'To delete clicked');
            ajaxRequest_deleteAttachedFile(rowid, 'deleteAttachedFile');
        });
        //
        //
        //
        //
        $(document).on("click", "span#link-noControlIspolActive", function() {
            $(window).on('shown.bs.modal', function(e) {
                $("#modal-noControlIspolActive > div.modal-dialog").css({
                    "width": "50%",
                    "min-width": "640px",
                    "max-width": "640px"
                });
            });
        });
        //
        //
        $(document).on("change", "input#DTE_Field_enbl_outbox_docType_change_0", function() {
            console.log('DTE_Field_enbl_outbox_docType_change_0 is clicked');
            if ($(this).is(':checked')) {
                console.log('DTE_Field_enbl_outbox_docType_change_0 is checked');
                $('#outbox-rowID-rel-alert').css('display', 'block');
            } else {
                console.log('DTE_Field_enbl_outbox_docType_change_0 is unchecked');
                $('#outbox-rowID-rel-alert').css('display', 'none');
            }
        });
        //
        //
        //
        //
        $(document).on("click", "span#link-logCheckouts", function() {
            var koddocmail = $(this).attr('data-id');
            console.log('#div.link modal-logCheckouts clicked', koddocmail);
            $(window).on('shown.bs.modal', function(e) {
                $("#modal-logCheckouts > div.modal-dialog").css({
                    "width": "50%",
                    "min-width": "640px",
                    "max-width": "640px"
                });
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                });
            });
            //
            $('#table-logCheckouts').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-incoming-process-showLogCheckouts.php",
                    type: "POST",
                    data: {
                        koddocmail: koddocmail
                    }
                },
                serverSide: true,
                columns: [{
                    data: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logCheckouts.timestamp"
                }, {
                    data: "mailbox_sp_users.namezayvtel"
                }, {
                    data: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logCheckouts.ispolStatus"
                }],
                columnDefs: [{
                        orderable: true,
                        searchable: true,
                        width: '19%',
                        targets: 0
                    },
                    {
                        orderable: false,
                        searchable: true,
                        width: '25%',
                        targets: 1
                    },
                    {
                        orderable: false,
                        searchable: false,
                        targets: 2,
                        render: function(data, type, row, meta) {
                            if (data == "1") {
                                return '<span class="text-danger"><i class="fa-solid fa-user-clock fa-xl"></i></span>';
                            } else {
                                return '<span class="text-success"><i class="fa-solid fa-user-check fa-xl"></i></span>';
                            }
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                select: false,
                processing: false,
                destroy: true,
                paging: true,
                pagingType: "full_numbers",
                searching: true,
                pageLength: 5,
                lengthChange: false,
                lengthMenu: [
                    [30, 50, 100, -1],
                    [30, 50, 100, "Все"]
                ],
                buttons: []
            });
        });
        //
        //
        $(document).on("click", "div#link-logChanges", function() {
            var koddocmail = $(this).attr('data-id');
            console.log('#div.link modal-logChanges clicked', koddocmail);
            $(window).on('shown.bs.modal', function(e) {
                $("#modal-logChanges > div.modal-dialog").css({
                    "width": "75%",
                    "min-width": "1024px",
                    "max-width": "1280px"
                });
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                });
            });
            //
            $('#table-logChanges').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-8'fi><'col-sm-4'p>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-incoming-process-showLogChanges.php",
                    type: "POST",
                    data: {
                        koddocmail: koddocmail
                    }
                },
                serverSide: true,
                createdRow: function(row, data, index) {},
                drawCallback: function(settings) {
                    $('[data-toggle="tooltip"]').tooltip({
                        html: true,
                        placement: 'top',
                        customClass: "tooltip-logchanges"
                    });
                },
                columns: [{
                    data: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logChanges.timestamp"
                }, {
                    data: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logChanges.action"
                }, {
                    data: "mailbox_sp_users.namezayvtel"
                }, {
                    data: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logChanges.changesTitle"
                }, {
                    data: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logChanges.changesNewVal"
                }, {
                    data: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logChanges.changesOldVal"
                }],
                columnDefs: [{
                    orderable: true,
                    searchable: true,
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return '<span style="color:#333">' + data + '</span>';
                    }
                }, {
                    orderable: false,
                    searchable: true,
                    targets: 1,
                    render: function(data, type, row, meta) {
                        if (data) {
                            switch (data) {
                                case "CRT":
                                    res =
                                        '<span style="color:#009a00"><i class="fa fa-plus-square" aria-hidden="true"></i></span>';
                                    break;
                                case "COMM-CRT":
                                    res =
                                        '<span style="color:#009a00"><i class="fa fa-plus-square" aria-hidden="true"></i></span>';
                                    break;
                                case "UPD":
                                    res =
                                        '<span style="color:#0083c5"><i class="fa fa-pencil-square" aria-hidden="true"></i></span>';
                                    break;
                                case "COMM-UPD":
                                    res =
                                        '<span style="color:#0083c5"><i class="fa fa-pencil-square" aria-hidden="true"></i></span>';
                                    break;
                                case "DEL":
                                    res =
                                        '<span style="color:#d9534f"><i class="fa fa-trash" aria-hidden="true"></i></span>';
                                    break;
                                case "COMM-DEL":
                                    res =
                                        '<span style="color:#d9534f"><i class="fa fa-trash" aria-hidden="true"></i></span>';
                                    break;
                                default:
                                    res = '<span>-</span>';
                            }
                            return res;
                        } else {
                            return '<span>-</span>';
                        }
                    }
                }, {
                    orderable: false,
                    searchable: true,
                    targets: 2,
                    render: function(data, type, row, meta) {
                        if (data !== "" && data !== null) {
                            return '<span style="color:#000">' +
                                data + '</span>';
                        } else {
                            return '<span></span>';
                        }
                    }
                }, {
                    orderable: false,
                    searchable: true,
                    targets: 3,
                    render: function(data, type, row, meta) {
                        let action = row
                            .<?php echo __MAIL_INCOMING_PREFIX; ?>_logChanges
                            .action;
                        switch (action) {
                            case "COMM-CRT":
                                pref =
                                    '<span style="color:#333; margin-right:5px"><i class="fa fa-comments" aria-hidden="true"></i></span>';
                                break;
                            case "COMM-UPD":
                                pref =
                                    '<span style="color:#333; margin-right:5px"><i class="fa fa-comments" aria-hidden="true"></i></span>';
                                break;
                            case "COMM-DEL":
                                pref =
                                    '<span style="color:#333; margin-right:5px"><i class="fa fa-comments" aria-hidden="true"></i></span>';
                                break;
                            default:
                                pref = '';
                        }
                        if (data != "" && data != null) {
                            res = data.replace(new RegExp("\\r?\\n",
                                "g"), "");
                            x = res.split('///');
                            var changeRow = "";
                            for (i = 0; i < x.length; i++) {
                                changeRow += x[i] + ((x.length > 1 && i < (x
                                        .length -
                                        1)) ?
                                    "<p style='margin-bottom:0'>---</p>" :
                                    "");
                            }
                            return pref + changeRow;
                        } else {
                            return '';
                        }
                    }
                }, {
                    orderable: false,
                    searchable: false,
                    targets: 4,
                    render: function(data, type, row, meta) {
                        if (data) {
                            let fullStr = data;
                            let shortStr = data.substr(0, 46) + " ..."
                            if (fullStr.length > 50) {
                                res =
                                    '<span data-toggle="tooltip" data-html="true" data-placement="auto" title="' +
                                    fullStr + '">' + shortStr + '</span>';
                            } else {
                                res = fullStr;
                            }
                            return '<span>' + res + '</span>';
                        } else {
                            return '<span>--</span>';
                        }
                    }
                }, {
                    orderable: false,
                    searchable: false,
                    targets: 5,
                    render: function(data, type, row, meta) {
                        if (data) {
                            let fullStr = data;
                            let shortStr = data.substr(0, 46) + " ..."
                            if (fullStr.length > 50) {
                                res =
                                    '<span data-toggle="tooltip" data-placement="auto" title="' +
                                    fullStr + '">' + shortStr + '</span>';
                            } else {
                                res = fullStr;
                            }
                            return '<span style="color: #999">' + res +
                                '</span>';
                        } else {
                            return '<span style="color: #999">--</span>';
                        }
                    }
                }],
                order: [
                    [0, 'desc']
                ],
                select: false,
                processing: false,
                destroy: true,
                paging: true,
                pagingType: "numbers",
                searching: true,
                pageLength: 5,
                lengthChange: false,
                lengthMenu: [
                    [30, 50, 100, -1],
                    [30, 50, 100, "Все"]
                ],
                buttons: []
            });
        });
        //
        //
        $(document).on('initEditor', function(e, inst) {
            inst.on('opened', function() {
                console.log('Form displayed');
                // ... add event handlers
            });
        });
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        $(document).on("click", "div#link-logComments", function() {
            var koddocmail = $(this).attr('data-id');
            console.log('#div.link modal-logComments clicked', koddocmail);
            $('#modal-logComments > div.modal-dialog').on('shown.bs.modal', function(e) {
                console.log('#modal-logComments > div.modal-dialog shown!');
                $("#modal-logComments > div.modal-dialog").css({
                    "width": "50%",
                    "min-width": "640px",
                    "max-width": "800px"
                });
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                });
            });
            // Работа с комментариями к документу
            editor_logComments = new $.fn.dataTable.Editor({
                display: "bootstrap",
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-incoming-process-showLogComments.php",
                    type: "POST",
                    data: {
                        koddocmail: koddocmail
                    }
                },
                i18n: {
                    create: {
                        title: '<h3 class="editorform-header-title">Новый комментарий</h3>'
                    },
                    edit: {
                        title: '<h3 class="editorform-header-title">Изменить комментарий</h3>'
                    },
                    remove: {
                        button: "Удалить",
                        title: '<h3 class="editorform-header-title">Удалить комментарий</h3>',
                        submit: "Удалить",
                        confirm: {
                            _: "Вы действительно хотите удалить %d записей?",
                            1: "Вы действительно хотите удалить эту запись?"
                        }
                    },
                    error: {
                        system: "Ошибка в работе сервиса! Свяжитесь с администратором."
                    },
                    multi: {
                        title: "Несколько значений",
                        info: "",
                        restore: "Отменить изменения"
                    },
                    datetime: {
                        previous: '<',
                        next: '>',
                        months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                            'Июль',
                            'Август',
                            'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
                        ],
                        weekdays: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб']
                    }
                },
                template: '#editorform-logComments',
                table: "#table-logComments",
                // destroy: true,
                fields: [{
                    label: "",
                    type: "textarea",
                    name: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments.commentText",
                    attr: {
                        placeholder: 'Комментарий к документу'
                    },
                    className: "block"
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments.koddocmail"
                }]
            });
            table_logComments = $('#table-logComments').dataTable({
                dom: "<'row'<'col-sm-5'B><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-12'p>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-incoming-process-showLogComments.php",
                    type: "POST",
                    data: {
                        koddocmail: koddocmail
                    }
                },
                serverSide: true,
                columns: [{
                    data: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments.username"
                }, {
                    data: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments.commentText"
                }, {
                    data: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments.timestamp"
                }],
                columnDefs: [{
                        orderable: false,
                        searchable: true,
                        width: '25%',
                        targets: 0,
                        render: function(data, type, row, meta) {
                            var controls =
                                '<div class="commentControls"><a href="" class="commentControls-edit">Изменить</a> / <a href="" class="commentControls-remove">Удалить</a></div>';
                            if (data != "" && data != null) {
                                return '<div class="commentUser">' + data + '</div>' +
                                    controls;
                            } else {
                                return '---';
                            }
                        }
                    },
                    {
                        orderable: false,
                        searchable: true,
                        width: 'auto',
                        targets: 1,
                        render: function(data, type, row, meta) {
                            if (data != "" && data != null) {
                                var update_timestamp = row
                                    .<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments
                                    .update_timestamp;
                                var update_username = row
                                    .<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments
                                    .update_username;
                                var updateStr = (update_timestamp != null &&
                                        update_timestamp != "") ?
                                    '<br>Изменено: ' +
                                    update_timestamp + ', ' + update_username : '';
                                return '<div class="commentBlock shadow px-3 py-2"><div class="commentText">' +
                                    data +
                                    '</div><div class="commentDate">Создано: ' + row
                                    .<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments
                                    .timestamp +
                                    updateStr +
                                    '</div></div>';
                            } else {
                                return '';
                            }
                        }
                    },
                    {
                        orderable: true,
                        visible: false,
                        targets: 2
                    }
                ],
                order: [
                    [2, 'desc']
                ],
                select: false,
                processing: false,
                destroy: true,
                paging: true,
                pagingType: "numbers",
                searching: true,
                pageLength: 5,
                lengthChange: false,
                lengthMenu: [
                    [30, 50, 100, -1],
                    [30, 50, 100, "Все"]
                ],
                buttons: [{
                    extend: "create",
                    editor: editor_logComments,
                    text: '<span class="">Новый комментарий</span>',
                    formButtons: ['Создать',
                        {
                            text: 'Отмена',
                            action: function() {
                                this.close();
                            }
                        }
                    ]
                }]
            });
            //
            editor_logComments
                .on('open', function(e, mode, action) {
                    this.field('<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments.koddocmail')
                        .val(
                            koddocmail);
                });
            editor_logComments
                .on('close', function(e, mode, action) {});
            editor_logComments
                .on('submitSuccess', function(e, json, data, action) {
                    console.log('submitSuccess');
                    ajaxRequest_countDocComments(koddocmail, 'countDocComments');
                });

            // Edit record
            $('#table-logComments').on('click', 'a.commentControls-edit', function(e) {
                e.preventDefault();

                editor_logComments
                    .title('Изменить комментарий')
                    .buttons({
                        "label": "Сохранить",
                        "fn": function() {
                            editor_logComments.submit()
                        }
                    })
                    .edit($(this).closest('tr'));
            });

            // Delete a record
            $('#table-logComments').on('click', 'a.commentControls-remove', function(e) {
                e.preventDefault();

                editor_logComments
                    .title('Удалить комментарий')
                    .message("Вы уверены, что хотите удалить эту запись?")
                    .buttons({
                        "label": "Удалить",
                        "fn": function() {
                            editor_logComments.submit()
                        }
                    })
                    .remove($(this).closest('tr'));
            });
        });
        $('#modal-logComments').on('hidden.bs.modal', function(e) {
            table_incoming.ajax.reload(null, false);
            console.log('#modal-logComments hidden!');
            editor_logComments.destroy();
        });
        //
        // ----- -- ----- -- ----- -- ----- -- ----- -- ----- 
        //
        $(document).on("click", "div#link-listAddFiles", function() {
            var koddocmail = $(this).attr('data-id');
            //
            //
            $('#modal-listAddFiles > div.modal-dialog').on('shown.bs.modal', function(e) {
                console.log('#modal-listAddFiles > div.modal-dialog shown!');
                $("#modal-listAddFiles > div.modal-dialog").css({
                    "width": "50%",
                    "min-width": "640px",
                    "max-width": "800px"
                });
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                });
            });
            // Работа с комментариями к документу
            editor_listAddFiles = new $.fn.dataTable.Editor({
                display: "bootstrap",
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-incoming-process-showLogAddFiles.php",
                    type: "POST",
                    data: {
                        koddocmail: koddocmail
                    }
                },
                i18n: {
                    create: {
                        title: '<h3 class="editorform-header-title">Новый комментарий</h3>'
                    },
                    edit: {
                        title: '<h3 class="editorform-header-title">Изменить комментарий</h3>'
                    },
                    remove: {
                        button: "Удалить",
                        title: '<h3 class="editorform-header-title">Удалить комментарий</h3>',
                        submit: "Удалить",
                        confirm: {
                            _: "Вы действительно хотите удалить %d записей?",
                            1: "Вы действительно хотите удалить эту запись?"
                        }
                    },
                    error: {
                        system: "Ошибка в работе сервиса! Свяжитесь с администратором."
                    },
                    multi: {
                        title: "Несколько значений",
                        info: "",
                        restore: "Отменить изменения"
                    },
                    datetime: {
                        previous: '<',
                        next: '>',
                        months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                            'Июль',
                            'Август',
                            'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
                        ],
                        weekdays: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб']
                    }
                },
                template: '#editorform-listAddFiles',
                table: "#table-listAddFiles",
                // destroy: true,
                fields: [{
                    label: "",
                    type: "textarea",
                    name: "<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>.comment",
                    attr: {
                        placeholder: 'Комментарий к файлу'
                    },
                    className: "block"
                }]
            });
            table_listAddFiles = $('#table-listAddFiles').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-incoming-process-showLogAddFiles.php",
                    type: "POST",
                    data: {
                        koddocmail: koddocmail
                    }
                },
                serverSide: true,
                columns: [{
                    data: "<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>.file_originalname"
                }, {
                    data: "<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>.comment"
                }, {
                    data: null,
                    className: "text-center listAddFiles-edit",
                    defaultContent: '<i class="fa fa-pencil"/>',
                    orderable: false
                }],
                columnDefs: [{
                        orderable: false,
                        searchable: true,
                        width: '40%',
                        targets: 0,
                        render: function(data, type, row, meta) {
                            return data ?
                                '<span class="attached-filelink"><a target="_blank" href="<?php echo __ROOT; ?>' +
                                row.<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>
                                .file_webpath +
                                '">' + row
                                .<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>
                                .file_originalname + '</a></span>' :
                                row.<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>
                                .file_originalname;
                        },
                    },
                    {
                        orderable: false,
                        searchable: true,
                        width: 'auto',
                        targets: 1,
                        render: function(data, type, row, meta) {
                            if (data !== "" && data !== null) {
                                return data;
                            } else {
                                return '<span style="color:#AAA">Комментария к файлу нет</span>';
                            }
                        }
                    },
                    {
                        orderable: false,
                        width: '2%',
                        searchable: false,
                        targets: 2
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                ordering: false,
                select: false,
                processing: false,
                destroy: true,
                paging: false,
                pagingType: "full_numbers",
                searching: true,
                pageLength: 10,
                lengthChange: false,
                lengthMenu: [
                    [30, 50, 100, -1],
                    [30, 50, 100, "Все"]
                ],
                buttons: [{
                    extend: "edit",
                    editor: editor_listAddFiles,
                    text: 'Комментарий',
                    formButtons: ['Сохранить',
                        {
                            text: 'Отмена',
                            action: function() {
                                this.close();
                            }
                        }
                    ]
                }]
            });
            // Edit record
            $('#table-listAddFiles').on('click', 'td.listAddFiles-edit', function(e) {
                e.preventDefault();
                editor_listAddFiles.edit($(this).closest('tr'), {
                    title: '<h3 class="editorform-header-title">Изменить комментарий</h3>',
                    buttons: 'Сохранить'
                });
            });
            //
            editor_listAddFiles
                .on('open', function(e, mode, action) {
                    // this.field('<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>.koddocmail').val(koddocmail);
                });
            editor_listAddFiles
                .on('close', function(e, mode, action) {
                    table_incoming.ajax.reload(null, false);
                    // table_listAddFiles.ajax.reload(null, false);
                });
            editor_listAddFiles.on('submitSuccess', function(e, json, data, action) {
                console.log('submitSuccess');
            });
        });
        $('#modal-listAddFiles').on('hidden.bs.modal', function(e) {
            console.log('#modal-listAddFiles hidden!');
            editor_listAddFiles.destroy();
        });
        //
        // ----- -- ----- -- ----- -- ----- -- ----- -- ----- 
        //
        $(document).on("click", "div#link-listLinkDocs", function() {
            var koddocmail = $(this).attr('data-id');
            $('#wrap-linkedOutgoing-showFiles').css("display", "none");
            $('#outputArea-linkedOutgoing-showFiles').html("");
            $('#modal-listLinkDocs > div.modal-dialog').on('shown.bs.modal', function(e) {
                console.log('#modal-listLinkDocs > div.modal-dialog shown!');
                $("#modal-listLinkDocs > div.modal-dialog").css({
                    "width": "50%",
                    "min-width": "640px",
                    "max-width": "800px"
                });
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                });
            });
            // Работа с комментариями к документу
            table_listLinkDocs = $('#table-listLinkDocs').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-incoming-process-showLogLinkDocs.php",
                    type: "POST",
                    data: {
                        koddocmail: koddocmail
                    }
                },
                serverSide: true,
                columns: [{
                        data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docID"
                    },
                    {
                        data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDate"
                    },
                    {
                        data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docAbout"
                    },
                    {
                        data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.ID",
                    }
                ],
                columnDefs: [{
                    orderable: false,
                    width: '6%',
                    targets: 0,
                    render: function(data, type, row, meta) {
                        if (data != null) {
                            return '<span class="link"><a href="outgoing-related.php?mode=related&record_id=' +
                                row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                .ID + '">' +
                                data + '</a></span>';
                        } else {
                            return '<span class=""><i class="fa-solid fa-ellipsis"></i></span>';
                        }
                    },
                }, {
                    orderable: false,
                    searchable: false,
                    width: '18%',
                    targets: 1,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                }, {
                    orderable: false,
                    searchable: false,
                    width: 'auto',
                    targets: 2,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                }, {
                    orderable: false,
                    searchable: false,
                    width: '16%',
                    targets: 3,
                    render: function(data, type, row, meta) {
                        return '<span><a id="btn-linkedOutgoing-showFiles" data-id="' +
                            row
                            .<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .koddocmail +
                            '" data-docid="' + row
                            .<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .outbox_docID +
                            '" class="link">Файлы</a></span>';
                    }
                }],
                order: [
                    [0, 'asc']
                ],
                ordering: false,
                select: false,
                processing: false,
                destroy: true,
                paging: true,
                pagingType: "full_numbers",
                searching: true,
                pageLength: 5,
                lengthChange: false,
                lengthMenu: [
                    [30, 50, 100, -1],
                    [30, 50, 100, "Все"]
                ],
                buttons: []
            });
        });
        $('#modal-listLinkDocs').on('hidden.bs.modal', function(e) {
            console.log('#modal-listLinkDocs hidden!');
        });
        //
        // ----- -- ----- -- ----- -- ----- -- ----- -- ----- 
        //
        $(document).on("click", "#btn-linkedOutgoing-showFiles", function() {
            var koddocmail = $(this).attr('data-id');
            var docid = $(this).attr('data-docid');
            console.log('#btn-linkedOutgoing-showFiles clicked!', koddocmail);
            //
            if ($('#wrap-linkedOutgoing-showFiles').css("display") !== "none") {
                $('#wrap-linkedOutgoing-showFiles').css("display", "none");
            } else {
                ajaxRequest_getLinkedRelFiles(koddocmail, 'getLinkedRelFiles');
                $('#linkedOutgoing-docID').text(docid);
            }
        });
        //
        // ----- -- ----- -- ----- -- ----- -- ----- -- ----- 
        //
        $(document).on("click", "div#link-listAdditionalLinkDocs", function() {
            var koddocmail = $(this).attr('data-id');
            $('#wrap-linkedOutgoing-showFiles').css("display", "none");
            $('#outputArea-linkedOutgoing-showFiles').html("");
            // 
            //
            $('#modal-listAdditionalLinkDocs').on('shown.bs.modal', function(e) {
                $("#modal-listAdditionalLinkDocs > div.modal-dialog").css({
                    "width": "50%",
                    "min-width": "640px",
                    "max-width": "800px"
                });
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                });
                //
                if ($('#table-listAdditionalLinkDocsMInc > tbody > tr > td').hasClass(
                        'dataTables_empty')) {
                    console.log('table-listAdditionalLinkDocsMInc empty!');
                    $('#table-listAdditionalLinkDocsMInc thead').css('display', 'none');
                } else {
                    $('#table-listAdditionalLinkDocsMInc thead').css('display', '');
                }
                //
                if ($('#table-listAdditionalLinkDocsMOut > tbody > tr > td').hasClass(
                        'dataTables_empty')) {
                    console.log('table-listAdditionalLinkDocsMOut empty!');
                    $('#table-listAdditionalLinkDocsMOut thead').css('display', 'none');
                } else {
                    $('#table-listAdditionalLinkDocsMOut thead').css('display', '');
                }
                //
                if ($('#table-listAdditionalLinkDocsDog > tbody > tr > td').hasClass(
                        'dataTables_empty')) {
                    console.log('table-listAdditionalLinkDocsDog empty!');
                    $('#table-listAdditionalLinkDocsDog thead').css('display', 'none');
                } else {
                    $('#table-listAdditionalLinkDocsDog thead').css('display', '');
                }
            });
            // 
            //
            var table_listAdditionalLinkDocsDog = $('#table-listAdditionalLinkDocsDog').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-docDogLinks.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-incoming-process-showLogAdditionalLinkDocsDog.php",
                    type: "POST",
                    data: {
                        koddocmail: koddocmail
                    }
                },
                serverSide: true,
                columns: [{
                        data: "dognet_docbase.docnumber"
                    },
                    {
                        data: "sp_contragents.nameshort"
                    },
                    {
                        data: "sp_objects.nameobjectshot"
                    },
                    {
                        data: "dognet_docbase.docnameshot"
                    }
                ],
                columnDefs: [{
                    orderable: false,
                    width: '10%',
                    targets: 0,
                    render: function(data, type, row, meta) {
                        if (data != null) {
                            return '<span class="link"><a href="<?php echo __ROOT; ?>/dognet/dognet-docview.php?docview_type=details&uniqueID=' +
                                row.dognet_docbase.koddoc +
                                '" target="_blank">' +
                                data + '</a></span>';
                        } else {
                            return '<span class=""><i class="fa-solid fa-ellipsis"></i></span>';
                        }
                    },
                }, {
                    orderable: false,
                    searchable: false,
                    width: '20%',
                    targets: 1,
                    render: function(data, type, row, meta) {
                        let fullStr = data;
                        if (typeof data !== "undefined" && data !== "" &&
                            data !== null) {
                            shortStr = data.substr(0, 28) + " ...";
                            if (fullStr.length > 25) {
                                return '<span class="" data-toggle="tooltip" data-html="true" data-placement="auto" title="' +
                                    fullStr + '">' + shortStr + '</span>';
                            } else {
                                return fullStr;
                            }
                        } else {
                            return "---";
                        }
                    }
                }, {
                    orderable: false,
                    searchable: false,
                    width: '20%',
                    targets: 2,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                }, {
                    orderable: false,
                    searchable: false,
                    width: 'auto',
                    targets: 3,
                    render: function(data, type, row, meta) {
                        let fullStr = data;
                        let shortStr = data.substr(0, 58) + " ..."
                        if (fullStr.length > 55) {
                            return '<span class="" data-toggle="tooltip" data-html="true" data-placement="auto" title="' +
                                fullStr + '">' + shortStr + '</span>';
                        } else {
                            return fullStr;
                        }
                    }
                }],
                order: [
                    [0, 'asc']
                ],
                ordering: false,
                select: false,
                processing: false,
                destroy: true,
                buttons: []
            });
            // 
            //
            var table_listAdditionalLinkDocsMOut = $('#table-listAdditionalLinkDocsMOut')
                .dataTable({
                    dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                        "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                    language: {
                        url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-docMailLinks.json"
                    },
                    ajax: {
                        url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-incoming-process-showLogAdditionalLinkDocsMOut.php",
                        type: "POST",
                        data: {
                            koddocmail: koddocmail
                        }
                    },
                    serverSide: true,
                    columns: [{
                            data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docID"
                        },
                        {
                            data: "sp_contragents.nameshort"
                        },
                        {
                            data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docAbout"
                        }
                    ],
                    columnDefs: [{
                        orderable: false,
                        width: '10%',
                        targets: 0,
                        render: function(data, type, row, meta) {
                            if (data != null) {
                                return '<span class="link"><a href="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/outgoing-related.php?mode=related&record_id=' +
                                    row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                    .ID + '">' +
                                    data + '</a></span>';
                            } else {
                                return '<span class=""><i class="fa-solid fa-ellipsis"></i></span>';
                            }
                        },
                    }, {
                        orderable: false,
                        searchable: false,
                        width: '20%',
                        targets: 1,
                        render: function(data, type, row, meta) {
                            let fullStr = data;
                            if (typeof data !== "undefined" && data !== "" &&
                                data !== null) {
                                shortStr = data.substr(0, 28) + " ...";
                                if (fullStr.length > 25) {
                                    return '<span class="" data-toggle="tooltip" data-html="true" data-placement="auto" title="' +
                                        fullStr + '">' + shortStr + '</span>';
                                } else {
                                    return fullStr;
                                }
                            } else {
                                return "---";
                            }
                        }
                    }, {
                        orderable: false,
                        searchable: false,
                        width: 'auto',
                        targets: 2,
                        render: function(data, type, row, meta) {
                            let fullStr = data;
                            let shortStr = data.substr(0, 58) + " ..."
                            if (fullStr.length > 55) {
                                return '<span class="" data-toggle="tooltip" data-html="true" data-placement="auto" title="' +
                                    fullStr + '">' + shortStr + '</span>';
                            } else {
                                return fullStr;
                            }
                        }
                    }],
                    order: [
                        [0, 'asc']
                    ],
                    ordering: false,
                    select: false,
                    processing: false,
                    destroy: true,
                    buttons: []
                });
            // 
            //
            var table_listAdditionalLinkDocsMInc = $('#table-listAdditionalLinkDocsMInc')
                .dataTable({
                    dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                        "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                    language: {
                        url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-docMailLinks.json"
                    },
                    ajax: {
                        url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-incoming-process-showLogAdditionalLinkDocsMInc.php",
                        type: "POST",
                        data: {
                            koddocmail: koddocmail
                        }
                    },
                    serverSide: true,
                    columns: [{
                            data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docID"
                        },
                        {
                            data: "sp_contragents.nameshort"
                        },
                        {
                            data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docAbout"
                        }
                    ],
                    columnDefs: [{
                        orderable: false,
                        width: '10%',
                        targets: 0,
                        render: function(data, type, row, meta) {
                            if (data != null) {
                                return '<span class="link"><a href="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/incoming-related.php?mode=related&record_id=' +
                                    row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                    .ID + '">' +
                                    data + '</a></span>';
                            } else {
                                return '<span class=""><i class="fa-solid fa-ellipsis"></i></span>';
                            }
                        },
                    }, {
                        orderable: false,
                        searchable: false,
                        width: '20%',
                        targets: 1,
                        render: function(data, type, row, meta) {
                            let fullStr = data;
                            if (typeof data !== "undefined" && data !== "" &&
                                data !== null) {
                                shortStr = data.substr(0, 28) + " ...";
                                if (fullStr.length > 25) {
                                    return '<span class="" data-toggle="tooltip" data-html="true" data-placement="auto" title="' +
                                        fullStr + '">' + shortStr + '</span>';
                                } else {
                                    return fullStr;
                                }
                            } else {
                                return "---";
                            }
                        }
                    }, {
                        orderable: false,
                        searchable: false,
                        width: 'auto',
                        targets: 2,
                        render: function(data, type, row, meta) {
                            let fullStr = data;
                            let shortStr = data.substr(0, 58) + " ..."
                            if (fullStr.length > 55) {
                                return '<span class="" data-toggle="tooltip" data-html="true" data-placement="auto" title="' +
                                    fullStr + '">' + shortStr + '</span>';
                            } else {
                                return fullStr;
                            }
                        }
                    }],
                    order: [
                        [0, 'asc']
                    ],
                    ordering: false,
                    select: false,
                    processing: false,
                    destroy: true,
                    buttons: []
                });
        });
        $('#modal-listAdditionalLinkDocs').on('hidden.bs.modal', function(e) {
            console.log('#modal-listAdditionalLinkDocs hidden!');
        });
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        $("#filterArchiveYear").on("focus", function() {

            $("#filterArchiveYear").on("keyup", function(e) {
                if (e.keyCode === 13 && $("#filterArchiveYear").val() !== "") {
                    let params = new URLSearchParams(document.location.search);
                    let year = params.get("year");
                    params.set('year', $("#filterArchiveYear").val());
                    document.location.search = params.toString();
                    console.log('filterArchiveYear!', 'enter', params.toString());
                } else if (e.keyCode === 13 && $("#filterArchiveYear").val() == "") {
                    let params = new URLSearchParams(document.location.search);
                    params.delete("year");
                    document.location.search = params.toString();
                }
            });

        });
        //
        let params = new URLSearchParams(document.location.search);
        let year = params.get("year");
        let relID = params.get("rel");
        console.log('URLSearchParams year', year);
        if (year !== null) {
            $("#filterArchiveYear").val(year);
        } else {
            $("#filterArchiveYear").val('');
        }
        if (checkVal(relID)) {
            let checkYear = ajaxRequest_checkOnCurrYearAsync(relID);
            if (checkYear) {
                console.log('URLSearchParams', 'check rel doc on current year >>>', checkYear);
                let щдвurl = 'index.php?' + params.toString();
                params.set('mode', 'thisyear');
                let newurl = 'index.php?' + params.toString();
                $.confirm({
                    title: "Сообщение от сервиса Почта",
                    content: 'По умолчанию внешние ссылкы на документ всегда ведут в соответствующий раздел Архива, но документ, который вы выбрали, относится к текущему году, поэтому вы будете автоматически перемещены с выбранным документом в раздел Входящие, либо же выберите "Остаться в архиве". Если вы все-таки хотели попасть в текущий год, но сделали неправильный выбор, просто перегрузите страницу, и сделайте его еще раз.',
                    autoClose: 'redirect|30000',
                    type: 'red',
                    typeAnimated: true,
                    columnClass: 'col-md-12',
                    draggable: false,
                    buttons: {
                        redirect: {
                            text: 'Перейти во Входящие',
                            btnClass: 'btn-blue',
                            action: function() {
                                window.location.replace(newurl);
                            }
                        },
                        close: {
                            text: 'Остаться в архиве',
                            btnClass: 'btn-default',
                            action: function() {}
                        }
                    }
                });
            }
        }
    });
</script>

<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/css/common-modals.css">
<?php
// ----- ----- ----- ----- -----
// Подключаем форму редактирования, форму поиска и выводим таблицу
// :::
include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3 . "/forms/mailbox-incoming-filters.php";
// ----- ----- ----- ----- -----
?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-incoming.css">
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-incoming-details.css">

<section>
    <div id="divOnlyIspolMe" class="form-check checkbox mb-3">
        <input class="form-check-input" type="checkbox" value="" id="chkOnlyIspolMe">
        <label class="form-check-label ml-2" for="chkOnlyIspolMe" data-toggle="popover" data-content='<p>Данный чекбокс фиксирует вас как ответственного в блоке фильтров и может быть снят только повторным кликом. Установка сохраняется как персональная настройка сервиса в БД. Очистка фильтров данный чекбокс не изменит.</p>'>
            <b>Показывать только документы, где я ответственный</b>
        </label>
    </div>

    <div id="mail-main-inbox" class="">
        <div class="demo-html"></div>
        <table id="inbox" class="table display compact" cellspacing="1" width="100%">
            <thead>
                <tr>
                    <th><i class='fa-solid fa-ellipsis-vertical'></i></th>
                    <th>Т</th>
                    <th>№</th>
                    <th>Рег</th>
                    <th>Тема документа</th>
                    <th>Организация-отправитель</th>
                    <th>Получатель</th>
                    <th>Ответственный</th>
                    <th data-toggle="tooltip" title="<p>Показывает установлен или нет по документу дедлайн.</p>">Д</th>
                    <th data-toggle="tooltip" title="<p>Если вы видите какой-либо символ в этом столбце - контроль исполнения (КИ) включен. Расшифровку символа можно поссмотреть в легенде под таблице с документами, либо наведя указатель мыши на сам символ.</p>">
                        КИ</th>
                    <th data-toggle="tooltip" title="<p>Основной прикрепленный файл к документу.</p>"><span class="">Ф</span><span class="small" style="font-size:65%">o</span></th>
                    <th data-toggle="tooltip" title="<p>Дполнительные прикрепленные файл(ы) к документу. Посмотреть список этих файлов можно во всплывающем окне кликнув на иконке в этом столбце. Допускается любое количество файлов и любых типов.</p>">
                        <span class="">Ф</span><span class="small" style="font-size:65%">д</span>
                    <th data-toggle="tooltip" title="<p>Ссылка в виде номера документа в исходящих, который является ответным либо запросом ответа по отношению к документу в строке списка.</p>">
                        <span class="">ЗО</span><span class="small" style="font-size:65%">o</span>
                    </th>
                    <th data-toggle="tooltip" title="<p>Дополнительные исходящие документы, которые были отмечены как те, на которые данный документ является ответным. Посмотреть список этих документов можно во всплывающем окне кликнув на иконке в этом столбце.</p>">
                        <span class="">ЗО</span><span class="small" style="font-size:65%">д</span>
                    </th>
                    <th data-toggle="tooltip" title="<p>Списки связанных входящих и исходящих документов, а также договоров и контрагентов, которые были отмечены как связанные с данным документом. Эти связи устанавливаются вручную при редактировании документа (вкладка Связи).</p>">
                        СВ</th>
                    <th data-toggle="tooltip" title="<p>Журнал действий с документом.</p>">Ж</th>
                    <th data-toggle="tooltip" title="<p>Чат по документу. Можно оставлять сообщения как непосредственно в чате, так и через форму редактирвоания. Комментарий исполнителя и дополнительная инофрмация о документе из формы редактирования также появится в этом чате.</p>">
                        К</th>
                </tr>
            </thead>
        </table>
    </div>
</section>

<div class="modal fade" data-backdrop="true" id="modal-noControlIspolActive" tabindex="-1" role="dialog" aria-labelledby="modal-noControlIspolActive-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-body">
                <div class="container-fluid">
                    <h5 class="modal-message">По документу контроль исполнения (КИ) не включен</h5>
                </div>
            </div>
        </div> <!-- end of modal content -->
    </div>
</div> <!-- end of modal -->

<?php
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 

# МОДАЛЬНОЕ ОКНО 
# Контроль исполнения по документу
# >>>>>

?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-incoming-modal-logCheckouts.css">
<div class="modal fade" data-backdrop="true" id="modal-logCheckouts" tabindex="-1" role="dialog" aria-labelledby="modal-logCheckouts-label" aria-hidden="true">
    <div class="modal-dialog modal-lg custom-modal" role="document">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-logCheckouts-label">Контроль исполнения по документу</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <table id="table-logCheckouts" class="table table-striped table-borderless" cellspacing="0" width="100%">
                        <thead class="thead-dark" style="display:none">
                            <tr>
                                <th>Время изменения</th>
                                <th>Ответственный</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div> <!-- end of modal content -->
    </div>
</div> <!-- end of modal -->
<?php

# <<<<< 

##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 


##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 

# МОДАЛЬНОЕ ОКНО 
# Журнал событий по документу
# >>>>>

?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-incoming-modal-logChanges.css">
<div class="modal fade" data-backdrop="true" id="modal-logChanges" tabindex="-1" role="dialog" aria-labelledby="modal-logChanges-label" aria-hidden="true">
    <div class="modal-dialog modal-lg custom-modal" role="document">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-logChanges-label">Журнал событий по документу</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <table id="table-logChanges" style="margin-bottom:20px !important" class="table table-striped table-borderless" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>Время</th>
                                <th>Т</th>
                                <th>Сервис/Пользователь</th>
                                <th>Описание</th>
                                <th>Новое значение</th>
                                <th>Старое значение</th>
                            </tr>
                        </thead>
                    </table>
                    <p class="text-danger small">*Операции связанные с загрузкой и удалением файлов, как основного так и
                        дополнительных, временно не фиксируются в этом журнале.</p>
                </div>
            </div>
        </div> <!-- end of modal content -->
    </div>
</div> <!-- end of modal -->
<?php

# <<<<< 

##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 


##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 

# МОДАЛЬНОЕ ОКНО 
# Комментарии по документу + форма редактирования
# >>>>>

?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-incoming-modal-logComments.css">
<div class="modal fade" data-backdrop="true" id="modal-logComments" tabindex="-1" role="dialog" aria-labelledby="modal-logComments-label" aria-hidden="true">
    <div class="modal-dialog modal-lg custom-modal" role="document">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-logComments-label">Комментарии по документу</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mt-2">
                <div class="container-fluid">
                    <table id="table-logComments" class="table table-borderless" cellspacing="0" width="100%">
                        <thead class="thead-dark" style="display:none">
                            <tr>
                                <th>Время</th>
                                <th>Пользователь</th>
                                <th>Комментарий</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div> <!-- end of modal content -->
    </div>
</div> <!-- end of modal -->

<div id="editorform-logComments" class="editorform simple">
    <div class="section d-flex flex-column">
        <div class="block w-100">
            <fieldset class="field">
                <editor-field name="<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments.commentText"></editor-field>
            </fieldset>
            <fieldset class="field">
                <editor-field name="<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments.koddocmail"></editor-field>
            </fieldset>
        </div>
    </div>
</div>
<?php

# <<<<< 

##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 


##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 

# МОДАЛЬНОЕ ОКНО 
# Прикрепленные дополнительные файлы + форма редактирования
# >>>>>

?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-incoming-modal-listAddFiles.css">
<div class="modal fade" data-backdrop="true" id="modal-listAddFiles" tabindex="-1" role="dialog" aria-labelledby="modal-listAddFiles-label" aria-hidden="true">
    <div class="modal-dialog modal-lg custom-modal" role="document">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-listAddFiles-label">Прикрепленные дополнительные файлы с комментариями
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="space20"></div>
                    <table id="table-listAddFiles" class="table table-borderless table-striped mb-3" cellspacing="0" width="100%">
                        <thead class="thead-dark" style="display:none">
                            <tr>
                                <th>Имя файла</th>
                                <th>Комментарий</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div> <!-- end of modal content -->
    </div>
</div> <!-- end of modal -->

<div id="editorform-listAddFiles" class="editorform simple">
    <div class="section d-flex flex-column">
        <div class="block w-100">
            <fieldset class="field">
                <editor-field name="<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>.comment"></editor-field>
            </fieldset>
            <fieldset class="field">
                <editor-field name="<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>.koddocmail"></editor-field>
            </fieldset>
        </div>
    </div>
</div>
<?php

# <<<<< 

##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 


##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 

# МОДАЛЬНОЕ ОКНО 
# Связанные исходящие письма
# >>>>>

?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-incoming-modal-listLinkDocs.css">
<div class="modal fade" data-backdrop="true" id="modal-listLinkDocs" tabindex="-1" role="dialog" aria-labelledby="modal-listLinkDocs-label" aria-hidden="true">
    <div class="modal-dialog modal-lg custom-modal" role="document">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-listLinkDocs-label">Еще исходящие документы, связанные как
                    "запрос-ответ" (ЗО)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="space20"></div>
                    <table id="table-listLinkDocs" class="table table-borderless table-striped" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>№</th>
                                <th>Регистрация</th>
                                <th>Тема документа</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div id="wrap-linkedOutgoing-showFiles" style="display: none">
                <p class="mb-1"><strong>Прикрепленные файлы к документу № 1-1/<span id="linkedOutgoing-docID"></span></strong></p>
                <div id="outputArea-linkedOutgoing-showFiles" class="mb-2"></div>
                <p class="text-muted small">*Пока выводится только основной прикрепленный файл к записи, но скоро будут
                    выводиться все</p>
            </div>
        </div> <!-- end of modal content -->
    </div>
</div> <!-- end of modal -->
<?php

# <<<<< 

##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 


##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 

# МОДАЛЬНОЕ ОКНО 
# Связанные Входящие / Исходящие / Договора
# >>>>>

?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-incoming-modal-listAdditionalLinkDocs.css">
<div class="modal fade" data-backdrop="true" id="modal-listAdditionalLinkDocs" tabindex="-1" role="dialog" aria-labelledby="modal-listAdditionalLinkDocs-label" aria-hidden="true">
    <div class="modal-dialog modal-lg custom-modal mail-features" role="document">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-listAdditionalLinkDocs-label">Связанные договорыСвязанные Входящие /
                    Исходящие /
                    Договоры / Контрагенты</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="title text-left text-dark mb-3">Связанные входящие письма</div>
                    <table id="table-listAdditionalLinkDocsMInc" class="table table-borderless table-striped mb-3" cellspacing="0" width="100%">
                        <thead class="thead-dark" style="display:none">
                            <tr>
                                <th>№ вх</th>
                                <th>Заказчик</th>
                                <th>Тема письма</th>
                            </tr>
                        </thead>
                    </table>

                    <div class="title text-left text-dark mb-3">Связанные исходящие письма</div>
                    <table id="table-listAdditionalLinkDocsMOut" class="table table-borderless table-striped mb-3" cellspacing="0" width="100%">
                        <thead class="thead-dark" style="display:none">
                            <tr>
                                <th>№ исх</th>
                                <th>Заказчик</th>
                                <th>Тема письма</th>
                            </tr>
                        </thead>
                    </table>

                    <div class="title text-left text-dark mb-3">Связанные договоры</div>
                    <table id="table-listAdditionalLinkDocsDog" class="table table-borderless table-striped" cellspacing="0" width="100%">
                        <thead class="thead-dark" style="display:none">
                            <tr>
                                <th>№ дог</th>
                                <th>Заказчик</th>
                                <th>Объект</th>
                                <th>Название договора</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div> <!-- end of modal content -->
    </div>
</div> <!-- end of modal -->

<?php

# <<<<< 

##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
?>