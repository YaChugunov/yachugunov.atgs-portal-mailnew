<?php
date_default_timezone_set('Europe/Moscow');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$ispolStatus_msg_0 = '<span class="errtext">В текущей конфигурации сервиса эта отметка будет недоступна, если вы не являетесь исполнителем по документу и(или) по нему не включен режим КИ (контроль исполнения).</span>';
$ispolStatus_msg_1 = '<span class="errtext">В текущей конфигурации сервиса управлять напоминаниями и уведомлениями исполнителю может только сам исполнитель. Кроме того, также должен быть активен режим КИ (контроль исполнения) и установлен дедлайн.</span>';
$ispolStatus_msg_2 = '<span class="inftext">Текущее состояние отметки по документу, сохраненное в БД</span>';
$ispolStatus_msg_3 = '<span class="inftext">Сначала сделайте отметку об исполнении документа</span>';
$ispolStatusOtherOn_msg_0 = '<span class="inftext text-primary">Вы можете отметить документ как исполненный за остальных ответственных</span>';
$ispolStatusOtherOff_msg_0 = '<span class="inftext text-primary">Вы можете снять отметку с докумена как исполненного за других ответственных</span>';
$tab3_ispolList_msg_0 = '<span class="errtext">Выберите ответственного(ых) либо сделайте отметку ниже < Без ответственных ></span>';
$deadline_default_days = 14;
$popoverLinkToKnow = "<span class='knoweledge-base-link'><a href='#nolink'>Подробнее в разделе Помощь</a></span>";

$_QRY_SystemSettings = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM mailbox_systemSettings WHERE typeMailbox = 'outgoing'"));
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
$_SESSION['outgoing_setControlIspolOnStart'] = $_QRY_UserSettings ? $_QRY_UserSettings['outgoing_setControlIspolOnStart'] : '0';
$_SESSION['outgoing_setDeadlineOnStart'] = $_QRY_UserSettings ? $_QRY_UserSettings['outgoing_setDeadlineOnStart'] : '0';
// ----- ----- ----- ----- ----- 
if (!isset($_SESSION['outgoing_pageLength']) && empty($_SESSION['outgoing_pageLength'])) {
    $_SESSION['outgoing_pageLength'] = $_QRY_UserSettings['outgoing_pageLengthDefault'];
}
// ----- ----- ----- ----- ----- 
if (!isset($_SESSION['outgoing_pageCurrent']) || $_SESSION['outgoing_pageCurrent'] == "") {
    $_SESSION['outgoing_pageCurrent'] = 'nodraw';
}
// ----- ----- ----- ----- ----- 
if (!isset($_SESSION['outgoing_selectedRowID']) || $_SESSION['outgoing_selectedRowID'] == "") {
    $_SESSION['outgoing_selectedRowID'] = '';
}
// ----- ----- ----- ----- ----- 
if (isset($_GET['mode'])) {
    switch ($_GET['mode']) {
        case 'thisyear':
            $__subsubtitle = 'Текущий год <span class="text-danger">/ ' . date("Y") . '</span>';
            $startTableDate = '"' . date('Y') . '-01-01 00:00:01"';
            $_SESSION['out_startTableDate'] = $startTableDate;
            $endTableDate = '"' . date('Y') . '-12-31 23:59:59"';
            $_SESSION['out_endTableDate'] = $endTableDate;
            if (isset($_GET['rel'])) {
                $__relID = $_GET['rel'];
            } else {
                $__relID = "norel";
            }
            break;
        case 'archive':
            if (isset($_GET['year']) && $_GET['year'] >= 2010 && $_GET['year'] <= date('Y')) {
                $__subsubtitle = 'Архив <span class="text-danger">/ ' . $_GET['year'] . '</span>';
                $startTableDate = '"' . $_GET["year"] . '-01-01 00:00:01"';
                $_SESSION['out_startTableDate'] = $startTableDate;
                $endTableDate = '"' . $_GET["year"] . '-12-31 23:59:59"';
                $_SESSION['out_endTableDate'] = $endTableDate;
            } else {
                $__subsubtitle = "Весь архив";
                $startTableDate = '"2010-01-01 00:00:01"';
                $_SESSION['out_startTableDate'] = $startTableDate;
                $endTableDate = '"' . date('Y') . '-12-31 23:59:59"';
                $_SESSION['out_endTableDate'] = $endTableDate;
            }
            break;
        default:
            $__subsubtitle = 'Текущий год <span class="text-danger">/ ' . date("Y") . '</span>';
            $startTableDate = '"' . date('Y') . '-01-01 00:00:01"';
            $_SESSION['out_startTableDate'] = $startTableDate;
            $endTableDate = '"' . date('Y') . '-12-31 23:59:59"';
            $_SESSION['out_endTableDate'] = $endTableDate;
    }
} else {
    $__subsubtitle = 'Текущий год <span class="text-danger">/ ' . date("Y") . '</span>';
    $startTableDate = '"' . date('Y') . '-01-01 00:00:01"';
    $_SESSION['out_startTableDate'] = $startTableDate;
    $endTableDate = '"' . date('Y') . '-12-31 23:59:59"';
    $_SESSION['out_endTableDate'] = $endTableDate;
}
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// 	require_once('../_assets/drivers/bd_remote.php');
// 	require_once(realpath('../_assets/functions/funcSecure.inc.php'));
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// Функция определения нового номера документа/записи (docID) для таблицы записей
//
function newDocID() {
    $query = mysqlQuery("SELECT MAX(outbox_docID) as lastDocID FROM " . __MAIL_OUTGOING_TABLENAME . " WHERE YEAR(outbox_docDate)=YEAR(NOW()) ORDER BY id DESC");
    $row = mysqli_fetch_assoc($query);
    $newDocID = $row['lastDocID'];
    $newDocID++;
    return $newDocID;
}
$__newDocID = newDocID();
$__newDocIDSTR = $__newDocID;

$_QRY_ISPOL = mysqli_fetch_array(mysqlQuery("SELECT status_ispolout, namezayvfio, showispoloutonly FROM mailbox_sp_users WHERE ID='" . $_SESSION['id'] . "'"));

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

        var _fieldTypes = DataTable.Editor ? DataTable.Editor.fieldTypes : DataTable.ext.editorFields;
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
    // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
    //
    var reqField_showIspolOnly = {
        showIspolOnly: function(response) {}
    };

    function ajaxRequest_saveToDB_showIspolOnly(responseHandler) {
        var _showispolonly = $('#chkOnlyIspolMe').prop('checked');
        var _userid = <?php echo $_SESSION['id']; ?>;
        // Fire off the request_addItem to /form.php
        request_showIspolOnly = $.ajax({
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-saveShowIspolonly.php',
            type: "post",
            cache: false,
            data: {
                userID: _userid,
                showispolonly: _showispolonly
            },
            success: reqField_showIspolOnly[responseHandler]
        });
        console.log('userID: ' + _userid + ' / showispolonly: ' + _showispolonly);
        // Callback handler that will be called on success
        request_showIspolOnly.done(function(request_showIspolOnly, textStatus, jqXHR) {
            res_showIspolOnly = request_showIspolOnly.replace(new RegExp("\\r?\\n", "g"), "");
            console.log("Response showIspolOnly (save): " + res_showIspolOnly);
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
        request_showIspolOnly.always(function() {});
    }
    //
    // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
    //
    function ajaxRequest_loadFromDB_showIspolOnly(responseHandler) {
        var _userid = <?php echo $_SESSION['id']; ?>;
        // Fire off the request_addItem to /form.php
        request_showIspolOnly = $.ajax({
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-getShowIspolonly.php',
            type: "post",
            cache: false,
            data: {
                userID: _userid
            },
            success: reqField_showIspolOnly[responseHandler]
        });
        console.log('userID: ' + _userid);
        // Callback handler that will be called on success
        request_showIspolOnly.done(function(request_showIspolOnly, textStatus, jqXHR) {
            res_showIspolOnly = request_showIspolOnly.replace(new RegExp("\\r?\\n", "g"), "");
            $("#chkOnlyIspolMe").prop('checked', (res_showIspolOnly === '1') ? true : false);
            console.log("Response window.showIspolOnly (load): " + res_showIspolOnly);
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
        request_showIspolOnly.always(function() {});
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
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-uploadAttachedFiles.php',
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
        request_uploadAttachedFiles.always(function() {});
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
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-listAttachedFiles.php',
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
        request_listAttachedFiles.always(function() {});
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
    var reqField_getLastAction = {
        getLastAction: function(response) {}
    };
    var reqField_getLastNotify = {
        getLastNotify: function(response) {}
    };
    var reqField_getLinkedRelFiles = {
        getLinkedRelFiles: function(response) {}
    };
    //
    // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
    //
    //
    // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
    //
    //
    // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
    //
    function ajaxRequest_deleteAttachedFile(rowid, responseHandler) {
        // Fire off the request_addItem to /form.php
        request_deleteAttachedFile = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-deleteAttachedFile.php',
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
        request_deleteAttachedFile.always(function() {});
    }
    //
    // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
    //
    function ajaxRequest_getLastDeadlineMail(koddocmail, userid, responseHandler) {
        // Fire off the request_addItem to /form.php
        request_getLastDeadlineMail = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-getLastDeadlineMail.php',
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
        request_getLastDeadlineMail.always(function() {

        });
    }
    //
    // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
    //
    function ajaxRequest_getLastNotify(action, koddocmail, parameter, responseHandler) {
        // Fire off the request_addItem to /form.php
        request_getLastNotify = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-getLastNotify.php',
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
        request_getLastNotify.always(function() {});
    }
    //
    // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
    //
    function ajaxRequest_getLastAction(koddocmail, parameter, responseHandler) {
        // Fire off the request_addItem to /form.php
        request_getLastAction = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-getLastAction.php',
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
                    case 'outbox_docDateDeadline':
                        $('#lastUpate-docDateDeadline').html(res_timestamp);
                        break;
                    case 'outbox_controlIspolActive':
                        $('#lastUpate-ispolActive').html(res_timestamp);
                        break;
                    case 'outbox_controlIspolMailReminder1':
                        $('#lastUpate-reminder1').html(res_timestamp);
                        break;
                        0
                    case 'outbox_controlIspolMailReminder2':
                        $('#lastUpate-reminder2').html(res_timestamp);
                        break;
                    case 'outbox_controlIspolCheckout':
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
        request_getLastAction.always(function() {});
    }
    //
    // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
    //
    function ajaxRequest_getLinkedRelFiles(koddocmail, responseHandler) {
        // Fire off the request_addItem to /form.php
        request_getLinkedRelFiles = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-getLinkedRelFiles.php',
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
                    $('#wrap-linkedIncoming-showFiles').css("display", "block");
                    for (i = 0; i < x1.length - 1; i++) {
                        console.log('request_getLinkedRelFiles X', i, x1[i])
                        $('#outputArea-linkedIncoming-showFiles').html(x1[i]);
                    }
                }
            } else if (response === "0") {
                $('#wrap-linkedIncoming-showFiles').css("display", "block");
                $('#outputArea-linkedIncoming-showFiles').html(
                    '<span class="text-danger">Нет прикрепленных файлов</span>');
            } else {
                $('#wrap-linkedIncoming-showFiles').css("display", "none");
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
    // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
    //
    var reqField_countDocComments = {
        countDocComments: function(response) {}
    };

    function ajaxRequest_countDocComments(koddocmail, responseHandler) {
        // Fire off the request_addItem to /form.php
        request_countDocComments = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-countDocComments.php',
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
    // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
    //
    var reqField_getUserCheckout = {
        getUserCheckout: function(response) {}
    };

    function ajaxRequest_getUserCheckout(koddocmail, userid, responseHandler) {
        // Fire off the request_addItem to /form.php
        request_getUserCheckout = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-getUserCheckout.php',
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
                        editor_outgoing.field('ispolStatus').val(1);
                    } else {
                        $('#DTE_Field_ispolStatus_0').prop('checked', false);
                        editor_outgoing.field('ispolStatus').val(0);
                    }
                    // Disable чекбокс статуса, если пользователь не в исполнителях
                    if (x1[1].indexOf(userID) !== -1 && editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolActive').val() == "1") {
                        // $('.ispolStatus-visible').css('display', 'table-cell');
                        editor_outgoing.field('ispolStatus').enable();
                        editor_outgoing.field('ispolStatus').labelInfo('<?php echo $ispolStatus_msg_2; ?>');
                        editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolUseDeadline').enable();
                        console.log('CONTROL CHECKPOINT (getUserCheckout) >>> user in ispol', x1[1].indexOf(
                            userID));
                    } else {
                        // $('.ispolStatus-visible').css('display', 'none');
                        $('.ispolStatus-chkText').css('color', '#AAA');
                        // $('#DTE_Field_ispolStatus_0').prop('disabled', true);
                        editor_outgoing.field('ispolStatus').disable();
                        editor_outgoing.field('ispolStatus').labelInfo('<?php echo $ispolStatus_msg_0; ?>');

                        editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailReminder1')
                            .disable();
                        editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailReminder2')
                            .disable();
                        editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailNotifyDL')
                            .disable();
                        $('#ispolStatus_msg_1').html('<?php echo $ispolStatus_msg_1; ?>')
                        console.log('CONTROL CHECKPOINT (getUserCheckout) >>> user not in ispol', x1[1].indexOf(
                            userID));
                    }
                }
            }
            console.log('CONTROL CHECKPOINT (getUserCheckout) >>> ispolStatus is recieved!', response)
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
    // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
    //
    var reqField_getFormFieldHelp = {
        getFormFieldHelp: function(response) {}
    };

    function ajaxRequest_getFormFieldHelp(fieldname, responseHandler) {
        // Fire off the request_addItem to /form.php
        request_getFormFieldHelp = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-getFormFieldHelp.php',
            cache: false,
            data: {
                fieldname: fieldname,
            },
            success: reqField_getFormFieldHelp[responseHandler]
        });
        // Callback handler that will be called on success
        request_getFormFieldHelp.done(function(response, textStatus, jqXHR) {
            console.log('FiledHelp is recieved!', response)
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
            if (response !== "0" && response !== "-1" && response !== "-2") {
                x = response.split("///");
                if (x.length > 0) {
                    console.log('title:', x[0]);
                    console.log('content:', x[1]);


                    // $.dialog({
                    //   bootstrapClasses: {
                    //     container: 'container',
                    //     containerFluid: 'container-fluid',
                    //     row: 'row',
                    //   },
                    //   title: x[0],
                    //   content: x[1],
                    //   draggable: true,
                    //   animateFromElement: false,
                    //   lazyOpen: false,
                    //   bgOpacity: null,
                    //   theme: 'bootstrap',
                    //   animation: 'opacity',
                    //   closeAnimation: 'opacity',
                    //   columnClass: 'col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1',
                    //   useBootstrap: true,
                    // });


                }
            }
        });
        // Callback handler that will be called on failure
        request_getFormFieldHelp.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_getFormFieldHelp.always(function() {});
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
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-getDocTypeLock.php',
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
                    $('select[id="DTE_Field_mailbox_outgoing-outbox_docType"]').prop('disabled', 'disabled');
                } else {
                    $('select[id="DTE_Field_mailbox_outgoing-outbox_docType"]').prop('disabled', false);
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
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    function ajaxRequest_getRelativeIncomingDataAsync(rowid) {
        var result = false;
        $.ajax({
            async: false,
            cache: false,
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxOutgoing-getRelativeIncomingData.php',
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
                console.log('ajaxRequest_getRelativeIncomingDataAsync', result);
            }
        });
        return result;
    }
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    var editor_outgoing;
    var table_outgoing;
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // 
    $(document).ready(function() {
        editor_outgoing = new $.fn.dataTable.Editor({
            display: "bootstrap",
            ajax: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process.php",
            table: "#outbox",
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
            template: '#customForm-mail-main-outbox',
            fields: [{
                    label: "Исх № (АТГС)",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docID",
                    def: function() {
                        newDocID = '<?php echo $__newDocID; ?>';
                        return newDocID;
                    },
                    className: 'block'
                }, {
                    label: "Основное входящее письмо, для которого редактируемое будет ответным ( Дата / № АТГС / № орг / Название орг / Тема )",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowID_rel",
                    type: "select2",
                    placeholder: "Начинайте вводить часть даты, номера письма АТГС или организации, названия компании или описания письма",
                    className: 'block'
                }, {
                    label: "Изменить тип указанного выше входящего документа на 'Запрос ответа' и связать с текущим исходящим документом (входящий запрос - текущий ответ)",
                    type: "checkbox",
                    name: "enbl_inbox_docType_change",
                    unselectedValue: 0,
                    options: {
                        "": 1
                    }
                }, {
                    label: "Отметить указанный входящий документ как исполненный для всех ответственных",
                    type: "checkbox",
                    name: "set_inbox_fullCheckout",
                    unselectedValue: 0,
                    options: {
                        "": 1
                    }
                }, {
                    label: "Дополнительные входящие письма, на которые текущий исходящий документ можно считать ответным",
                    type: "checkbox",
                    name: "enbl_inbox_rowIDadd_rel",
                    unselectedValue: 0,
                    options: {
                        "": 1
                    }
                }, {
                    label: "Дополнительные входящие письма, для которых редактируемое будет ответным ( Дата / Номер / Контрагент / Тема )",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDList_rel",
                    type: "select",
                    placeholder: "Начинайте вводить часть даты, номера, названия компании или описания письма",
                    multiple: false,
                    separator: ',',
                    className: 'block'
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDadd_rel"
                }, {
                    label: "Дата / Номер / Контрагент / Тема",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docIDs_links",
                    type: "select",
                    placeholder: "Начинайте вводить часть даты, номера, названия компании или описания письма",
                    multiple: false,
                    separator: ',',
                    className: 'block'
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_rowIDs_links"
                }, {
                    label: "Дата / Номер / Контрагент / Тема",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_docIDs_links",
                    type: "select",
                    placeholder: "Начинайте вводить часть даты, номера, названия компании или описания письма",
                    multiple: false,
                    separator: ',',
                    className: 'block'
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDs_links"
                }, {
                    label: "Номер / Название",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.dognet_docIDs_links",
                    type: "select",
                    placeholder: 'Начинайте вводить часть номера или названия договора',
                    multiple: false,
                    separator: ',',
                    className: 'block'
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.dognet_rowIDs_links"
                }, {
                    label: "Название",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.sp_docIDs_links",
                    type: "select",
                    placeholder: 'Начинайте вводить часть названия контрагента',
                    multiple: false,
                    separator: ',',
                    className: 'block'
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.sp_rowIDs_links"
                }, {
                    label: "Тип",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docType",
                    type: "select",
                    def: 0,
                    placeholder: 'Тип документа',
                    className: "block"
                }, {
                    label: "Дата",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDate",
                    type: "datetime",
                    def: function() {
                        return new Date();
                    },
                    format: 'DD.MM.YYYY HH:mm:ss',
                    className: "block"
                }, {
                    label: '',
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docAbout",
                    attr: {
                        placeholder: 'Краткое описание документа'
                    },
                    className: "block"
                }, {
                    label: "Организация / Получатель (Справочник)",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz",
                    type: "select2",
                    placeholder: '---',
                    className: 'block'
                }, {
                    label: "Организация",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient",
                    className: 'block'
                }, {
                    label: "Адресант",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipientName",
                    className: 'block'
                }, {
                    label: "Ввести вручную",
                    type: "checkbox",
                    name: "enblRecipientManual",
                    unselectedValue: 0,
                    options: {
                        "": 1
                    }
                }, {
                    label: "Подписант",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSender_kodzayvtel",
                    type: "select",
                    placeholder: "Подписант документа",
                    className: "block"
                }, {
                    label: "№ вх (получ)",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSourceID",
                    attr: {
                        placeholder: 'Вх № орг'
                    },
                    className: 'block'
                }, {
                    label: "Дата вх (получ)",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSourceDate",
                    type: "datetime",
                    def: function() {
                        return new Date();
                    },
                    format: 'DD.MM.YYYY',
                    className: 'block'
                }, {
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileID",
                    type: "upload",
                    display: function(id) {
                        var koddocmail = table_outgoing.file(
                                '<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>', id)
                            .koddocmail;
                        var filewebpath = '<?php echo __ROOT; ?>' + table_outgoing.file(
                            '<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>', id).file_webpath;
                        var filename = table_outgoing.file(
                                '<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>', id)
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
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.msgDocFileID",
                    attr: {
                        disabled: 'disabled'
                    },
                    className: 'block'
                }, {
                    type: "readonly",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.lnkDocFileID"
                }, {
                    label: "Уведомить испонилнителя по email о назначении",
                    type: "checkbox",
                    name: "toSendEmail",
                    unselectedValue: 0,
                    options: {
                        "": 1
                    }
                }, {
                    label: "Исполнитель",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docContractor_kodispolout",
                    type: "select",
                    def: "---",
                    placeholder: "Выберите исполнителя",
                    className: "block"
                }, {
                    label: "Первичный комментарий исполнителя",
                    type: "textarea",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docContractorComment",
                    def: "",
                    attr: {
                        placeholder: 'Первичный комментарий исполнителя'
                    },
                    className: "block"
                }, {
                    label: "Дополнительная информация",
                    type: "textarea",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docComment",
                    def: "",
                    attr: {
                        placeholder: 'Общий комментарий к документу'
                    }
                }, {
                    label: "",
                    type: "checkbox",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docType_lock",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail"
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileIDadd",
                    def: null
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileIDtmp"
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_tmp1"
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_tmp2"
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_tmp3"
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.toSendEmail"
                }, {
                    label: "Режим контроля исполнения (КИ)",
                    type: "checkbox",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolActive",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }, {
                    label: "",
                    type: "checkbox",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolCheckout",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }, {
                    label: "",
                    type: "textarea",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolCheckoutComment",
                    def: "",
                    attr: {
                        placeholder: 'Текст, который вы тут введете, после сохранения формы будет добавлен к общему списку (чату) с комментариями по документу. В этом поле он не сохранится.'
                    },
                    className: "block"
                }, {
                    label: "Дедлайн",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDateDeadline",
                    type: "datetime",
                    format: 'DD.MM.YYYY',
                    def: function() {
                        return moment();
                    }
                }, {
                    label: "Напоминание исполнителю(ям) #1 (когда до дедлайна остается менее 3-х дней )",
                    type: "checkbox",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailReminder1",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }, {
                    label: "Напоминание исполнителю(ям) #2 (когда до дедлайна остается менее 1-го дня )",
                    type: "checkbox",
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailReminder2",
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
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailNotifyDL",
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
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailNotifyCheckout",
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
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailSpecialNotifyCheckout",
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
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailSpecialNotifyDL",
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
                    name: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolUseDeadline",
                    options: [{
                        label: "",
                        value: 1
                    }],
                    separator: '',
                    unselectedValue: 0
                }
            ]
        });
        //
        // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
        //
        // Управление размером диалогового окна редактирования заявки
        editor_outgoing.on('open', function() {
            $(".modal-dialog").css({
                "width": "65%",
                "min-width": "850px",
                "max-width": "1024px"
            });
            //
            $('#uploadFiles-result > table').html('');
            $('#listFiles-result').html('');
            // - - - - - + - - - - - + - - - - -
            //
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
                    formData.append('rowID', editor_outgoing.ids());
                    formData.append('docID', editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docID').val());
                    formData.append('koddocmail', editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail').val());
                    for (var pair of formData.entries()) {
                        console.log(pair[0] + ', ' + pair[1]);
                    }
                    ajaxRequest_uploadAttachedFiles(formData, 'uploadAttachedFiles');
                }
            });
            // - - - - - + - - - - - + - - - - -
            // #listFiles-result > p:nth-child(4) > button
            //

            outbox_rowIDadd_rel = editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDadd_rel')
                .val();
            if (outbox_rowIDadd_rel !== "" && outbox_rowIDadd_rel !== null) {
                console.log("dependent outbox_rowIDadd_rel !==:", outbox_rowIDadd_rel)
                $(".rowIDadd-enbl").css('display', 'block');
                $('input[id="DTE_Field_enbl_inbox_rowIDadd_rel_0"]').prop('checked', true)
                $('#inbox-rowIDadd-rel-alert').css('display', 'block');
                editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDadd_rel')
                    .enable();
                editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDList_rel')
                    .enable();
            } else {
                console.log("dependent inbox_rowIDadd_rel else:", outbox_rowIDadd_rel)
                $(".rowIDadd-enbl").css('display', 'none');
                $('input[id="DTE_Field_enbl_inbox_rowIDadd_rel_0"]').prop('checked', false)
                editor_outgoing.field('enbl_inbox_rowIDadd_rel').val(0);
                $('#inbox-rowIDadd-rel-alert').css('display', 'none');
            }
            // 
            // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
            // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
            // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
            // 
            if ($('#popoverCheckboxEnable').prop('checked')) {
                $.getJSON(
                        "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/fields-help-data.json"
                    )
                    .done(function(json) {
                        console.log("----- getJSON >>>>>");
                        let placement = 'top';
                        for (var key1 in json.data) {
                            if (key1 == "toSendEmail") {
                                editor_outgoing.field(key1).label(json.data[
                                    key1]['label2']);
                            } else if (key1 == "ispolStatus") {
                                editor_outgoing.field(key1).label(json.data[
                                    key1]['label2']);
                            } else if (key1 == "enblRecipientManual") {
                                editor_outgoing.field(key1).label(json.data[
                                    key1]['label2']);
                            } else if (key1 == "enbl_inbox_docType_change") {
                                editor_outgoing.field(key1).label(json.data[
                                    key1]['label2']);
                            } else {
                                editor_outgoing.field(
                                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.' + key1)
                                    .label(json.data[
                                        key1]['label2']);
                            }
                            for (var key2 in json.data[key1]) {
                                console.log(key1, key2, json.data[key1][key2]);
                            }
                            console.log(json.data[key1]['id']);

                            var $content = json.data[key1]['popoverContent'].length > 1450 ?
                                json.data[key1][
                                    'popoverContent'
                                ].substring(0,
                                    1450) +
                                " ... <?php echo $popoverLinkToKnow; ?>" :
                                json.data[key1][
                                    'popoverContent'
                                ];

                            if (key1 == "outbox_docType") {
                                placement = 'left';
                            }

                            $('label[for="' + json.data[key1]['id'] +
                                    '"] span[fieldName="popoverContent-' + key1 + '"] sup.popoverElemet'
                                )
                                .popover({
                                    html: true,
                                    trigger: 'hover',
                                    placement: placement,
                                    // title: json.data[key1]['popoverTitle'],
                                    content: $content,
                                    customClass: 'mail-popover',
                                });
                        }
                        console.log("<<<<< getJSON -----");

                    })
                    .fail(function(jqxhr, textStatus, error) {
                        var err = textStatus + ", " + error;
                        console.log("Request Failed: " + err);
                    });
            } else {
                $.getJSON(
                        "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/fields-help-data.json"
                    )
                    .done(function(json) {
                        console.log("----- getJSON >>>>>");
                        for (var key1 in json.data) {
                            if (key1 == "toSendEmail") {
                                editor_outgoing.field(key1).label(json.data[
                                    key1]['label1']);
                            } else if (key1 == "ispolStatus") {
                                editor_outgoing.field(key1).label(json.data[
                                    key1]['label1']);
                            } else if (key1 == "enblRecipientManual") {
                                editor_outgoing.field(key1).label(json.data[
                                    key1]['label1']);
                            } else if (key1 == "enbl_inbox_docType_change") {
                                editor_outgoing.field(key1).label(json.data[
                                    key1]['label1']);
                            } else {
                                editor_outgoing.field(
                                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.' + key1)
                                    .label(json.data[
                                        key1]['label1']);
                            }

                        }
                        console.log("<<<<< getJSON -----");

                    })
                    .fail(function(jqxhr, textStatus, error) {
                        var err = textStatus + ", " + error;
                        console.log("Request Failed: " + err);
                    });
            }
        });
        editor_outgoing.off('close', function() {
            $(".modal-dialog").css({
                "width": "80%",
                "min-width": "none",
                "max-width": "none"
            });
        });
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
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

        editor_outgoing.on('initCreate', function(e) {
            editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.msgDocFileID').show(false);
            //editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.lnkDocFileID').hide(false);
            editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.msgDocFileID').val(
                'Сначала создайте запись!');
            editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileID').hide();
            editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileID').disable();
            editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docID').disable();
            editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDate').disable();
            window.txtlink = '';
            window.initCreate = 1;
            console.log('window.initCreate', window.initCreate);
        });
        //
        //
        editor_outgoing.on('initEdit', function(e, node, data, items, type) {
            console.log('outbox_docFileID', data.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileID);
            editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileIDtmp').val(data
                .<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                .outbox_docFileID);
            editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docID').disable();
            editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDate').disable();
            ajaxRequest_listAttachedFiles(editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail')
                .val(),
                'listAttachedFiles');

            window.initCreate = 0;
            if (data.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileID == "" || data
                .<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                .outbox_docFileID ==
                null) {
                editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.msgDocFileID').hide(false);
                //editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.lnkDocFileID').hide(false);
                editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileID').show(
                    false);
                editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileID').enable();
                window.txtlink = '';
            } else {
                editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.msgDocFileID').hide(false);
                //editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.lnkDocFileID').show(false);
                //editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.lnkDocFileID').val('Если вы хотите обновить прикрепленный файл, создайте новую запись и удалите старую!');
                //editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileID').hide(false);
                //editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileID').disable();
                if (window.initCreate != 1) {
                    // filelink = "http://<?php echo $_SERVER['HTTP_HOST']; ?>/mail" + data
                    //     .<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>.file_webpath + "";
                    //window.txtlink = '<a target="_blank" href="'+filelink+'">Текущий прикрепленный файл</a>';
                }
            }
            //
            if (editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.toSendEmail').val() ===
                "1") {
                console.log('toSendEmail checked');
                $('#DTE_Field_toSendEmail_0').prop('checked', false);
            } else {
                console.log('toSendEmail !?');
            }
            var koddocmail = data.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail;
            var userid = <?php echo $_SESSION['id']; ?>;
            ajaxRequest_getLastDeadlineMail(koddocmail, userid, 'getLastDeadlineMail');
            ajaxRequest_getUserCheckout(koddocmail, userid, 'getUserCheckout');
            ajaxRequest_getDocTypeLock(koddocmail, 'getDocTypeLock');

        });
        //
        //
        editor_outgoing.on('preOpen', function(e, mode, action) {
            $.getJSON(
                    "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/fields-help-data.json"
                )
                .done(function(json) {
                    console.log("----- getJSON >>>>>");
                    for (var key1 in json.data) {
                        if (key1 == "toSendEmail") {
                            editor_outgoing.field(key1).label(json.data[
                                key1]['label1']);
                        } else if (key1 == "ispolStatus") {
                            editor_outgoing.field(key1).label(json.data[
                                key1]['label1']);
                        } else if (key1 == "enblRecipientManual") {
                            editor_outgoing.field(key1).label(json.data[
                                key1]['label1']);
                        } else if (key1 == "enbl_inbox_docType_change") {
                            editor_outgoing.field(key1).label(json.data[
                                key1]['label1']);
                        } else {
                            editor_outgoing.field(
                                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.' + key1)
                                .label(json.data[
                                    key1]['label1']);
                        }
                    }
                    console.log("<<<<< getJSON -----");

                })
                .fail(function(jqxhr, textStatus, error) {
                    var err = textStatus + ", " + error;
                    console.log("Request Failed: " + err);
                });
            if (action === "create") {
                var senderKod = "<?php echo $_QRY_UserSettings['outgoing_kodSenderDefault']; ?>";
                var orgKod = "<?php echo $_QRY_UserSettings['outgoing_kodRecipientOrgDefault']; ?>";
                var ispolKod = "<?php echo $_QRY_UserSettings['outgoing_kodIspolDefault']; ?>";
                var textAbout = "<?php echo $_QRY_UserSettings['outgoing_textAboutDefault']; ?>";
                senderKod = checkVal(senderKod) ? senderKod : '000000000000000';
                orgKod = checkVal(orgKod) ? orgKod : null;
                ispolKod = checkVal(ispolKod) ? ispolKod : '000000000000000';
                textAbout = checkVal(textAbout) ? textAbout : '';
                editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSender_kodzayvtel').val(
                    senderKod);
                editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz').val(orgKod);
                editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docContractor_kodispolout').val(
                    ispolKod);
                editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docAbout').val(textAbout);
                //
                // ----- ----- ----- ----- ----- 
                //
                let setControlIspolOnStart = '<?php echo $_SESSION['outgoing_setControlIspolOnStart']; ?>';
                editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolActive')
                    .val(setControlIspolOnStart);
                if (setControlIspolOnStart == '1') {
                    $('input[id="DTE_Field_mailbox_outgoing-outbox_controlIspolActive_0"]').prop('checked',
                        true);
                } else {
                    $('input[id="DTE_Field_mailbox_outgoing-outbox_controlIspolActive_0"]').prop('checked',
                        false);
                }
                //
                let setDeadlineOnStart = '<?php echo $_SESSION['outgoing_setDeadlineOnStart']; ?>';
                editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolUseDeadline').val(
                    setDeadlineOnStart);
                if (setDeadlineOnStart == '1') {
                    $('input[id="DTE_Field_mailbox_outgoing-outbox_controlIspolUseDeadline_0"]').prop(
                        'checked', true);
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDateDeadline')
                        .val(moment().add(<?php echo $deadline_default_days; ?>, 'days').format(
                            "DD.MM.YYYY"));
                } else {
                    $('input[id="DTE_Field_mailbox_outgoing-outbox_controlIspolUseDeadline_0"]').prop(
                        'checked', false);
                }
            }
        });
        //
        //
        var openVals;
        editor_outgoing.on('open', function(e, mode, action) {
                console.log('editor_outgoing.on("open")', 'triggered');

                // if (editor_outgoing.field(
                //         '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolUseDeadline').val() ==
                //     '0') {
                //     editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDateDeadline')
                //         .val('');
                // }

                $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_rowID_rel').select2({
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


                $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_rowIDList_rel').select2({
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
                var inbox_rowIDadd_rel = editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDadd_rel').val();
                if (inbox_rowIDadd_rel !== "" && inbox_rowIDadd_rel !== null) {
                    x3 = inbox_rowIDadd_rel.split(",");
                    x3.forEach(function(val) {
                        selVal = val;
                        selText = $(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_rowIDList_rel option[value=' +
                            selVal + ']').text();
                        option = '<option value="' + selVal + '">' + selText + '</option>';
                        console.log(option);
                        $('#linked-mail-listDocs').append(option);
                    });
                }

                $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-outbox_docIDs_links').select2({
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
                var outbox_rowIDs_links = editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_rowIDs_links')
                    .val();
                if (outbox_rowIDs_links !== "" && outbox_rowIDs_links !== null) {
                    x1 = outbox_rowIDs_links.split(",");
                    x1.forEach(function(val) {
                        selVal = val;
                        selText = $(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-outbox_docIDs_links option[value=' +
                            selVal + ']').text();
                        option = '<option value="' + selVal + '">' + selText + '</option>';
                        console.log(option);
                        $('#linked-mail-outgoingDocs').append(option);
                    });
                }
                // 
                // 
                //
                $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_docIDs_links').select2({
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
                var inbox_rowIDs_links = editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDs_links')
                    .val();
                if (inbox_rowIDs_links !== "" && inbox_rowIDs_links !== null) {
                    x1 = inbox_rowIDs_links.split(",");
                    x1.forEach(function(val) {
                        selVal = val;
                        selText = $(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_docIDs_links option[value=' +
                            selVal + ']').text();
                        option = '<option value="' + selVal + '">' + selText + '</option>';
                        console.log(option);
                        $('#linked-mail-incomingDocs').append(option);
                    });
                }
                // 
                // 
                //
                $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-dognet_docIDs_links').select2({
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
                var dognet_rowIDs_links = editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.dognet_rowIDs_links')
                    .val();
                if (dognet_rowIDs_links !== "" && dognet_rowIDs_links !== null) {
                    x2 = dognet_rowIDs_links.split(",");
                    x2.forEach(function(val) {
                        selVal = val;
                        selText = $(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-dognet_docIDs_links option[value=' +
                            selVal + ']').text();
                        option = '<option value="' + selVal + '">' + selText + '</option>';
                        console.log(option);
                        $('#linked-dognet-docs').append(option);
                    });
                }
                // 
                // 
                //
                $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-sp_docIDs_links').select2({
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
                var sp_rowIDs_links = editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.sp_rowIDs_links')
                    .val();
                if (sp_rowIDs_links !== "" && sp_rowIDs_links !== null) {
                    x2 = sp_rowIDs_links.split(",");
                    x2.forEach(function(val) {
                        selVal = val;
                        selText = $(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-sp_docIDs_links option[value=' +
                            selVal + ']').text();
                        option = '<option value="' + selVal + '">' + selText + '</option>';
                        console.log(option);
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

                editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailSpecialNotifyCheckout')
                    .disable();
                editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailSpecialNotifyDL')
                    .disable();

                // Store the values of the fields on open
                openVals = JSON.stringify(editor_outgoing.get());
                $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-outbox_docRecipient_kodzakaz').select2({
                    allowClear: true,
                    placeholder: 'Выберите организацию используя поиск (внутри списка)',
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
                        }
                    }
                });

                // ----- ----- ----- ----- -----

                // editor_outgoing.on('preClose', function(e) {
                //     // On close, check if the values have changed and ask for closing confirmation if they have
                //     if (openVals !== JSON.stringify(editor_outgoing.get())) {
                //         return confirm(
                //             'Уверены, что хотите завершить редактирование?'
                //         );
                //     }
                // })
                // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
                // >>> BLOCK BEGIN
                // БЛОК ОБРАБОТКИ ОШИБОК ЗАПОЛНЕНИЯ ФОРМЫ
                // Редакция от 20221120
                // 
                editor_outgoing.on('preSubmit', function(e, data, action) {
                    var errmsg_1, errmsg_2, errmsg_3, errmsh_4, errmsg_5, errmsg_6 = false;
                    if (action !== "remove") {
                        //
                        // Вкладка TAB-1
                        if (editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSender_kodzayvtel')
                            .val() == '') {
                            $('div.editorform li.nav-item.tab1>a.nav-link').addClass("errmsg");
                            editor_outgoing.field(
                                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSender_kodzayvtel'
                                )
                                .error('Выберите отправителя документа');
                            // e.preventDefault;
                            errmsg_1 = true;
                        } else if (editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docAbout')
                            .val() == '') {
                            $('div.editorform li.nav-item.tab1>a.nav-link').addClass("errmsg");
                            editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docAbout').error(
                                'Краткое описание документа обязательно');
                            // e.preventDefault;
                            errmsg_1 = true;
                        } else {
                            $('div.editorform li.nav-item.tab1>a.nav-link').removeClass("errmsg");
                            errmsg_1 = false;
                        }
                        //
                        // Вкладка TAB-3
                        if (editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docContractor_kodispolout'
                            ).val() == '') {
                            $('div.editorform li.nav-item.tab3>a.nav-link').addClass("errmsg");
                            editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docContractor_kodispolout'
                            ).error(
                                'Выберите исполнителя либо выберите "---"');
                            // e.preventDefault;
                            errmsg_3 = true;
                        } else {
                            $('div.editorform li.nav-item.tab3>a.nav-link').removeClass("errmsg");
                            errmsg_3 = false;
                        }
                        //
                        // Вкладка TAB-4
                        if ($('#doc-editor-tab-4 span.select2-selection__rendered').text() === '' || $(
                                '#doc-editor-tab-4 span.select2-selection__rendered').text() ===
                            'Выберите организацию используя поиск (внутри списка)') {
                            $('div.editorform li.nav-item.tab4>a.nav-link').addClass("errmsg");
                            editor_outgoing.field(
                                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz'
                                )
                                .error('Выберите получателя письма');
                            // e.preventDefault;
                            errmsg_4 = true;
                        } else {
                            $('div.editorform li.nav-item.tab4>a.nav-link').removeClass("errmsg");
                            errmsg_4 = false;
                        }
                        //
                        if (errmsg_1 || errmsg_2 || errmsg_3 || errmsg_4 || errmsg_5 || errmsg_6) {
                            console.log('errmsg_1', errmsg_1, 'errmsg_3', errmsg_3, 'errmsg_4',
                                errmsg_4, 'errmsg_6', errmsg_6);
                            // e.preventDefault;
                            return false;
                        } else {
                            return true;
                        }
                    }
                })
                // БЛОК ОБРАБОТКИ ОШИБОК ЗАПОЛНЕНИЯ ФОРМЫ
                // >>> BLOCK END
                // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
                $('#mailbox-rel-links-btnAdd').click(function() {
                    if (editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDList_rel').val() !==
                        "") {
                        selText = $(
                                '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_rowIDList_rel option:selected'
                            )
                            .text();
                        selValue = $(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_rowIDList_rel option:selected'
                        ).val();
                        $('#linked-mail-listDocs').append(
                            '<option value="' + selValue +
                            '" style="color:#999; font-style:italic">' + selText +
                            '</option>');
                        var selMulti = $.map($(
                            "#linked-mail-listDocs option"
                        ), function(el, i) {
                            return $(el).val();
                        });
                        var selMultiStr = selMulti.join(",");
                        editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDadd_rel').val(
                            selMultiStr);
                        $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_rowIDList_rel')
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
                        console.log('selMulti:', selMulti);
                        var selMultiStr = selMulti.join(",");
                        editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDadd_rel').val(
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
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_docIDs_links option:selected'
                        )
                        .val() !== "") {
                        selText = $(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_docIDs_links option:selected'
                        ).text();
                        selValue = $(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_docIDs_links option:selected'
                        ).val();
                        $('#linked-mail-incomingDocs').append(
                            '<option value="' + selValue + '" style="color:green;">' + selText +
                            '</option>');
                        var selMulti = $.map($(
                            "#linked-mail-incomingDocs option"
                        ), function(el, i) {
                            return $(el).val();
                        });
                        var selMultiStr = selMulti.join(",");
                        editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDs_links').val(
                            selMultiStr);
                        $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_docIDs_links')
                            .val(null).trigger(
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
                    console.log('selMulti:', selMulti);
                    var selMultiStr = selMulti.join(",");
                    editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDs_links')
                        .val(
                            selMultiStr);

                    console.log('field inbox_rowIDs_links:', editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDs_links').val());
                });
                //
                // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
                //
                $('#outbox-doc-links-btnAdd').click(function() {
                    if ($(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-outbox_docIDs_links option:selected'
                        )
                        .val() !== "") {
                        selText = $(
                                '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-outbox_docIDs_links option:selected'
                            )
                            .text();
                        selValue = $(
                                '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-outbox_docIDs_links option:selected'
                            )
                            .val();
                        $('#linked-mail-outgoingDocs').append(
                            '<option value="' + selValue + '" style="color:green;">' + selText +
                            '</option>');
                        var selMulti = $.map($(
                            "#linked-mail-outgoingDocs option"
                        ), function(el, i) {
                            return $(el).val();
                        });
                        console.log('selMulti:', selMulti);
                        var selMultiStr = selMulti.join(",");
                        editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_rowIDs_links').val(
                            selMultiStr);
                        $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-outbox_docIDs_links')
                            .val(null).trigger(
                                "change");
                    }
                });
                //
                $('#outbox-doc-links-btnRemove').click(function() {
                    $('#linked-mail-outgoingDocs option:selected').remove();
                    var selMulti = $.map($("#linked-mail-outgoingDocs option"), function(el, i) {
                        return $(el).val();
                    });
                    var selMultiStr = selMulti.join(",");
                    editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_rowIDs_links').val(
                        selMultiStr);
                });
                //
                // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
                //
                $('#dognet-doc-links-btnAdd').click(function() {
                    if ($(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-dognet_docIDs_links option:selected'
                        )
                        .val() !== "") {
                        selText = $(
                                '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-dognet_docIDs_links option:selected'
                            )
                            .text();
                        selValue = $(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-dognet_docIDs_links option:selected'
                        ).val();
                        $('#linked-dognet-docs').append(
                            '<option value="' + selValue + '" style="color:green;">' + selText +
                            '</option>');
                        var selMulti = $.map($(
                            "#linked-dognet-docs option"
                        ), function(el, i) {
                            return $(el).val();
                        });
                        var selMultiStr = selMulti.join(",");
                        editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.dognet_rowIDs_links').val(
                            selMultiStr);
                        $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-dognet_docIDs_links')
                            .val(null).trigger(
                                "change");
                    }
                });
                $('#dognet-doc-links-btnRemove').click(function() {
                    $('#linked-dognet-docs option:selected').remove();
                    var selMulti = $.map($(
                        "#linked-dognet-docs option"
                    ), function(el, i) {
                        return $(el).val();
                    });
                    var selMultiStr = selMulti.join(",");
                    editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.dognet_rowIDs_links').val(
                        selMultiStr);
                });
                //
                // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
                //
                $('#sp-doc-links-btnAdd').click(function() {
                    if ($(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-sp_docIDs_links option:selected'
                        )
                        .val() !== "") {
                        selText = $(
                                '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-sp_docIDs_links option:selected'
                            )
                            .text();
                        selValue = $(
                            '#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-sp_docIDs_links option:selected'
                        ).val();
                        $('#linked-sp-docs').append(
                            '<option value="' + selValue + '" class="text-dark"">' + selText +
                            '</option>');
                        var selMulti = $.map($(
                            "#linked-sp-docs option"
                        ), function(el, i) {
                            return $(el).val();
                        });
                        var selMultiStr = selMulti.join(",");
                        editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.sp_rowIDs_links').val(
                            selMultiStr);
                        $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-sp_docIDs_links')
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
                    console.log('selMulti:', selMulti);
                    var selMultiStr = selMulti.join(",");
                    editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.sp_rowIDs_links').val(
                        selMultiStr);
                });
                //
                // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
                //
                $('#popoverCheckboxEnable').click(function() {
                    if ($(this).is(':checked')) {
                        $.getJSON(
                                "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/fields-help-data.json"
                            )
                            .done(function(json) {
                                console.log("----- getJSON >>>>>");
                                let placement = 'top';
                                for (var key1 in json.data) {
                                    if (key1 == "toSendEmail") {
                                        editor_outgoing.field(key1).label(json.data[
                                            key1]['label2']);
                                    } else if (key1 == "ispolStatus") {
                                        editor_outgoing.field(key1).label(json.data[
                                            key1]['label2']);
                                    } else if (key1 == "enblRecipientManual") {
                                        editor_outgoing.field(key1).label(json.data[
                                            key1]['label2']);
                                    } else if (key1 == "enbl_inbox_docType_change") {
                                        editor_outgoing.field(key1).label(json.data[
                                            key1]['label2']);
                                    } else {
                                        editor_outgoing.field(
                                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.' + key1)
                                            .label(json.data[
                                                key1]['label2']);
                                    }
                                    for (var key2 in json.data[key1]) {
                                        console.log(key1, key2, json.data[key1][key2]);
                                    }
                                    console.log(json.data[key1]['id']);

                                    var $content = json.data[key1]['popoverContent'].length > 1450 ?
                                        json.data[key1][
                                            'popoverContent'
                                        ].substring(0,
                                            1450) +
                                        " ... <?php echo $popoverLinkToKnow; ?>" :
                                        json.data[key1][
                                            'popoverContent'
                                        ];

                                    if (key1 == "outbox_docType") {
                                        placement = 'left';
                                    }

                                    $('label[for="' + json.data[key1]['id'] +
                                            '"] span[fieldName="popoverContent-' + key1 +
                                            '"] sup.popoverElemet')
                                        .popover({
                                            html: true,
                                            trigger: 'hover',
                                            placement: placement,
                                            // title: json.data[key1]['popoverTitle'],
                                            content: $content,
                                            customClass: 'mail-popover',
                                        });
                                }
                                console.log("<<<<< getJSON -----");

                            })
                            .fail(function(jqxhr, textStatus, error) {
                                var err = textStatus + ", " + error;
                                console.log("Request Failed: " + err);
                            });
                    } else {
                        $.getJSON(
                                "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/fields-help-data.json"
                            )
                            .done(function(json) {
                                console.log("----- getJSON >>>>>");
                                for (var key1 in json.data) {
                                    if (key1 == "toSendEmail") {
                                        editor_outgoing.field(key1).label(json.data[
                                            key1]['label1']);
                                    } else if (key1 == "ispolStatus") {
                                        editor_outgoing.field(key1).label(json.data[
                                            key1]['label1']);
                                    } else if (key1 == "enblRecipientManual") {
                                        editor_outgoing.field(key1).label(json.data[
                                            key1]['label1']);
                                    } else if (key1 == "enbl_inbox_docType_change") {
                                        editor_outgoing.field(key1).label(json.data[
                                            key1]['label1']);
                                    } else {
                                        editor_outgoing.field(
                                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.' + key1)
                                            .label(json.data[
                                                key1]['label1']);
                                    }
                                }
                                console.log("<<<<< getJSON -----");

                            })
                            .fail(function(jqxhr, textStatus, error) {
                                var err = textStatus + ", " + error;
                                console.log("Request Failed: " + err);
                            });
                    }
                });
                console.log("CHECK >>> .on(open) outbox_controlIspolActive", editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolActive').val());
                if (editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolActive')
                    .val() == "1") {
                    editor_outgoing.field('ispolStatus').enable();
                    editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolUseDeadline').enable();
                } else {
                    editor_outgoing.field('ispolStatus').disable();
                    editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolUseDeadline').disable();
                }

                console.log("CHECK >>> .on(open) ispolStatus", $('input[id="DTE_Field_ispolStatus_0"]').val());
                if ($('input[id="DTE_Field_ispolStatus_0"]').val() == "1") {
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailNotifyCheckout')
                        .enable();
                } else {
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailNotifyCheckout')
                        .disable();
                }
            })
            //
            // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
            //
            .on('preSubmit', function() {
                $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-inbox_docIDs_links').val(null).trigger(
                    "change");
                $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-outbox_docIDs_links').val(null).trigger(
                    "change");
                $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-dognet_docIDs_links').val(null).trigger(
                    "change");
                $('#DTE_Field_<?php echo __MAIL_OUTGOING_TABLENAME; ?>-sp_docIDs_links').val(null)
                    .trigger(
                        "change");
            })
            //
            // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
            //
            .on('postCreate postEdit close preBlur', function() {
                editor_outgoing.off('preClose');
                // Обновление страницы
                location.reload();
                // table_outgoing.ajax.reload(null, false);
                // document.getElementById("doc-editor-menu-tab-1-errmsg").innerHTML = '';
                // document.getElementById("doc-editor-menu-tab-2-errmsg").innerHTML = '';
                // document.getElementById("doc-editor-menu-tab-3-errmsg").innerHTML = '';
                // document.getElementById("doc-editor-menu-tab-4-errmsg").innerHTML = '';
                // document.getElementById("doc-editor-menu-tab-5-errmsg").innerHTML = '';
            })
            //
            // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
            //
            .on('submitError', function(e, xhr, err, thrown, data) {
                if ((thrown == "SyntaxError: Unexpected number in JSON at position 4" || thrown ==
                        "SyntaxError: Unexpected token < in JSON at position 0" ||
                        "SyntaxError: Unexpected non - whitespace character after JSON at position 4") &&
                    err ==
                    "parsererror") {
                    editor_outgoing.off('preClose');
                    editor_outgoing.close();
                }
                console.log("err: " + err);
                console.log("thrown: " + thrown);
                console.log("xhr: " + JSON.stringify(xhr));
            });
        //
        //
        editor_outgoing.dependent('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowID_rel',
            function(val, data, callback, e) {
                if (val !== "" && val !== null) {
                    $('div.rowID-rel-only').css('display', 'block');
                    console.log('editor_outgoing - mailbox_outgoing.inbox_rowID_rel', val);
                    var relVal = ajaxRequest_getRelativeIncomingDataAsync(val);
                    if (relVal[2] !== '') {
                        editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz').val(
                            relVal[2]);
                    } else {
                        editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz').val('');
                    }
                    if (relVal[5] === '1' && relVal[4] !== '1' && relVal[3] === '1') {
                        $('div.inbox-setCheckout').css('display', 'block');
                        $('input#DTE_Field_set_inbox_fullCheckout_change_0').prop('checked', false);
                        $('div.inbox-setCheckout-alert').css('display', 'block');
                    } else {
                        $('div.inbox-setCheckout').css('display', 'none');
                        $('input#DTE_Field_set_inbox_fullCheckout_change_0').prop('checked', false);
                        $('div.inbox-setCheckout-alert').css('display', 'none');
                    }
                } else {
                    $('div.rowID-rel-only').css('display', 'none');
                    $('input#DTE_Field_enbl_inbox_docType_change_0').prop('checked', false);
                    $('div.rowID-rel-only-alert').css('display', 'none');

                    $('div.inbox-setCheckout').css('display', 'none');
                    $('input#DTE_Field_set_inbox_fullCheckout_change_0').prop('checked', false);
                    $('div.inbox-setCheckout-alert').css('display', 'none');
                }
                callback(true);
            }
        );
        //
        //
        editor_outgoing.dependent('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolUseDeadline',
            function(val, data, callback, e) {
                console.log("editor_outgoing.dependent('outbox_controlIspolUseDeadline')", val);
                if (val == "1") {
                    editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDateDeadline')
                        .enable();
                    var datedeadline = editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDateDeadline').val();
                    if (checkVal(datedeadline)) {
                        editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDateDeadline')
                            .val(moment(datedeadline, "DD.MM.YYYY").format("DD.MM.YYYY"));
                    } else {
                        editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDateDeadline')
                            .val(moment().add(<?php echo $deadline_default_days; ?>, 'days').format(
                                "DD.MM.YYYY"));
                    }
                    var ispols = String(editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docContractor_kodispolout')
                        .val());
                    var userid = String(<?php echo $_SESSION['mail_user_kodispol']; ?>);
                    console.log("CHECKPOINT 2471 >>>", ispols, userid, checkArrOnString(ispols, userid));
                    if (checkArrOnString(ispols, userid) != -1) {
                        editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailReminder1')
                            .enable();
                        editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailReminder2')
                            .enable();
                        editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailNotifyDL')
                            .enable();
                    }
                } else {
                    editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDateDeadline')
                        .disable();
                    editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDateDeadline')
                        .val(null);
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailReminder1')
                        .disable();
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailReminder1')
                        .val(0);
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailReminder2')
                        .disable();
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailReminder2')
                        .val(0);
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailNotifyDL')
                        .disable();
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailNotifyDL')
                        .val(0);
                }
                callback(true);
            }
        );
        //
        //
        editor_outgoing.dependent('ispolStatus',
            function(val, data, callback, e) {
                console.log("CHECK >>> dependent ispolStatus", $('input[id="DTE_Field_ispolStatus_0"]').is(
                    ':checked'), val);
                if ($('input[id="DTE_Field_ispolStatus_0"]').is(':checked')) {
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailNotifyCheckout'
                        )
                        .enable();
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolCheckoutComment')
                        .show();
                } else {
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailNotifyCheckout'
                        )
                        .disable();
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolMailNotifyCheckout'
                        )
                        .val(0);
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolCheckoutComment')
                        .hide();
                }
                callback(true);
            }
        );
        //
        //
        editor_outgoing.dependent('enblRecipientManual', function(val, data, callback, e) {
            if (val == 1) {
                editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient')
                    .enable();
                editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipientName')
                    .enable();
                editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_tmp1').set(
                    editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz')
                    .get());
                editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_tmp2').set(
                    editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient').get());
                editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_tmp3').set(
                    editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipientName').get());
                editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz').set(
                    "000000000000000");
                editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz')
                    .disable();
                editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz').hide(
                    false);
                // $("#recipient_filter").val(null);
                // $("#recipient_filter").disable;
                $(".sDS1").hide();
            } else {
                if ((editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_tmp1')
                        .get()) !=
                    '') {
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz')
                        .set(
                            editor_outgoing.field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_tmp1')
                            .get());
                }
                if ((editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_tmp2')
                        .get()) !=
                    '') {
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient')
                        .set(
                            editor_outgoing
                            .field(
                                '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_tmp2').get());
                }
                if ((editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_tmp3')
                        .get()) !=
                    '') {
                    editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipientName').set(
                        editor_outgoing
                        .field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_tmp3').get());
                }

                editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient')
                    .disable();
                editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipientName')
                    .disable();
                editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz')
                    .enable();
                editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz').show(
                    false);
                $(".sDS1").show();
            }
            callback(true);
        });
        //
        // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
        //
        editor_outgoing.dependent('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docType',
            function(val, data, callback, e) {
                if (val === '2') {
                    editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowID_rel')
                        .enable();
                    $("#editorform-editor-tabs-menu > li.tab-incoming-rel").removeClass("hide");
                } else if (val === '3') {
                    $("#editorform-editor-tabs-menu > li.tab-incoming-rel").addClass("hide");
                } else {
                    $("#editorform-editor-tabs-menu > li.tab-incoming-rel").addClass("hide");
                    editor_outgoing.field('enbl_inbox_rowIDadd_rel').val(0);
                    editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowID_rel')
                        .disable();
                    editor_outgoing.val('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowID_rel', null);
                    editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDadd_rel')
                        .disable();
                    editor_outgoing.val('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDadd_rel',
                        null);
                    editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDList_rel')
                        .disable();
                    editor_outgoing.val('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDList_rel',
                        null);
                    $('#linked-mail-listDocs option').remove();
                    $('#linked-mail-listDocs').prop('disabled', true);
                }
                callback(true);
            }
        );
        //
        //
        editor_outgoing.dependent('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolActive',
            function(val, data, callback, e) {
                console.log("CHECK >>> dependent outbox_controlIspolActive", val);
                if (val == "1") {
                    var koddocmail = editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail').val();
                    var userid = <?php echo $_SESSION['id']; ?>;
                    ajaxRequest_getUserCheckout(koddocmail, userid, 'getUserCheckout');
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolUseDeadline')
                        .enable();
                } else {
                    editor_outgoing.field('ispolStatus').disable();
                    editor_outgoing.field('ispolStatus').val(0);
                    $('input[id="DTE_Field_ispolStatus_0"]').prop('checked', false);
                    editor_outgoing.field('ispolStatus').labelInfo('<?php echo $ispolStatus_msg_0; ?>');
                    editor_outgoing.field(
                            '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolUseDeadline')
                        .disable();
                    editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolUseDeadline').val(
                        0);
                }
                callback(true);
            }
        );
        //
        //
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // editor_outgoing.dependent('enbl_outbox_rowIDadd_rel', function(val, data, callback, e) {
        // console.log('enbl_outbox_rowIDadd_rel is', val[0]);
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
        table_outgoing = $('#outbox').DataTable({
            dom: "<'row '<'col-sm-6'B><'col-sm-6 d-inline-flex justify-content-end'fl>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-6'i><'col-sm-6'p>>",
            language: {
                url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-mail.json"
            },
            ajax: {
                url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process.php",
                type: "POST"
            },
            serverSide: true,
            stateSave: true,
            stateDuration: -1,
            stateSaveCallback: function(settings, data) {
                sessionStorage.setItem('DataTables_' + settings.sInstance, JSON.stringify(data))
            },
            stateLoadCallback: function(settings) {
                return JSON.parse(sessionStorage.getItem('DataTables_' + settings.sInstance))
            },
            createdRow: function(row, data, index) {
                if (data.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docType == 3) {
                    if (data.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_docID_rel == '' ||
                        data.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_docID_rel == null) {
                        $(row).css('background-color', 'rgb(226, 201, 201)');
                    } else {
                        $(row).css('background-color', 'rgb(201, 226, 201)');
                    }
                }
                $('[data-toggle="tooltip"]', row).tooltip({
                    html: true,
                    placement: 'top',
                    trigger: "hover",
                    customClass: 'tooltip-outgoing'
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
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docTypeSTR", // (1)
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docIDSTR", // (2)
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDate" // (3)
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docAbout", // (4)
                    defaultContent: ""
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient" // (5)
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSenderSTR" // (6)
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docContractorSTR" // (7)
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolUseDeadline" // (8)
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolDays" // (9)
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileID", // (10)
                    render: function(id) {
                        let ext = id ? table_outgoing.file(
                                '<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>', id)
                            .file_extension : "";
                        let ext_ico = '<i class="fa-solid fa-file fa-lg"></i>';
                        if (ext === "pdf") {
                            ext_ico = '<i class="fa-solid fa-file-pdf fa-lg"></i>';
                        }
                        return id ?
                            '<span data-toggle="tooltip" title="Открыть или сохранить основной прикрепленный файл к документу" class=""><a class="" target="_blank" href="' +
                            table_outgoing.file(
                                '<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>', id)
                            .file_webpath +
                            '">' + ext_ico + '</a></span>' :
                            '-';
                    },
                    defaultContent: "",
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileIDadd", // (11)
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_INCOMING_FILES_TABLENAME; ?>.file_webpath", // (12)
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDadd_rel", // (13)
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_rowIDs_links", // (14)
                    className: "text-center"
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>.file_originalname", // (15)
                    className: "text-center"
                },
                {
                    class: "", // (16)
                    searchable: false,
                    orderable: false,
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolCheckoutWhen",
                    defaultContent: '<span class=""><i class="fa-regular fa-rectangle-list"></i></span>'
                },
                {
                    class: "", // (17)
                    searchable: false,
                    orderable: false,
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.cntComments",
                    defaultContent: ""
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolActive", // (18)
                    searchable: true,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolCheckout", // (19)
                    searchable: true,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docContractorDEPT", // (20)
                    searchable: true,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail", // (21)
                    searchable: true,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolCheckoutID", // (22)
                    searchable: true,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSourceID", // (23)
                    searchable: true,
                    visible: false
                },
                {
                    data: "<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolStatusDeadline", // (24)
                    searchable: true,
                    visible: false
                }
            ],
            columnDefs: [{
                    orderable: false,
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.docmailext ==
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
                            fullDate + '" style="text-decoration:none" data-toggle="tooltip">' +
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
                        let shortStr = data.substr(0, 30) + " ...";
                        let sourceID = row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .outbox_docSourceID !== "" ? row
                            .<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSourceID :
                            "no ID";
                        let sourceDate = row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .outbox_docSourceDate !== "" &&
                            row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .outbox_docSourceDate !==
                            null && typeof row
                            .<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSourceDate !==
                            "undefined" ? moment(row
                                .<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSourceDate,
                                'DD.MM.YYYY HH:mm:ss')
                            .format(
                                'DD.MM.YYYY') : "no date";
                        let sourceInfo = (sourceID == "no ID" || sourceDate == "no date") ?
                            '<span><i style="color:#E0E0E0" class="fa-regular fa-lightbulb fa-lg" aria-hidden="true"></i></span>' :
                            '<span data-toggle="tooltip" data-placement="top" title="Внутренний входящий номер получателя - ' +
                            sourceID + ' от ' +
                            sourceDate + '"><i class="fa-solid fa-lightbulb fa-lg"></i></span>';
                        let tags =
                            '<span style="padding-left:5px; padding-right:5px" data-toggle="tooltip" data-placement="top" title="Теги по документу (функция скоро будет добавлена)"><i style="color:#E0E0E0" class="fa-solid fa-tags fa-lg" aria-hidden="true"></i></span>';
                        if (fullStr.length > 30) {
                            return '<span style="float:left"><span data-toggle="tooltip" data-placement="top" title="' +
                                data.replace(/["']/g, '') + '">' + shortStr +
                                '</span></span><span style="float:right">' + tags + sourceInfo +
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
                        res = data.replace(new RegExp("\\r?\\n", "g"), "");
                        x = res.split(',');
                        contractor_2 = x[0];
                        if (data !== "---") {
                            if (row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                .outbox_controlIspolActive === '1') {
                                return '<span style="float:left">' + contractor_2 +
                                    '</span><span style="float:right"><a href="#" data-toggle="tooltip" data-placement="top" title="Исполнитель - ' +
                                    data +
                                    '"><div id="link-logCheckouts" data-toggle="modal" data-type="logCheckouts" data-id="' +
                                    row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail +
                                    '" data-target="#modal-logCheckouts"><i style="font-size:1.0em; margin-right:1px" class="fa fa-user-o" aria-hidden="true"></i></div></a></span>';
                            } else {
                                return '<span style="float:left">' + contractor_2 +
                                    '</span><span style="float:right"><a href="#" data-toggle="tooltip" data-placement="top" title="Исполнитель - ' +
                                    data +
                                    '"><div id="link-noControlIspolActive" data-toggle="modal" data-type="noControlIspolActive" data-id="' +
                                    row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail +
                                    '" data-target="#modal-noControlIspolActive"><i style="font-size:1.0em; margin-right:1px" class="fa fa-user-o" aria-hidden="true"></i></div></a></span>';
                            }
                        } else {
                            return '<span style="float:left">' + data + '</span>';
                        }
                    }
                },
                {
                    orderable: false,
                    searchable: true,
                    targets: 8,
                    render: function(data, type, row, meta) {
                        var deadlineTooltip = moment(row
                            .<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .outbox_docDateDeadline,
                            'DD.MM.YYYY').format('DD.MM.YYYY');
                        if (row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .outbox_controlIspolUseDeadline ===
                            '1') {
                            return '<span class="has-tooltip-deadline" data-toggle="tooltip" title="Дедлайн: ' +
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
                        var checkoutWhen = row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .outbox_controlIspolCheckoutWhen;
                        // Текущая дата
                        var date1 = moment().format('YYYY-MM-DD HH:mm:ss');
                        // Дедлайн по документу
                        var date2 = moment(row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .outbox_docDateDeadline, 'DD.MM.YYYY').format();
                        var deadlineTooltip = moment(row
                            .<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .outbox_docDateDeadline,
                            'DD.MM.YYYY').format('DD.MM.YYYY');
                        // Дата создания записи
                        var date3 = moment(row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                .outbox_docCreatedWhen,
                                'DD.MM.YYYY HH:mm')
                            .format();
                        var diffDays = moment(date2).diff(moment(date1), 'days');
                        diffDays = (diffDays > 9) ? "9+" : diffDays;
                        var diffSecs = moment(date2).diff(moment(date1), 'seconds');
                        if (row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .outbox_controlIspolActive === '1') {
                            if (row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                .outbox_controlIspolUseDeadline ===
                                '1') {
                                if (row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                    .outbox_controlIspolCheckout ===
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
                                    } else if (diffSecs > 0 && diffSecs <= controlDiffAlarm) {
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
                                if (row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                    .outbox_controlIspolCheckout ===
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
                    },
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
                                row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
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
                        if (data != null && row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .inbox_rowID_rel != null &&
                            row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowID_rel != 0) {
                            return '<div class=""><span data-toggle="tooltip" title="Номер документа, связанный с текущим как ответный, либо являющийся запросом для текущего. При клике вы перейдете к этому документу в разделе входящих." class=""><a class="text-dark" href="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/index.php?type=in&mode=thisyear&rel=' +
                                row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                .inbox_koddocmail_rel +
                                '"><div class="docnum">' + row
                                .<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                .inbox_docID_rel + '</div></a></span></div>';
                        } else if (data == null && row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .inbox_rowID_rel !=
                            0 &&
                            row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowID_rel != null
                        ) {
                            return '<div class=""><span data-toggle="tooltip" title="Номер документа, связанный с текущим как ответный, либо являющийся запросом для текущего. При клике вы перейдете к этому документу в разделе входящих." class=""><a class="text-dark" href="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/index.php?type=in&mode=thisyear&rel=' +
                                row
                                .<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_koddocmail_rel +
                                '"><div class="docnum">' +
                                row
                                .<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                .inbox_docID_rel + '</div></a></span></div>';
                        } else if (data != null && (row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                .inbox_rowID_rel ==
                                null || row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                .inbox_rowID_rel == 0)) {
                            return '<div class=""><a href="' + data +
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
                                row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail +
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
                        outboxLinks = row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .inbox_rowIDs_links;
                        dognetLinks = row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                            .dognet_rowIDs_links;
                        if ((inboxLinks !== null && typeof inboxLinks !== 'undefined' &&
                                inboxLinks !== "") ||
                            (outboxLinks !== null && typeof outboxLinks !== 'undefined' &&
                                outboxLinks !== "") ||
                            (dognetLinks !== null && typeof dognetLinks !== 'undefined' &&
                                dognetLinks !== "")) {
                            return '<div id="link-listAdditionalLinkDocs" data-toggle="modal" data-type="listAdditionalLinkDocs" data-id="' +
                                row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail +
                                '" data-target="#modal-listAdditionalLinkDocs"><span data-toggle="tooltip" title="Посмотреть все связи по документу" class=""><i class="fa fa-chain fa-lg" aria-hidden="true"></i></span></div>';
                        } else {
                            return "-";
                        }
                    },
                    targets: 14
                },
                {
                    orderable: false,
                    visible: false,
                    targets: 15
                },
                {
                    orderable: false,
                    targets: 16,
                    render: function(data, type, row, meta) {
                        return '<div id="link-logChanges" data-toggle="modal" data-type="logChanges" data-id="' +
                            row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail +
                            '" data-target="#modal-logChanges"><span data-toggle="tooltip" title="Открыть журнал действий пользователй по документу" class=""><i class="fa-regular fa-rectangle-list fa-lg"></i></span></div>';
                    }
                },
                {
                    orderable: false,
                    targets: 17,
                    render: function(data, type, row, meta) {
                        if (data && data > 0) {
                            return '<div id="link-logComments" data-toggle="modal" data-type="logComments" data-id="' +
                                row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                                .koddocmail +
                                '" data-target="#modal-logComments"><span data-toggle="tooltip" title="В чате по документу есть сообщения, открыть чат по документу" class=""><i class="fa-solid fa-comments fa-lg"></i></span></div>';
                        } else {
                            return '<div id="link-logComments" data-toggle="modal" data-type="logComments" data-id="' +
                                row.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
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
            pageLength: <?php echo $_SESSION['outgoing_pageLength']; ?>,
            lengthChange: true,
            lengthMenu: [
                [15, 30, 50, -1],
                [15, 30, 50, "Все"]
            ],
            buttons: [{
                    text: '<i class="fa-solid fa-rotate"></i>',
                    action: function(e, dt, node, config) {
                        $('#mail-outgoing-filters-block *').filter('input, select').each(function(
                            index, element) {
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

                        $('#mail-outgoing-filters-block *').filter('input, select').each(function(
                            index, element) {
                            sessionStorage.removeItem('outbox_' + $(element).attr('id'));
                        });

                        // var ispolname =
                        //     '<?php echo $_QRY_ISPOL["namezayvfio"] != "" ? $_QRY_ISPOL["namezayvfio"] : ""; ?>';
                        // var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispolout"]; ?>';
                        // var showIspolOutOnly = '<?php echo $_QRY_ISPOL["showispoloutonly"]; ?>';
                        // if (ispolStatus === "1" && ispolname != "" && showIspolOutOnly ===
                        //     "1") {
                        //     if ($("#chkOnlyIspolMe").prop('checked') === true) {
                        //         table_outgoing
                        //             .columns(7)
                        //             .search(ispolname)
                        //             .draw();
                        //         $("#filterIspol").val(ispolname);
                        //     } else {
                        //         table_outgoing.columns().search('').draw();
                        //     }
                        // } else {
                        //     table_outgoing.columns().search('').draw();
                        // }
                        $('#columnSearch_btnClear').click();
                        table_outgoing.rows('.selected').deselect();
                    },
                    className: 'btn-dark refreshButton'
                },
                {
                    extend: "create",
                    editor: editor_outgoing,
                    text: 'Добавить запись',
                    formButtons: [{
                            text: 'Добавить',
                            className: 'btn-dark createButton',
                            action: function() {
                                console.log('btn-dark createButton', 'triggered')
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
                    editor: editor_outgoing,
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
                    editor: editor_outgoing,
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
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        table_outgoing.on('draw', function() {
            // do something with the ID of the selected items
            console.log('TOOOOOOLTIPSSSSS');
        });
        table_outgoing.on('length', function(e, settings, len) {
            ajaxRequest_saveSessionVars('outgoing_pageLength', len);
        });
        table_outgoing.on('page', function(e, settings, len) {
            var info = table_outgoing.page.info();
            console.log('outgoing_pageCurrent #4', info.page);
            ajaxRequest_saveSessionVars('outgoing_pageCurrent', info.page);
        });
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        // Array to track the ids of the details displayed rows
        var detailRows = [];
        $('#outbox tbody').on('click', 'tr td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table_outgoing.row(tr);
            var idx = $.inArray(tr.attr('id'), detailRows);

            if (row.child.isShown()) {
                tr.removeClass('details');
                row.child.hide();

                // Remove from the 'open' array
                detailRows.splice(idx, 1);
            } else {
                tr.addClass('details');
                rowData = table_outgoing.row(row);
                d = row.data();

                var docFileID = d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileID;
                d.mainfile = docFileID != "" && docFileID != null ?
                    '<span class="attached-filelink"><span class="" style="color:#00bf00; font-size:0.8rem; margin-right: 5px;"><i class="fa-solid fa-paperclip"></i></span><a target="_blank" href="<?php echo __ROOT; ?>' +
                    row.data().<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>.file_webpath +
                    '">' + row.data().<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>.file_originalname +
                    '</a></span>' :
                    'Файл не прикреплен';
                //
                d.recipientName = d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient !==
                    "" ? d
                    .<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient : "---";
                d.ispolActive = d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolActive ==
                    1 ?
                    '<span class="" style=""><i class="fa-regular fa-square-check"></i></span>' :
                    '<span class="" style=""><i class="fa-regular fa-square"></i></span>';
                d.deadlineDate = isValidDate(d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                        .outbox_docDateDeadline) === true ?
                    '<span class="" style="">' + d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                    .outbox_docDateDeadline + '</span>' :
                    '<span class="" style="">---</span>';
                d.reminder1 = d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                    .outbox_controlIspolMailReminder1 ==
                    1 ?
                    '<span class="" style=""><i class="fa-regular fa-square-check"></i></span>' :
                    '<span class="" style=""><i class="fa-regular fa-square"></i></span>';
                d.reminder2 = d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                    .outbox_controlIspolMailReminder2 ==
                    1 ?
                    '<span class="" style=""><i class="fa-regular fa-square-check"></i></span>' :
                    '<span class="" style=""><i class="fa-regular fa-square"></i></span>';
                d.ispolCheckout = d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                    .outbox_controlIspolCheckout ==
                    1 ?
                    '<span class="" style=""><i class="fa-regular fa-square-check"></i></span>' :
                    '<span class="" style=""><i class="fa-regular fa-square"></i></span>';
                d.ispolCheckoutWhen = row.data().<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                    .outbox_controlIspolCheckoutWhen ===
                    null ? "" : row.data().<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                    .outbox_controlIspolCheckoutWhen;
                switch (d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_controlIspolStatus) {
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

                ajaxRequest_getLastAction(d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail,
                    'outbox_docDateDeadline', 'getLastAction');
                ajaxRequest_getLastAction(d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail,
                    'outbox_controlIspolActive', 'getLastAction');
                ajaxRequest_getLastAction(d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail,
                    'outbox_controlIspolMailReminder1', 'getLastAction');
                ajaxRequest_getLastAction(d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail,
                    'outbox_controlIspolMailReminder2', 'getLastAction');
                ajaxRequest_getLastAction(d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail,
                    'outbox_controlIspolCheckout', 'getLastAction');

                ajaxRequest_getLastNotify('MAIL_REMIND', d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                    .koddocmail,
                    'Reminder1:1', 'getLastNotify');
                ajaxRequest_getLastNotify('MAIL_REMIND', d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                    .koddocmail,
                    'Reminder2:1', 'getLastNotify');
                console.log(row.data().<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                    .outbox_controlIspolCheckoutWhen);
                rowData.child(<?php include('templates/mailbox-outgoing-details.tpl'); ?>).show();

                ajaxRequest_listAttachedFiles(row.data().<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                    .koddocmail,
                    'listAttachedFiles');

                // Add to the 'open' array
                if (idx === -1) {
                    detailRows.push(tr.attr('id'));
                }
                // Определяем просрочен ли дедлайн или нет
                moment.locale('en');
                var date1 = moment(new Date(), 'YYYY-MM-DD HH:mm:ss').format();
                var date2 = moment(row.data().<?php echo __MAIL_OUTGOING_TABLENAME; ?>
                        .outbox_docDateDeadline,
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
        table_outgoing.on('draw', function() {
            $.each(detailRows, function(i, id) {
                $('#' + id + ' td.details-control').trigger('click');
            });
        });

        // ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

        $('#columnSearch_btnApply').on('click', function() {
            console.log("Исполнитель: " + $("#filterIspol").val());
            table_outgoing.ajax.reload(null, true);
            table_outgoing
                .columns(1)
                .search($("#filterType").val())
                .draw();

            table_outgoing
                .columns(2)
                .search($("#filterNumber").val())
                .draw();

            table_outgoing
                .columns(4)
                .search($("#filterAbout").val())
                .draw();

            table_outgoing
                .columns(5)
                .search($("#filterRecipient").val())
                .draw();

            table_outgoing
                .columns(6)
                .search($("#filterSender").val())
                .draw();

            table_outgoing
                .columns(7)
                .search($("#filterIspol").val())
                .draw();

            table_outgoing
                .columns(18)
                .search($("#filterInControl").val())
                .draw();

            table_outgoing
                .columns(19)
                .search($("#filterCheckout").val())
                .draw();

            table_outgoing
                .columns(20)
                .search($("#filterDept").val())
                .draw();

            table_outgoing
                .columns(21)
                .search($("#filterKOD").val())
                .draw();

            table_outgoing
                .columns(23)
                .search($("#filterSourceID").val())
                .draw();

            table_outgoing
                .columns(24)
                .search($("#filterIspolDL").val())
                .draw();

            $('#mail-outgoing-filters-block *').filter('input, select').each(function(index, element) {
                sessionStorage.removeItem('outbox_' + $(element).attr('id'));
                sessionStorage.setItem('outbox_' + $(element).attr('id'), $(element).val());
                console.log(index, 'sessionStorage.getItem()', element, sessionStorage.getItem(
                    'outbox_' + $(
                        element).attr('id')));
            });

        });

        $('#columnSearch_btnClear, button.columnSearch_btnClear').on('click', function() {
            // Очищаем блок фильтров, отслеживая заблокированные элементы
            $('#mail-outgoing-filters-block *').filter('input, select').each(function(index, element) {
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
            var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispolout"]; ?>';
            var showIspolOutOnly = '<?php echo $_QRY_ISPOL["showispoloutonly"]; ?>';
            if (ispolStatus === "1" && ispolname != "" && showIspolOutOnly ===
                "1") {
                if ($("#chkOnlyIspolMe").prop('checked') === true) {
                    // table_outgoing
                    //     .columns(7)
                    //     .search(ispolname)
                    //     .draw();
                    $("#filterIspol").val(ispolname);
                } else {
                    // table_outgoing.columns().search('').draw();
                }
            } else {
                // table_outgoing.columns().search('').draw();
            }
            $('#mail-outgoing-filters-block *').filter('input, select').each(function(index, element) {
                sessionStorage.removeItem('outbox_' + $(element).attr('id'));
            });
            // table_outgoing.columns().search('').draw();
            $('#columnSearch_btnApply').click();
            table_outgoing.one('draw', function() {
                window.location.replace(removeURLParameter(document.location.href, 'rel'));
            });
        });

        // ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

        if (getAllUrlParams().record_id) {
            table_outgoing.on('draw', function() {
                var indexes = table_outgoing.rows().eq(0).filter(
                    function(rowIdx) {
                        return table_outgoing.cell(rowIdx, 2).data() === getAllUrlParams()
                            .record_id ?
                            true : false;
                    });
                table_outgoing.rows(indexes).select();
                table_outgoing.rows({
                    selected: false
                }).nodes().to$().css({
                    "display": "none"
                });
                table_outgoing.rows(indexes).deselect();
                // 			alert ( table_outgoing.column( 2 ).data().indexOf(getAllUrlParams().doc_id) );
                // 			alert ( table_outgoing.row( {page: 'all', selected:true} ).ids() );
                // 			table_outgoing.ajax.reload();
            });
            table_outgoing.ajax.reload(null, false);
        }

        // ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
        table_outgoing.one('draw', function() {
            if (getAllUrlParams().doc_id) {
                // 			table_outgoing.ajax.reload(null, false);
                // 			table_outgoing.page.len(-1);
            }
        });

        // ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- -----

        table_outgoing.one('preDraw', function() {
            var relID = "<?php echo $__relID; ?>";
            if (relID == "norel") {
                ajaxRequest_loadFromDB_showIspolOnly('showIspolOnly');
                var ispolname =
                    '<?php echo $_QRY_ISPOL["namezayvfio"] != "" ? $_QRY_ISPOL["namezayvfio"] : ""; ?>';
                var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispolout"]; ?>';
                var showIspolOnly = '<?php echo $_QRY_ISPOL["showispoloutonly"]; ?>';
                if (ispolStatus === "1" && ispolname != "" && showIspolOnly === "1") {
                    table_outgoing
                        .columns(7)
                        .search(ispolname)
                        .draw();
                    $("#filterIspol").val(ispolname);
                    $("#filterIspol").prop('disabled', true);
                }
            } else {
                table_outgoing
                    .columns(21)
                    .search(relID)
                    .draw();
                $("#filterKOD").val(relID);
                $('p.mail-outgoing-filters-button i').show();
                $('button.columnSearch_btnClear.btnTop').show();
            }
        });
        //
        //
        table_outgoing.on('draw', function() {
            if (($('#filterKOD').val() != "") || ($('#filterIspol').val() != "") || ($('#filterType')
                    .val() != "") || ($('#filterNumber')
                    .val() != "") || ($('#filterAbout').val() != "") || ($('#filterSender').val() !=
                    "") ||
                ($(
                    '#filterInControl').val() != "") || ($('#filterCheckout').val() != "") || ($(
                    '#filterDept').val() != "") || ($('#filterRecipient').val() != "") || ($(
                    '#filterSourceID').val() != "") || ($('#filterIspolDL').val() != "")) {
                $('p.mail-outgoing-filters-button i').show();
                $('button.columnSearch_btnClear.btnTop').show();
                console.log('Filters In');
            } else {
                $('p.mail-outgoing-filters-button i').hide();
                $('button.columnSearch_btnClear.btnTop').hide();
                console.log('Filters Out');
            }

            var notEmpty;
            $("#mail-outgoing-filters-block .form-control").each(function() {
                var element = $(this);
                if (element.val() != "") {
                    notEmpty = true;
                    console.log(element, element.val());
                    element.addClass('filter-active');
                } else {
                    element.removeClass('filter-active');
                }
            });
            var rowCurrent = '<?php echo $_SESSION['outgoing_selectedRowID']; ?>';
            if (rowCurrent !== '') {
                // table_outgoing.row('#' + rowCurrent).select();
            }
        });
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        table_outgoing.on('click', 'tr', function() {
            var id = table_outgoing.row(this).id();
            ajaxRequest_saveSessionVars('outgoing_selectedRowID', id)
        });
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        table_outgoing.one('preDraw', function(e, settings, data) {
            // $('#filterKOD').val('');
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
            var pageCurrent = '<?php echo $_SESSION['outgoing_pageCurrent']; ?>';
            var rowCurrent = '<?php echo $_SESSION['outgoing_selectedRowID']; ?>';
            console.log('outgoing_selectedRowID', rowCurrent);
            console.log('outgoing_pageCurrent #1', Number(rowCurrent));
            if (pageCurrent !== 'nodraw' && pageCurrent !== '' && pageCurrent !== null) {
                console.log('outgoing_pageCurrent #2', Number(pageCurrent));
                table_outgoing.one('draw', function() {
                    // table_outgoing.page(Number(pageCurrent)).draw('page');
                    // ajaxRequest_saveSessionVars('outgoing_pageCurrent', 'nodraw');
                    console.log('outgoing_pageCurrent #3', 'Table is drawn', table_outgoing.page
                        .info().page);
                });
            }
            if (rowCurrent !== '') {
                // table_outgoing.row('#' + rowCurrent).select();
            }
        });
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        $("#chkOnlyIspolMe").click(function() {
            ajaxRequest_saveToDB_showIspolOnly('showIspolOnly');

            var ispolname =
                '<?php echo $_QRY_ISPOL["namezayvfio"] != "" ? $_QRY_ISPOL["namezayvfio"] : ""; ?>';
            var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispolout"]; ?>';
            if (ispolStatus === "1" && ispolname != "") {
                if ($("#chkOnlyIspolMe").prop('checked') === true) {
                    // table_outgoing
                    //     .columns(7)
                    //     .search(ispolname)
                    //     .draw();
                    $("#filterIspol").val(ispolname);
                    $("#filterIspol").prop('disabled', true);

                    $('#mail-outgoing-filters-block *').filter('input, select').each(function(index,
                        element) {
                        sessionStorage.removeItem('outbox_' + $(element).attr('id'));
                        sessionStorage.setItem('outbox_' + $(element).attr('id'), $(element).val());
                        console.log(index, 'sessionStorage.getItem()', element, sessionStorage
                            .getItem('outbox_' + $(element).attr('id')));
                    });
                    ajaxRequest_getOutgoingStats_v2('1', 'getOutgoingStats');
                    $('#mail-outgoing-deck-block .card-header i').removeClass('d-none');
                } else {
                    table_outgoing.columns().search('').draw(false);
                    $("#filterIspol").val('');
                    $("#filterIspol").prop('disabled', false);

                    $('#mail-outgoing-filters-block *').filter('input, select').each(function(index,
                        element) {
                        sessionStorage.removeItem('outbox_' + $(element).attr('id'));
                        sessionStorage.setItem('outbox_' + $(element).attr('id'), $(element).val());
                        console.log(index, 'sessionStorage.getItem()', element, sessionStorage
                            .getItem('outbox_' + $(element).attr('id')));
                    });
                    ajaxRequest_getOutgoingStats_v2('0', 'getOutgoingStats');
                    $('#mail-outgoing-deck-block .card-header i').addClass('d-none');
                }
                $('#columnSearch_btnApply').click();
            } else {
                table_outgoing.columns().search('').draw(false);
                $("#filterIspol").val('');
            }
        });

        var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispolout"]; ?>';
        if (ispolStatus === "1") {
            $("#divOnlyIspolMe").css('display', 'block');
        } else {
            $("#divOnlyIspolMe").css('display', 'none');
        }

        $("#chkOnlyInControl").click(function() {

        });


        $(document).on("click", '.help-popover-label', function() {
            var fieldname = $(this).attr('fieldname');
            console.log('Clicked on', fieldname);
            ajaxRequest_getFormFieldHelp(fieldname, 'getFormFieldHelp');

        });


        $(document).on("click", ".remove-file", function() {
            var rowid = $(this).attr('rowid');
            var koddocmail = $(this).attr('koddocmail');
            console.log(rowid, 'To delete clicked');
            ajaxRequest_deleteAttachedFile(rowid, 'deleteAttachedFile');
        });
        $(document).on("click", ".remove-mainfile", function() {
            var rowid = $(this).attr('rowid');
            var koddocmail = $(this).attr('koddocmail');
            console.log(rowid, 'To delete mainfile clicked');
            editor_outgoing.field('<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docFileID').val('');
            ajaxRequest_deleteAttachedFile(rowid, 'deleteAttachedFile');
        });
        //
        // ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- ----- -- 
        //
        $(document).on("click", "div#link-noControlIspolActive", function() {
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
        $(document).on("change", "input#DTE_Field_enbl_inbox_docType_change_0", function() {
            console.log('DTE_Field_enbl_inbox_docType_change_0 is clicked');
            if ($(this).is(':checked')) {
                console.log('DTE_Field_enbl_inbox_docType_change_0 is checked');
                $('#inbox-rowID-rel-alert').css('display', 'block');
            } else {
                console.log('DTE_Field_enbl_inbox_docType_change_0 is unchecked');
                $('#inbox-rowID-rel-alert').css('display', 'none');
            }
        });
        //
        //
        $(document).on("change", "input#DTE_Field_enbl_inbox_rowIDadd_rel_0", function() {
            console.log('DTE_Field_enbl_inbox_rowIDadd_rel_0 is clicked');
            if ($(this).is(':checked')) {
                $(".rowIDadd-enbl").css('display', 'block');
                editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDadd_rel').enable();
                editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDList_rel')
                    .enable();
                editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDList_rel').focus();
                $('#linked-mail-listDocs').prop('disabled', false);
                $('#inbox-rowIDadd-rel-alert').css('display', 'block');
            } else {
                $(".rowIDadd-enbl").css('display', 'none');
                editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDadd_rel')
                    .disable();
                editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDadd_rel').val('');
                editor_outgoing.field(
                        '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDList_rel')
                    .disable();
                editor_outgoing.field(
                    '<?php echo __MAIL_OUTGOING_TABLENAME; ?>.inbox_rowIDList_rel').val('');
                $('#linked-mail-listDocs option').remove();
                $('#linked-mail-listDocs').prop('disabled', true);
                $('#inbox-rowIDadd-rel-alert').css('display', 'none');
            }
        });
        //
        //
        $(document).on("click", "div#link-logCheckouts", function() {
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

            $('#table-logCheckouts').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process-showLogCheckouts.php",
                    type: "POST",
                    data: {
                        koddocmail: koddocmail
                    }
                },
                serverSide: true,
                columns: [{
                    data: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_logCheckouts.timestamp"
                }, {
                    data: "mailbox_sp_users.namezayvtel"
                }, {
                    data: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_logCheckouts.ispolStatus"
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

            $('#table-logChanges').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-8'fi><'col-sm-4'p>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process-showLogChanges.php",
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
                    data: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_logChanges.timestamp"
                }, {
                    data: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_logChanges.action"
                }, {
                    data: "mailbox_sp_users.namezayvfio"
                }, {
                    data: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_logChanges.changesTitle"
                }, {
                    data: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_logChanges.changesNewVal"
                }, {
                    data: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_logChanges.changesOldVal"
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
                            .<?php echo __MAIL_OUTGOING_PREFIX; ?>_logChanges
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
                                    "<p style='margin-bottom:0'>---</p>" : "");
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
                                    '<span data-toggle="tooltip" data-placement="auto" title="' +
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
                            return '<span style="color: #999">' + res + '</span>';
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

        $(document).on('initEditor', function(e, inst) {
            inst.on('opened', function() {
                console.log('Form displayed');
                // ... add event handlers
            });
        });

        $(document).on("click", "div#link-logComments", function() {
            var koddocmail = $(this).attr('data-id');
            console.log('#div.link modal-logComments clicked', koddocmail);
            //
            //
            $('#modal-logComments > div.modal-dialog').on('shown.bs.modal', function(e) {
                console.log('#modal-logComments > div.modal-dialog shown!');
                $("#modal-logComments > div.modal-dialog").css({
                    "width": "50%",
                    "min-width": "640px",
                    "max-width": "800px"
                });
                //
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                });
                //
            });
            /*
            -
            Работа с комментариями к документу
            -
            ----- ----- ----- ----- ----- 
            */
            // 
            editor_logComments = new $.fn.dataTable.Editor({
                display: "bootstrap",
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process-showLogComments.php",
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
                    label: "Текст комментария",
                    type: "textarea",
                    name: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments.commentText",
                    attr: {
                        placeholder: 'Комментарий к документу'
                    },
                    className: "block"
                }, {
                    label: "",
                    type: "hidden",
                    name: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments.koddocmail"
                }]
            });
            table_logComments = $('#table-logComments').dataTable({
                dom: "<'row'<'col-sm-5'B><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-12'p>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process-showLogComments.php",
                    type: "POST",
                    data: {
                        koddocmail: koddocmail
                    }
                },
                serverSide: true,
                columns: [{
                    data: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments.username"
                }, {
                    data: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments.commentText"
                }, {
                    data: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments.timestamp"
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
                                    .<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments
                                    .update_timestamp;
                                var update_username = row
                                    .<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments
                                    .update_username;
                                var updateStr = (update_timestamp != null &&
                                        update_timestamp != "") ?
                                    '<br>Изменено: ' +
                                    update_timestamp + ', ' + update_username : '';
                                if (row
                                    .<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments
                                    .action == "FORM" && row
                                    .<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments
                                    .commentAdd !== '' && row
                                    .<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments
                                    .commentAdd !== null) {
                                    var commAdd =
                                        '<div class="commentAdd text-info">' +
                                        row
                                        .<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments
                                        .commentAdd + '</div>';
                                } else {
                                    var commAdd = '';
                                }
                                return '<div class="commentBlock shadow px-3 py-2"><div class="commentText">' +
                                    data +
                                    '</div><div class="commentDate">Создано: ' + row
                                    .<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments
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
                    this.field('<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments.koddocmail').val(
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
            table_outgoing.ajax.reload(null, false);
            console.log('#modal-logComments hidden!');
            editor_logComments.destroy();
        });
        //
        // ----- -- ----- -- ----- -- ----- -- ----- -- ----- 
        //
        $(document).on("click", "div#link-listAddFiles", function() {
            var koddocmail = $(this).attr('data-id');
            console.log('#div.link modal-listAddFiles clicked', koddocmail);
            //
            //
            $('#modal-listAddFiles > div.modal-dialog').on('shown.bs.modal', function(e) {
                console.log('#modal-listAddFiles > div.modal-dialog shown!');
                $("#modal-listAddFiles > div.modal-dialog").css({
                    "width": "50%",
                    "min-width": "640px",
                    "max-width": "800px"
                });
                //
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                });
                //
            });
            /*
            -
            Работа с комментариями к документу
            -
            ----- ----- ----- ----- ----- 
            */
            // 
            editor_listAddFiles = new $.fn.dataTable.Editor({
                display: "bootstrap",
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process-showLogAddFiles.php",
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
                    name: "<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>.comment",
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
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process-showLogAddFiles.php",
                    type: "POST",
                    data: {
                        koddocmail: koddocmail
                    }
                },
                serverSide: true,
                columns: [{
                    data: "<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>.file_originalname"
                }, {
                    data: "<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>.comment"
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
                                row.<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>
                                .file_webpath +
                                '">' + row
                                .<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>
                                .file_originalname + '</a></span>' :
                                row.<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>
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
            //
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
                    // this.field('<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>.koddocmail').val(koddocmail);
                });
            editor_listAddFiles
                .on('close', function(e, mode, action) {
                    table_outgoing.ajax.reload(null, false);
                    // table_listAddFiles.ajax.reload(null, false);
                });
            /*
            ----- ----- ----- ----- ----- 
            */
            // editor_listAddFiles
            //     .clear()
            //     .add({
            //         label: "Текст комментария",
            //         type: "textarea",
            //         name: "<?php echo __MAIL_OUTGOING_PREFIX; ?>_listAddFiles.commentText",
            //         attr: {
            //             placeholder: 'Комментарий к документу'
            //         }
            //     });
            editor_listAddFiles.on('submitSuccess', function(e, json, data, action) {
                console.log('submitSuccess');
            });
        });
        $('#modal-listAddFiles').on('hidden.bs.modal', function(e) {
            console.log('#modal-listAddFiles hidden!');
            editor_listAddFiles.destroy();
        });


        $(document).on("click", "div#link-listLinkDocs", function() {
            var koddocmail = $(this).attr('data-id');
            console.log('#div.link modal-listLinkDocs clicked', koddocmail);
            //
            //
            $('#wrap-linkedOutgoing-showFiles').css("display", "none");
            $('#outputArea-linkedOutgoing-showFiles').html("");
            //
            //
            $('#modal-listLinkDocs > div.modal-dialog').on('shown.bs.modal', function(e) {
                console.log('#modal-listLinkDocs > div.modal-dialog shown!');
                $("#modal-listLinkDocs > div.modal-dialog").css({
                    "width": "50%",
                    "min-width": "640px",
                    "max-width": "800px"
                });
                //
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                });
                //
            });
            /*
            -
            Работа с комментариями к документу
            -
            ----- ----- ----- ----- ----- 
            */
            // 
            table_listLinkDocs = $('#table-listLinkDocs').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-mail.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process-showLogLinkDocs.php",
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
                        data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDate"
                    },
                    {
                        data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docAbout"
                    },
                    {
                        data: "<?php echo __MAIL_INCOMING_TABLENAME; ?>.ID",
                    }
                ],
                columnDefs: [{
                    orderable: false,
                    width: '6%',
                    targets: 0,
                    render: function(data, type, row, meta) {
                        if (data != null) {
                            return '<span class="link"><a href="index.php?type=in&mode=archive&rel=' +
                                row.<?php echo __MAIL_INCOMING_TABLENAME; ?>
                                .koddocmail + '">' +
                                data + '</a></span>';
                        } else {
                            return '<span class="glyphicon glyphicon-option-horizontal"></span>';
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
                        return '<span><a id="btn-linkedIncoming-showFiles" data-id="' +
                            row
                            .<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail +
                            '" data-docid="' + row
                            .<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docID +
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

        $(document).on("click", "#btn-linkedIncoming-showFiles", function() {
            var koddocmail = $(this).attr('data-id');
            var docid = $(this).attr('data-docid');
            console.log('#btn-linkedIncoming-showFiles clicked!', koddocmail);
            //
            if ($('#wrap-linkedIncoming-showFiles').css("display") !== "none") {
                $('#wrap-linkedIncoming-showFiles').css("display", "none");
            } else {
                ajaxRequest_getLinkedRelFiles(koddocmail, 'getLinkedRelFiles');
                $('#linkedIncoming-docID').text(docid);
            }

        });

        /*
        ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 

        ОБРАБОТЧИК ВЫЗОВА

        ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
        */
        $(document).on("click", "div#link-listAdditionalLinkDocs", function() {
            var koddocmail = $(this).attr('data-id');
            console.log('#div.link modal-listAdditionalLinkDocs clicked', koddocmail);
            //
            //
            $('#wrap-linkedOutgoing-showFiles').css("display", "none");
            $('#outputArea-linkedOutgoing-showFiles').html("");
            //
            //
            $('#modal-listAdditionalLinkDocs').on('shown.bs.modal', function(e) {
                console.log('#modal-listAdditionalLinkDocs shown!');
                $("#modal-listAdditionalLinkDocs > div.modal-dialog").css({
                    "width": "50%",
                    "min-width": "640px",
                    "max-width": "800px"
                });
                //
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
                //
                if ($('#table-listAdditionalLinkDocsSP > tbody > tr > td').hasClass(
                        'dataTables_empty')) {
                    console.log('table-listAdditionalLinkDocsSP empty!');
                    $('#table-listAdditionalLinkDocsSP thead').css('display', 'none');
                } else {
                    $('#table-listAdditionalLinkDocsSP thead').css('display', '');
                }
            });
            /*
            -
            Работа с комментариями к документу
            -
            ----- ----- ----- ----- ----- 
            */
            var table_listAdditionalLinkDocsDog = $('#table-listAdditionalLinkDocsDog').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-docDogLinks.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process-showLogAdditionalLinkDocsDog.php",
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
                        data: "sp_objects.nameobjectshot"
                    },
                    {
                        data: "sp_contragents.nameshort"
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
                                row.dognet_docbase.koddoc + '" target="_blank">' +
                                data + '</a></span>';
                        } else {
                            return '<span class="glyphicon glyphicon-option-horizontal"></span>';
                        }
                    },
                }, {
                    orderable: false,
                    searchable: false,
                    width: '20%',
                    targets: 1,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                }, {
                    orderable: false,
                    searchable: false,
                    width: '20%',
                    targets: 2,
                    render: function(data, type, row, meta) {
                        let fullStr = data;
                        let shortStr = data.substr(0, 28) + " ..."
                        if (fullStr.length > 25) {
                            return '<a href="#" data-toggle="tooltip" data-placement="top" title="' +
                                fullStr + '">' + shortStr + '</a>';
                        } else {
                            return fullStr;
                        }
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
                            return '<a href="#" data-toggle="tooltip" data-placement="top" title="' +
                                fullStr + '">' + shortStr + '</a>';
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
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-docSPLinks.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process-showLogAdditionalLinkDocsSP.php",
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
                            let shortStr = row.sp_contragents.nameshort.substr(0,
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
            var table_listAdditionalLinkDocsMOut = $('#table-listAdditionalLinkDocsMOut').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-docMailLinks.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process-showLogAdditionalLinkDocsMOut.php",
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
                            return '<span class="glyphicon glyphicon-option-horizontal"></span>';
                        }
                    },
                }, {
                    orderable: false,
                    searchable: false,
                    width: '20%',
                    targets: 1,
                    render: function(data, type, row, meta) {
                        let fullStr = data;
                        let shortStr = data.substr(0, 28) + " ..."
                        if (fullStr.length > 25) {
                            return '<a href="#" data-toggle="tooltip" data-placement="top" title="' +
                                fullStr + '">' + shortStr + '</a>';
                        } else {
                            return fullStr;
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
                            return '<a href="#" data-toggle="tooltip" data-placement="top" title="' +
                                fullStr + '">' + shortStr + '</a>';
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
            var table_listAdditionalLinkDocsMInc = $('#table-listAdditionalLinkDocsMInc').dataTable({
                dom: "<'row'<'col-sm-5'><'col-sm-4'><'col-sm-3'>>" +
                    "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-8'><'col-sm-4'>>",
                language: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/dt_russian-docMailLinks.json"
                },
                ajax: {
                    url: "<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/process/mailbox-outgoing-process-showLogAdditionalLinkDocsMInc.php",
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
                            return '<span class="glyphicon glyphicon-option-horizontal"></span>';
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
        setInterval(function() {
            if (ajaxRequest_checkSessionAsync(sessionID)) {
                if (!editor_outgoing.display()) {
                    table_outgoing.ajax.reload(null, false);
                }
            }
        }, 2 * 60 * 1000); // mins * secs/min * 1000 milsec
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        // ajaxRequest_getOutgoingStats('getOutgoingStats');

        ajaxRequest_loadFromDB_showIspolOnly('showIspolOnly');
        // 
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
        // 
        var ispolname =
            '<?php echo $_QRY_ISPOL["namezayvfio"] != "" ? $_QRY_ISPOL["namezayvfio"] : ""; ?>';
        var ispolStatus = '<?php echo $_QRY_ISPOL["status_ispolout"]; ?>';
        var showIspolOutOnly = '<?php echo $_QRY_ISPOL["showispoloutonly"]; ?>';
        if (ispolStatus === "1" && ispolname != "" && showIspolOutOnly ===
            "1") {
            if ($("#chkOnlyIspolMe").prop('checked') === true) {
                $("#filterIspol").val(ispolname);
            } else {
                $("#filterIspol").val('');
            }
            ajaxRequest_getOutgoingStats_v2('1', 'getOutgoingStats');
            $('#mail-outgoing-deck-block .card-header i').removeClass('d-none');
        } else {
            ajaxRequest_getOutgoingStats_v2('0', 'getOutgoingStats');
            $('#mail-outgoing-deck-block .card-header i').addClass('d-none');
        }

        // var state = table_outgoing.state.loaded(); // get the current state
        // table_outgoing.clear(); // clear the table
        // table_outgoing.draw(); // redraw the table

        $('#mail-outgoing-filters-block *').filter('input, select').each(function(index, element) {
            var itemName = 'outbox_' + $(element).attr('id');
            var itemID = $(element).attr('id');
            var itemVal = sessionStorage.getItem(itemName);
            var tagName = $(element).prop('tagName').toLowerCase();
            if (tagName === 'select' && itemVal !== '') {
                $('#' + itemID + ' option[value="' + itemVal + '"]').prop('selected', true);
                $('#' + itemID).val(itemVal);
            }
            if (tagName === 'input' && itemVal !== '') {
                if ($('#' + itemID).val() != itemVal) {
                    $('#' + itemID).val(itemVal);
                }
            }
            console.log('Outgoing sessionStorage checkpoint [reload]', index, tagName, itemID, itemName,
                '>>> storage.val = ' + itemVal, '>>> $.val = ' + $('#' + itemID).val());
        });
    });
</script>

<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/common-modals.css">
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/common-customform.css">
<?php
// ----- ----- ----- ----- -----
// Подключаем форму и выводим таблицу входящей почты
// :::
include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3 . "/forms/mailbox-outgoing-customForm.php";
include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3 . "/forms/mailbox-outgoing-filters.php";
// ----- ----- ----- ----- -----
?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-outgoing.css">
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-outgoing-details.css">

<section>
    <div id="divOnlyIspolMe" class="form-check checkbox mb-3">
        <input class="form-check-input" type="checkbox" value="" id="chkOnlyIspolMe">
        <label class="form-check-label ml-2" for="chkOnlyIspolMe" data-toggle="popover" data-content='<p>Данный чекбокс фиксирует вас как ответственного в блоке фильтров и может быть снят только повторным кликом. Установка сохраняется как персональная настройка сервиса в БД. Очистка фильтров данный чекбокс не изменит.</p>'>
            <b>Показывать только документы, где я исполнитель</b>
        </label>
    </div>

    <div id="mail-main-outbox" class="">
        <div class="demo-html"></div>
        <table id="outbox" class="table display compact" cellspacing="1" width="100%">
            <thead>
                <tr>
                    <th><i class='fa-solid fa-ellipsis-vertical'></i></th>
                    <th>Т</th>
                    <th>№</th>
                    <th>Рег</th>
                    <th>Тема документа</th>
                    <th>Организация-получатель</th>
                    <th>Отправитель</th>
                    <th>Исполнитель</th>
                    <th data-toggle="tooltip" title="<p>Показывает установлен или нет по документу дедлайн.</p>">Д</th>
                    <th data-toggle="tooltip" title="<p>Если вы видите какой-либо символ в этом столбце - контроль исполнения (КИ) включен. Расшифровку символа можно поссмотреть в легенде под таблице с документами, либо наведя указатель мыши на сам символ.</p>">
                        КИ</th>
                    <th data-toggle="tooltip" title="<p>Основной прикрепленный файл к документу.</p>"><span class="">Ф</span><span class="small" style="font-size:65%">o</span></th>
                    <th data-toggle="tooltip" title="<p>Дполнительные прикрепленные файл(ы) к документу. Посмотреть список этих файлов можно во всплывающем окне кликнув на иконке в этом столбце. Допускается любое количество файлов и любых типов.</p>">
                        <span class="">Ф</span><span class="small" style="font-size:65%">д</span>
                    </th>
                    <th data-toggle="tooltip" title="<p>Ссылка в виде номера документа во входящих, который является ответным либо запросом ответа по отношению к документу в строке списка.</p>">
                        <span class="">ЗО</span><span class="small" style="font-size:65%">o</span>
                    </th>
                    <th data-toggle="tooltip" title="<p>Дополнительные входящие документы, которые были отмечены как те, на которые данный документ является ответным. Посмотреть список этих документов можно во всплывающем окне кликнув на иконке в этом столбце.</p>">
                        <span class="">ЗО</span><span class="small" style="font-size:65%">д</span>
                    </th>
                    <th data-toggle="tooltip" title="<p>Списки связанных входящих и исходящих документов, а также договоров и контрагентов, которые были отмечены как связанные с данным документом. Эти связи устанавливаются вручную при редактировании документа (вкладка Связи).</p>">
                        СВ</th>
                    <th></th>
                    <th data-toggle="tooltip" title="<p>Журнал действий с документом.</p>">Ж</th>
                    <th data-toggle="tooltip" title="<p>Чат по документу. Можно оставлять сообщения как непосредственно в чате, так и через форму редактирвоания. Комментарий исполнителя и дополнительная инофрмация о документе из формы редактирования также появится в этом чате.</p>">
                        К</th>
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
        <div class="modal-content">
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
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-outgoing-modal-logCheckouts.css">
<div class="modal fade" data-backdrop="true" id="modal-logCheckouts" tabindex="-1" role="dialog" aria-labelledby="modal-logCheckouts-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
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
                                <th class="text-left" width="25%">Время изменения</th>
                                <th class="text-left" width="65%">Ответственный</th>
                                <th class="text-right" width="10%">Статус</th>
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
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-outgoing-modal-logChanges.css">
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
                    <p class="text-danger small">*Операции связанные с загрузкой и удалением файлов, как основного так
                        и
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
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-outgoing-modal-logComments.css">
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
                <editor-field name="<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments.commentText"></editor-field>
            </fieldset>
            <fieldset class="field">
                <editor-field name="<?php echo __MAIL_OUTGOING_PREFIX; ?>_logComments.koddocmail"></editor-field>
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
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-outgoing-modal-listAddFiles.css">
<div class="modal fade" data-backdrop="true" id="modal-listAddFiles" tabindex="-1" role="dialog" aria-labelledby="modal-listAddFiles-label" aria-hidden="true">
    <div class="modal-dialog modal-lg custom-modal" role="document">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-listAddFiles-label">Прикрепленные дополнительные файлы с
                    комментариями
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

<div id="editorform-listAddFiles">
    <div class="section d-flex flex-column">
        <div class="block w-100">
            <fieldset class="field">
                <editor-field name="<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>.comment"></editor-field>
            </fieldset>
            <fieldset class="field">
                <editor-field name="<?php echo __MAIL_OUTGOING_FILES_TABLENAME; ?>.koddocmail"></editor-field>
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
# Связанные входящие письма
# >>>>>

?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-outgoing-modal-listLinkDocs.css">
<div class="modal fade" data-backdrop="true" id="modal-listLinkDocs" tabindex="-1" role="dialog" aria-labelledby="modal-listLinkDocs-label" aria-hidden="true">
    <div class="modal-dialog modal-lg custom-modal" role="document">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-listLinkDocs-label">Еще входящие документы, связанные как
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
            <div id="wrap-linkedIncoming-showFiles" style="display: none">
                <p class="mb-1"><strong>Прикрепленные файлы к письму № 1-2/<span id="linkedIncoming-docID"></span></strong></p>
                <div id="outputArea-linkedIncoming-showFiles" class="mb-2"></div>
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
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-outgoing-modal-listAdditionalLinkDocs.css">
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
                    <div class="space20"></div>
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
                                <th>Объект</th>
                                <th>Заказчик</th>
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
?>