const showPush = (num, timestamp, serviceName, type, title, maintext, subtext1, subtext2, subtext3, specialtext, link1, link2, mode, id, readed) => {
    console.log("showPush!", num, timestamp, serviceName, type, title, maintext, subtext1, subtext2, subtext3, specialtext, link1, link2, id, readed);
    for (var i = 1; i <= 5; i++) {
        if ($('#mailnew-toast-' + i + ' div.toast').hasClass('show')) {
            if ($('#mailnew-toast-' + i + ' div.toast').attr('data-toast-id') == id) {
                break;
            }
        } else if ($('#mailnew-toast-' + i + ' div.toast').hasClass('hide')) {
            if ($('#mailnew-toast-' + i + ' div.toast').attr('data-toast-id') === id) {
                $('#mailnew-toast-' + i).removeClass(["d-none"]);
                $('#mailnew-toast-' + i + ' div.toast').removeClass(["toast-system", "toast-info", "toast-warning", "toast-danger"]).addClass('toast-' + type);
                $('#mailnew-toast-' + i + ' div.toast-header div.header-text').html(title);
                $('#mailnew-toast-' + i + ' div.main-text').html(maintext);
                $('#mailnew-toast-' + i + ' div.sub-text1').html(subtext1);
                $('#mailnew-toast-' + i + ' div.sub-text2').html(subtext2);
                $('#mailnew-toast-' + i + ' div.sub-text3').html(subtext3);
                $('#mailnew-toast-' + i + ' div.special-text').html(specialtext);
                $('#mailnew-toast-' + i + ' div.link-text1').html('<a href="' + link1 + '" target="_blank" title="Открыть Профиль документа">Перейти в Профиль документа</a>');
                $('#mailnew-toast-' + i + ' div.link-text2').html(link2);
                $('#mailnew-toast-' + i + ' div.toast-timestamp').text(moment(timestamp, "DD.MM.YYYY HH:mm:ss").format('DD.MM.YYYY HH:mm:ss'));
                $('#mailnew-toast-' + i + ' div.toast').attr('data-toast-id', id);
                $('#mailnew-toast-' + i + ' div.toast').toast('show');
                break;
            }
        } else {
            if ($('#mailnew-toast-' + i + ' div.toast').attr('data-toast-id') != id) {
                $('#mailnew-toast-' + i).removeClass(["d-none"]);
                $('#mailnew-toast-' + i + ' div.toast').removeClass(["toast-system", "toast-info", "toast-warning", "toast-danger"]).addClass('toast-' + type);
                $('#mailnew-toast-' + i + ' div.toast-header div.header-text').html(title);
                $('#mailnew-toast-' + i + ' div.main-text').html(maintext);
                $('#mailnew-toast-' + i + ' div.sub-text1').html(subtext1);
                $('#mailnew-toast-' + i + ' div.sub-text2').html(subtext2);
                $('#mailnew-toast-' + i + ' div.sub-text3').html(subtext3);
                $('#mailnew-toast-' + i + ' div.special-text').html(specialtext);
                $('#mailnew-toast-' + i + ' div.link-text1').html('<a href="' + link1 + '" target="_blank" title="Открыть Профиль документа">Перейти в Профиль документа</a>');
                $('#mailnew-toast-' + i + ' div.link-text2').html(link2);
                $('#mailnew-toast-' + i + ' div.toast-timestamp').text(moment(timestamp, "DD.MM.YYYY HH:mm:ss").format('DD.MM.YYYY HH:mm:ss'));
                $('#mailnew-toast-' + i + ' div.toast').attr('data-toast-id', id);
                $('#mailnew-toast-' + i + ' div.toast').toast('show');
                break;
            }
        }
    }
}
// ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
// PUSH check 
// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
var reqField_getListPush = {
    getListPush: function (response) {
        res = response.replace(new RegExp("\\r?\\n", "g"), "");
    }
};
const ajaxRequest_getListPush = (responseHandler) => {
    request_getListPush = $.ajax({
        type: "post",
        url: 'http://192.168.1.89/mailnew/php/examples/simple/main/main/process/ajaxrequests/ajaxReq-getListPush.php',
        cache: false,
        data: {},
        success: reqField_getListPush[responseHandler]
    });
    // Callback handler that will be called on success
    request_getListPush.done(function (response, textStatus, jqXHR) {
        res = response.replace(new RegExp("\\r?\\n", "g"), "");
        console.log('getListPush!', res);
        if (res !== "no messages" && res !== "error") {
            toasts = res.split('<!>');
            let part = [{}];
            console.log('getListPush!', 'founded new!', res, "usePush >>>", toasts[toasts.length - 1]);
            if (toasts[toasts.length - 1] !== '0') {
                for (var i = 0; i < (toasts.length - 1); i++) {
                    parts = toasts[i].split('///');
                    part[i] = {
                        'number': parts[0],
                        'msg_timestamp': moment(parts[1]).format('DD.MM.YYYY HH:mm:ss'),
                        'servicename': parts[2],
                        'msg_type': parts[3],
                        'msg_title': parts[4],
                        'msg_maintext': parts[5],
                        'msg_subtext1': parts[6],
                        'msg_subtext2': parts[7],
                        'msg_subtext3': parts[8],
                        'msg_specialtext': parts[9],
                        'msg_link1': parts[10],
                        'msg_link2': parts[11],
                        'msg_id': parts[12],
                        'msg_readed': parts[13],
                    }
                    showPush(part[i]['number'], part[i]['msg_timestamp'], part[i]['servicename'], part[i]['msg_type'], part[i]['msg_title'], part[i]['msg_maintext'], part[i]['msg_subtext1'], part[i]['msg_subtext2'], part[i]['msg_subtext3'], part[i]['msg_specialtext'], part[i]['msg_link1'], part[i]['msg_link2'], null, part[i]['msg_id'], part[i]['msg_readed']);
                }
            }
        }
    });
    request_getListPush.fail(function (jqXHR, textStatus, errorThrown) {
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
    // Callback handler that will be called regardless
    // if the request_addItem failed or succeeded
    request_getListPush.always(function () { });
}

// ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
// PUSH checked
// ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
var reqField_setMessageToChecked = {
    setMessageToChecked: function (response) {
        console.log('setMessageToChecked!', response);
    }
};
const ajaxRequest_setMessageToChecked = async (msgid, responseHandler) => {
    request_setMessageToChecked = $.ajax({
        type: "post",
        url: 'http://192.168.1.89/mailnew/php/examples/simple/main/main/process/ajaxrequests/ajaxReq-setMessageToChecked.php',
        cache: false,
        data: {
            msgid: msgid
        },
        success: reqField_setMessageToChecked[responseHandler]
    });
    // Callback handler that will be called on success
    request_setMessageToChecked.done(function (response, textStatus, jqXHR) {
        if (response === 'success') {
            console.log('remove unread class!');
            $('#listMessages-output').find(`div[data-msg-id='${msgid}']`).removeClass(
                'unread');
            $('div.toasts-block').find(`div[data-toast-id='${msgid}']`).toast('hide');
            $('div.toasts-block').find(`div[data-toast-id='${msgid}']`).parents('div').eq(0).addClass(["d-none"]);

        }

    });
    request_setMessageToChecked.fail(function (jqXHR, textStatus, errorThrown) {
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
    // Callback handler that will be called regardless
    // if the request_addItem failed or succeeded
    request_setMessageToChecked.always(function () { });
}

// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
// MESSAGES : получение всего списка сообщений пользователя, с выделением непрочитанных
// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
var reqField_checkUnreadMessages = {
    checkUnreadMessages: function (response) {
        res = response.replace(new RegExp("\\r?\\n", "g"), "");
        console.log('checkUnreadMessages!', res);
        if (res === "unreads founded") {
            $('#listMessages-icon>i').removeClass('text-secondary').addClass(
                'fa-beat-fade text-warning');
        } else {
            $('#listMessages-icon>i').removeClass('fa-beat-fade text-warning').addClass(
                'text-secondary');
        }
    }
};
const ajaxRequest_checkUnreadMessages = async (responseHandler) => {
    request_checkUnreadMessages = $.ajax({
        type: "post",
        url: 'http://192.168.1.89/mailnew/php/examples/simple/main/main/process/ajaxrequests/ajaxReq-checkUnreadMessages.php',
        cache: false,
        data: {},
        success: reqField_checkUnreadMessages[responseHandler]
    });
    // Callback handler that will be called on success
    request_checkUnreadMessages.done(function (response, textStatus, jqXHR) { });
    request_checkUnreadMessages.fail(function (jqXHR, textStatus, errorThrown) {
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
    // Callback handler that will be called regardless
    // if the request_addItem failed or succeeded
    request_checkUnreadMessages.always(function () { });
}

// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
// MESSAGES : получение всего списка сообщений пользователя, с выделением непрочитанных
// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
var reqField_getListMessages = {
    getListMessages: function (response) {
        $('#listMessages-output').empty();
        res = response.replace(new RegExp("\\r?\\n", "g"), "");
        if (res !== "no messages" && res !== "error") {
            console.log('getListMessages!', response);
            messages = res.split('<!>');
            part = [{}];
            for (var i = 0; i < messages.length; i++) {
                out_box = '';
                msg = messages[i].split('///');
                msg[i] = {
                    'number': msg[0],
                    'msg_timestamp': moment(msg[1]).format('DD.MM.YYYY HH:mm:ss'),
                    'servicename': msg[2],
                    'msg_type': msg[3],
                    'msg_title': msg[4],
                    'msg_maintext': msg[5],
                    'msg_subtext1': msg[6],
                    'msg_subtext2': msg[7],
                    'msg_subtext3': msg[8],
                    'msg_specialtext': msg[9],
                    'msg_link1': msg[10],
                    'msg_link2': msg[11],
                    'msg_id': msg[12],
                    'readed': msg[13],
                }
                unread = msg[i]['readed'] === '1' ? '' : ' unread';
                unread_fa = msg[i]['readed'] === '1' ? '' : ' fa-beat';
                //
                out_box += '<div class="message-single d-flex flex-column shadow mb-2 p-2' + unread + '" data-msg-id="' + msg[i]['msg_id'] + '">';
                out_box += '<div class="msg-title">' + msg[i]['msg_title'] + '</div>';
                out_box += '<div class="msg-maintext">' + msg[i]['msg_maintext'] + '</div>';
                out_box += '<div class="msg-subtext1">' + msg[i]['msg_subtext1'] + '</div>';
                out_box += '<div class="msg-subtext2">' + msg[i]['msg_subtext2'] + '</div>';
                out_box += '<div class="msg-subtext3">' + msg[i]['msg_subtext3'] + '</div>';
                out_box += '<div class="msg-specialtext">' + msg[i]['msg_specialtext'] + '</div>';
                out_box += '<div class="msg-link1 my-1"><a href="' + msg[i]['msg_link1'] + '" target="_blank" title="Открыть Профиль документа">Перейти в Профиль документа</a></div>';
                out_box += '<div class="msg-link2">' + msg[i]['msg_link2'] + '</div>';
                out_box += '<div class="msg-timestamp mt-2 text-right"><span class="badge badge-light mr-2">' + msg[i]['servicename'] + '</span><span class="' + msg[i]['msg_type'] + ' mr-2"><i class="fa-solid fa-circle fa-xs' + unread_fa + '"></i></span>' + msg[i]['msg_timestamp'] + '</div></div>';
                //
                $('#listMessages-output').append(out_box);
                // $('#listMessages-sideblock-output').append(out_box);
            }
        } else {
            console.log('getListMessages!', 'no messages');
        }
        $('#listMessages-output').append(
            '<div class="pushMsg-showall mt-2 text-right"><a href="#nolink-anchor" class="" target="">Посмотреть все уведомления</a></div>'
        );
    }
};
const ajaxRequest_getListMessages = async (responseHandler) => {
    request_getListMessages = $.ajax({
        type: "post",
        url: 'http://192.168.1.89/mailnew/php/examples/simple/main/main/process/ajaxrequests/ajaxReq-getListMessages.php',
        cache: false,
        data: {},
        success: reqField_getListMessages[responseHandler]
    });
    // Callback handler that will be called on success
    request_getListMessages.done(function (response, textStatus, jqXHR) {
        res = response.replace(new RegExp("\\r?\\n", "g"), "");
        $('#ListMessages-modal').modal('show');
    });
    request_getListMessages.fail(function (jqXHR, textStatus, errorThrown) {
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
    // Callback handler that will be called regardless
    // if the request_addItem failed or succeeded
    request_getListMessages.always(function () { });
}
const ajaxRequest_getListMessagesSideblock = async (responseHandler) => {
    request_getListMessages = $.ajax({
        type: "post",
        url: 'http://192.168.1.89/mailnew/php/examples/simple/main/main/process/ajaxrequests/ajaxReq-getListMessages.php',
        cache: false,
        data: {},
        success: reqField_getListMessages[responseHandler]
    });
    // Callback handler that will be called on success
    request_getListMessages.done(function (response, textStatus, jqXHR) {
        res = response.replace(new RegExp("\\r?\\n", "g"), "");
    });
    request_getListMessages.fail(function (jqXHR, textStatus, errorThrown) {
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
    // Callback handler that will be called regardless
    // if the request_addItem failed or succeeded
    request_getListMessages.always(function () { });
}
