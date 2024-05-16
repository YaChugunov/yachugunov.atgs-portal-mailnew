<?php
?>
<script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH; ?>/js/userSettings-control.js">
</script>
<script type="text/javascript" language="javascript" class="init" src="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH; ?>/js/userMessages-control.js">
</script>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css">
<script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>


<script type="text/javascript" language="javascript" class="">
    //
    var sessionID = '<?php echo session_id(); ?>';
    var userID_session = '<?php echo $_SESSION['id']; ?>';
    var userLogin_session = '<?php echo $_SESSION['login']; ?>';
    //
    const showPushMessage = async (serviceName, title, message, mode, type, delay) => {
        $('#mailnew-toast div.header-text').text(title);
        $('#mailnew-toast div.main-text').html(message);
        $('#mailnew-toast div.toast-timestamp').text(moment().format(
            'DD.MM.YYYY HH:mm:ss'));
        $('#' + serviceName + '-toast>div.toast').attr('data-delay', delay * 1000);
        $('#' + serviceName + '-toast>div.toast').toast('show');
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
    // 
    // ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
    // Функция проверки текущей PHP сессии
    //
    function ajaxRequest_checkSessionAsync(sessionID) {
        var result = false;
        $.ajax({
            async: false,
            cache: false,
            type: "post",
            url: "<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/_assets/user-phpscript/ajaxReq-checkPHPSession.php",
            data: {
                sessionID: sessionID
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
    // Функция проверки текущей PHP сессии
    //
    var reqField_checkSession = {
        chksession: function(response) {}
    };

    function ajaxRequest_checkSession(data, responseHandler) {
        var response = false;

        // Fire off the request to /form.php
        request = $.ajax({
            url: "<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/_assets/user-phpscript/ajaxReq-checkPHPSession.php",
            type: "post",
            cache: false,
            data: {
                sessionID: data
            },
            success: reqField_checkSession[responseHandler]
        });
        // Callback handler that will be called on success
        request.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
            if (res == '0') {
                // $("#sessionStatus_msg").html("Ваша сессия закончена. Войдите в систему снова.");
                // $("#sessionStatus-modalBox").modal("show");
                console.log('ajaxRequest_checkSession >>>', res, 'Сессия закончена');
            } else if (res == '-1') {
                // $("#sessionStatus_msg").html("Вы в системе, но ваша сессия устарела. Просто обновите текущую страницу.");
                // $("#sessionStatus-modalBox").modal("show");
                console.log('ajaxRequest_checkSession >>>', res, 'Сессия устарела');
            } else {
                console.log('ajaxRequest_checkSession >>>', res,
                    'Сессия крепка и стабильна! :)');
            }
        });
        // Callback handler that will be called on failure
        request.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request failed or succeeded
        request.always(function() {});
    }

    setInterval(function() {
        ajaxRequest_checkSession(sessionID, 'chksession');
        if (!ajaxRequest_checkSessionAsync(sessionID)) {
            console.log('ajaxRequest_checkSessionAsync', ajaxRequest_checkSessionAsync(sessionID));
            $('#modal-alarmMessage .modal-message').text(
                'Ваша сессия устарела. Закройте это окно и авторизуйтесь в сервисе снова. Если вы уже повторно авторизовались в этом браузере (например в другой вкладке), то эта страница просто перезагрузится в новой сессии.'
            );
            $('#modal-alarmMessage .modal-footer button').text('Закрыть и авторизоваться');
            $('#modal-alarmMessage').modal('show');
        } else {
            $('#modal-alarmMessage .modal-message').text('');
            $('#modal-alarmMessage .modal-footer button').text('');
            $('#modal-alarmMessage').modal('hide');
        }
    }, 30000);
</script>

<div id="mailnew" class="">
    <?php
    if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
        if (checkUserAuthorization_defaultDB($_SESSION['login'], $_SESSION['password']) == -1) {
            // Редирект на главную страницу
    ?>
            <meta http-equiv="refresh" content="0; url=<?php echo __ROOT; ?>">
        <?php
        } else {
            // $_QRY_getUser = mysqlQuery("SELECT * FROM mailbox_userSettingsUI WHERE ID = '{$_SESSION['id']}'");
            // $_ROW_getUser = mysqli_fetch_array($_QRY_getUser);
            // $use_lightTheme = isset($_QRY_getUser) ? $_ROW_getUser['use_lightTheme'] : 0;

            // При удачном входе пользователю выдается все, что расположено НИЖЕ звездочек
            // ************************************************************************************
            if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 5, 1) == 1) {
                if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
                    define('__MAIL_RESTR', '/restr_5.test');
                    define('__MAIL_RESTR5', '/restr_5.test');
                } else {
                    define('__MAIL_RESTR', '/restr_5');
                    define('__MAIL_RESTR5', '/restr_5');
                }
            } elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 4, 1) == 1) {
                if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
                    define('__MAIL_RESTR', '/restr_4.test');
                    define('__MAIL_RESTR4', '/restr_4.test');
                } else {
                    define('__MAIL_RESTR', '/restr_4');
                    define('__MAIL_RESTR4', '/restr_4');
                }
            } elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
                if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
                    define('__MAIL_RESTR', '/restr_3.test');
                    define('__MAIL_RESTR3', '/restr_3.test');
                } else {
                    define('__MAIL_RESTR', '/restr_3');
                    define('__MAIL_RESTR3', '/restr_3');
                }
            } elseif (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 1) == 1) {
                if ((__MAIL_TESTMODE_ON == TRUE || __MAIL_TESTMODE_ON == 1) && checkUserInTestMode_defaultDB($_SESSION['id'], 'mailnew') == 1 && __MAIL_TESTMODE_TYPE < 3) {
                    define('__MAIL_RESTR', '/restr_2.test');
                    define('__MAIL_RESTR2', '/restr_2.test');
                } else {
                    define('__MAIL_RESTR', '/restr_2');
                    define('__MAIL_RESTR2', '/restr_2');
                }
            }
        ?>
            <div id="mailnew-top" class="text-start">
                <?php include 'mailnew-topblock.php'; ?>
            </div>

            <div id="mailnew-center" class="container-xl mt-5">
                <?php
                if (isset($_GET['type']) && !empty($_GET['type'])) {
                    if ($_GET['type'] == 'main') {
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_WORKPATH . "/mailbox-main.php";
                    } elseif ($_GET['type'] == 'in') {
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_WORKPATH  . "/mailbox-incoming.php";
                    } elseif ($_GET['type'] == 'out') {
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_WORKPATH  . "/mailbox-outgoing.php";
                    } elseif ($_GET['type'] == 'settings') {
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_WORKPATH  . "/mailbox-settings.php";
                    } elseif ($_GET['type'] == 'help') {
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_WORKPATH  . "/mailbox-help.php";
                    } elseif ($_GET['type'] == 'devlog') {
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_WORKPATH  . "/mailbox-devlog.php";
                    } else {
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_WORKPATH . "/mailbox-main.php";
                    }
                } elseif (isset($_GET['mode']) && !empty($_GET['mode']) && $_GET['mode'] == 'profile') {
                    include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_WORKPATH . "/profile/mailbox-main-profile.php";
                } else {
                    include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_WORKPATH . "/mailbox-main.php";
                }
                ?>
            </div>

            <div id="mailnew-bottom" class="container-xl mt-5"></div>

</div>

<div aria-live="polite" aria-atomic="true" class="toasts-block position-fixed" style="z-index:9; opacity:1; top:110px; left:0">
    <!-- Разместите его: 
    `.toast-container` для интервала между тостами
    `top-0` и `end-0` для размещения всплывающих уведомлений в правом верхнем углу
    `.p-3`, чтобы тосты не прилипали к краю контейнера -->
    <div class="p-3">

        <div id="mailnew-toast-1" class="d-none">
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-toast-id="" data-autohide="false">
                <div class="toast-header">
                    <strong class="mr-auto">
                        <div class="header-text"></div>
                    </strong>
                    <button type="button" class="close" data-dismiss="toast"><span class="text-white small">x</span></button>
                </div>
                <div class="toast-body">
                    <div class="main-text mb-1"></div>
                    <div class="sub-text1"></div>
                    <div class="sub-text2"></div>
                    <div class="sub-text3"></div>
                    <div class="special-text my-1"></div>
                    <div class="link-text1"></div>
                    <div class="link-text2"></div>
                    <div class="undermain-text">
                        <div class="toast-timestamp text-right"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="mailnew-toast-2" class="d-none">
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-toast-id="" data-autohide="false">
                <div class="toast-header">
                    <strong class="mr-auto">
                        <div class="header-text"></div>
                    </strong>
                    <button type="button" class="close" data-dismiss="toast"><span class="text-white small">x</span></button>
                </div>
                <div class="toast-body">
                    <div class="main-text mb-1"></div>
                    <div class="sub-text1"></div>
                    <div class="sub-text2"></div>
                    <div class="sub-text3"></div>
                    <div class="special-text my-1"></div>
                    <div class="link-text1"></div>
                    <div class="link-text2"></div>
                    <div class="undermain-text">
                        <div class="toast-timestamp text-right"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="mailnew-toast-3" class="d-none">
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-toast-id="" data-autohide="false">
                <div class="toast-header">
                    <strong class="mr-auto">
                        <div class="header-text"></div>
                    </strong>
                    <button type="button" class="close" data-dismiss="toast"><span class="text-white small">x</span></button>
                </div>
                <div class="toast-body">
                    <div class="main-text mb-1"></div>
                    <div class="sub-text1"></div>
                    <div class="sub-text2"></div>
                    <div class="sub-text3"></div>
                    <div class="special-text my-1"></div>
                    <div class="link-text1"></div>
                    <div class="link-text2"></div>
                    <div class="undermain-text">
                        <div class="toast-timestamp text-right"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="mailnew-toast-4" class="d-none">
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-toast-id="" data-autohide="false">
                <div class="toast-header">
                    <strong class="mr-auto">
                        <div class="header-text"></div>
                    </strong>
                    <button type="button" class="close" data-dismiss="toast"><span class="text-white small">x</span></button>
                </div>
                <div class="toast-body">
                    <div class="main-text mb-1"></div>
                    <div class="sub-text1"></div>
                    <div class="sub-text2"></div>
                    <div class="sub-text3"></div>
                    <div class="special-text my-1"></div>
                    <div class="link-text1"></div>
                    <div class="link-text2"></div>
                    <div class="undermain-text">
                        <div class="toast-timestamp text-right"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="mailnew-toast-5" class="d-none">
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-toast-id="" data-autohide="false">
                <div class="toast-header">
                    <strong class="mr-auto">
                        <div class="header-text"></div>
                    </strong>
                    <button type="button" class="close" data-dismiss="toast"><span class="text-white small">x</span></button>
                </div>
                <div class="toast-body">
                    <div class="main-text mb-1"></div>
                    <div class="sub-text1"></div>
                    <div class="sub-text2"></div>
                    <div class="sub-text3"></div>
                    <div class="special-text my-1"></div>
                    <div class="link-text1"></div>
                    <div class="link-text2"></div>
                    <div class="undermain-text">
                        <div class="toast-timestamp text-right"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="loadedData-help-modal" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog light-theme modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadedData-help-output" class=""></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="userSettings-modal" data-keyboard="false" data-backdrop="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" style="margin-right:-0.5rem !important; margin-top:7.5rem !important">
        <div class="modal-content modal-customstyle-1">
            <div class="modal-body">
                <h3 class="text-light">Настройка интерфейса почты</h3>
                <div id="userSettings-block" class="mb-4">
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="userSettingsSwitch-usePush" data-dbfield="use_pushMessages">
                        <label class="custom-control-label" for="userSettingsSwitch-usePush">Разрешить push-уведомления
                            сервиса</label>
                    </div>
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="userSettingsSwitch-showIncDashboard" data-dbfield="incoming_showDashboard">
                        <label class="custom-control-label" for="userSettingsSwitch-showIncDashboard">Показать/скрыть
                            доску (dashboard) над списком входящих</label>
                    </div>
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="userSettingsSwitch-showOutDashboard" data-dbfield="outgoing_showDashboard">
                        <label class="custom-control-label" for="userSettingsSwitch-showOutDashboard">Показать/скрыть
                            доску (dashboard) над списком исходящих</label>
                    </div>
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="userSettingsSwitch-showLegend" data-dbfield="mailbox_showLegend">
                        <label class="custom-control-label" for="userSettingsSwitch-showLegend">Показать/скрыть
                            блок с описание иконок, символов и обозначений в списках входящей и исходящей почты</label>
                    </div>
                    <?php // if ($_SESSION['id'] === '999' && $_SESSION['login'] === 'yachugunov') { 
                    ?>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="userSettingsSwitch-useLightTheme" data-dbfield="use_lightTheme">
                        <label class="custom-control-label" for="userSettingsSwitch-useLightTheme">Светлая тема
                            (страница сразу обновится)</label>
                    </div>
                    <?php // } 
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ListMessages-modal" data-keyboard="false" data-backdrop="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable fixed-top" style="margin-right:-0.5rem !important; margin-top:0.5rem !important; max-height: calc(100% - 1rem) !important">
        <div class="modal-content modal-customstyle-1" style="max-height:100% !important">
            <div class="modal-body">
                <h3 class="text-light">Ваши уведомления</h3>
                <div id="listMessages-output" class=""></div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-alarmMessage" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modal-alarmMessage-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow border-0 bg-white text-dark" style="font-family:'Stolzl Book',sans-serif">
            <div class="modal-body pb-0">
                <div class="container-fluid">
                    <p class="modal-message text-center" style="font-size:1.0rem"></p>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-top-0 pt-0">
                <button type="button" class="btn btn-danger text-white btn-sm" data-dismiss="modal" onclick="location.reload()">Кнопка</button>
            </div>
        </div> <!-- end of modal content -->
    </div>
</div> <!-- end of modal -->

<?php
            if ($_SESSION['id'] != '999' && $_SESSION['login'] != 'yachugunov') {
                include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_WORKPATH  . "/mailnew-updates.php";
            } else {
                include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_WORKPATH  . "/mailnew-updates-test.php";
            }
            // ************************************************************************************
            // При удачном входе пользователю выдается все, что расположено ВЫШЕ звездочек
        }
    } else {
        # Редирект на главную страницу
?>
<meta http-equiv="refresh" content="0; url=<?php echo __ROOT; ?>">
<?php
    }
?>
<script type="text/javascript" language="javascript">
    $(window).on("load", function() {

        $("#listMessages-icon").click(function() {
            ajaxRequest_getListMessages('getListMessages');
        });
        $("#listMessages-sideblock-icon").click(function() {
            // ajaxRequest_getListMessagesSideblock('getListMessages');
        });
        $("#userSettings-icon").click(function() {
            getUserSettings();
        });

    });
    // 
    // 
    $(document).ready(function() {

        var userID_session = '<?php echo $_SESSION['id']; ?>';
        var userLogin_session = '<?php echo $_SESSION['login']; ?>';


        $('#switchTheme-icon').on('click', function(e) {
            var useLightTheme = <?php echo $use_lightTheme; ?>;
            var switchTheme = $('#userSettings-block input[id="userSettingsSwitch-useLightTheme"]');
            setfield = switchTheme.attr('data-dbfield');
            setval = (useLightTheme === 1) ? 0 : 1;
            ajaxRequest_saveUserSettings(setfield, setval, 'saveUserSettings');
        });

        if (1 === 1) {
            setInterval(function() {
                use_pushMessages = <?php echo $use_pushMessages; ?>;
                console.log("getListPush!", "checkUnreadMessages!", "getListPush (from mailnew) >>>",
                    use_pushMessages);
                ajaxRequest_getListPush('getListPush');
                ajaxRequest_checkUnreadMessages('checkUnreadMessages');
            }, 30000);
        }

        $(".toast button.close").click(function() {
            console.log("Toast closed!", "setMessageToChecked-1!", $(this).closest(
                '.toast').attr(
                "data-toast-id"));
            let toastid = $(this).closest('.toast').attr("data-toast-id");
            ajaxRequest_setMessageToChecked(toastid, 'setMessageToChecked');
        });

        $('#userSettings-modal .custom-switch>input').on('change',
            function(e) {
                $(this).val($(this).is(':checked') ? 1 : 0);
                setfield = $(this).attr('data-dbfield');
                setval = $(this).val();
                ajaxRequest_saveUserSettings(setfield, setval, 'saveUserSettings');
            });


        $(document).on('click', '.message-single.unread', function() {
            let msgid = $(this).attr('data-msg-id');
            console.log("setMessageToChecked-2!", msgid);
            ajaxRequest_setMessageToChecked(msgid, 'setMessageToChecked');
            ajaxRequest_checkUnreadMessages('checkUnreadMessages');
        });

    });
</script>