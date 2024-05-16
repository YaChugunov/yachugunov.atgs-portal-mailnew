<?php
date_default_timezone_set('Europe/Moscow');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('memory_limit', '200M');

$ispolStatus_msg_0         = '<span class="errtext text-primary">В текущей конфигурации сервиса эта отметка будет недоступна, если вы не являетесь исполнителем по документу и(или) по нему не включен режим КИ (контроль исполнения).</span>';
$ispolStatus_msg_1         = '<span class="errtext text-primary">В текущей конфигурации сервиса управлять напоминаниями и уведомлениями исполнителю(ям) могут только сами исполнители. Кроме того, также должен быть активен режим КИ (контроль исполнения) и установлен дедлайн.</span>';
$ispolStatus_msg_2         = '<span class="inftext text-primary">Текущее состояние отметки по документу, сохраненное в БД</span>';
$ispolStatus_msg_3         = '<span class="inftext text-primary">Сначала сделайте отметку об исполнении документа</span>';
$ispolStatusOtherOn_msg_0  = '<span class="inftext text-primary">Вы можете отметить документ как исполненный за остальных ответственных</span>';
$ispolStatusOtherOff_msg_0 = '<span class="inftext text-primary">Вы можете снять отметку с документа как исполненного за других ответственных</span>';
$tab3_ispolList_msg_0      = '<span class="errtext text-primary">Выберите ответственного(ых) либо сделайте отметку ниже \"Без ответственных\"</span>';
$deadline_default_days     = 14;
$popoverLinkToKnow         = "<span class='knoweledge-base-link'><a href='#nolink>Подробнее в разделе Помощь</a></span>";

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
$_QRY_UserSettings                             = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM mailbox_userSettingsUI WHERE userid = '{$_SESSION['id']}'"));
$_SESSION['incoming_setControlIspolOnStart'] = $_QRY_UserSettings ? $_QRY_UserSettings['incoming_setControlIspolOnStart'] : '0';
$_SESSION['incoming_setDeadlineOnStart']     = $_QRY_UserSettings ? $_QRY_UserSettings['incoming_setDeadlineOnStart'] : '0';
// ----- ----- ----- ----- -----
if (!isset($_SESSION['incoming_pageLength']) && empty($_SESSION['incoming_pageLength'])) {
    $_SESSION['incoming_pageLength'] = !empty($_QRY_UserSettings['incoming_pageLengthDefault']) ? $_QRY_UserSettings['incoming_pageLengthDefault'] : '15';
}
// ----- ----- ----- ----- -----
if (!isset($_SESSION['incoming_pageCurrent']) || "" == $_SESSION['incoming_pageCurrent']) {
    $_SESSION['incoming_pageCurrent'] = 'nodraw';
}
// ----- ----- ----- ----- -----
if (!isset($_SESSION['incoming_selectedRowID']) || "" == $_SESSION['incoming_selectedRowID']) {
    $_SESSION['incoming_selectedRowID'] = '';
}
// ----- ----- ----- ----- -----
if (isset($_GET['mode'])) {
    switch ($_GET['mode']) {
        case 'thisyear':
            $__subsubtitle                  = 'Текущий год <span class="text-danger">/ ' . date("Y") . '</span>';
            $startTableDate                 = '"' . (date('Y') - 1) . '-07-01 00:00:01"';
            $_SESSION['in_startTableDate']  = $startTableDate;
            $endTableDate                   = '"' . date('Y') . '-12-31 23:59:59"';
            $_SESSION['in_endTableDate']    = $endTableDate;
            if (isset($_GET['rel'])) {
                $__relID = $_GET['rel'];
            } else {
                $__relID = "norel";
            }
            break;
        case 'archive':
            if (isset($_GET['year']) && $_GET['year'] >= 2007 && $_GET['year'] <= date('Y')) {
                $__subsubtitle                  = 'Архив <span class="text-danger">/ ' . $_GET['year'] . '</span>';
                $startTableDate                 = '"' . $_GET["year"] . '-01-01 00:00:01"';
                $_SESSION['inArch_startTableDate']  = $startTableDate;
                $endTableDate                   = '"' . $_GET["year"] . '-12-31 23:59:59"';
                $_SESSION['inArch_endTableDate']    = $endTableDate;
            } else {
                $__subsubtitle                   = "Весь архив";
                $startTableDate                  = '"2007-01-01 00:00:01"';
                $_SESSION['inArch_startTableDate'] = $startTableDate;
                $endTableDate                    = '"' . date('Y') . '-12-31 23:59:59"';
                $_SESSION['inArch_endTableDate']   = $endTableDate;
            }
            break;
        default:
            $__subsubtitle                   = 'Текущий год <span class="text-danger">/ ' . date("Y") . '</span>';
            $startTableDate                  = '"' . (date('Y') - 1) . '-07-01 00:00:01"';
            $_SESSION['in_startTableDate'] = $startTableDate;
            $endTableDate                    = '"' . date('Y') . '-12-31 23:59:59"';
            $_SESSION['in_endTableDate']   = $endTableDate;
    }
} else {
    $__subsubtitle                   = 'Текущий год <span class="text-danger">/ ' . date("Y") . '</span>';
    $startTableDate                  = '"' . (date('Y') - 1) . '-07-01 00:00:01"';
    $_SESSION['in_startTableDate'] = $startTableDate;
    $endTableDate                    = '"' . date('Y') . '-12-31 23:59:59"';
    $_SESSION['in_endTableDate']   = $endTableDate;
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
        // console.log('isValidDate', str, d, str.indexOf(d.format('DD.DM.YYYY')) >= 0)
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
            // console.log('function checkArrOnString >>>', stringAsArray.split(","), stringOnSearch, arr.indexOf(stringOnSearch));
            return arr.indexOf(stringOnSearch);
        } else {
            return -2;
        }
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // Функция задержки на действие
    //
    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function() {
                callback.apply(context, args);
            }, ms || 0);
        };
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
    var reqField_showIspolOnly = {
        showIspolOnly: function(response) {}
    };
    //
    function ajaxRequest_saveToDB_showIspolOnly(responseHandler) {
        var _showispolonly = $('#chkOnlyIspolMe').prop('checked');
        var _userid = <?php echo $_SESSION['id']; ?>;

        // Fire off the request_addItem to /form.php
        request_showIspolOnly = $.ajax({
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-saveShowIspolonly.php',
            type: "post",
            cache: false,
            data: {
                userID: _userid,
                showispolonly: _showispolonly
            },
            success: reqField_showIspolOnly[responseHandler]
        });
        // console.log('userID: ' + _userid + ' / showispolonly: ' + _showispolonly);
        // Callback handler that will be called on success
        request_showIspolOnly.done(function(request_showIspolOnly, textStatus, jqXHR) {
            res_showIspolOnly = request_showIspolOnly.replace(new RegExp("\\r?\\n", "g"), "");
            // console.log("Response showIspolOnly (save): " + res_showIspolOnly);
            ajaxRequest_loadFromDB_showIspolOnly('showIspolOnly');
        });
        // Callback handler that will be called on failure
        request_showIspolOnly.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_showIspolOnly.always(function() {

        });
    }
    //
    function ajaxRequest_loadFromDB_showIspolOnly(responseHandler) {
        var _userid = <?php echo $_SESSION['id']; ?>;

        // Fire off the request_addItem to /form.php
        request_showIspolOnly = $.ajax({
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getShowIspolonly.php',
            type: "post",
            cache: false,
            data: {
                userID: _userid
            },
            success: reqField_showIspolOnly[responseHandler]
        });
        // console.log('userID: ' + _userid);
        // Callback handler that will be called on success
        request_showIspolOnly.done(function(request_showIspolOnly, textStatus, jqXHR) {
            res_showIspolOnly = request_showIspolOnly.replace(new RegExp("\\r?\\n", "g"), "");
            $("#chkOnlyIspolMe").prop('checked', (res_showIspolOnly === '1') ? true : false);
            // console.log("Response window.showIspolOnly (load): " + res_showIspolOnly);
        });
        // Callback handler that will be called on failure
        request_showIspolOnly.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_showIspolOnly.always(function() {

        });
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
            // console.log('Ajax upload response:', response);
            response.forEach(function(msg) {
                $('#uploadFiles-result > table').append(msg);
            });
        }
    };

    function ajaxRequest_uploadAttachedFiles(formData, responseHandler) {

        // Fire off the request_addItem to /form.php
        request_uploadAttachedFiles = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-uploadAttachedFiles.php',
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
            // console.log('listAttachedFiles:', response);
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
                // console.log('Ajax list response:', response, x1);
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

    function ajaxRequest_listAttachedFiles(koddocmail, responseHandler) {

        // Fire off the request_addItem to /form.php
        request_listAttachedFiles = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-listAttachedFiles.php',
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
            // console.log('request_listAttachedFiles (res_attached):', res_attached)
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
                // console.log('Deleted! / ' + res);
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
                // console.log('Error!');
            }
        }
    };

    function ajaxRequest_deleteAttachedFile(rowid, responseHandler) {

        // Fire off the request_addItem to /form.php
        request_deleteAttachedFile = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-deleteAttachedFile.php',
            cache: false,
            data: {
                rowID: rowid
            },
            success: reqField_deleteAttachedFile[responseHandler]
        });
        // Callback handler that will be called on success
        request_deleteAttachedFile.done(function(response, textStatus, jqXHR) {
            // console.log('request_deleteAttachedFile', response)
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
                // console.log('Ajax getLastDeadlineMail response:', res_trigger, res_timestamp);
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
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getLastDeadlineMail.php',
            cache: false,
            data: {
                koddocmail: koddocmail,
                userid: userid
            },
            success: reqField_getLastDeadlineMail[responseHandler]
        });
        // Callback handler that will be called on success
        request_getLastDeadlineMail.done(function(response, textStatus, jqXHR) {
            // console.log('request_getLastDeadlineMail', response)
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
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getLastNotify.php',
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
            // console.log('request_getLastNotify', response)
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
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getLastAction.php',
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
            // console.log('request_getLastAction', response)
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
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getLinkedRelFiles.php',
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
                        // console.log('request_getLinkedRelFiles X', i, x1[i])
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
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-countDocComments.php',
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
            // console.log('DocComments has counted!', res_reqField_countDocComments)
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
    var reqField_getUserCheckout = {
        getUserCheckout: function(response) {
            // console.log('RESULT Function ajaxRequest_getUserCheckout():', response)
        }
    };
    //
    function ajaxRequest_getUserCheckout(koddocmail, userid, responseHandler) {
        // console.log('REQ Function ajaxRequest_getUserCheckout(), pars:', koddocmail, userid)
        // Fire off the request_addItem to /form.php
        request_getUserCheckout = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getUserCheckout.php',
            cache: false,
            data: {
                userid: userid,
                koddocmail: koddocmail
            },
            success: reqField_getUserCheckout[responseHandler]
        });
        // Callback handler that will be called on success
        request_getUserCheckout.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
            if (response !== "-1" && response !== "-2" && response !== "-3") {
                x1 = res.split("///");
                var userID = String(<?php echo $_SESSION['id']; ?>);
                if (x1.length > 0) {
                    // Обновляем чекбокс статуса исполнения
                    if (x1[0] == "1") {
                        $('#DTE_Field_ispolStatus_0').prop('checked', true);
                        editor_incoming.field('ispolStatus').val(1);
                    } else {
                        $('#DTE_Field_ispolStatus_0').prop('checked', false);
                        editor_incoming.field('ispolStatus').val(0);
                    }
                    // Disable чекбокс статуса, если пользователь не в исполнителях
                    if (x1[1].indexOf(userID) !== -1 && editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive').val() == "1") {
                        // $('.ispolStatus-visible').css('display', 'table-cell');
                        // $('#DTE_Field_ispolStatus_0').prop('disabled', false);
                        editor_incoming.field('ispolStatus').enable();
                        editor_incoming.field('ispolStatus').labelInfo('<?php echo $ispolStatus_msg_2; ?>');
                        editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline').enable();
                        // console.log('user in ispol', x1[1].indexOf(userID));
                    } else {
                        // $('.ispolStatus-visible').css('display', 'none');
                        $('.ispolStatus-chkText').css('color', '#AAA');
                        // $('#DTE_Field_ispolStatus_0').prop('disabled', true);
                        editor_incoming.field('ispolStatus').disable();
                        editor_incoming.field('ispolStatus').labelInfo('<?php echo $ispolStatus_msg_0; ?>');

                        editor_incoming.field(
                                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder1')
                            .disable();
                        editor_incoming.field(
                                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder2')
                            .disable();
                        editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyDL').disable();
                        $('#ispolStatus_msg_1').html('<?php echo $ispolStatus_msg_1; ?>')
                        // console.log('user not in ispol', x1[1].indexOf(userID));
                    }
                }
            }
            // console.log('ispolStatus is recieved!', response)
        });
        // Callback handler that will be called on failure
        request_getUserCheckout.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_getUserCheckout.always(function() {});
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
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getDocTypeLock.php',
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
            // console.log('getDocTypeLock is recieved!', res);
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
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    //
    function ajaxRequest_getRelativeOutgoingDataAsync(rowid) {
        var result = false;
        $.ajax({
            async: false,
            cache: false,
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-getRelativeOutgoingData.php',
            data: {
                rowid: rowid
            },
            success: function(response) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                if (response != 'no data' && response != 'error -2' && response != 'error -3') {
                    resArr = JSON.parse(response);
                    // result = response.replace(new RegExp("\\r?\\n", "g"), "");
                    result = JSON.parse(response);
                } else {
                    result = '';
                }
                // console.log('ajaxRequest_getRelativeOutgoingDataAsync', result);
            }
        });
        return result;
    }
    //
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    //
    // trim, rtrim, ltrim
    function trim(str, chr) {
        var rgxtrim = (!chr) ? new RegExp('^\\s+|\\s+$', 'g') : new RegExp('^' + chr + '+|' + chr + '+$', 'g');
        return str.replace(rgxtrim, '');
    }

    function rtrim(str, chr) {
        var rgxtrim = (!chr) ? new RegExp('\\s+$') : new RegExp(chr + '+$');
        return str.replace(rgxtrim, '');
    }

    function ltrim(str, chr) {
        var rgxtrim = (!chr) ? new RegExp('^\\s+') : new RegExp('^' + chr + '+');
        return str.replace(rgxtrim, '');
    }
    //
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    //
    function ajaxRequest_checkInUseDocStateDataAsync(action, koddocmail) {
        var result = false;
        $.ajax({
            async: false,
            cache: false,
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-checkInUseDocState.php',
            data: {
                action: action,
                koddocmail: koddocmail
            },
            success: function(response) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                if (response !== 'error on set' && response !== 'error on unset' && response !==
                    'no data on check' && response !== 'error on check' && response !== 'no data' &&
                    response !== 'error -1' && response !== 'error -2' && response !== 'unset all ok' &&
                    response !== 'error on unset all') {
                    // resArr = JSON.parse(response);
                    // result = response.replace(new RegExp("\\r?\\n", "g"), "");
                    result = JSON.parse(response);
                } else if (response === 'unset all ok') {
                    result = response;
                } else {
                    result = 'no result';
                }
                console.log('ajaxRequest_checkInUseDocStateDataAsync', result);
            }
        });
        return result;
    }
    //
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    //
    function ajaxRequest_getSelectOptions(scripturl, fieldname) {
        result = "";
        $.ajax({
            url: scripturl,
            async: false,
            cache: false,
            type: 'post',
            dataType: 'json',
            success: function(json) {
                result = json.options[fieldname];
            }
        });
        return result;
    }
    //
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    //
    function ajaxRequest_checkOnSimilar(text) {
        result = "";
        $.ajax({
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-mailboxIncoming-checkManulaContragentOnSimilar.php',
            data: {
                text: text,
            },
            async: false,
            cache: false,
            type: 'post',
            dataType: 'json',
            success: function(data) {
                result = data;
            }
        });
        return result;
    }
    //
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    //
    // Pipelining function for DataTables. To be used to the `ajax` option of DataTables
    //
    $.fn.dataTable.pipeline = function(opts) {
        // Configuration options
        var conf = $.extend({
                pages: 5, // number of pages to cache
                url: '', // script url
                data: null, // function or object with parameters to send to the server
                // matching how `ajax.data` works in DataTables
                method: 'GET' // Ajax HTTP method
            },
            opts
        );

        // Private variables for storing the cache
        var cacheLower = -1;
        var cacheUpper = null;
        var cacheLastRequest = null;
        var cacheLastJson = null;

        return function(request, drawCallback, settings) {
            var ajax = false;
            var requestStart = request.start;
            var drawStart = request.start;
            var requestLength = request.length;
            var requestEnd = requestStart + requestLength;

            if (settings.clearCache) {
                // API requested that the cache be cleared
                ajax = true;
                settings.clearCache = false;
            } else if (
                cacheLower < 0 ||
                requestStart < cacheLower ||
                requestEnd > cacheUpper
            ) {
                // outside cached data - need to make a request
                ajax = true;
            } else if (
                JSON.stringify(request.order) !==
                JSON.stringify(cacheLastRequest.order) ||
                JSON.stringify(request.columns) !==
                JSON.stringify(cacheLastRequest.columns) ||
                JSON.stringify(request.search) !==
                JSON.stringify(cacheLastRequest.search)
            ) {
                // properties changed (ordering, columns, searching)
                ajax = true;
            }

            // Store the request for checking next time around
            cacheLastRequest = $.extend(true, {}, request);

            if (ajax) {
                // Need data from the server
                if (requestStart < cacheLower) {
                    requestStart = requestStart - requestLength * (conf.pages - 1);

                    if (requestStart < 0) {
                        requestStart = 0;
                    }
                }

                cacheLower = requestStart;
                cacheUpper = requestStart + requestLength * conf.pages;

                request.start = requestStart;
                request.length = requestLength * conf.pages;

                // Provide the same `data` options as DataTables.
                if (typeof conf.data === 'function') {
                    // As a function it is executed with the data object as an arg
                    // for manipulation. If an object is returned, it is used as the
                    // data object to submit
                    var d = conf.data(request);
                    if (d) {
                        $.extend(request, d);
                    }
                } else if ($.isPlainObject(conf.data)) {
                    // As an object, the data given extends the default
                    $.extend(request, conf.data);
                }

                return $.ajax({
                    type: conf.method,
                    url: conf.url,
                    data: request,
                    dataType: 'json',
                    cache: false,
                    success: function(json) {
                        cacheLastJson = $.extend(true, {}, json);

                        if (cacheLower != drawStart) {
                            json.data.splice(0, drawStart - cacheLower);
                        }
                        if (requestLength >= -1) {
                            json.data.splice(requestLength, json.data.length);
                        }

                        drawCallback(json);
                    }
                });
            } else {
                json = $.extend(true, {}, cacheLastJson);
                json.draw = request.draw; // Update the echo for each response
                json.data.splice(0, requestStart - cacheLower);
                json.data.splice(requestLength, json.data.length);

                drawCallback(json);
            }
        };
    };

    // Register an API method that will empty the pipelined data, forcing an Ajax
    // fetch on the next draw (i.e. `table.clearPipeline().draw()`)
    DataTable.Api.register('clearPipeline()', function() {
        return this.iterator('table', function(settings) {
            settings.clearCache = true;
        });
    });
    //
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    //
    var editor_incoming;
    var table_incoming;
    //
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    //
    $(document).ready(function() {
        editor_incoming = new $.fn.dataTable.Editor({
            display: "bootstrap",
            ajax: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process.php",
            table: "#inbox",
            formOptions: {
                main: {
                    focus: 1, // Initial focus on title_body input field.
                    onBackground: 'none' // Do not close form on click outside.
                }
            },
            i18n: {
                create: {
                    title: '<h3 class="editorform-header-title">Новая запись</h3>'
                },
                edit: {
                    title: '<h3 class="editorform-header-title">Изменить параметры записи</h3>'
                },
                remove: {
                    button: 'Удалить',
                    title: '<h3 class="editorform-header-title">Удалить запись</h3>',
                    submit: 'Удалить',
                    confirm: {
                        _: 'Вы действительно хотите удалить %d записей?',
                        1: 'Вы действительно хотите удалить эту запись?'
                    }
                },
                error: {
                    system: 'Ошибка в работе сервиса! Свяжитесь с администратором.'
                },
                multi: {
                    title: 'Несколько значений',
                    info: '',
                    restore: 'Отменить изменения'
                },
                datetime: {
                    previous: '<',
                    next: '>',
                    months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август',
                        'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
                    ],
                    weekdays: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб']
                }
            },
            template: '#customForm-mail-main-inbox',
            fields: [{
                    label: "Вх № (АТГС)",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docID",
                    def: function() {
                        newDocID = '<?php echo $__newDocID; ?>';
                        return newDocID;
                    },
                    className: 'block'
                }, {
                    label: "Основное исходящее письмо, для которого редактируемое будет ответным ( Дата / № АТГС / № орг / Название орг / Тема )",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel",
                    type: "select",
                    placeholder: "Начинайте вводить часть даты, номера письма АТГС или организации, названия компании или описания письма",
                    multiple: false,
                    separator: ',',
                    className: 'block'
                }, {
                    label: "Изменить тип указанного выше исходящего письма на 'Запрос ответа' и связать с текущим входящим документом (исходящий запрос - текущий ответ)",
                    type: "checkbox",
                    name: "enbl_outbox_docType_change",
                    unselectedValue: 0,
                    options: {
                        "": 1
                    }
                }, {
                    label: "Отметить указанный исходящий документ как исполненный для всех ответственных",
                    type: "checkbox",
                    name: "set_outbox_fullCheckout",
                    unselectedValue: 0,
                    options: {
                        "": 1
                    }
                }, {
                    label: "Дополнительные исходящие письма, на которые текущий входящий документ можно считать ответным",
                    type: "checkbox",
                    name: "enbl_outbox_rowIDadd_rel",
                    unselectedValue: 0,
                    options: {
                        "": 1
                    }
                }, {
                    label: "Дополнительные исходящие письма, для которых редактируемое будет ответным ( Дата / Номер / Контрагент / Тема )",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel",
                    type: "select",
                    placeholder: 'Начинайте вводить часть даты, номера, названия компании или описания письма',
                    multiple: false,
                    separator: ',',
                    className: 'block'
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel"
                }, {
                    label: "Дата / Номер / Контрагент / Тема",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docIDs_links",
                    type: "select",
                    placeholder: 'Начинайте вводить часть даты, номера, названия компании или описания письма',
                    multiple: false,
                    separator: ',',
                    className: 'block'
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_rowIDs_links"
                }, {
                    label: "Дата / Номер / Контрагент / Тема",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_docIDs_links",
                    type: "select",
                    placeholder: 'Начинайте вводить часть даты, номера, названия компании или описания письма',
                    multiple: false,
                    separator: ',',
                    className: 'block'
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDs_links"
                }, {
                    label: "Номер / Название",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.dognet_docIDs_links",
                    type: "select",
                    placeholder: 'Начинайте вводить часть номера или названия договора',
                    multiple: false,
                    separator: ',',
                    className: 'block'
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.dognet_rowIDs_links"
                }, {
                    label: "Название",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.sp_docIDs_links",
                    type: "select",
                    placeholder: 'Начинайте вводить часть названия контрагента',
                    multiple: false,
                    separator: ',',
                    className: 'block'
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.sp_rowIDs_links"
                }, {
                    label: "Тип",
                    name: '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docType',
                    type: "select",
                    def: 0,
                    placeholder: 'Тип документа',
                    className: "block"
                }, {
                    label: "Дата",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDate",
                    type: "datetime",
                    def: function() {
                        return moment();
                    },
                    format: 'DD.MM.YYYY HH:mm:ss',
                    className: "block"
                }, {
                    label: "Тема",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docAbout",
                    attr: {
                        placeholder: 'Краткое описание документа'
                    },
                    className: "block"
                }, {
                    label: "Контрагент в Справочнике",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz",
                    type: "select2",
                    placeholder: '---',
                    className: 'block'
                }, {
                    label: "Название контрагента (ручной ввод)",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender",
                    className: 'block'
                }, {
                    label: "Подписант",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSenderName",
                    className: 'block'
                }, {
                    label: "Ручной ввод",
                    type: "checkbox",
                    name: "enblSenderManual",
                    unselectedValue: 0,
                    options: {
                        "": 1
                    }
                }, {
                    label: "Адресант",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docRecipient_kodzayvtel",
                    type: "select",
                    placeholder: "Адресант документа",
                    className: "block"
                }, {
                    label: "№ исх (отпр)",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSourceID",
                    attr: {
                        placeholder: 'Исх № орг'
                    },
                    className: 'block'
                }, {
                    label: "Дата исх (отпр)",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSourceDate",
                    type: "datetime",
                    def: function() {
                        return moment();
                    },
                    format: 'DD.MM.YYYY',
                    className: 'block'
                }, {
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID",
                    type: "upload",
                    display: function(id) {
                        var koddocmail = table_incoming.file(
                                '<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>', id)
                            .koddocmail;
                        var filewebpath = '<?php echo __ROOT; ?>' + table_incoming.file(
                            '<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>', id).file_webpath;
                        var filename = table_incoming.file(
                                '<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>', id)
                            .file_originalname;

                        var removeFile = (id != "" && id != null) ? '<span rowid="' + id +
                            '" koddocmail="' + koddocmail +
                            '" class="remove-mainfile"><i class="fa-solid fa-trash-can"></i></span>' :
                            '<span rowid="' + id + '" koddocmail="' + koddocmail + '"></span>';
                        var preFix = (koddocmail != "" && koddocmail != null) ?
                            '<span class="attached-file-clipped"><i class="fa-solid fa-paperclip"></i></span>' :
                            '<span class="attached-file-temporary"><i class="fa-regular fa-clock"></i></span>';
                        return '<div class="lnkDocFileID"><table class="table compact table-striped table-listattachments-inform"><tbody><tr><td width="20px"><span class="attached-filelink">' +
                            preFix +
                            '</span></td><td><span class="attached-filelink"><a target="_blank" href="' +
                            filewebpath + '">' + filename +
                            '</a></span></td><td width="20px"><span class="attached-filelink">' +
                            removeFile +
                            '</span></td></tr></tbody></table></div>';
                    },
                    dragDrop: false,
                    dragDropText: 'Перенесите файл сюда для загрузки',
                    fileReadText: '',
                    processingText: 'Файл загружается...',
                    uploadText: 'Выберите файл',
                    clearText: '',
                    noFileText: '<div style="padding-top:0; padding-left:0"><span style="color:red">Основной файл не прикреплен</span></div>'
                }, {
                    type: "readonly",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.msgDocFileID",
                    attr: {
                        disabled: 'disabled'
                    },
                    className: 'block'
                }, {
                    type: "readonly",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.lnkDocFileID"
                }, {
                    label: "Выберите одного или нескольких ответственных",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractor_kodzayvispol",
                    type: "select",
                    placeholderDisabled: true,
                    multiple: true,
                    separator: ',',
                    className: "block"
                }, {
                    label: "Уведомить ответственных по email о назначении",
                    type: "checkbox",
                    name: "toSendEmail",
                    unselectedValue: 0,
                    options: {
                        "": 1
                    }
                }, {
                    label: "Без ответственных (---) / Очистить список, если ответственные уже были выбраны",
                    type: "checkbox",
                    name: "ispolSelectedClear",
                    unselectedValue: 0,
                    options: {
                        "": 1
                    }
                }, {
                    label: "Комментарии ответственных",
                    type: "textarea",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractorComment",
                    def: "",
                    attr: {
                        placeholder: 'Комментарии ответственных'
                    }
                }, {
                    label: "Дополнительная информация",
                    type: "textarea",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docComment",
                    def: "",
                    attr: {
                        placeholder: 'Комментарий к документу'
                    }
                }, {
                    label: "",
                    type: "checkbox",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docType_lock",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail"
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileIDadd",
                    def: null
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileIDtmp"
                }, {
                    label: "",
                    // type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp1"
                }, {
                    label: "",
                    // type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp2"
                }, {
                    label: "",
                    // type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp3"
                }, {
                    label: "",
                    // type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp4"
                }, {
                    label: "",
                    // type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp5"
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.toSendEmail"
                }, {
                    label: "",
                    type: "hidden",
                    name: "selectedContractorIDs"
                }, {
                    label: "Режим контроля исполнения (КИ)",
                    type: "checkbox",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }, {
                    label: "",
                    type: "checkbox",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolCheckout",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }, {
                    label: "",
                    type: "textarea",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolCheckoutComment",
                    def: "",
                    attr: {
                        placeholder: 'Текст, который вы тут введете, после сохранения формы будет добавлен к общему списку (чату) с комментариями по документу. В этом поле он не сохранится.'
                    },
                    className: "block"
                }, {
                    label: "Дедлайн",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDateDeadline",
                    type: "datetime",
                    format: "DD.MM.YYYY",
                    def: function() {
                        return moment();
                    }
                },
                {
                    label: "Напоминание исполнителю(ям) #1 (когда до дедлайна остается менее 3-х дней )",
                    type: "checkbox",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder1",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                },
                {
                    label: "Напоминание исполнителю(ям) #2 (когда до дедлайна остается менее 1-го дня )",
                    type: "checkbox",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder2",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                },
                {
                    label: "Уведомить исполнителя(ей) о наступлении дедлайна",
                    type: "checkbox",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyDL",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                },
                {
                    label: "Уведомить исполнителя(ей) об исполнении документа",
                    type: "checkbox",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyCheckout",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                },
                {
                    label: "Уведомить меня на email об исполнении по этому документу",
                    type: "checkbox",
                    name: "checkbox_controlIspolMailUserListNotifyCheckout",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                },
                {
                    label: "Уведомить меня на email о наступлении дедлайна по этому документу",
                    type: "checkbox",
                    name: "checkbox_controlIspolMailUserListNotifyDL",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                },
                {
                    label: "Уведомить руководство на email о наступлении дедлайна по этому документу",
                    type: "checkbox",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyDL",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                },
                {
                    label: "Уведомить руководство на email о выполнении по этому документу",
                    type: "checkbox",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyCheckout",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }, {
                    label: "Я исполнил документ",
                    type: "checkbox",
                    name: "ispolStatus",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }, {
                    label: "Использовать дедлайн",
                    type: "checkbox",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }, {
                    label: "Установить отметку об исполнении всеми остальными ответственными",
                    type: "checkbox",
                    name: "ispolStatusOtherOn",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }, {
                    label: "Снять отметку об исполнении всеми остальными ответственными",
                    type: "checkbox",
                    name: "ispolStatusOtherOff",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailUserListNotifyCheckout"
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailUserListNotifyDL"
                }, {
                    label: "",
                    type: "hidden",
                    name: "inbox_controlIspolActive_preOpen"
                }, {
                    label: "Название контрагента в списке ранее введенных вручную",
                    name: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm",
                    type: "select2",
                    placeholder: '---',
                    className: 'block'
                }
            ]
        });
        //
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        //
        // Управление размером диалогового окна редактирования заявки
        editor_incoming.on('open', function(e, mode, action) {
            $(".modal-dialog").css({
                "width": "65%",
                "min-width": "850px",
                "max-width": "1024px",
                "height": "auto"
            });

            $('#uploadFiles-result > table').html('');
            $('#listFiles-result').html('');

            // MILTIPLE UPLOAD FILES
            // Обрабатываем загрузчик файлов
            $("#js-file").change(function() {
                var userID = <?php echo $_SESSION['id']; ?>;
                if (window.FormData === undefined) {
                    alert('В вашем браузере FormData не поддерживается')
                } else {
                    var formData = new FormData();
                    $.each($("#js-file")[0].files, function(key, input) {
                        formData.append('file[]', input);
                    });
                    formData.append('userID', userID);
                    formData.append('rowID', editor_incoming.ids());
                    formData.append('docID', editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docID').val());
                    formData.append('koddocmail', editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail').val());
                    for (var pair of formData.entries()) {
                        // console.log(pair[0] + ', ' + pair[1]);
                    }
                    ajaxRequest_uploadAttachedFiles(formData, 'uploadAttachedFiles');
                }
            });

            inbox_rowIDadd_rel = editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel')
                .val();
            if (inbox_rowIDadd_rel !== "" && inbox_rowIDadd_rel !== null) {
                // console.log("dependent outbox_rowIDadd_rel !==:", inbox_rowIDadd_rel);
                $(".rowIDadd-enbl").css('display', 'block');
                $('input[id="DTE_Field_enbl_outbox_rowIDadd_rel_0"]').prop('checked', true);
                editor_incoming.field('enbl_outbox_rowIDadd_rel').val(1);
                $('#outbox-rowIDadd-rel-alert').css('display', 'block');
                editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel')
                    .enable();
                editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel')
                    .enable();
            } else {
                // console.log("dependent outbox_rowIDadd_rel else:", inbox_rowIDadd_rel);
                $(".rowIDadd-enbl").css('display', 'none');
                $('input[id="DTE_Field_enbl_outbox_rowIDadd_rel_0"]').prop('checked', false)
                editor_incoming.field('enbl_outbox_rowIDadd_rel').val(0);
                $('#outbox-rowIDadd-rel-alert').css('display', 'none');
            }
            //
            // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
            // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
            // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
            //
            if ($('#popoverCheckboxEnable').is(':checked')) {
                $.getJSON(
                        "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/fields-help-data.json"
                    )
                    .done(function(json) {
                        // console.log("----- getJSON >>>>>");
                        let placement = 'top';
                        for (var key1 in json.data) {
                            if (key1 == "toSendEmail") {
                                editor_incoming.field(key1).label(json.data[
                                    key1]['label2']);
                            } else if (key1 == "ispolStatus") {
                                editor_incoming.field(key1).label(json.data[
                                    key1]['label2']);
                            } else if (key1 == "ispolStatusOtherOn") {
                                editor_incoming.field(key1).label(json.data[
                                    key1]['label2']);
                            } else if (key1 == "ispolStatusOtherOff") {
                                editor_incoming.field(key1).label(json.data[
                                    key1]['label2']);
                            } else if (key1 == "enblSenderManual") {
                                editor_incoming.field(key1).label(json.data[
                                    key1]['label2']);
                            } else if (key1 == "enbl_outbox_docType_change") {
                                editor_incoming.field(key1).label(json.data[
                                    key1]['label2']);
                            } else {
                                editor_incoming.field(
                                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.' +
                                        key1)
                                    .label(json.data[
                                        key1]['label2']);
                            }
                            for (var key2 in json.data[key1]) {
                                // console.log(key1, key2, json.data[key1][key2]);
                            }
                            // console.log(json.data[key1]['id']);

                            var $content = json.data[key1]['popoverContent'].length > 1450 ?
                                json.data[key1][
                                    'popoverContent'
                                ].substring(0,
                                    1450) +
                                " ... <?php echo $popoverLinkToKnow; ?>" :
                                json.data[key1][
                                    'popoverContent'
                                ];

                            if (key1 == "inbox_docType") {
                                placement = 'left';
                            }

                            $('label[for="' + json.data[key1]['id'] +
                                    '"] > span[fieldName="popoverContent-' + key1 +
                                    '"] sup.popoverElemet')
                                .popover({
                                    html: true,
                                    trigger: 'hover',
                                    placement: placement,
                                    //title: json.data[key1]['popoverTitle'],
                                    content: $content,
                                    customClass: 'mail-popover',
                                });
                        }
                        // console.log("<<<<< getJSON -----");

                    })
                    .fail(function(jqxhr, textStatus, error) {
                        var err = textStatus + ", " + error;
                        // console.log("Request Failed: " + err);
                    });
            } else {
                $.getJSON(
                        "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/fields-help-data.json"
                    )
                    .done(function(json) {
                        // console.log("----- getJSON >>>>>");
                        for (var key1 in json.data) {
                            if (key1 == "toSendEmail") {
                                editor_incoming.field(key1).label(json.data[
                                    key1]['label1']);
                            } else if (key1 == "ispolStatus") {
                                editor_incoming.field(key1).label(json.data[
                                    key1]['label1']);
                            } else if (key1 == "ispolStatusOtherOn") {
                                editor_incoming.field(key1).label(json.data[
                                    key1]['label1']);
                            } else if (key1 == "ispolStatusOtherOff") {
                                editor_incoming.field(key1).label(json.data[
                                    key1]['label1']);
                            } else if (key1 == "enblSenderManual") {
                                editor_incoming.field(key1).label(json.data[
                                    key1]['label1']);
                            } else if (key1 == "enbl_outbox_docType_change") {
                                editor_incoming.field(key1).label(json.data[
                                    key1]['label1']);
                            } else {
                                editor_incoming.field(
                                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.' +
                                        key1)
                                    .label(json.data[
                                        key1]['label1']);
                            }
                        }
                        // console.log("<<<<< getJSON -----");

                    })
                    .fail(function(jqxhr, textStatus, error) {
                        var err = textStatus + ", " + error;
                        // console.log("Request Failed: " + err);
                    });
            }
            //
            // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
            // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
            // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
            //
            editor_incoming.dependent('<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel',
                function(val, data, callback, e) {
                    if (val !== "" && val !== null) {
                        $('div.rowID-rel-only').css('display', 'block');
                        // console.log('editor_incoming - mailbox_incoming.outbox_rowID_rel', val);
                        var relVal = ajaxRequest_getRelativeOutgoingDataAsync(val);
                        if (relVal[2] !== '') {
                            // editor_incoming.field(
                            //         '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz')
                            //     .val(
                            //         relVal[2]);
                        } else {
                            editor_incoming.field(
                                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz')
                                .val('');
                        }

                        if (relVal[6] !== 'no' && relVal[7] !== 'no' && relVal[8] !== 'no') {
                            $('div.outbox-relExist-alert').css('display', 'block');
                            $('div.outbox-relExist-alert .outbox-relExist-alert-msg').html(
                                '<p class="mb-1"><b>На этот документ ранее уже был дан ответ в другом документе:</b><br><span class=""><a href="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/index.php?type=in&mode=thisyear&rel=' +
                                relVal[8] + '">Входящее # 1-2/' + relVal[6] + ' от ' + relVal[9] +
                                ' - ' +
                                relVal[10] +
                                '</span></a></p><p class="mb-1">Даже если вы сохраните сейчас выбранный выше документ как объект для вашего ответа, то связь сохранена все равно не будет по причине указанной выше.</p><p class="mb-1">Рекомендуется либо удалить существующую связь перейдя в этот документ по ссылке выше, а потом повторить операцию. Либо же внести этот документ как дополнительный, на который редактируемый является ответным, используя специальный раздел ниже ниже (раздел уже открыт, документ выбран, вам остается только нажать кнопку "Добавить").</p>'
                            );
                            $('#select2-DTE_Field_mailbox_incoming-outbox_rowID_rel-container')
                                .addClass(
                                    'text-danger');
                            $('input[id="DTE_Field_enbl_outbox_rowIDadd_rel_0"]').prop('checked', true);
                            editor_incoming.field('enbl_outbox_rowIDadd_rel').val(1);
                            editor_incoming.field(
                                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel')
                                .val(relVal[0]);
                        } else {
                            $('div.outbox-relExist-alert').css('display', 'none');
                            $('div.outbox-relExist-alert .outbox-relExist-alert-msg').html('');
                            $('#select2-DTE_Field_mailbox_incoming-outbox_rowID_rel-container')
                                .removeClass(
                                    'text-danger');
                            // $('input[id="DTE_Field_enbl_outbox_rowIDadd_rel_0"]').prop('checked', false);
                            // editor_incoming.field('enbl_outbox_rowIDadd_rel').val(0);
                            // editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel').val('');
                        }

                        if (relVal[5] === '1' && relVal[4] !== '1' && relVal[3] === '1') {
                            $('div.outbox-setCheckout').css('display', 'block');
                            $('input#DTE_Field_set_outbox_fullCheckout_change_0').prop('checked',
                                false);
                            $('div.outbox-setCheckout-alert').css('display', 'block');
                        } else {
                            $('div.outbox-setCheckout').css('display', 'none');
                            $('input#DTE_Field_set_outbox_fullCheckout_change_0').prop('checked',
                                false);
                            $('div.outbox-setCheckout-alert').css('display', 'none');
                        }
                        $('input#DTE_Field_enbl_outbox_docType_change_0').prop('checked', true);
                        $('input#DTE_Field_enbl_outbox_docType_change_0').prop('disabled', true);
                    } else {
                        $('div.rowID-rel-only').css('display', 'none');
                        $('input#DTE_Field_enbl_outbox_docType_change_0').prop('checked', false);
                        $('div.rowID-rel-only-alert').css('display', 'none');

                        $('div.outbox-setCheckout').css('display', 'none');
                        $('input#DTE_Field_set_outbox_fullCheckout_change_0').prop('checked', false);
                        $('div.outbox-setCheckout-alert').css('display', 'none');

                        $('div.outbox-relExist-alert').css('display', 'none');
                    }
                    callback(true);
                }
            );
            // console.log('->>> Current check point #1', editor_incoming.field(
            //     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel').val());
            if (editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel').val() ==
                null ||
                editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel').val() ==
                '') {
                console.log('->>> Current check point #2');
                $('div.rowID-rel-only').css('display', 'none');
                $('input#DTE_Field_enbl_outbox_docType_change_0').prop('checked', false);
            }
            //
            // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
            // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
            // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
            //
            editor_incoming.dependent('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docType', function(
                val, data,
                callback, e) {
                var inbox_controlIspolActive_preOpen = data.values.inbox_controlIspolActive_preOpen;
                console.log('inbox_controlIspolActive_preOpen', inbox_controlIspolActive_preOpen);
                if (val === '2') {
                    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
                    // ## ЗАГРУЖАЕМ ПОЛЯ SELECT
                    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel').update(
                        ajaxRequest_getSelectOptions(
                            "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-loadSelectOutgoingRel.php",
                            "<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel"));
                    //
                    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel').update(
                        ajaxRequest_getSelectOptions(
                            "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-loadSelectOutgoingRelAdd.php",
                            "<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel"));
                    //
                    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
                    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel')
                        .enable();
                    $("#editorform-editor-tabs-menu > li.tab-outgoing-rel").removeClass("hide");
                    // $("input[id='DTE_Field_enbl_outbox_rowIDadd_rel_0']").prop('checked', false);
                    //
                    // 20231016 >>> UPDATE
                    // Устанавливаем режим КИ, если выбран тип документа "Запрос ответа"
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive')
                        .val(inbox_controlIspolActive_preOpen);
                    if (inbox_controlIspolActive_preOpen == 1) {
                        $("input[id='DTE_Field_mailbox_incoming-inbox_controlIspolActive_0']").prop(
                            'checked',
                            true);
                    } else {
                        $("input[id='DTE_Field_mailbox_incoming-inbox_controlIspolActive_0']").prop(
                            'checked',
                            false);
                    }
                    // 20231016 <<< UPDATE
                } else if (val === '3') {
                    $("#editorform-editor-tabs-menu > li.tab-outgoing-rel").addClass("hide");
                    //
                    // 20231016 >>> UPDATE
                    // Устанавливаем режим КИ, если выбран тип документа "Запрос ответа"
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive')
                        .val(1);
                    $("input[id='DTE_Field_mailbox_incoming-inbox_controlIspolActive_0']").prop(
                        'checked',
                        true);
                    // 20231016 <<< UPDATE
                } else {
                    $("#editorform-editor-tabs-menu > li.tab-outgoing-rel").addClass("hide");
                    editor_incoming.field('enbl_outbox_rowIDadd_rel').val(0);
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel')
                        .disable();
                    editor_incoming.val('<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel',
                        null);
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel')
                        .disable();
                    editor_incoming.val(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel',
                        null);
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel')
                        .disable();
                    editor_incoming.val(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel',
                        null);
                    $('#linked-mail-listDocs option').remove();
                    $('#linked-mail-listDocs').prop('disabled', true);
                    //
                    // 20231016 >>> UPDATE
                    // Устанавливаем режим КИ, если выбран тип документа "Запрос ответа"
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive')
                        .val(inbox_controlIspolActive_preOpen);
                    if (inbox_controlIspolActive_preOpen == 1) {
                        $("input[id='DTE_Field_mailbox_incoming-inbox_controlIspolActive_0']").prop(
                            'checked',
                            true);
                    } else {
                        $("input[id='DTE_Field_mailbox_incoming-inbox_controlIspolActive_0']").prop(
                            'checked',
                            false);
                    }
                    // 20231016 <<< UPDATE

                }
                callback(true);
            });
        });
        //
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        //
        editor_incoming.off('close', function() {
            $(".modal-dialog").css({
                "width": "80%",
                "min-width": "none",
                "max-width": "none"
            });
        });
        //
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        //
        var txtlink;
        var initCreate;

        function formatState(state) {
            if (!state.id) {
                return state.text;
            }
            str = (state.text.length > 160) ? state.text.substr(0, 120) + ' ...' : state.text;
            var $state = $(
                '<span>' + str + '</span>'
            );
            return $state;
        };
        //
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        //
        editor_incoming.on('initCreate', function(e) {
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.msgDocFileID').show(false);
            //editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.lnkDocFileID').hide(false);
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.msgDocFileID').val(
                'Сначала создайте запись!');
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID').hide();
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID').disable();
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docID').disable();
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDate').disable();
            window.txtlink = '';
            window.initCreate = 1;
            // console.log('window.initCreate', window.initCreate);
        });
        //
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        //
        editor_incoming.on('initEdit', function(e, node, data, items, type) {
            // console.log('inbox_docFileID', data.<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID);
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileIDtmp').val(data
                .<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID);
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docID').disable();
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDate')
                .disable();
            ajaxRequest_listAttachedFiles(editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail')
                .val(),
                'listAttachedFiles');

            window.initCreate = 0;
            if (data.<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID == "" || data
                .<?php echo __MAIL_INCOMING_TABLENAME; ?>
                .inbox_docFileID ==
                null) {
                editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.msgDocFileID').hide(
                    false);
                //editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.lnkDocFileID').hide(false);
                editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID').show(
                    false);
                editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID')
                    .enable();
                window.txtlink = '';
            } else {
                editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.msgDocFileID').hide(
                    false);
                //editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.lnkDocFileID').show(false);
                //editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.lnkDocFileID').val('Если вы хотите обновить прикрепленный файл, создайте новую запись и удалите старую!');
                //editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID').hide(false);
                //editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID').disable();
                if (window.initCreate != 1) {
                    // filelink = "<?php echo __ROOT; ?>/mail" + data
                    //     .<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>.file_webpath + "";
                    //window.txtlink = '<a target="_blank" href="'+filelink+'">Текущий прикрепленный файл</a>';
                }
            }
            //
            if (editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.toSendEmail').val() ===
                "1") {
                // console.log('toSendEmail checked');
                $('#DTE_Field_toSendEmail_0').prop('checked', false);
            } else {
                // console.log('toSendEmail !?');
            }
            var koddocmail = data.<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail;
            var userid = <?php echo $_SESSION['id']; ?>;
            ajaxRequest_getLastDeadlineMail(koddocmail, userid,
                'getLastDeadlineMail');
            ajaxRequest_getUserCheckout(koddocmail, userid,
                'getUserCheckout');
            ajaxRequest_getDocTypeLock(koddocmail, 'getDocTypeLock');
        });
        //
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        //
        editor_incoming.on('preOpen', function(e, mode, action) {
            $.getJSON(
                    "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/fields-help-data.json"
                )
                .done(function(json) {
                    // console.log("----- getJSON >>>>>");
                    for (var key1 in json.data) {
                        if (key1 == "toSendEmail") {
                            editor_incoming.field(key1).label(json.data[
                                key1]['label1']);
                        } else if (key1 == "ispolStatus") {
                            editor_incoming.field(key1).label(json.data[
                                key1]['label1']);
                        } else if (key1 == "ispolStatusOtherOn") {
                            editor_incoming.field(key1).label(json.data[
                                key1]['label1']);
                        } else if (key1 == "ispolStatusOtherOff") {
                            editor_incoming.field(key1).label(json.data[
                                key1]['label1']);
                        } else if (key1 == "enblSenderManual") {
                            editor_incoming.field(key1).label(json.data[
                                key1]['label1']);
                        } else if (key1 == "enbl_outbox_docType_change") {
                            editor_incoming.field(key1).label(json.data[
                                key1]['label1']);
                        } else {
                            editor_incoming.field(
                                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.' + key1)
                                .label(json.data[
                                    key1]['label1']);
                        }
                    }
                    // console.log("<<<<< getJSON -----");

                })
                .fail(function(jqxhr, textStatus, error) {
                    var err = textStatus + ", " + error;
                    // console.log("Request Failed: " + err);
                });
            //
            if (action === 'create') {
                let setControlIspolOnStart =
                    '<?php echo $_SESSION['incoming_setControlIspolOnStart']; ?>';
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive')
                    .val(setControlIspolOnStart);
                if (setControlIspolOnStart == '1') {
                    $('input[id="DTE_Field_mailbox_incoming-inbox_controlIspolActive_0"]').prop(
                        'checked',
                        true);
                } else {
                    $('input[id="DTE_Field_mailbox_incoming-inbox_controlIspolActive_0"]').prop(
                        'checked',
                        false);
                }
                //
                let setDeadlineOnStart = '<?php echo $_SESSION['incoming_setDeadlineOnStart']; ?>';
                editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline').val(
                    setDeadlineOnStart);
                if (setDeadlineOnStart == '1') {
                    $('input[id="DTE_Field_mailbox_incoming-inbox_controlIspolUseDeadline_0"]').prop(
                        'checked', true);
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDateDeadline')
                        .val(moment().add(<?php echo $deadline_default_days; ?>, 'days').format(
                            "DD.MM.YYYY"));
                } else {
                    $('input[id="DTE_Field_mailbox_incoming-inbox_controlIspolUseDeadline_0"]').prop(
                        'checked', false);
                }
            }
            //
            //
            if (action === 'edit') {
                var userID = <?php echo $_SESSION['id']; ?>;
                var userListNotifyCheckout = editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailUserListNotifyCheckout'
                    )
                    .val();
                var userListNotifyDL = editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailUserListNotifyDL')
                    .val();
                var koddocmail = editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail')
                    .val();
                var checkInUse = ajaxRequest_checkInUseDocStateDataAsync('check', koddocmail);
                if (checkInUse !== "no result") {
                    if (checkInUse[2] != userID) {
                        $.dialog({
                            title: 'Упс, проблемка...',
                            content: '<p class="small">Данный документ сейчас редактирует ваш коллега <span class=""><b>' +
                                checkInUse[3] + ' ' + checkInUse[4] +
                                '</b></span>. Попробуйте позже.</p>',
                            type: 'red',
                            typeAnimated: true,
                            draggable: false,
                            columnClass: 'medium',
                        });
                        return false;
                    } else {
                        var setInUse = ajaxRequest_checkInUseDocStateDataAsync('set', koddocmail);
                    }
                } else {
                    var setInUse = ajaxRequest_checkInUseDocStateDataAsync('set', koddocmail);
                }
                // console.log('checkInUse', checkInUse);
                // ----- ----- ----- ----- -----
                if (checkVal(userListNotifyCheckout) === 1) {
                    if (userListNotifyCheckout.indexOf(userID) !== -1) {
                        $('input[id="DTE_Field_checkbox_controlIspolMailUserListNotifyCheckout_0"]').prop(
                            'checked', true);
                        editor_incoming.field('checkbox_controlIspolMailUserListNotifyCheckout').val(1);
                        // console.log('field checkbox_controlIspolMailUserListNotifyCheckout', editor_incoming.field('checkbox_controlIspolMailUserListNotifyCheckout').val());
                    } else {
                        $('input[id="DTE_Field_checkbox_controlIspolMailUserListNotifyCheckout_0"]').prop(
                            'checked', false);
                        editor_incoming.field('checkbox_controlIspolMailUserListNotifyCheckout').val(0);
                        // console.log('checkbox_controlIspolMailUserListNotifyCheckout', userListNotifyCheckout.indexOf(userID));
                        // console.log('field checkbox_controlIspolMailUserListNotifyCheckout', editor_incoming.field('checkbox_controlIspolMailUserListNotifyCheckout').val());
                    }
                }
                //
                if (checkVal(userListNotifyDL) === 1) {
                    if (userListNotifyDL.indexOf(userID) !== -1) {
                        $('input[id="DTE_Field_checkbox_controlIspolMailUserListNotifyDL_0"]').prop(
                            'checked', true);
                        editor_incoming.field('checkbox_controlIspolMailUserListNotifyDL').val(1);
                        // console.log('field checkbox_controlIspolMailUserListNotifyDL', editor_incoming.field('checkbox_controlIspolMailUserListNotifyDL').val());
                    } else {
                        $('input[id="DTE_Field_checkbox_controlIspolMailUserListNotifyDL_0"]').prop(
                            'checked', false);
                        editor_incoming.field('checkbox_controlIspolMailUserListNotifyDL').val(0);
                        // console.log('checkbox_controlIspolMailUserListNotifyDL', userListNotifyDL.indexOf(userID));
                        // console.log('field checkbox_controlIspolMailUserListNotifyDL', editor_incoming.field('checkbox_controlIspolMailUserListNotifyDL').val());
                    }
                }
                //
                if (editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz').val() ===
                    "000000000000000") {
                    console.log("CHECK POINT #2147 >>>", editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz').val());
                    setTimeout(function() {
                        editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm'
                        ).enable();
                        // editor_incoming.field(
                        //     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm'
                        // ).show(
                        //     false);
                        // $('#customForm-mail-main-inbox .sender-searchinspmanual')
                        //     .fadeIn('slow')
                        //     .css('display', 'block');


                    }, 500);

                }
            }
            editor_incoming.field('inbox_controlIspolActive_preOpen').val(editor_incoming.field(
                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive').val());

            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm')
                .hide();
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender').hide();
            editor_incoming.field('enblSenderManual').hide();

        });
        //
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        //
        editor_incoming.on('close', function(e) {
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel').update([]);
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel').update(
                []);
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docIDs_links').update([]);
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_docIDs_links').update(
                []);
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.dognet_docIDs_links').update(
                []);
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.sp_docIDs_links').update([]);
        });
        //
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        // == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == == 
        //
        var openVals;
        editor_incoming.on('open', function(e, mode, action) {
            // console.log('editor_incoming.on("open")', 'triggered');

            $('#DTE_Field_mailbox_incoming-inbox_docContractor_kodzayvispol option:first').prop('disabled',
                true);
            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_rowID_rel').select2({
                width: '100%',
                allowClear: true,
                placeholder: "Начинайте вводить часть даты, номера, названия компании или описания письма",
                minimumInputLength: 2,
                templateResult: formatState,
                language: {
                    // You can find all of the options in the language files provided in the
                    // build. They all must be functions that return the string that should be
                    // displayed.
                    errorLoading: function() {
                        return "Результаты не могут быть загружены.";
                    },
                    inputTooLong: function() {
                        return "Слишком много введенных символов.";
                    },
                    inputTooShort: function() {
                        return "Введите больше символов для поиска...";
                    },
                    maximumSelected: function() {
                        return "Слишко много элементов выбрано.";
                    }
                }
            });

            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_rowIDList_rel').select2({
                width: '100%',
                allowClear: true,
                placeholder: "Начинайте вводить часть даты, номера, названия компании или описания письма",
                minimumInputLength: 2,
                templateResult: formatState,
                language: {
                    // You can find all of the options in the language files provided in the
                    // build. They all must be functions that return the string that should be
                    // displayed.
                    errorLoading: function() {
                        return "Результаты не могут быть загружены.";
                    },
                    inputTooLong: function() {
                        return "Слишком много введенных символов.";
                    },
                    inputTooShort: function() {
                        return "Введите больше символов для поиска...";
                    },
                    maximumSelected: function() {
                        return "Слишко много элементов выбрано.";
                    }
                }
            });
            var outbox_rowIDadd_rel = editor_incoming.field(
                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel').val();
            if (outbox_rowIDadd_rel !== "" && outbox_rowIDadd_rel !== null) {
                x3 = outbox_rowIDadd_rel.split(",");
                x3.forEach(function(val) {
                    selVal = val;
                    selText = $(
                        '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_rowIDList_rel option[value=' +
                        selVal + ']').text();
                    option = '<option value="' + selVal + '">' + selText + '</option>';
                    // console.log(option);
                    $('select#linked-mail-listDocs').append(option);
                });
            }

            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docIDs_links').select2({
                width: '100%',
                allowClear: false,
                minimumInputLength: 2,
                templateResult: formatState,
                language: {
                    // You can find all of the options in the language files provided in the
                    // build. They all must be functions that return the string that should be
                    // displayed.
                    errorLoading: function() {
                        return "Результаты не могут быть загружены.";
                    },
                    inputTooLong: function() {
                        return "Слишком много введенных символов.";
                    },
                    inputTooShort: function() {
                        return "Введите больше символов для поиска...";
                    },
                    maximumSelected: function() {
                        return "Слишко много элементов выбрано.";
                    }
                }
            });
            var inbox_rowIDs_links = editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_rowIDs_links')
                .val();
            if (inbox_rowIDs_links !== "" && inbox_rowIDs_links !== null) {
                x1 = inbox_rowIDs_links.split(",");
                x1.forEach(function(val) {
                    selVal = val;
                    selText = $(
                        '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docIDs_links option[value=' +
                        selVal + ']').text();
                    option = '<option value="' + selVal + '">' + selText + '</option>';
                    // console.log(option);
                    $('#linked-mail-incomingDocs').append(option);
                });
            }
            //
            //
            //
            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_docIDs_links').select2({
                width: '100%',
                allowClear: false,
                minimumInputLength: 2,
                templateResult: formatState,
                language: {
                    // You can find all of the options in the language files provided in the
                    // build. They all must be functions that return the string that should be
                    // displayed.
                    errorLoading: function() {
                        return "Результаты не могут быть загружены.";
                    },
                    inputTooLong: function() {
                        return "Слишком много введенных символов.";
                    },
                    inputTooShort: function() {
                        return "Введите больше символов для поиска...";
                    },
                    maximumSelected: function() {
                        return "Слишко много элементов выбрано.";
                    }
                }
            });
            var outbox_rowIDs_links = editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDs_links')
                .val();
            if (outbox_rowIDs_links !== "" && outbox_rowIDs_links !== null) {
                x1 = outbox_rowIDs_links.split(",");
                x1.forEach(function(val) {
                    selVal = val;
                    selText = $(
                        '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_docIDs_links option[value=' +
                        selVal + ']').text();
                    option = '<option value="' + selVal + '">' + selText + '</option>';
                    // console.log(option);
                    $('#linked-mail-outgoingDocs').append(option);
                });
            }
            //
            //
            //
            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-dognet_docIDs_links').select2({
                width: '100%',
                allowClear: false,
                minimumInputLength: 2,
                templateResult: formatState,
                language: {
                    // You can find all of the options in the language files provided in the
                    // build. They all must be functions that return the string that should be
                    // displayed.
                    errorLoading: function() {
                        return "Результаты не могут быть загружены.";
                    },
                    inputTooLong: function() {
                        return "Слишком много введенных символов.";
                    },
                    inputTooShort: function() {
                        return "Введите больше символов для поиска...";
                    },
                    maximumSelected: function() {
                        return "Слишко много элементов выбрано.";
                    }
                }

            });
            var dognet_rowIDs_links = editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.dognet_rowIDs_links')
                .val();
            if (dognet_rowIDs_links !== "" && dognet_rowIDs_links !== null) {
                x2 = dognet_rowIDs_links.split(",");
                x2.forEach(function(val) {
                    selVal = val;
                    selText = $(
                        '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-dognet_docIDs_links option[value=' +
                        selVal + ']').text();
                    option = '<option value="' + selVal + '">' + selText + '</option>';
                    // console.log(option);
                    $('#linked-dognet-docs').append(option);
                });
            }
            //
            //
            //
            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-sp_docIDs_links').select2({
                width: '100%',
                allowClear: false,
                minimumInputLength: 2,
                templateResult: formatState,
                language: {
                    // You can find all of the options in the language files provided in the
                    // build. They all must be functions that return the string that should be
                    // displayed.
                    errorLoading: function() {
                        return "Результаты не могут быть загружены.";
                    },
                    inputTooLong: function() {
                        return "Слишком много введенных символов.";
                    },
                    inputTooShort: function() {
                        return "Введите больше символов для поиска...";
                    },
                    maximumSelected: function() {
                        return "Слишко много элементов выбрано.";
                    }
                }

            });
            var sp_rowIDs_links = editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.sp_rowIDs_links')
                .val();
            if (sp_rowIDs_links !== "" && sp_rowIDs_links !== null) {
                x2 = sp_rowIDs_links.split(",");
                x2.forEach(function(val) {
                    selVal = val;
                    selText = $(
                        '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-sp_docIDs_links option[value=' +
                        selVal + ']').text();
                    option = '<option value="' + selVal + '">' + selText + '</option>';
                    // console.log(option);
                    $('#linked-sp-docs').append(option);
                });
            }

            if (window.initCreate == 1 && action == "create") {
                // $('.docFileID').css('display', 'none');
                $('div#mainFile').css('display', 'none');
                $('div#addFiles').css('display', 'none');
                $('div.editorform div.section div.divUploads div.title').removeClass('show').addClass(
                    'hide');

            }

            editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyCheckout')
                .disable();
            editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyDL')
                .disable();

            // Store the values of the fields on open
            openVals = JSON.stringify(editor_incoming.get());
            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docSender_kodzakaz').select2({
                placeholder: 'Выберите организацию используя поиск (внутри списка)',
                allowClear: true,
                minimumInputLength: 2, // only start searching when the user has input 3 or more characters
                width: '100%',
                language: {
                    // You can find all of the options in the language files provided in the
                    // build. They all must be functions that return the string that should be
                    // displayed.
                    errorLoading: function() {
                        return "Результаты не могут быть загружены.";
                    },
                    inputTooLong: function() {
                        return "Слишком много введенных символов.";
                    },
                    inputTooShort: function() {
                        return "Введите больше символов для поиска...";
                    },
                    maximumSelected: function() {
                        return "Слишко много элементов выбрано.";
                    },
                    noResults: function() {
                        setTimeout(function() {
                            //     editor_incoming.field(
                            //         '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm'
                            //     ).show(false);
                            //     $('#customForm-mail-main-inbox .sender-searchinspmanual')
                            //         .fadeIn('slow')
                            //         .css('display', 'block');

                            setTimeout(function() {

                                // Меняем свойство блока ручного ввода контрагента на BLOCK
                                $('#customForm-mail-main-inbox .sender-manualinput-alert')
                                    .fadeIn('slow')
                                    .css('display', 'block');


                                editor_incoming.field(
                                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender'
                                    )
                                    .show(false);
                                editor_incoming.field('enblSenderManual').show(
                                    false);
                                $('#customForm-mail-main-inbox .sender-manualinput-chk')
                                    .fadeIn(
                                        'slow').css('display', 'block');
                                $('#customForm-mail-main-inbox .sender-manualinput-text')
                                    .fadeIn(
                                        'slow').css('display', 'block');
                            }, 500);


                        }, 500);
                        return 'Ничего не найдено';
                    },
                    searching: function() {
                        if (editor_incoming.field(
                                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz'
                            ).val() !== "000000000000000") {
                            // $('#customForm-mail-main-inbox .sender-searchinspmanual').fadeOut(
                            //     'slow').css('display', 'none');
                            // editor_incoming.field(
                            //     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm'
                            // ).hide(false);

                            $('#customForm-mail-main-inbox .sender-manualinput-chk').fadeOut(
                                    'slow')
                                .css('display', 'none');
                            $('#customForm-mail-main-inbox .sender-manualinput-text').fadeOut(
                                    'slow')
                                .css('display', 'none');

                            editor_incoming.field(
                                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender')
                                .hide(false);
                            editor_incoming.field('enblSenderManual').hide(false);
                        } else {

                            // Меняем свойство блока ручного ввода контрагента на BLOCK
                            $('#customForm-mail-main-inbox .sender-manualinput-alert')
                                .fadeIn('slow')
                                .css('display', 'block');

                            // $('#customForm-mail-main-inbox .sender-searchinspmanual').fadeIn(
                            //     'slow').css('display', 'block');
                            // editor_incoming.field(
                            //     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm'
                            // ).show(false);

                            // $('#customForm-mail-main-inbox .sender-manualinput-chk').fadeOut(
                            //         'slow')
                            //     .css('display', 'none');
                            // $('#customForm-mail-main-inbox .sender-manualinput-text').fadeOut(
                            //         'slow')
                            //     .css('display', 'none');


                            editor_incoming.field(
                                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender')
                                .show(false);
                            editor_incoming.field('enblSenderManual').show(false);
                        }
                        return 'Поиск…';
                    }
                }
            });
            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docSender_kodzakazm').select2({
                placeholder: 'Выберите организацию используя поиск (внутри списка)',
                allowClear: true,
                minimumInputLength: 2, // only start searching when the user has input 3 or more characters
                width: '100%',
                language: {
                    // You can find all of the options in the language files provided in the
                    // build. They all must be functions that return the string that should be
                    // displayed.
                    errorLoading: function() {
                        return "Результаты не могут быть загружены.";
                    },
                    inputTooLong: function() {
                        return "Слишком много введенных символов.";
                    },
                    inputTooShort: function() {
                        return "Введите больше символов для поиска...";
                    },
                    maximumSelected: function() {
                        return "Слишко много элементов выбрано.";
                    },
                    noResults: function() {
                        setTimeout(function() {




                            editor_incoming.field(
                                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender'
                                )
                                .show(false);
                            editor_incoming.field('enblSenderManual').show(false);
                            $('#customForm-mail-main-inbox .sender-manualinput-chk')
                                .fadeIn(
                                    'slow').css('display', 'block');
                            $('#customForm-mail-main-inbox .sender-manualinput-text')
                                .fadeIn(
                                    'slow').css('display', 'block');
                        }, 500);
                        return 'Ничего не найдено';
                    },
                    searching: function() {



                        $('#customForm-mail-main-inbox .sender-manualinput-chk').fadeOut('slow')
                            .css('display', 'none');
                        $('#customForm-mail-main-inbox .sender-manualinput-text').fadeOut(
                                'slow')
                            .css('display', 'none');
                        editor_incoming.field(
                                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender')
                            .hide(false);
                        editor_incoming.field('enblSenderManual').hide(false);
                        return 'Поиск…';
                    }
                }
            });


            // ----- ----- ----- ----- -----

            // editor_incoming.on('preClose', function(e) {
            //     // On close, check if the values have changed and ask for closing confirmation if they have
            //     if (openVals !== JSON.stringify(editor_incoming.get())) {
            //         return confirm(
            //             'Уверены, что хотите завершить редактирование?'
            //         );
            //     }
            // })


            $("#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docContractor_kodzayvispol")
                .change(
                    function() {

                        var value = $(
                            '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docContractor_kodzayvispol option:selected'
                        ).text();
                        var rr = [];
                        var vv = [];
                        var ispolsHTML = ""
                        $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docContractor_kodzayvispol :selected')
                            .each(
                                function(i, selected) {
                                    rr[i] = " " + $(selected).text() + " ";
                                    vv[i] = $(selected).val();
                                    ispolsHTML += '<span class="badge badge-dark mx-1">' + $(selected)
                                        .text() + '</span>';
                                });
                        if (vv.length > 0) {
                            $('#ispol-selected-str').html(ispolsHTML);
                        } else {
                            $('#ispol-selected-str').html('<?php echo $tab3_ispolList_msg_0; ?>');
                        }
                        editor_incoming.field('selectedContractorIDs').val(vv.toString());
                    });
            //
            // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- --
            //
            $('#DTE_Field_ispolSelectedClear_0').click(function() {
                if ($(this).prop('checked') == true) {
                    $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docContractor_kodzayvispol option')
                        .prop(
                            'selected', false);
                    $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docContractor_kodzayvispol')
                        .prop(
                            'disabled', true);
                    var value = $(
                        '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docContractor_kodzayvispol option:selected'
                    ).text();
                    var rr = [];
                    $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docContractor_kodzayvispol option:selected')
                        .each(function(i, selected) {
                            selectedText = ($(selected).val() == '000000000000000') ?
                                '---' :
                                $(selected).text();
                            rr[i] = " " + selectedText + " ";
                        });
                    // console.log('CHECKPOINT (1947) >>>', rr.toString());
                    $('#ispol-selected-str').html(rr.toString());
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractor_kodzayvispol'
                        )
                        .set(
                            "000000000000000");
                    editor_incoming.field('toSendEmail').disable();
                    editor_incoming.field('toSendEmail').set(0);
                    // // console.log("XXXXX: "+editor_incoming.field( '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractor_kodzayvispol' ).val());
                } else {
                    $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docContractor_kodzayvispol')
                        .prop(
                            'disabled', false);
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractor_kodzayvispol'
                        )
                        .set(
                            null);
                }
            });
            //
            // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- --
            //
            $('#mailbox-rel-links-btnAdd').click(function() {
                if (editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel')
                    .val() !==
                    "") {
                    selText = $(
                            '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_rowIDList_rel option:selected'
                        )
                        .text();
                    selValue = $(
                            '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_rowIDList_rel option:selected'
                        )
                        .val();
                    // Проверяем есть ли уже такой элемент в списке
                    var exists = 0 != $('#linked-mail-listDocs option[value=' + selValue + ']')
                    if (!exists) {
                        $('#linked-mail-listDocs').append(
                            '<option value="' + selValue +
                            '" class="text-dark">' + selText +
                            '</option>');
                        var selMulti = $.map($(
                            "#linked-mail-listDocs option"
                        ), function(el, i) {
                            return $(el).val();
                        });
                        var selMultiStr = selMulti.join(",");
                        editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel').val(
                            selMultiStr);
                    }
                    $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_rowIDList_rel')
                        .val(null).trigger(
                            "change");
                }
            });
            //
            $('#mailbox-rel-links-btnRemove').click(function() {
                $('#linked-mail-listDocs option:selected').remove();

                if (typeof $('#linked-mail-listDocs option:first').val() !== "undefined" || $(
                        '#linked-mail-listDocs option:first').val() !== "") {
                    var selMulti = $.map($(
                        "#linked-mail-listDocs option"
                    ), function(el, i) {
                        return $(el).val();
                    });
                    // console.log('selMulti:', selMulti);
                    var selMultiStr = selMulti.join(",");
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel').val(
                        selMultiStr);
                }
            });
            //
            $('#mailbox-rel-links-btnAdd, #mailbox-rel-links-btnRemove').click(function() {});
            //
            // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- --
            //
            $('#inbox-doc-links-btnAdd').click(function() {
                if ($(
                        '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docIDs_links option:selected'
                    )
                    .val() !== "") {
                    selText = $(
                            '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docIDs_links option:selected'
                        )
                        .text();
                    selValue = $(
                            '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docIDs_links option:selected'
                        )
                        .val();
                    // Проверяем есть ли уже такой элемент в списке
                    var exists = 0 != $('#linked-mail-incomingDocs option[value=' + selValue + ']')
                    if (!exists) {
                        $('#linked-mail-incomingDocs').append(
                            '<option value="' + selValue +
                            '" style="color:#999; font-style:italic">' + selText +
                            '</option>');
                        var selMulti = $.map($(
                            "#linked-mail-incomingDocs option"
                        ), function(el, i) {
                            return $(el).val();
                        });
                        // console.log('selMulti:', selMulti);
                        var selMultiStr = selMulti.join(",");
                        editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_rowIDs_links').val(
                            selMultiStr);
                        // console.log('field inbox_rowIDs_links:', editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_rowIDs_links').val());
                    }
                    $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docIDs_links')
                        .val(
                            null).trigger(
                            "change");
                }
            });
            //
            $('#inbox-doc-links-btnRemove').click(function() {
                $('#linked-mail-incomingDocs option:selected').remove();

                var selMulti = $.map($(
                    "#linked-mail-incomingDocs option"
                ), function(el, i) {
                    return $(el).val();
                });
                // console.log('selMulti:', selMulti);
                var selMultiStr = selMulti.join(",");
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_rowIDs_links')
                    .val(selMultiStr);
            });
            //
            // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- --
            //
            $('#outbox-doc-links-btnAdd').click(function() {
                if ($(
                        '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_docIDs_links option:selected'
                    )
                    .val() !== "") {
                    selText = $(
                            '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_docIDs_links option:selected'
                        )
                        .text();
                    selValue = $(
                            '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_docIDs_links option:selected'
                        )
                        .val();
                    // Проверяем есть ли уже такой элемент в списке
                    var exists = 0 != $('#linked-mail-outgoingDocs option[value=' + selValue + ']')
                    if (!exists) {
                        $('#linked-mail-outgoingDocs').append(
                            '<option value="' + selValue +
                            '" style="color:#999; font-style:italic">' + selText +
                            '</option>');
                        var selMulti = $.map($(
                            "#linked-mail-outgoingDocs option"
                        ), function(el, i) {
                            return $(el).val();
                        });
                        // console.log('selMulti:', selMulti);
                        var selMultiStr = selMulti.join(",");
                        editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDs_links').val(
                            selMultiStr);
                        // console.log('field outbox_rowIDs_links:', editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDs_links').val());
                    }
                    $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_docIDs_links')
                        .val(null).trigger(
                            "change");
                }
            });
            //
            $('#outbox-doc-links-btnRemove').click(function() {
                $('#linked-mail-outgoingDocs option:selected').remove();

                var selMulti = $.map($(
                    "#linked-mail-outgoingDocs option"
                ), function(el, i) {
                    return $(el).val();
                });
                // console.log('selMulti:', selMulti);
                var selMultiStr = selMulti.join(",");
                editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDs_links').val(
                    selMultiStr);

                // console.log('field outbox_rowIDs_links:', editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDs_links').val());

            });
            //
            // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- --
            //
            $('#dognet-doc-links-btnAdd').click(function() {
                if ($(
                        '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-dognet_docIDs_links option:selected'
                    )
                    .val() !== "") {
                    selText = $(
                            '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-dognet_docIDs_links option:selected'
                        )
                        .text();
                    selValue = $(
                        '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-dognet_docIDs_links option:selected'
                    ).val();
                    // Проверяем есть ли уже такой элемент в списке
                    var exists = 0 != $('#linked-dognet-docs option[value=' + selValue + ']')
                    if (!exists) {
                        $('#linked-dognet-docs').append(
                            '<option value="' + selValue +
                            '" style="color:#999; font-style:italic">' + selText +
                            '</option>');
                        var selMulti = $.map($(
                            "#linked-dognet-docs option"
                        ), function(el, i) {
                            return $(el).val();
                        });
                        var selMultiStr = selMulti.join(",");
                        editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.dognet_rowIDs_links').val(
                            selMultiStr);
                    }
                    $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-dognet_docIDs_links')
                        .val(null).trigger(
                            "change");
                }
            });
            //
            $('#dognet-doc-links-btnRemove').click(function() {
                $('#linked-dognet-docs option:selected').remove();

                var selMulti = $.map($(
                    "#linked-dognet-docs option"
                ), function(el, i) {
                    return $(el).val();
                });
                // console.log('selMulti:', selMulti);
                var selMultiStr = selMulti.join(",");
                editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.dognet_rowIDs_links').val(
                    selMultiStr);
            });
            //
            // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- --
            //
            $('#sp-doc-links-btnAdd').click(function() {
                if ($(
                        '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-sp_docIDs_links option:selected'
                    )
                    .val() !== "") {
                    selText = $(
                            '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-sp_docIDs_links option:selected'
                        )
                        .text();
                    selValue = $(
                        '#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-sp_docIDs_links option:selected'
                    ).val();
                    // Проверяем есть ли уже такой элемент в списке
                    var exists = 0 != $('#linked-sp-docs option[value=' + selValue + ']')
                    if (!exists) {
                        $('#linked-sp-docs').append(
                            '<option value="' + selValue +
                            '" style="color:#999; font-style:italic">' + selText +
                            '</option>');
                        var selMulti = $.map($(
                            "#linked-sp-docs option"
                        ), function(el, i) {
                            return $(el).val();
                        });
                        var selMultiStr = selMulti.join(",");
                        editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.sp_rowIDs_links').val(
                            selMultiStr);
                    }
                    $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-sp_docIDs_links')
                        .val(null).trigger(
                            "change");
                }
            });
            //
            $('#sp-doc-links-btnRemove').click(function() {
                $('#linked-sp-docs option:selected').remove();

                var selMulti = $.map($(
                    "#linked-sp-docs option"
                ), function(el, i) {
                    return $(el).val();
                });
                // console.log('selMulti:', selMulti);
                var selMultiStr = selMulti.join(",");
                editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.sp_rowIDs_links').val(
                    selMultiStr);
            });
            //
            // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- --
            //
            $('#popoverCheckboxEnable').click(function() {
                if ($(this).is(':checked')) {
                    $.getJSON(
                            "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/fields-help-data.json"
                        )
                        .done(function(json) {
                            // console.log("----- getJSON >>>>>");
                            let placement = 'top';
                            for (var key1 in json.data) {
                                if (key1 == "toSendEmail") {
                                    editor_incoming.field(key1).label(json.data[
                                        key1]['label2']);
                                } else if (key1 == "ispolStatus") {
                                    editor_incoming.field(key1).label(json.data[
                                        key1]['label2']);
                                } else if (key1 == "ispolStatusOtherOn") {
                                    editor_incoming.field(key1).label(json.data[
                                        key1]['label2']);
                                } else if (key1 == "ispolStatusOtherOff") {
                                    editor_incoming.field(key1).label(json.data[
                                        key1]['label2']);
                                } else if (key1 == "enblSenderManual") {
                                    editor_incoming.field(key1).label(json.data[
                                        key1]['label2']);
                                } else if (key1 == "enbl_outbox_docType_change") {
                                    editor_incoming.field(key1).label(json.data[
                                        key1]['label2']);
                                } else {
                                    editor_incoming.field(
                                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.' +
                                            key1)
                                        .label(json.data[
                                            key1]['label2']);
                                }
                                for (var key2 in json.data[key1]) {
                                    // console.log(key1, key2, json.data[key1][key2]);
                                }
                                // console.log(json.data[key1]['id']);

                                var $content = json.data[key1]['popoverContent'].length >
                                    1450 ?
                                    json.data[key1][
                                        'popoverContent'
                                    ].substring(0,
                                        1450) +
                                    " ... <?php echo $popoverLinkToKnow; ?>" :
                                    json.data[key1][
                                        'popoverContent'
                                    ];

                                if (key1 == "inbox_docType") {
                                    placement = 'left';
                                }

                                $('label[for="' + json.data[key1]['id'] +
                                    '"] > span[fieldName="popoverContent-' + key1 +
                                    '"] sup.popoverElemet').popover({
                                    html: true,
                                    trigger: 'hover',
                                    placement: placement,
                                    // title: json.data[key1]['popoverTitle'],
                                    content: $content,
                                    customClass: 'mail-popover',
                                });
                            }
                            // console.log("<<<<< getJSON -----");

                        })
                        .fail(function(jqxhr, textStatus, error) {
                            var err = textStatus + ", " + error;
                            // console.log("Request Failed: " + err);
                        });
                } else {
                    $.getJSON(
                            "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/fields-help-data.json"
                        )
                        .done(function(json) {
                            // console.log("----- getJSON >>>>>");
                            for (var key1 in json.data) {
                                if (key1 == "toSendEmail") {
                                    editor_incoming.field(key1).label(json.data[
                                        key1]['label1']);
                                } else if (key1 == "ispolStatus") {
                                    editor_incoming.field(key1).label(json.data[
                                        key1]['label1']);
                                } else if (key1 == "ispolStatusOtherOn") {
                                    editor_incoming.field(key1).label(json.data[
                                        key1]['label1']);
                                } else if (key1 == "ispolStatusOtherOff") {
                                    editor_incoming.field(key1).label(json.data[
                                        key1]['label1']);
                                } else if (key1 == "enblSenderManual") {
                                    editor_incoming.field(key1).label(json.data[
                                        key1]['label1']);
                                } else if (key1 == "enbl_outbox_docType_change") {
                                    editor_incoming.field(key1).label(json.data[
                                        key1]['label1']);
                                } else {
                                    editor_incoming.field(
                                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.' +
                                            key1)
                                        .label(json.data[
                                            key1]['label1']);
                                }
                            }
                            // console.log("<<<<< getJSON -----");

                        })
                        .fail(function(jqxhr, textStatus, error) {
                            var err = textStatus + ", " + error;
                            // console.log("Request Failed: " + err);
                        });
                }
            });
            //
            // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- --
            //
            $('#customForm-mail-main-inbox li a.nav-link[href="#doc-editor-tab-7"]').click(function() {
                console.log("Click on doc-editor-tab-7 tab!", $(
                    '#DTE_Field_mailbox_incoming-outbox_docIDs_links option').length, $(
                    '#DTE_Field_mailbox_incoming-inbox_docIDs_links option').length, $(
                    '#DTE_Field_mailbox_incoming-dognet_docIDs_links option').length, $(
                    '#DTE_Field_mailbox_incoming-sp_docIDs_links option').length);

                //
                // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
                // ЗАГРУЖАЕМ СПИСКИ В ПОЛЯ ТИПА SELECT
                // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
                //
                if ($('#DTE_Field_mailbox_incoming-outbox_docIDs_links option').length <= 1) {
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_docIDs_links').update(
                        ajaxRequest_getSelectOptions(
                            "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-loadSelectOutgoingLinks.php",
                            "<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_docIDs_links"));
                }
                //
                if ($('#DTE_Field_mailbox_incoming-inbox_docIDs_links option').length <= 1) {
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docIDs_links').update(
                        ajaxRequest_getSelectOptions(
                            "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-loadSelectIncomingLinks.php",
                            "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docIDs_links"));
                }
                //
                if ($('#DTE_Field_mailbox_incoming-dognet_docIDs_links option').length <= 1) {
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.dognet_docIDs_links').update(
                        ajaxRequest_getSelectOptions(
                            "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-loadSelectDognetLinks.php",
                            "<?php echo __MAIL_INCOMING_TABLENAME; ?>.dognet_docIDs_links"));
                }
                //
                if ($('#DTE_Field_mailbox_incoming-sp_docIDs_links option').length <= 1) {
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.sp_docIDs_links').update(
                        ajaxRequest_getSelectOptions(
                            "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-loadSelectSPLinks.php",
                            "<?php echo __MAIL_INCOMING_TABLENAME; ?>.sp_docIDs_links"));
                }
                //
                // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
                // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
                // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
                //
            });


            console.log("CHECK >>> .on(open) inbox_controlIspolActive", editor_incoming.field(
                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive').val());
            if (editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive')
                .val() == "1") {
                editor_incoming.field('ispolStatus').enable();
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline'
                    )
                    .enable();
                editor_incoming.field('ispolStatusOtherOn').enable();
                editor_incoming.field('ispolStatusOtherOff').enable();
            } else {
                editor_incoming.field('ispolStatus').disable();
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline'
                    )
                    .disable();
                editor_incoming.field('ispolStatusOtherOn').disable();
                editor_incoming.field('ispolStatusOtherOff').disable();
            }

            // console.log("CHECK >>> .on(open) ispolStatus", $('input[id="DTE_Field_ispolStatus_0"]').val());
            if ($('input[id="DTE_Field_ispolStatus_0"]').val() == "1") {
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyCheckout'
                    )
                    .enable();
            } else {
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyCheckout'
                    )
                    .disable();
            }


            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docSender_kodzakazm').on(
                'select2:select',
                function(e) {
                    // Do something
                    console.log('SELECT inbox_docSender_kodzakazm >>>',
                        'select2:select event is fired');
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz').val(
                        "000000000000000");
                });

            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docSender_kodzakazm').on(
                'select2:unselect',
                function(e) {
                    // Do something
                    console.log('SELECT inbox_docSender_kodzakazm >>>',
                        'select2:unselect event is fired');
                    // editor_incoming.field(
                    //     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz').val(
                    //     null);
                });

        })
        // ----- -- ----- -- ----- --
        // >>> BLOCK BEGIN
        // БЛОК ОБРАБОТКИ ОШИБОК ЗАПОЛНЕНИЯ ФОРМЫ
        // Редакция от 20221120
        //
        editor_incoming.on('postSubmit close', function(e, json, data, action, xhr) {
            // var unsetInUse = ajaxRequest_checkInUseDocStateDataAsync('unset', koddocmail);
        })
        // ----- -- ----- -- ----- --
        // >>> BLOCK BEGIN
        // БЛОК ОБРАБОТКИ ОШИБОК ЗАПОЛНЕНИЯ ФОРМЫ
        // Редакция от 20221120
        //
        editor_incoming.on('preSubmit', function(e, data, action) {
            // console.log('preSubmit triggered');
            var errmsg_1, errmsg_2, errmsg_3, errmsh_4, errmsg_5 = false;
            if (action !== "remove") {
                //
                // Вкладка №1
                if (editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docRecipient_kodzayvtel'
                    )
                    .val() == '') {
                    $('div.editorform li.nav-item.tab1>a.nav-link').addClass("errmsg");
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docRecipient_kodzayvtel'
                        )
                        .error('Выберите получателя документа');
                    // e.preventDefault;
                    errmsg_1 = true;
                } else if (editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docAbout')
                    .val() == '') {
                    $('div.editorform li.nav-item.tab1>a.nav-link').addClass("errmsg");
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docAbout').error(
                        'Краткое описание документа обязательно');
                    // e.preventDefault;
                    errmsg_1 = true;
                } else {
                    $('div.editorform li.nav-item.tab1>a.nav-link').removeClass("errmsg");
                    errmsg_1 = false;
                }
                //
                // Вкладка №3
                if (editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractor_kodzayvispol'
                    ).val() == '') {
                    $('div.editorform li.nav-item.tab3>a.nav-link').addClass("errmsg");
                    $('#ispol-selected-str').html('<?php echo $tab3_ispolList_msg_0; ?>');
                    errmsg_3 = true;
                } else {
                    $('div.editorform li.nav-item.tab3>a.nav-link').removeClass("errmsg");
                    errmsg_3 = false;
                }
                //
                // Вкладка №4
                if (editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSourceID').val() ==
                    '') {
                    $('div.editorform li.nav-item.tab4>a.nav-link').addClass("errmsg");
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSourceID').error(
                        'Введите исходящий номер');
                    // e.preventDefault;
                    errmsg_4 = true;
                } else if ($('#select2-DTE_Field_mailbox_incoming-inbox_docSender_kodzakaz-container')
                    .text() === '' || $(
                        '#select2-DTE_Field_mailbox_incoming-inbox_docSender_kodzakaz-container').text() ===
                    'Выберите организацию используя поиск (внутри списка)') {
                    $('div.editorform li.nav-item.tab4>a.nav-link').addClass("errmsg");
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz')
                        .error('Выберите отправителя письма');
                    // e.preventDefault;
                    errmsg_4 = true;
                } else if ($('#select2-DTE_Field_mailbox_incoming-inbox_docSender_kodzakaz-container')
                    .text() === 'В справочнике отсутствует (введено вручную)' && $(
                        '#select2-DTE_Field_mailbox_incoming-inbox_docSender_kodzakazm-container')
                    .text() === '') {
                    $('div.editorform li.nav-item.tab4>a.nav-link').addClass("errmsg");
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm')
                        .error('Выберите отправителя письма из ранее введенных вручную или введите нового');
                    // e.preventDefault;
                    errmsg_4 = true;
                } else {
                    $('div.editorform li.nav-item.tab4>a.nav-link').removeClass("errmsg");
                    errmsg_4 = false;
                }
                //
                if (errmsg_1 || errmsg_3 || errmsg_4) {
                    // console.log('errmsg_1', errmsg_1, 'errmsg_3', errmsg_3, 'errmsg_4', errmsg_4);
                    e.preventDefault;
                    return false;
                } else {
                    return true;
                }
            }

            // console.log('preSubmit triggered 2');
            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-inbox_docIDs_links').val(null)
                .trigger(
                    "change");
            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-outbox_docIDs_links').val(
                    null)
                .trigger(
                    "change");
            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-dognet_docIDs_links').val(
                    null)
                .trigger(
                    "change");
            $('#DTE_Field_<?php echo __MAIL_INCOMING_TABLENAME; ?>-sp_docIDs_links').val(null)
                .trigger(
                    "change");
        })
        // БЛОК ОБРАБОТКИ ОШИБОК ЗАПОЛНЕНИЯ ФОРМЫ
        // >>> BLOCK END
        // ----- -- ----- -- ----- --
        //
        editor_incoming.on('postCreate postEdit close', function(e) {
            // editor_incoming.off('preClose');
            // Обновление страницы
            // location.reload();
            // table_incoming.ajax.reload(null, false);
            // $("#doc-editor-menu-tab-1-errmsg").html('');
            // $("#doc-editor-menu-tab-2-errmsg").html('');
            // $("#doc-editor-menu-tab-3-errmsg").html('');
            // $("#doc-editor-menu-tab-4-errmsg").html('');
            // $("#doc-editor-menu-tab-5-errmsg").html('');


            // useDL = editor_incoming.field(
            //     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline').val();
            // useBM = editor_incoming.field(
            //         '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyDL'
            //     )
            //     .val();
            // if (useDL == '1' && useBM !== '1') {
            //     // console.log("Дедлайн", useDL, "Уведомления", useBM);
            //     $.confirm({
            //         theme: 'light',
            //         columnClass: 'col-md-12',
            //         title: 'Внимание!',
            //         content: 'В текущей конфигурации документа включен контроль исполнения и установлен дедлайн, но при этом отключено уведомление ркуовосдтва об истечении срока исполнения. Вы уверены, что вы не забыли выставить соответствующую настройку?',
            //         buttons: {
            //             cancel: {
            //                 text: 'Сохранить не включая уведомление',
            //                 action: function() {
            //                     editor_incoming.field(
            //                         '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyDL'
            //                     ).val(0);
            //                     editor_incoming.submit();
            //                     // $.alert('Не включать');
            //                     e.preventDefault;
            //                     return true;
            //                 }
            //             },
            //             confirm: {
            //                 text: 'Включить уведомление руководства и сохранить',
            //                 btnClass: 'btn-red',
            //                 keys: ['enter', 'shift'],
            //                 action: function() {
            //                     editor_incoming.field(
            //                         '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyDL'
            //                     ).val(1);
            //                     editor_incoming.submit();
            //                     // $('#DTE_Field_mailbox_incoming-inbox_controlIspolMailSpecialNotifyDL_0').val();
            //                     // $.alert('Включить уведомление');
            //                     return true;
            //                 }
            //             }
            //         }
            //     });
            //     e.preventDefault;
            // return true;
            // }


        })
        //
        //
        editor_incoming.on('submitError', function(e, xhr, err, thrown, data) {
            // console.log(data);
            if ((thrown == "SyntaxError: Unexpected number in JSON at position 4" || thrown ==
                    "SyntaxError: Unexpected token < in JSON at position 0" ||
                    "SyntaxError: Unexpected non - whitespace character after JSON at position 4"
                ) &&
                err ==
                "parsererror") {
                editor_incoming.off('preClose');
                editor_incoming.close();
            }
            // console.log("err: " + err);
            // console.log("thrown: " + thrown);
            // console.log("xhr: " + xhr);
        });
        //
        //
        //
        // editor_incoming.dependent('selectedContractorIDs',
        //     function(val, data, callback, e) {
        //         var userKodispol =<?php echo $_SESSION['mail_user_kodispol']; ?>;
        //         var checkUserOnContractor = checkArrOnString(val, userKodispol);
        //         if (checkUserOnContractor !== 1) {
        //             editor_incoming.field(
        //                     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder1')
        //                 .disable();
        //             editor_incoming.field(
        //                     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder2')
        //                 .disable();
        //             editor_incoming.field(
        //                     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyDL')
        //                 .disable();
        //             editor_incoming.field(
        //                     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyCheckout')
        //                 .disable();
        //         } else {
        //             editor_incoming.field(
        //                     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder1')
        //                 .enable();
        //             editor_incoming.field(
        //                     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder2')
        //                 .enable();
        //             editor_incoming.field(
        //                     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyDL')
        //                 .enable();
        //             editor_incoming.field(
        //                     '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyCheckout')
        //                 .enable();
        //         }
        //         callback(true);
        //     }
        // );
        //
        //
        editor_incoming.dependent(
            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline',
            function(val, data, callback, e) {
                // console.log("editor_incoming.dependent('inbox_controlIspolUseDeadline')", val);
                userIDS = '<?php echo $_SESSION['id']; ?>';
                username = '<?php echo $_SESSION['lastname']; ?>';
                userlogin = '<?php echo $_SESSION['login']; ?>';
                if (val == "1") {
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDateDeadline')
                        .enable();
                    var datedeadline = editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDateDeadline').val();
                    // console.log('datedeadline', datedeadline);
                    if (checkVal(datedeadline)) {
                        editor_incoming.field(
                                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDateDeadline')
                            .val(moment(datedeadline, "DD.MM.YYYY").format("DD.MM.YYYY"));
                    } else {
                        editor_incoming.field(
                                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDateDeadline')
                            .val(moment().add(<?php echo $deadline_default_days; ?>, 'days').format(
                                "DD.MM.YYYY"));
                    }
                    var ispols = String(editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractor_kodzayvispol'
                        )
                        .val());
                    var userid = String(<?php echo $_SESSION['mail_user_kodispol']; ?>);
                    // console.log("CHECKPOINT 2471 >>>", ispols, userid, checkArrOnString(ispols, userid));
                    if (checkArrOnString(ispols, userid) != -1) {
                        editor_incoming.field(
                                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder1'
                            )
                            .enable();
                        editor_incoming.field(
                                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder2'
                            )
                            .enable();
                        editor_incoming.field(
                                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyDL'
                            )
                            .enable();
                    }
                    if (userIDS === "999" || userIDS === "1011" || userIDS === "1114" || userIDS === "1067" ||
                        username == "Никифорова" || userlogin == "liliya@atgs.ru") {
                        editor_incoming.field(
                                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyDL'
                            )
                            .enable();
                        $('fieldset.inbox_controlIspolMailSpecialNotifyDL').addClass('blink');
                    }
                    editor_incoming.field('checkbox_controlIspolMailUserListNotifyDL').enable();
                } else {
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDateDeadline')
                        .disable();
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDateDeadline')
                        .val(null);
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder1')
                        .disable();
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder1')
                        .val(0);
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder2')
                        .disable();
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder2')
                        .val(0);
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyDL')
                        .disable();
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyDL')
                        .val(0);
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyDL')
                        .disable();
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyDL')
                        .val(0);
                    $('fieldset.inbox_controlIspolMailSpecialNotifyDL').removeClass('blink');
                    editor_incoming.field('checkbox_controlIspolMailUserListNotifyDL').disable();
                    editor_incoming.field('checkbox_controlIspolMailUserListNotifyDL').val(0);
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailUserListNotifyDL')
                        .val(null);
                }
                // callback(true);
            }
        );
        //
        //
        editor_incoming.dependent(['ispolStatus', 'ispolStatusOtherOn', 'ispolStatusOtherOff'],
            function(val, data, callback, e) {
                // console.log("CHECK >>> dependent ispolStatus", $('input[id="DTE_Field_ispolStatus_0"]').is(':checked'), val);
                if ($(
                        'input[id="DTE_Field_ispolStatus_0"], input[id="DTE_Field_ispolStatusOtherOn_0"], input[id="DTE_Field_ispolStatusOtherOff_0"]'
                    )
                    .is(':checked')) {
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyCheckout'
                        )
                        .enable();
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolCheckoutComment'
                        )
                        .show();
                } else {
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyCheckout'
                        )
                        .disable();
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyCheckout'
                        )
                        .val(0);
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolCheckoutComment'
                        )
                        .hide();
                }
                callback(true);
            }
        );
        //
        //
        editor_incoming.dependent(
            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractor_kodzayvispol',
            function(val, data, callback, e) {
                if (val == "" || val == "000000000000000") {
                    editor_incoming.field('toSendEmail').disable();
                    editor_incoming.field('toSendEmail').set(0);
                } else {
                    editor_incoming.field('toSendEmail').enable();
                    if (editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.toSendEmail')
                        .val() !=
                        "1") {
                        editor_incoming.field('toSendEmail').set(1);
                    } else {
                        editor_incoming.field('toSendEmail').set(0);
                    }
                }
                // console.log('CONTROL(2487) >>> inbox_docContractor_kodzayvispol', val);
                callback(true);
            }
        );
        //
        //
        editor_incoming.dependent(
            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz',
            function(val, data, callback, e) {
                if (val == "000000000000000") {

                    // Меняем свойство блока ручного ввода контрагента на BLOCK
                    $('#customForm-mail-main-inbox .sender-manualinput-alert').css('display', 'block');

                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm')
                        .enable();
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm')
                        .show();
                } else {

                    // Меняем свойство блока ручного ввода контрагента на BLOCK
                    $('#customForm-mail-main-inbox .sender-manualinput-alert').css('display', 'none');

                }
                // console.log('CONTROL(3496) >>> inbox_docSender_kodzakaz', val);
                callback(true);
            }
        );
        //
        //
        editor_incoming.dependent('enblSenderManual', function(val, data, callback, e) {
            // console.log("editor_incoming.dependent('enblSenderManual')", val);
            if (val == "1") {
                editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender')
                    .enable();
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSenderName')
                    .enable();
                editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp1').set(
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz')
                    .get());
                editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp2').set(
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender').get());
                editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp3').set(
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSenderName').get());
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz')
                    .set(
                        "000000000000000");
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz')
                    .disable();
                // editor_incoming.field(
                //         '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz')
                //     .hide(false);
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm')
                    .disable();
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm')
                    .hide(false);
                // $("#sender_filter").val(null);
                // $("#sender_filter").disable;
                $(".sDS1").hide();
            } else {
                if ((editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp1')
                        .get()) !=
                    '') {
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz')
                        .set(
                            editor_incoming
                            .field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp1').get());
                }
                if ((editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp2')
                        .get()) !=
                    '') {
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender')
                        .set(
                            editor_incoming
                            .field(
                                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp2').get());
                }
                if ((editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp3')
                        .get()) !=
                    '') {
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSenderName')
                        .set(
                            editor_incoming
                            .field(
                                '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp3').get());
                }
                editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender')
                    .disable();
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSenderName')
                    .disable();
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz')
                    .enable();
                // editor_incoming.field(
                //         '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz')
                //     .show(false);
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm')
                    .enable();
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakazm')
                    .show(false);
                $(".sDS1").show();
            }
            callback(true);
        });
        //
        //
        //
        //
        editor_incoming.dependent('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive',
            function(val, data, callback, e) {
                // console.log("CHECK >>> dependent inbox_controlIspolActive", val);
                if (val == "1") {
                    var koddocmail = editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail').val();
                    var userid = <?php echo $_SESSION['id']; ?>;
                    ajaxRequest_getUserCheckout(koddocmail, userid, 'getUserCheckout');
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline')
                        .enable();
                    editor_incoming.field('checkbox_controlIspolMailUserListNotifyCheckout').enable();
                    editor_incoming.field('ispolStatusOtherOn').enable();
                    editor_incoming.field('ispolStatusOtherOff').enable();
                } else {
                    editor_incoming.field('ispolStatus').disable();
                    editor_incoming.field('ispolStatus').val(0);
                    $('input[id="DTE_Field_ispolStatus_0"]').prop('checked', false);
                    editor_incoming.field('ispolStatus').labelInfo('<?php echo $ispolStatus_msg_0; ?>');

                    editor_incoming.field('ispolStatusOtherOn').disable();
                    editor_incoming.field('ispolStatusOtherOn').val(0);
                    $('input[id="DTE_Field_ispolStatusOtherOn_0"]').prop('checked', false);
                    editor_incoming.field('ispolStatusOtherOff').disable();
                    editor_incoming.field('ispolStatusOtherOff').val(0);
                    $('input[id="DTE_Field_ispolStatusOtherOff_0"]').prop('checked', false);

                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline')
                        .disable();
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline')
                        .val(0);
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyDL')
                        .disable();
                    editor_incoming.field(
                            '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyDL')
                        .val(0);
                    editor_incoming.field('checkbox_controlIspolMailUserListNotifyCheckout').disable();
                    editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailUserListNotifyCheckout'
                    ).val(null);
                }
                callback(true);
            }
        );
        //
        //
        $(document).on("click", "input#DTE_Field_checkbox_controlIspolMailUserListNotifyCheckout_0", function() {
            // console.log('DTE_Field_checkbox_controlIspolMailuserListNotifyCheckout_0 is clicked');
            let userID = <?php echo $_SESSION['id']; ?>;
            let userListNotifyCheckout_New = '';
            let userListNotifyCheckout_Save = '';
            let userListNotifyCheckout = editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailUserListNotifyCheckout')
                .val();

            if ($(this).is(':checked')) {
                if (userListNotifyCheckout !== "") {
                    if (userListNotifyCheckout.indexOf(userID) === -1) {
                        userListNotifyCheckout_New = (checkVal(userListNotifyCheckout) === 0) ? userID :
                            userListNotifyCheckout +
                            ',' + userID;
                    }
                } else {
                    userListNotifyCheckout_New = userID;
                }
                userListNotifyCheckout_Save = userListNotifyCheckout_New;
            } else {
                if (userListNotifyCheckout !== "") {
                    if (userListNotifyCheckout.indexOf(userID) !== -1) {
                        userListNotifyCheckout_New = userListNotifyCheckout.replace(userID, '');
                    }
                }
                // userListNotifyCheckout_New = userListNotifyCheckout_New.replace(',,', '');
                userListNotifyCheckout_New = trim(userListNotifyCheckout_New, ',,');
                userListNotifyCheckout_New = rtrim(userListNotifyCheckout_New, ',');
                userListNotifyCheckout_New = ltrim(userListNotifyCheckout_New, ',');
                userListNotifyCheckout_Save = userListNotifyCheckout_New;
            }
            editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailUserListNotifyCheckout')
                .val(userListNotifyCheckout_Save);
        });
        //
        //
        $(document).on("click", "input#DTE_Field_checkbox_controlIspolMailUserListNotifyDL_0", function() {
            // console.log('DTE_Field_checkbox_controlIspolMailUserListNotifyDL_0 is clicked');
            let userID = <?php echo $_SESSION['id']; ?>;
            let userListNotifyDL_New = '';
            let userListNotifyDL_Save = '';
            let userListNotifyDL = editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailUserListNotifyDL')
                .val();

            if ($(this).is(':checked')) {
                if (userListNotifyDL !== "") {
                    if (userListNotifyDL.indexOf(userID) === -1) {
                        userListNotifyDL_New = (checkVal(userListNotifyDL) === 0) ? userID :
                            userListNotifyDL +
                            ',' + userID;
                    }
                } else {
                    userListNotifyDL_New = userID;
                }
                userListNotifyDL_Save = userListNotifyDL_New;
            } else {
                if (userListNotifyDL !== "") {
                    if (userListNotifyDL.indexOf(userID) !== -1) {
                        userListNotifyDL_New = userListNotifyDL.replace(userID, '');
                    }
                }
                // userListNotifyDL_New = userListNotifyDL_New.replace(',,', '');
                userListNotifyDL_New = trim(userListNotifyDL_New, ',,');
                userListNotifyDL_New = rtrim(userListNotifyDL_New, ',');
                userListNotifyDL_New = ltrim(userListNotifyDL_New, ',');
                userListNotifyDL_Save = userListNotifyDL_New;
            }
            editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailUserListNotifyDL')
                .val(userListNotifyDL_Save);
        });
        //
        //
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // editor_incoming.dependent('enbl_outbox_rowIDadd_rel', function(val, data, callback, e) {
        // // console.log('enbl_outbox_rowIDadd_rel is', val[0]);
        // if (val[0] !== '2') {
        //     $('#docType2-section-rowID-add').hide();
        // }
        // });
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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
                url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/dt_russian-mail.json"
            },
            ajax: {
                url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process.php",
                type: "POST"
            },
            // ajax: DataTable.pipeline({
            //     url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process.php",
            //     method: "POST",
            //     pages: 5 // number of pages to cache
            // }),
            serverSide: true,
            stateSave: true,
            stateDuration: -1,
            stateSaveCallback: function(settings, data) {
                sessionStorage.setItem('DataTables_Inc' + settings.sInstance, JSON.stringify(data))
            },
            stateLoadCallback: function(settings) {
                return JSON.parse(sessionStorage.getItem('DataTables_Inc' + settings.sInstance))
            },
            createdRow: function(row, data, index) {
                let docType = data.<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docType;
                let relRowID = data.<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel;
                let relDocID = data.<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_docID_rel;
                let relRowIDadd = data.<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel;
                let ctrlActive = data.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                    .inbox_controlIspolActive;
                let ctrlCheckout = data.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                    .inbox_controlIspolCheckout;
                //
                let alarmColor = 'rgb(226, 201, 201)';
                let warningColor = 'rgb(247, 215, 178)';
                let successColor = 'rgb(201, 226, 201)';
                let defaultColor = 'inherit';
                //
                console.log('checkVal(relRowID)', checkVal(relRowID), 'checkVal(relRowIDadd)', checkVal(
                    relRowIDadd), 'ctrlActive', ctrlActive, 'ctrlCheckout', ctrlCheckout);
                if (docType == 3) {
                    ;
                    if (checkVal(relRowID) == 0 && checkVal(relRowIDadd) == 0 && ctrlActive == 1 &&
                        ctrlCheckout == 0) {
                        $(row).css('background-color', alarmColor);
                    } else if (checkVal(relRowID) == 0 && checkVal(relRowIDadd) == 0 &&
                        ctrlActive ==
                        1 && ctrlCheckout == 1) {
                        $(row).css('background-color', warningColor);
                    } else if ((checkVal(relRowID) == 1 || checkVal(relRowIDadd) == 1) &&
                        ctrlActive ==
                        1 && ctrlCheckout == 0) {
                        $(row).css('background-color', warningColor);
                    } else if ((checkVal(relRowID) == 1 || checkVal(relRowIDadd) == 1) &&
                        ctrlActive == 1 && ctrlCheckout == 1) {
                        $(row).css('background-color', successColor);
                    } else {
                        $(row).css('background-color', defaultColor);
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
                        let shortStr = data.substr(0, 31) + " ...";
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
                        if (fullStr.length > 31) {
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
                        let fullStrTooltip = data;
                        let shortStr = data.substr(0, 29) + '...';
                        let manualInputIcon = '';
                        let manualInputClass = '';
                        let kodzakaz = row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_docSender_kodzakaz;
                        if (kodzakaz === "000000000000000") {
                            manualInputIcon =
                                '<span data-toggle="tooltip" data-placement="top" title="Название контрагента введено вручную. Возможно просто кто-то не счел нужным его поискать в Справочнике и сделать доступным в Почте, или же добавить как новую оргнизацию." class="float-left"><sup><i class="fa-solid fa-star-of-life fa-xs text-danger"></i><i class="fa-solid fa-star-of-life fa-xs text-danger mr-1"></i></sup></span>';
                            manualInputClass = '';
                        }
                        if (fullStr.length > 29) {
                            return '<span class="about' + manualInputClass +
                                '" data-toggle="tooltip" title="' +
                                fullStrTooltip +
                                '">' + shortStr + manualInputIcon + '</span>';
                        } else {
                            return '<span class="about' + manualInputClass +
                                '">' + fullStr + manualInputIcon + '</span>';
                        }
                    }
                },
                {
                    orderable: false,
                    searchable: true,
                    targets: 6,
                    render: function(data, type, row, meta) {
                        specialMailDL = row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                            .inbox_controlIspolMailSpecialNotifyDL;

                        if (specialMailDL == '1') {
                            prefix =
                                '<span data-toggle="tooltip" title="Руководство получит уведомление при наступлении срока дедлайна в случае, если к этому моменту документ не будет исполнен" class="text-primary pr-1" style="position:relative; top:-2px"><i class="fa-solid fa-share fa-2xs fa-beat-fade" aria-hidden="true"></i></span>'
                        } else {
                            prefix = '';
                        }
                        return '<div class="d-flex"><div class="">' + prefix + data +
                            '</div></div>';
                    }
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
                                        '. Контроль исполнения активен. Дедлайна не было."><span class="text-success"><i class="fa-solid fa-circle-check fa-lg"></i></span></span>';
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
                            return '<div class=""><span data-toggle="tooltip" title="Номер документа, связанный с текущим как ответный, либо являющийся запросом для текущего. При клике вы перейдете к этому документу в разделе исходящих." class=""><a class="text-dark" href="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/index.php?type=out&mode=thisyear&rel=' +
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
                            return '<div class=""><span data-toggle="tooltip" title="Номер документа, связанный с текущим как ответный, либо являющийся запросом для текущего. При клике вы перейдете к этому документу в разделе исходящих." class=""><a class="text-dark" href="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/index.php?type=out&mode=thisyear&rel=' +
                                row.<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_koddocmail_rel +
                                '"><div class="docnum">' + row
                                .<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_docID_rel +
                                '</div></a></span></div>';
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
                        table_incoming.ajax.reload(null, false);
                        table_incoming.rows('.selected').deselect();
                    },
                    className: 'btn-dark refreshButton'
                },
                {
                    extend: "create",
                    editor: editor_incoming,
                    text: 'Добавить запись',
                    formButtons: [{
                            text: 'Добавить',
                            className: 'btn-dark createButton',
                            action: function() {
                                // console.log('btn-dark createButton', 'triggered')
                                this.submit();
                            }
                        },
                        {
                            text: 'Отмена',
                            className: 'btn-dark cancelButton',
                            action: function() {
                                this.close();
                            }
                        }
                    ],
                    className: 'btn-dark createButton'
                },
                {
                    extend: "edit",
                    editor: editor_incoming,
                    text: "Редактировать",
                    formButtons: [{
                            text: 'Сохранить',
                            className: 'btn-dark editButton',
                            action: function() {
                                this.submit();
                            }
                        },
                        {
                            text: 'Отмена',
                            className: 'btn-dark cancelButton',
                            action: function() {
                                this.close();
                            }
                        }
                    ],
                    className: 'btn-dark editButton'
                },
                {
                    extend: "remove",
                    editor: editor_incoming,
                    text: "Удалить",
                    formButtons: [{
                            text: 'Удалить',
                            className: 'btn-danger removeButton',
                            action: function() {
                                this.submit();
                            }
                        },
                        {
                            text: 'Отмена',
                            className: 'btn-dark cancelButton',
                            action: function() {
                                this.close();
                            }
                        }
                    ],
                    className: 'btn-danger removeButton'
                }
            ]
        });
        table_incoming.on('draw', function() {
            // do something with the ID of the selected items
            // console.log('TOOOOOOLTIPSSSSS');
        });
        table_incoming.on('length', function(e, settings, len) {
            ajaxRequest_saveSessionVars('incoming_pageLength', len);
        });
        table_incoming.on('page', function(e, settings, len) {
            var info = table_incoming.page.info();
            // console.log('incoming_pageCurrent #4', info.page);
            ajaxRequest_saveSessionVars('incoming_pageCurrent', info.page);
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
                    .<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>.file_originalname +
                    '</a></span>' :
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
                        ispolStatus_str =
                            'Контроль исполнения активен, дедлайн есть, документ не исполнен';
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
        editor_incoming.on('postCreate postEdit close', function() {
            editor_incoming.off('preClose');
            var tableInfo = table_incoming.page.info();
            var currentPage = tableInfo.page;
            ajaxRequest_saveSessionVars('incoming_pageCurrent', currentPage);
            location.reload();
        })
        //
        //
        $('#columnSearch_btnApply').on('click', function() {
            // console.log("Ответственный: " + $("#filterIspol").val());
            table_incoming.ajax.reload(null, true);
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

            $('#mail-incoming-filters-block *').filter('input, select').each(function(index,
                element) {
                sessionStorage.removeItem('inbox_' + $(element).attr('id'));
                sessionStorage.setItem('inbox_' + $(element).attr('id'), $(element).val());
                // console.log(index, 'sessionStorage.getItem()', element, sessionStorage.getItem('inbox_' + $(element).attr('id')));
            });
        });
        //
        //
        $('#columnSearch_btnClear, button.columnSearch_btnClear').on('click', function() {
            // Очищаем блок фильтров, отслеживая заблокированные элементы
            $('#mail-incoming-filters-block *').filter('input, select').each(function(index,
                element) {
                var itemID = $(element).attr('id');
                var tagName = $(element).prop('tagName').toLowerCase();
                if (!$(element).prop('disabled')) {
                    sessionStorage.removeItem('inbox_' + itemID);
                }
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
            //
            var ispolname =
                '<?php echo $_QRY_ISPOL["namezayvfio"] != "" ? $_QRY_ISPOL["namezayvfio"] : ""; ?>';
            var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispol"]; ?>';
            var showIspolOnly =
                '<?php echo $_QRY_ISPOL["showispolonly"]; ?>';
            if (ispolStatus === "1" && ispolname !== "" && showIspolOnly ===
                "1") {
                if ($("#chkOnlyIspolMe").prop('checked') === true) {
                    // table_incoming
                    //     .columns(7)
                    //     .search(ispolname)
                    //     .draw();
                    $("#filterIspol").val(ispolname);
                } else {
                    // table_incoming.columns().search('').draw();
                }
            } else {
                // table_incoming.columns().search('').draw();
            }
            $('#mail-incoming-filters-block *').filter('input, select').each(function(index,
                element) {
                sessionStorage.removeItem('inbox_' + $(element).attr('id'));
            });
            // table_incoming.columns().search('').draw();
            $('#columnSearch_btnApply').click();
            table_incoming.one('draw', function() {
                window.location.replace(removeURLParameter(document.location.href, 'rel'));
            });
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
                ajaxRequest_loadFromDB_showIspolOnly('showIspolOnly');
                var ispolname =
                    '<?php echo $_QRY_ISPOL["namezayvfio"] != "" ? $_QRY_ISPOL["namezayvfio"] : ""; ?>';
                var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispol"]; ?>';
                var showIspolOnly = '<?php echo $_QRY_ISPOL["showispolonly"]; ?>';
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
            if (($('#filterKOD').val() != "") || ($('#filterIspol').val() != "") || ($(
                        '#filterType')
                    .val() != "") || ($('#filterNumber').val() != "") || ($('#filterAbout').val() !=
                    "") ||
                ($(
                    '#filterSender').val() != "") || ($('#filterInControl').val() != "") || ($(
                    '#filterCheckout').val() != "") || ($(
                    '#filterDept').val() != "") || ($('#filterRecipient').val() != "") || ($(
                    '#filterSourceID').val() != "") || ($('#filterIspolDL').val() != "")) {
                $('p.mail-incoming-filters-button i').show();
                $('button.columnSearch_btnClear.btnTop').show();
                // console.log('Filters In');
            } else {
                $('p.mail-incoming-filters-button i').hide();
                $('button.columnSearch_btnClear.btnTop').hide();
                // console.log('Filters Out');
            }

            var notEmpty;
            $("#mail-incoming-filters-block .form-control").each(function() {
                var element = $(this);
                if (element.val() != "") {
                    notEmpty = true;
                    // console.log(element, element.val());
                    element.addClass('filter-active');
                } else {
                    element.removeClass('filter-active');
                }
            });
            // var rowCurrent = '<?php echo $_SESSION['incoming_selectedRowID']; ?>';
            // if (rowCurrent !== '') {
            //     table_incoming.row('#' + rowCurrent).select();
            // }
        });
        //
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        //
        table_incoming.on('click', 'tr', function() {
            var id = table_incoming.row(this).id();
            ajaxRequest_saveSessionVars('incoming_selectedRowID', id)
        });
        //
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        //
        table_incoming.one('preDraw', function() {
            // $('#filterKOD').val('');
            // $('#filterIspol').val('');
            // $('#filterType').val('');
            // $('#filterNumber').val('');
            // $('#filterAbout').val('');
            // $('#filterSender').val('');
            // $('#filterInControl').val('');
            // $('#filterCheckout').val('');
            // $('#filterDept').val('');
            // $('#filterRecipient').val('');
            // $('#filterSourceID').val('');
            // $('#filterIspolDL').val('');
            //
            var pageCurrent = '<?php echo $_SESSION['incoming_pageCurrent']; ?>';
            var rowCurrent = '<?php echo $_SESSION['incoming_selectedRowID']; ?>';
            // console.log('incoming_selectedRowID', rowCurrent);
            // console.log('incoming_pageCurrent #1', Number(rowCurrent));
            if (pageCurrent !== 'nodraw' && pageCurrent !== '' && pageCurrent !== null) {
                // console.log('incoming_pageCurrent #2', Number(pageCurrent));
                table_incoming.one('draw', function() {
                    // table_incoming.page(Number(pageCurrent)).draw('page');
                    // ajaxRequest_saveSessionVars('incoming_pageCurrent', 'nodraw');
                    // console.log('incoming_pageCurrent #3', 'Table is drawn', table_incoming.page.info().page);
                });
            }
            if (rowCurrent !== '') {
                // table_incoming.row('#' + rowCurrent).select();
            }
        });
        //
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        //
        $("#chkOnlyIspolMe").click(function() {
            ajaxRequest_saveToDB_showIspolOnly('showIspolOnly');
            var ispolname =
                '<?php echo $_QRY_ISPOL["namezayvfio"] != "" ? $_QRY_ISPOL["namezayvfio"] : ""; ?>';
            var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispol"]; ?>';
            if (ispolStatus === "1" && ispolname != "") {
                if ($("#chkOnlyIspolMe").prop('checked') === true) {
                    // table_incoming
                    //     .columns(7)
                    //     .search(ispolname)
                    //     .draw();
                    $("#filterIspol").val(ispolname);
                    $("#filterIspol").prop('disabled', true);

                    $('#mail-incoming-filters-block *').filter('input, select').each(function(index,
                        element) {
                        sessionStorage.removeItem('inbox_' + $(element).attr('id'));
                        sessionStorage.setItem('inbox_' + $(element).attr('id'), $(element)
                            .val());
                        // console.log(index, 'sessionStorage.getItem()', element, sessionStorage.getItem('inbox_' + $(element).attr('id')));
                    });
                    ajaxRequest_getIncomingStats_v2('1', 'getIncomingStats');
                    $('#mail-incoming-deck-block .card-header i').removeClass('d-none');
                } else {
                    table_incoming.columns().search('').draw(false);
                    $("#filterIspol").val('');
                    $("#filterIspol").prop('disabled', false);

                    $('#mail-incoming-filters-block *').filter('input, select').each(function(index,
                        element) {
                        sessionStorage.removeItem('inbox_' + $(element).attr('id'));
                        sessionStorage.setItem('inbox_' + $(element).attr('id'), $(element)
                            .val());
                        // console.log(index, 'sessionStorage.getItem()', element, sessionStorage.getItem('inbox_' + $(element).attr('id')));
                    });
                    ajaxRequest_getIncomingStats_v2('0', 'getIncomingStats');
                    $('#mail-incoming-deck-block .card-header i').addClass('d-none');
                }
                $('#columnSearch_btnApply').click();
            } else {
                table_incoming.columns().search('').draw(false);
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
            // console.log(rowid, 'To delete clicked');
            ajaxRequest_deleteAttachedFile(rowid, 'deleteAttachedFile');
        });
        //
        //
        $(document).on("click", ".remove-mainfile", function() {
            var rowid = $(this).attr('rowid');
            var koddocmail = $(this).attr('koddocmail');
            // console.log(rowid, 'To delete mainfile clicked');
            editor_incoming.field('<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID').val(
                '');
            ajaxRequest_deleteAttachedFile(rowid, 'deleteAttachedFile');
        });
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
            // console.log('DTE_Field_enbl_outbox_docType_change_0 is clicked');
            if ($(this).is(':checked')) {
                // console.log('DTE_Field_enbl_outbox_docType_change_0 is checked');
                $('#outbox-rowID-rel-alert').css('display', 'block');
            } else {
                // console.log('DTE_Field_enbl_outbox_docType_change_0 is unchecked');
                $('#outbox-rowID-rel-alert').css('display', 'none');
            }
        });
        //
        //
        $(document).on("change", "input#DTE_Field_enbl_outbox_rowIDadd_rel_0", function() {
            // console.log('DTE_Field_enbl_outbox_rowIDadd_rel_0 is clicked');
            if ($(this).is(':checked')) {
                $(".rowIDadd-enbl").css('display', 'block');
                editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel').enable();
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel')
                    .enable();
                editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel').focus();
                $('#linked-mail-listDocs').prop('disabled', false);
                $('#outbox-rowIDadd-rel-alert').css('display', 'block');
            } else {
                $(".rowIDadd-enbl").css('display', 'none');
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel')
                    .disable();
                editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel').val('');
                editor_incoming.field(
                        '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel')
                    .disable();
                editor_incoming.field(
                    '<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel').val('');
                $('#linked-mail-listDocs option').remove();
                $('#linked-mail-listDocs').prop('disabled', true);
                $('#outbox-rowIDadd-rel-alert').css('display', 'none');
            }
        });
        //
        //
        $(document).on("click", "span#link-logCheckouts", function() {
            var koddocmail = $(this).attr('data-id');
            // console.log('#div.link modal-logCheckouts clicked', koddocmail);
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
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-showLogCheckouts.php",
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
                                return '<span class="text-success"><i class="fa-solid fa-user-check fa-xl"></i></span>';
                            } else {
                                return '<span class="text-danger"><i class="fa-solid fa-user-clock fa-xl"></i></span>';
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
            // console.log('#div.link modal-logChanges clicked', koddocmail);
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
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-showLogChanges.php",
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
                        customClass: 'tooltip-logchanges'
                    });
                },
                columns: [{
                    data: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logChanges.timestamp"
                }, {
                    data: "<?php echo __MAIL_INCOMING_PREFIX; ?>_logChanges.action"
                }, {
                    data: "mailbox_sp_users.namezayvfio"
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
                // console.log('Form displayed');
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
            // console.log('#div.link modal-logComments clicked', koddocmail);
            $('#modal-logComments > div.modal-dialog').on('shown.bs.modal', function(e) {
                // console.log('#modal-logComments > div.modal-dialog shown!');
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
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-showLogComments.php",
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
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-showLogComments.php",
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
                                return '<div class="commentUser">' + data +
                                    '</div>' +
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
                                if (row
                                    .<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments
                                    .action == "FORM" && row
                                    .<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments
                                    .commentAdd !== '' && row
                                    .<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments
                                    .commentAdd !== null) {
                                    var commAdd =
                                        '<div class="commentAdd text-info">' +
                                        row
                                        .<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments
                                        .commentAdd + '</div>';
                                } else {
                                    var commAdd = '';
                                }
                                return '<div class="commentBlock shadow px-3 py-2"><div class="commentText">' +
                                    data +
                                    '</div><div class="commentDate">Создано: ' + row
                                    .<?php echo __MAIL_INCOMING_PREFIX; ?>_logComments
                                    .timestamp +
                                    updateStr + commAdd + '</div></div>';
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
                    // console.log('submitSuccess');
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
            // console.log('#modal-logComments hidden!');
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
                // console.log('#modal-listAddFiles > div.modal-dialog shown!');
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
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-showLogAddFiles.php",
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
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-showLogAddFiles.php",
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
                // console.log('submitSuccess');
            });
        });
        $('#modal-listAddFiles').on('hidden.bs.modal', function(e) {
            // console.log('#modal-listAddFiles hidden!');
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
                // console.log('#modal-listLinkDocs > div.modal-dialog shown!');
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
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-showLogLinkDocs.php",
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
                            return '<span class="link"><a href="index.php?type=out&mode=archive&rel=' +
                                row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                .koddocmail + '">' +
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
            // console.log('#modal-listLinkDocs hidden!');
        });
        //
        // ----- -- ----- -- ----- -- ----- -- ----- -- -----
        //
        $(document).on("click", "#btn-linkedOutgoing-showFiles", function() {
            var koddocmail = $(this).attr('data-id');
            var docid = $(this).attr('data-docid');
            // console.log('#btn-linkedOutgoing-showFiles clicked!', koddocmail);
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
                    // console.log('table-listAdditionalLinkDocsMInc empty!');
                    $('#table-listAdditionalLinkDocsMInc thead').css('display', 'none');
                } else {
                    $('#table-listAdditionalLinkDocsMInc thead').css('display', '');
                }
                //
                if ($('#table-listAdditionalLinkDocsMOut > tbody > tr > td').hasClass(
                        'dataTables_empty')) {
                    // console.log('table-listAdditionalLinkDocsMOut empty!');
                    $('#table-listAdditionalLinkDocsMOut thead').css('display', 'none');
                } else {
                    $('#table-listAdditionalLinkDocsMOut thead').css('display', '');
                }
                //
                if ($('#table-listAdditionalLinkDocsDog > tbody > tr > td').hasClass(
                        'dataTables_empty')) {
                    // console.log('table-listAdditionalLinkDocsDog empty!');
                    $('#table-listAdditionalLinkDocsDog thead').css('display', 'none');
                } else {
                    $('#table-listAdditionalLinkDocsDog thead').css('display', '');
                }
                //
                if ($('#table-listAdditionalLinkDocsSP > tbody > tr > td').hasClass(
                        'dataTables_empty')) {
                    // console.log('table-listAdditionalLinkDocsSP empty!');
                    $('#table-listAdditionalLinkDocsSP thead').css('display', 'none');
                } else {
                    $('#table-listAdditionalLinkDocsSP thead').css('display', '');
                }
            });
            //
            //
            var table_listAdditionalLinkDocsDog = $('#table-listAdditionalLinkDocsDog').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/dt_russian-docDogLinks.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-showLogAdditionalLinkDocsDog.php",
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
            var table_listAdditionalLinkDocsSP = $('#table-listAdditionalLinkDocsSP').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/dt_russian-docSPLinks.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-showLogAdditionalLinkDocsSP.php",
                    type: "POST",
                    data: {
                        koddocmail: koddocmail
                    }
                },
                serverSide: true,
                columns: [{
                        data: "sp_contragents.kodcontragent"
                    },
                    {
                        data: "sp_contragents_opf.abbr"
                    },
                    {
                        data: "sp_contragents.namefull"
                    }
                ],
                columnDefs: [{
                    orderable: false,
                    width: '20%',
                    targets: 0,
                    render: function(data, type, row, meta) {
                        if (checkVal(data)) {
                            return '<span class="link"><a href="<?php echo __ROOT; ?>/sp/index.php?type=contragents&mode=profile&uid=' +
                                row.sp_contragents.kodcontragent +
                                '" target="_blank">' +
                                data + '</a></span>';
                        } else {
                            return '<span class=""><i class="fa-solid fa-ellipsis"></i></span>';
                        }
                    },
                }, {
                    orderable: false,
                    searchable: false,
                    width: '5%',
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
                        if (checkVal(data)) {
                            let fullStr = data;
                            let shortStr = data.substr(0, 58) + " ..."
                            if (fullStr.length > 55) {
                                return '<span class="" data-toggle="tooltip" data-html="true" data-placement="auto" title="' +
                                    fullStr + '">' + shortStr + '</span>';
                            } else {
                                return fullStr;
                            }
                        } else {
                            let fullStr = row.sp_contragents.nameshort;
                            let shortStr = row.sp_contragents.nameshort.substr(
                                    0,
                                    58) +
                                " ...";
                            if (fullStr.length > 55) {
                                return '<span class="" data-toggle="tooltip" data-html="true" data-placement="auto" title="' +
                                    fullStr + '">' + shortStr + '</span>';
                            } else {
                                return fullStr;
                            }
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
                        url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/dt_russian-docMailLinks.json"
                    },
                    ajax: {
                        url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-showLogAdditionalLinkDocsMOut.php",
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
                                return '<span class="link"><a href="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/index.php?type=out&mode=archive&rel=' +
                                    row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                    .koddocmail + '">' +
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
                        url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/dt_russian-docMailLinks.json"
                    },
                    ajax: {
                        url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/process/mailbox-incoming-process-showLogAdditionalLinkDocsMInc.php",
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
                                return '<span class="link"><a href="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/index.php?type=in&mode=archive&rel=' +
                                    row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                    .koddocmail + '">' +
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
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        $(document).on("keyup", "input#DTE_Field_mailbox_incoming-inbox_docSender", delay(function(e) {
            let checkOnSimilar = ajaxRequest_checkOnSimilar(this.value);
            console.log('Time elapsed 2!', this.value, checkOnSimilar);
        }, 500));
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        $(document).on("click", "input#DTE_Field_ispolStatus_0", function() {
            // console.log('DTE_Field_ispolStatus_0 is clicked');
            if ($(this).is(':checked')) {
                // console.log('DTE_Field_ispolStatus_0 is checked');
                // $.confirm({
                //     title: 'Отметка об исполнении установлена',
                //     theme: 'supervan', // 'material', 'bootstrap'
                //     columnClass: 'medium',
                //     content: '' +
                //         '<form action="" class="formName">' +
                //         '<div class="form-group">' +
                //         '<label>Ваш комментарий</label>' +
                //         '<textarea placeholder="Любой текст" class="name form-control" required></textarea>' +
                //         '</div>' +
                //         '</form>',
                //     buttons: {
                //         formSubmit: {
                //             text: 'Submit',
                //             btnClass: 'btn-blue',
                //             action: function() {
                //                 var name = this.$content.find('.name').val();
                //                 if (!name) {
                //                     $.alert('provide a valid name');
                //                     return false;
                //                 }
                //                 $.alert('Your name is ' + name);
                //             }
                //         },
                //         cancel: function() {
                //             //close
                //         },
                //     },
                //     onContentReady: function() {
                //         // bind to events
                //         var jc = this;
                //         this.$content.find('form').on('submit', function(e) {
                //             // if the user submits the form by pressing enter in the field.
                //             e.preventDefault();
                //             jc.$$formSubmit.trigger(
                //                 'click'); // reference the button and click it
                //         });
                //     }
                // });
            } else {
                // console.log('DTE_Field_ispolStatus_0 is unchecked');
                // $.confirm({
                //     title: 'Отметка об исполнении снята',
                //     theme: 'supervan', // 'material', 'bootstrap'
                //     columnClass: 'medium',
                //     content: '' +
                //         '<form action="" class="formName">' +
                //         '<div class="form-group">' +
                //         '<label>Ваш комментарий</label>' +
                //         '<textarea placeholder="Любой текст" class="name form-control" required></textarea>' +
                //         '</div>' +
                //         '</form>',
                //     buttons: {
                //         formSubmit: {
                //             text: 'Submit',
                //             btnClass: 'btn-blue',
                //             action: function() {
                //                 var name = this.$content.find('.name').val();
                //                 if (!name) {
                //                     $.alert('provide a valid text');
                //                     return false;
                //                 }
                //                 $.alert('Your text is ' + name);
                //             }
                //         },
                //         cancel: function() {
                //             //close
                //         },
                //     },
                //     onContentReady: function() {
                //         // bind to events
                //         var jc = this;
                //         this.$content.find('form').on('submit', function(e) {
                //             // if the user submits the form by pressing enter in the field.
                //             e.preventDefault();
                //             jc.$$formSubmit.trigger(
                //                 'click'); // reference the button and click it
                //         });
                //     }
                // });
            }
        });
        //
        //

        $('#modal-listAdditionalLinkDocs').on('hidden.bs.modal', function(e) {
            // console.log('#modal-listAdditionalLinkDocs hidden!');
        });
        //
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        //
        setInterval(function() {
            if (ajaxRequest_checkSessionAsync(sessionID)) {
                if (!editor_incoming.display()) {
                    table_incoming.ajax.reload(null, false);
                }
            }
        }, 2 * 60 * 1000); // mins * secs/min * 1000 milsec
        //
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        //
        // ajaxRequest_getIncomingStats('getIncomingStats');

        ajaxRequest_loadFromDB_showIspolOnly('showIspolOnly');
        //
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        //
        var ispolname =
            '<?php echo $_QRY_ISPOL["namezayvfio"] != "" ? $_QRY_ISPOL["namezayvfio"] : ""; ?>';
        var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispol"]; ?>';
        var showIspolOnly = '<?php echo $_QRY_ISPOL["showispolonly"]; ?>';
        if (ispolStatus === "1" && ispolname != "" && showIspolOnly ===
            "1") {
            if ($("#chkOnlyIspolMe").prop('checked') === true) {
                $("#filterIspol").val(ispolname);
            } else {
                $("#filterIspol").val('');
            }
            ajaxRequest_getIncomingStats_v2('1', 'getIncomingStats');
            $('#mail-incoming-deck-block .card-header i').removeClass('d-none');
        } else {
            ajaxRequest_getIncomingStats_v2('0', 'getIncomingStats');
            $('#mail-incoming-deck-block .card-header i').addClass('d-none');
        }

        $('#mail-incoming-filters-block *').filter('input, select').each(function(index, element) {
            var itemName = 'inbox_' + $(element).attr('id');
            var itemID = $(element).attr('id');
            var itemVal = sessionStorage.getItem(itemName);
            var tagName = $(element).prop('tagName').toLowerCase();
            if (tagName === 'select' && itemVal !== '') {
                $('#' + itemID + ' option:contains("' + itemVal + '")').prop('selected', true);
                $('#' + itemID).val(itemVal);
            }
            if (tagName === 'input' && itemVal !== '') {
                if ($('#' + itemID).val() != itemVal) {
                    $('#' + itemID).val(itemVal);
                }
            }
            // console.log('Incoming sessionStorage checkpoint [reload]', index, tagName, itemID, itemName,'>>> storage.val = ' + itemVal, '>>> $.val = ' + $('#' + itemID).val());
        });

        var unsetAllInUse = ajaxRequest_checkInUseDocStateDataAsync('unsetAll', 'unsetAll');
        //
        //
    });
</script>

<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/css/common-modals.css">
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/css/common-customform.css">
<?php
// ----- ----- ----- ----- -----
// Подключаем форму редактирования, форму поиска и выводим таблицу
// :::
include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5 . "/forms/mailbox-incoming-customForm.php";
include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5 . "/forms/mailbox-incoming-filters.php";
// ----- ----- ----- ----- ----- 
?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/css/mailbox-incoming.css">
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/css/mailbox-incoming-details.css">

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
                    <th>17</th>
                    <th>18</th>
                    <th>19</th>
                    <th>20</th>
                    <th>21</th>
                    <th>22</th>
                    <th>23</th>
                    <th>24</th>
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

; ?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/css/mailbox-incoming-modal-logCheckouts.css">
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

; ?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/css/mailbox-incoming-modal-logChanges.css">
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

; ?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/css/mailbox-incoming-modal-logComments.css">
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

; ?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/css/mailbox-incoming-modal-listAddFiles.css">
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

; ?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/css/mailbox-incoming-modal-listLinkDocs.css">
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

; ?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/css/mailbox-incoming-modal-listAdditionalLinkDocs.css">
<div class="modal fade" data-backdrop="true" id="modal-listAdditionalLinkDocs" tabindex="-1" role="dialog" aria-labelledby="modal-listAdditionalLinkDocs-label" aria-hidden="true">
    <div class="modal-dialog modal-lg custom-modal mail-features" role="document">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-listAdditionalLinkDocs-label">Связанные Входящие / Исходящие /
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

                    <div class="title text-left text-dark mb-3">Связанные контрагенты</div>
                    <table id="table-listAdditionalLinkDocsSP" class="table table-borderless table-striped" cellspacing="0" width="100%">
                        <thead class="thead-dark" style="display:none">
                            <tr>
                                <th>ID</th>
                                <th>ОПФ</th>
                                <th>Полное название</th>
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
; ?>