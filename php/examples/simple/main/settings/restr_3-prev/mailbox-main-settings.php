<?php
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
?>
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<script type="text/javascript" language="javascript" class="">
//
var reqField_loadSettingsData = {
    loadSettingsData: function(response) {}
};
var reqField_saveSettingsData = {
    saveSettingsData: function(response) {}
};
//
function ajaxRequest_loadSettingsData(action, userID, responseHandler) {
    var res = false;
    $('#uploadSettings-spinner').fadeIn();

    var use_personalSettings = $('input[data-fieldname="use_personalSettings"]').is(':checked') ? 1 : 0;
    var use_pushMessages = $('input[data-fieldname="use_pushMessages"]').is(':checked') ? 1 : 0;
    var incoming_showDashboard = $('input[data-fieldname="incoming_showDashboard"]').is(':checked') ? 1 : 0;
    var outgoing_showDashboard = $('input[data-fieldname="outgoing_showDashboard"]').is(':checked') ? 1 : 0;
    var mailbox_showLegend = $('input[data-fieldname="mailbox_showLegend"]').is(':checked') ? 1 : 0;
    var outgoing_kodSenderDefault = $('select[data-fieldname="outgoing_kodSenderDefault"]').val();
    var outgoing_kodRecipientOrgDefault = $('select[data-fieldname="outgoing_kodRecipientOrgDefault"]').val();
    var outgoing_kodIspolDefault = $('select[data-fieldname="outgoing_kodIspolDefault"]').val();
    var outgoing_setMeIspolOnStart = $('input[data-fieldname="outgoing_setMeIspolOnStart"]').is(':checked') ? 1 : 0;
    var outgoing_textAboutDefault = $('input[data-fieldname="outgoing_textAboutDefault"]').val();
    var incoming_subscribeWeekReminder = $('input[data-fieldname="incoming_subscribeWeekReminder"]').is(':checked') ?
        1 : 0;
    var incoming_setControlIspolOnStart = $('input[data-fieldname="incoming_setControlIspolOnStart"]').is(':checked') ?
        1 : 0;
    var incoming_setDeadlineOnStart = $('input[data-fieldname="incoming_setDeadlineOnStart"]').is(':checked') ?
        1 : 0;
    var outgoing_setControlIspolOnStart = $('input[data-fieldname="outgoing_setControlIspolOnStart"]').is(':checked') ?
        1 : 0;
    var outgoing_setDeadlineOnStart = $('input[data-fieldname="outgoing_setDeadlineOnStart"]').is(':checked') ?
        1 : 0;


    // Fire off the request_check_sysStatus to /form.php
    request_loadSettingsData = $.ajax({
        url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_SETTINGS_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxSettings-loadSettingsData.php',
        type: "post",
        cache: false,
        data: {
            action: action,
            userID: userID,
            use_personalSettings: use_personalSettings,
            use_pushMessages: use_pushMessages,
            incoming_showDashboard: incoming_showDashboard,
            outgoing_showDashboard: outgoing_showDashboard,
            mailbox_showLegend: mailbox_showLegend,
            outgoing_kodSenderDefault: outgoing_kodSenderDefault,
            outgoing_kodRecipientOrgDefault: outgoing_kodRecipientOrgDefault,
            outgoing_kodIspolDefault: outgoing_kodIspolDefault,
            outgoing_setMeIspolOnStart: outgoing_setMeIspolOnStart,
            outgoing_textAboutDefault: outgoing_textAboutDefault,
            incoming_subscribeWeekReminder: incoming_subscribeWeekReminder,
            incoming_setControlIspolOnStart: incoming_setControlIspolOnStart,
            incoming_setDeadlineOnStart: incoming_setDeadlineOnStart,
            outgoing_setControlIspolOnStart: outgoing_setControlIspolOnStart,
            outgoing_setDeadlineOnStart: outgoing_setDeadlineOnStart,
        },
        success: reqField_loadSettingsData[responseHandler]
    });
    // Callback handler that will be called on success
    request_loadSettingsData.done(function(response, textStatus, jqXHR) {
        // res = response.replace(new RegExp("\\r?\\n", "g"), "");
        res = response;
        console.log(res);
        var host = window.location.protocol + "//" + window.location.host;

        var x = res.split(' | ');
        var x1 = (checkVal(x[0]) === 1) ? x[0] : "";
        var x2 = (checkVal(x[1]) === 1) ? x[1] : "";
        var x3 = (checkVal(x[2]) === 1) ? x[2] : "";
        var x4 = (checkVal(x[3]) === 1) ? x[3] : "";
        var x5 = (checkVal(x[4]) === 1) ? x[4] : "";
        // 
        var params1 = x1.split('///');
        var use_personalSettings = params1[0];
        var use_pushMessages = params1[1];
        var incoming_showDashboard = params1[2];
        var outgoing_showDashboard = params1[3];
        var mailbox_showLegend = params1[4];
        var outgoing_kodSenderDefault = params1[5];
        var outgoing_kodRecipientOrgDefault = params1[6];
        var outgoing_kodIspolDefault = params1[7];
        var outgoing_setMeIspolOnStart = params1[8];
        var outgoing_textAboutDefault = params1[9];
        var incoming_subscribeWeekReminder = params1[10];
        var incoming_setControlIspolOnStart = params1[11];
        var incoming_setDeadlineOnStart = params1[12];
        var outgoing_setControlIspolOnStart = params1[13];
        var outgoing_setDeadlineOnStart = params1[14];
        // 
        if (response != "-1" && response != "-2" && response != "0") {
            if (use_pushMessages == 1) {
                if (!$('input[data-fieldname="use_pushMessages"]').is(':checked')) {
                    $('#use_pushMessages').prop('checked', true);
                }
            } else {
                if ($('input[data-fieldname="use_pushMessages"]').is(':checked')) {
                    $('#use_pushMessages').prop('checked', false);
                }
            }

            if (incoming_showDashboard == 1) {
                if (!$('input[data-fieldname="incoming_showDashboard"]').is(':checked')) {
                    $('#incoming_showDashboard').prop('checked', true);
                }
            } else {
                if ($('input[data-fieldname="incoming_showDashboard"]').is(':checked')) {
                    $('#incoming_showDashboard').prop('checked', false);
                }
            }

            if (outgoing_showDashboard == 1) {
                if (!$('input[data-fieldname="outgoing_showDashboard"]').is(':checked')) {
                    $('#outgoing_showDashboard').prop('checked', true);
                }
            } else {
                if ($('input[data-fieldname="outgoing_showDashboard"]').is(':checked')) {
                    $('#outgoing_showDashboard').prop('checked', false);
                }
            }

            if (mailbox_showLegend == 1) {
                if (!$('input[data-fieldname="mailbox_showLegend"]').is(':checked')) {
                    $('#mailbox_showLegend').prop('checked', true);
                }
            } else {
                if ($('input[data-fieldname="mailbox_showLegend"]').is(':checked')) {
                    $('#mailbox_showLegend').prop('checked', false);
                }
            }

            if (outgoing_kodSenderDefault != "") {
                $('select[data-fieldname="outgoing_kodSenderDefault"] option[value=' +
                    outgoing_kodSenderDefault +
                    ']').prop('selected', true);
            } else {
                $('select[data-fieldname="outgoing_kodSenderDefault"] option[value=000000000000000]').prop(
                    'selected', true);
            }

            if (outgoing_kodRecipientOrgDefault != "") {
                $('select[data-fieldname="outgoing_kodRecipientOrgDefault"]').val(
                    outgoing_kodRecipientOrgDefault);
                $('select[data-fieldname="outgoing_kodRecipientOrgDefault"]').select2({
                    theme: 'bootstrap4'
                }).trigger('change');
            } else {
                $('select[data-fieldname="outgoing_kodRecipientOrgDefault"]').val(null);
                $('select[data-fieldname="outgoing_kodRecipientOrgDefault"]').select2().trigger('change');
            }

            if (outgoing_kodIspolDefault != "") {
                $('select[data-fieldname="outgoing_kodIspolDefault"] option[value=' +
                    outgoing_kodIspolDefault +
                    ']').prop('selected', true);
            } else {
                $('select[data-fieldname="outgoing_kodIspolDefault"] option[value=000000000000000]')
                    .prop(
                        'selected', true);
            }

            if (outgoing_setMeIspolOnStart == 1) {
                if (!$('input[data-fieldname="outgoing_setMeIspolOnStart"]').is(':checked')) {
                    $('#chkOnSetMeIpsolOnStart').prop('checked', true);
                }
            } else {
                if ($('input[data-fieldname="outgoing_setMeIspolOnStart"]').is(':checked')) {
                    $('#chkOnSetMeIpsolOnStart').prop('checked', false);
                }
            }

            if (outgoing_textAboutDefault != "") {
                $('input[data-fieldname="outgoing_textAboutDefault"]').val(outgoing_textAboutDefault);
            } else {
                $('input[data-fieldname="outgoing_textAboutDefault"]').val('');
            }

            if (incoming_subscribeWeekReminder == 1) {
                if (!$('input[data-fieldname="incoming_subscribeWeekReminder"]').is(':checked')) {
                    $('#incoming_subscribeWeekReminder').prop('checked', true);
                }
            } else {
                if ($('input[data-fieldname="incoming_subscribeWeekReminder"]').is(':checked')) {
                    $('#incoming_subscribeWeekReminder').prop('checked', false);
                }
            }

            if (incoming_setControlIspolOnStart == 1) {
                if (!$('input[data-fieldname="incoming_setControlIspolOnStart"]').is(':checked')) {
                    $('#incoming_setControlIspolOnStart').prop('checked', true);
                }
            } else {
                if ($('input[data-fieldname="incoming_setControlIspolOnStart"]').is(':checked')) {
                    $('#incoming_setControlIspolOnStart').prop('checked', false);
                }
            }

            if (incoming_setDeadlineOnStart == 1) {
                if (!$('input[data-fieldname="incoming_setDeadlineOnStart"]').is(':checked')) {
                    $('#incoming_setDeadlineOnStart').prop('checked', true);
                }
            } else {
                if ($('input[data-fieldname="incoming_setDeadlineOnStart"]').is(':checked')) {
                    $('#incoming_setDeadlineOnStart').prop('checked', false);
                }
            }

            if (outgoing_setControlIspolOnStart == 1) {
                if (!$('input[data-fieldname="outgoing_setControlIspolOnStart"]').is(':checked')) {
                    $('#outgoing_setControlIspolOnStart').prop('checked', true);
                }
            } else {
                if ($('input[data-fieldname="outgoing_setControlIspolOnStart"]').is(':checked')) {
                    $('#outgoing_setControlIspolOnStart').prop('checked', false);
                }
            }

            if (outgoing_setDeadlineOnStart == 1) {
                if (!$('input[data-fieldname="outgoing_setDeadlineOnStart"]').is(':checked')) {
                    $('#outgoing_setDeadlineOnStart').prop('checked', true);
                }
            } else {
                if ($('input[data-fieldname="outgoing_setDeadlineOnStart"]').is(':checked')) {
                    $('#outgoing_setDeadlineOnStart').prop('checked', false);
                }
            }
        } else {

        }
        // request is complete
        $('#uploadSettings-spinner').fadeOut();
    });
    // Callback handler that will be called on failure
    request_loadSettingsData.fail(function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus);
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
    // Callback handler that will be called regardless
    // if the request_check_sysStatus failed or succeeded
    request_loadSettingsData.always(function() {

    });
}
// 








var reqField_settingsUI = {
    showIspolOnly: function(response) {}
};
//
function ajaxRequest_parameterSaveToDB(responseHandler, objectID) {
    var fieldName = $(objectID).attr('data-fieldname');
    var userID = <?php echo $_SESSION['id']; ?>;

    // Fire off the request_addItem to /form.php
    request_settingsUI = $.ajax({
        url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_SETTINGS_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxSettings-parameterSaveToDB.php',
        type: "post",
        cache: false,
        data: {
            userID: userID,
            parameterValue: parameterValue
        },
        success: reqField_settingsUI[responseHandler]
    });
    // Callback handler that will be called on success
    request_settingsUI.done(function(response, textStatus, jqXHR) {
        res = response.replace(new RegExp("\\r?\\n", "g"), "");
    });
    // Callback handler that will be called on failure
    request_settingsUI.fail(function(jqXHR, textStatus, errorThrown) {
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
    // Callback handler that will be called regardless
    // if the request_addItem failed or succeeded
    request_settingsUI.always(function() {

    });
}
//
function ajaxRequest_settingsLoadFromDB(responseHandler) {
    var _userid = <?php echo $_SESSION['id']; ?>;

    // Fire off the request_addItem to /form.php
    request_showIspolOnly = $.ajax({
        url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_SETTINGS_WORKPATH . __MAIL_RESTR3; ?>/process/ajaxrequests/ajaxReq-mailboxSettings-settingsLoadFromDB.php',
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
    request_showIspolOnly.always(function() {

    });
}



//
</script>
<?php
#
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
#
?>
<style>
#mail-settings-block {
    font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
}

/* not active */
.nav-pills .custom-pill .nav-link:not(.active) {
    background-color: transparent;
    border-radius: 4px;
    color: #999 !important;
}

.nav-pills .custom-pill .nav-link:not(.active):hover {
    /* color: #111; */
}

/* active (faded) */
.nav-pills .custom-pill .nav-link.active {
    background-color: #f1f1f1;
    border-radius: 4px;
    color: #111 !important;
}

#myIcon {
    position: fixed;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    background: #fff;
    z-index: 1001;
}

button#updateSettings.btn:focus {
    box-shadow: none;
}

.select2-container .select2-selection--single,
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px;
}
</style>
<?php
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
?>
<div class="container-xl mt-5">
    <div id="mail-settings-center-block" class="w-100">

        <ul class="nav nav-pills bg-transparent border border-grey text-dark rounded-lg px-3 py-3 mb-5" role="tablist">
            <li class="nav-item custom-pill">
                <a class="nav-link active" data-toggle="pill" href="#settings-common">Интерфейс</a>
            </li>
            <li class="nav-item custom-pill">
                <a class="nav-link" data-toggle="pill" href="#settings-incoming">Входящие</a>
            </li>
            <li class="nav-item custom-pill">
                <a class="nav-link" data-toggle="pill" href="#settings-outgoing">Исходящие</a>
            </li>
            <li class="nav-item custom-pill">
                <a class="nav-link" data-toggle="pill" href="#settings-feedback">Уведомления</a>
            </li>
            <li class="nav-item custom-pill ml-auto">
                <span id="uploadSettings-spinner" class="spinner-border spinner-border-sm text-danger mr-1"
                      role="status" style="position:relative; top:0.1rem">
                    <span class="sr-only">Loading...</span>
                </span>
                <button id="updateSettings" type="button" class="btn btn-outline-danger">Сохранить</button>
            </li>
        </ul>
        <div class="tab-content d-flex flex-column">
            <div class="tab-pane container active" id="settings-common" role="tabpanel"
                 aria-labelledby="settings-common-tab" style="min-height:480px">
                <div class="row">
                    <h4 class="text-info mb-3 w-100">Интерфейс</h4>
                    <div class="col">
                        <div class="col-12">
                            <div class="align-items-center custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" value="" id="chkOnSetLightTheme"
                                       data-fieldname="chkOnSetLightTheme" disabled>
                                <label class="custom-control-label" for="chkOnSetLightTheme">
                                    Включить светлую тему <span class="text-danger mx-1">Скоро</span></label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="align-items-center custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="mailbox_showLegend"
                                       data-fieldname="mailbox_showLegend">
                                <label class="custom-control-label" for="mailbox_showLegend">Показать/скрыть блок с
                                    описание
                                    иконок, символов и обозначений в списках входящей и исходящей почты</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane container" id="settings-incoming" role="tabpanel"
                 aria-labelledby="settings-incoming-tab" style="min-height:480px">
                <div class="row mb-5">
                    <h4 class="text-info mb-3 w-100">Интерфейс</h4>
                    <div class="col">
                        <div class="col-12">
                            <div class="align-items-center custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="incoming_showDashboard"
                                       data-fieldname="incoming_showDashboard">
                                <label class="custom-control-label" for="incoming_showDashboard">Показать/скрыть
                                    доску
                                    (dashboard) с
                                    информационными карточками над основным списком входящих</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <h4 class="text-info mb-3 w-100">Функционал</h4>
                    <div class="col">
                        <div class="col-12">
                            <div class="align-items-center custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" value=""
                                       id="incoming_setControlIspolOnStart"
                                       data-fieldname="incoming_setControlIspolOnStart">
                                <label class="custom-control-label" for="incoming_setControlIspolOnStart">
                                    Включить контроль исполнения по-умолчанию (можно убрать в процессе
                                    редактирования)</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="align-items-center custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" value=""
                                       id="incoming_setDeadlineOnStart" data-fieldname="incoming_setDeadlineOnStart">
                                <label class="custom-control-label" for="incoming_setDeadlineOnStart">
                                    Включить дедлайн по-умолчанию сроком в 2 недели (можно убрать или изменить в
                                    процессе
                                    редактирования)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane container" id="settings-outgoing" role="tabpanel"
                 aria-labelledby="settings-outgoing-tab" style="min-height:480px">
                <div class="row mb-5">
                    <h4 class="text-info mb-3 w-100">Интерфейс</h4>
                    <div class="col-12">
                        <div class="col">
                            <div class="align-items-center custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="outgoing_showDashboard"
                                       data-fieldname="outgoing_showDashboard">
                                <label class="custom-control-label" for="outgoing_showDashboard">Показать/скрыть доску
                                    (dashboard) с информационными карточками над основным списком исходящих</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-5">
                    <h4 class="text-info mb-3 w-100">Функционал</h4>
                    <div class="col">
                        <div class="col-12">
                            <div class="align-items-center custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" value=""
                                       id="outgoing_setControlIspolOnStart"
                                       data-fieldname="outgoing_setControlIspolOnStart">
                                <label class="custom-control-label" for="outgoing_setControlIspolOnStart">
                                    Включить контроль исполнения по-умолчанию (можно убрать в процессе
                                    редактирования)</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="align-items-center custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" value=""
                                       id="outgoing_setDeadlineOnStart" data-fieldname="outgoing_setDeadlineOnStart">
                                <label class="custom-control-label" for="outgoing_setDeadlineOnStart">
                                    Включить дедлайн по-умолчанию сроком в 2 недели (можно убрать или изменить в
                                    процессе
                                    редактирования)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <h4 class="text-info mb-3 w-100">Предустановки "по умолчанию" для быстрого создания исходящего
                        письма<span data-toggle="popover"
                              data-content="<p>Настройки ниже являются обязательными при создании исходящего документа. Установив эти настройки заранее вы сможете создавать его в два клика зарезервировав номер, а потом уже внести необходимые изменения.</p>"><sup><i
                                   class="fa-solid fa-exclamation-circle fa-xs pl-1 text-danger"></i></sup></span></h4>
                    <div class="col-4">
                        <div class="align-items-center mb-3">
                            <label class="" for="selRecipientOrg"><b>Организация-получатель по
                                    умолчанию</b></label>
                            <select class="form-control" id="selRecipientOrg"
                                    data-fieldname="outgoing_kodRecipientOrgDefault">
                                <?php
                                $_QRY = mysqlQuery(" SELECT kodcontragent, nameshort, namefull FROM sp_contragents WHERE useinmail = '1' AND koddel != '99' ORDER BY namefull ASC ");
                                while ($_ROW = mysqli_fetch_assoc($_QRY)) {
                                ?>
                                <option value='<?php echo $_ROW["kodcontragent"]; ?>'>
                                    <?php echo $_ROW["namefull"] != "" ? $_ROW["namefull"] : $_ROW["nameshort"]; ?>
                                </option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="align-items-center mb-3">
                            <label class="" for="selSender"><b>Отправитель по умолчанию</b></label>
                            <select class="form-control" id="selSender" data-fieldname="outgoing_kodSenderDefault">
                                <?php
                                $_QRY = mysqlQuery(" SELECT kodzayvtel, namezayvfio, namezayvtel FROM mailbox_sp_users WHERE status_zayvtel = '1' AND namezayvfio != '' ORDER BY namezayvfio ASC ");
                                while ($_ROW = mysqli_fetch_assoc($_QRY)) {
                                ?>
                                <option value='<?php echo $_ROW["kodzayvtel"]; ?>'>
                                    <?php echo $_ROW["namezayvtel"] != "" ? $_ROW["namezayvtel"] : $_ROW["namezayvfio"]; ?>
                                </option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="align-items-center mb-3">
                            <label class="" for="selIspolout"><b>Исполнитель по умолчанию</b></label>
                            <select class="form-control" id="selIspolout" data-fieldname="outgoing_kodIspolDefault">
                                <?php
                                $_QRY = mysqlQuery(" SELECT kodispolout, namezayvfio, namezayvtel FROM mailbox_sp_users WHERE status_ispolout = '1' AND namezayvfio != '' ORDER BY namezayvfio ASC ");
                                while ($_ROW = mysqli_fetch_assoc($_QRY)) {
                                ?>
                                <option value='<?php echo $_ROW["kodispolout"]; ?>'>
                                    <?php echo $_ROW["namezayvtel"] != "" ? $_ROW["namezayvtel"] : $_ROW["namezayvfio"]; ?>
                                </option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 my-3">
                        <div class="form-group">
                            <label class="" for="textAbout"><b>Тема документа по умолчанию</b></label>
                            <input class="form-control" id="textAbout" data-fieldname="outgoing_textAboutDefault"
                                   type="text" placeholder="Описание исходящего письма 'по умолчанию'">
                        </div>
                        <button id="btnClear-outgoingDefaults" type="button" class="btn btn-outline-danger">Очистить
                            предустановки</button>

                    </div>
                </div>
            </div>
            <div class="tab-pane container" id="settings-feedback" role="tabpanel"
                 aria-labelledby="settings-feedback-tab" style="min-height:480px">
                <div class="row">
                    <h4 class="text-info mb-3 w-100">Управление уведомлениями и подписками на рассылки</h4>
                    <div class="col-12">
                        <div class="col">
                            <div class="align-items-center custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="use_pushMessages"
                                       data-fieldname="use_pushMessages">
                                <label class="custom-control-label" for="use_pushMessages">Разрешить push-уведомления
                                    сервиса</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="col">
                            <div class="align-items-center custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" value=""
                                       id="incoming_subscribeWeekReminder"
                                       data-fieldname="incoming_subscribeWeekReminder">
                                <label class="custom-control-label" for="incoming_subscribeWeekReminder">
                                    Подписаться на еженедельную (понедельник, 10:15) рассылку списка
                                    <u><b>неисполненных</b></u> входящих
                                    документов, стоящих на контроле исполнения (КИ), по которым срок исполнения уже
                                    истёк или истечет в ближайшую неделю.
                                    <span data-toggle="popover"
                                          data-content="<p>Для проверки берутся документы на старше <u>одного месяца</u>. Не каждый документ, попадающий в этот список, действительно является не исполненным. Возможно просто ответственный не поставил отметку об исполнении.</p>"><sup><i
                                               class="fa-solid fa-exclamation-circle fa-xs text-danger"></i></sup></span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
?>
<script type="text/javascript" language="javascript" class="init">
$(window).on("load", function() {
    var userID = '<?php echo $_SESSION['id']; ?>';
    ajaxRequest_loadSettingsData('load', userID, 'loadSettingsData');

    $(document).on("click", "button#updateSettings", function() {
        var userID = '<?php echo $_SESSION['id']; ?>';
        console.log(userID);
        ajaxRequest_loadSettingsData('save', userID, 'loadSettingsData');
    });

    $(document).on("click", "button#btnClear-outgoingDefaults", function() {
        $("#selRecipientOrg").val(null).trigger('change');
        $("#selSender").val('000000000000000');
        $("#selIspolout").val('000000000000000');
        $("#textAbout").val('');
    });

    $('#selRecipientOrg').select2({
        theme: 'bootstrap4',
        allowClear: true,
        placeholder: "Выберите организацию",
        initSelection: function(element, callback) {}
    });
    $('span[data-toggle="popover"]')
        .popover({
            html: true,
            trigger: 'hover',
            placement: 'top',
            customClass: 'mail-popover',
        });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"
        integrity="sha256-AFAYEOkzB6iIKnTYZOdUf9FFje6lOTYdwRJKwTN5mks=" crossorigin="anonymous"></script>