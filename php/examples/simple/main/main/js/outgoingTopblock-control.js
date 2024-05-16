// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
// MESSAGES : получение всего списка сообщений пользователя, с выделением непрочитанных
// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
var reqField_getOutgoingStats = {
    getOutgoingStats: function (response) {
        res = response.replace(new RegExp("\\r?\\n", "g"), "");
        console.log('getOutgoingStats!', res);
        if (res !== 'error' && res !== 'undefined' && res !== '') {
            x = res.split('///');
            total = checkVal(x[0])==1 ? x[0] : "!?";
            noispol = checkVal(x[1])==1 ? x[1] : "!?";
            noattach = checkVal(x[2])==1 ? x[2] : "!?";
            CTRLon = checkVal(x[3])==1 ? x[3] : "!?";
            CTRLnotexec = checkVal(x[4])==1 ? x[4] : "!?";
            DLon = checkVal(x[5])==1 ? x[5] : "!?";
            DL3days = checkVal(x[6])==1 ? x[6] : "!?";
            DL1day = checkVal(x[7])==1 ? x[7] : "!?";
            DLexpired = checkVal(x[8])==1 ? x[8] : "!?";
            $('#outStats-total').text(total);
            $('#outStats-noispol').text(noispol);
            $('#outStats-noattach').text(noattach);
            $('#outStats-CTRLon').text(CTRLon);
            $('#outStats-CTRLnotexec').text(CTRLnotexec);
            $('#outStats-DLon').text(DLon);
            $('#outStats-DL3days').text(DL3days);
            $('#outStats-DL1day').text(DL1day);
            $('#outStats-DLexpired').text(DLexpired);
        } else {
        }
    }
};
const ajaxRequest_getOutgoingStats = async (responseHandler) => {
    request_getOutgoingStats = $.ajax({
        type: "post",
        url: 'http://192.168.1.89/mailnew/php/examples/simple/main/main/process/ajaxrequests/ajaxReq-getOutgoingStats.php',
        cache: false,
        data: {},
        success: reqField_getOutgoingStats[responseHandler]
    });
    // Callback handler that will be called on success
    request_getOutgoingStats.done(function (response, textStatus, jqXHR) { });
    request_getOutgoingStats.fail(function (jqXHR, textStatus, errorThrown) {
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
    // Callback handler that will be called regardless
    // if the request_addItem failed or succeeded
    request_getOutgoingStats.always(function () { });
}
// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
// MESSAGES : получение всего списка сообщений пользователя, с выделением непрочитанных
// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
var reqField_getOutgoingStats_v2 = {
    getOutgoingStats: function (response) {
        res = response.replace(new RegExp("\\r?\\n", "g"), "");
        console.log('getOutgoingStats!', res);
        if (res !== 'error' && res !== 'undefined' && res !== '') {
            x = res.split('///');
            total = checkVal(x[0])==1 ? x[0] : "!?";
            noispol = checkVal(x[1])==1 ? x[1] : "!?";
            noattach = checkVal(x[2])==1 ? x[2] : "!?";
            CTRLon = checkVal(x[3])==1 ? x[3] : "!?";
            CTRLnotexec = checkVal(x[4])==1 ? x[4] : "!?";
            DLon = checkVal(x[5])==1 ? x[5] : "!?";
            DL3days = checkVal(x[6])==1 ? x[6] : "!?";
            DL1day = checkVal(x[7])==1 ? x[7] : "!?";
            DLexpired = checkVal(x[8])==1 ? x[8] : "!?";
            $('#outStats-total').text(total);
            $('#outStats-noispol').text(noispol);
            $('#outStats-noattach').text(noattach);
            $('#outStats-CTRLon').text(CTRLon);
            $('#outStats-CTRLnotexec').text(CTRLnotexec);
            $('#outStats-DLon').text(DLon);
            $('#outStats-DL3days').text(DL3days);
            $('#outStats-DL1day').text(DL1day);
            $('#outStats-DLexpired').text(DLexpired);
        } else {
        }
    }
};
const ajaxRequest_getOutgoingStats_v2 = async (ispolOnly, responseHandler) => {
    request_getOutgoingStats = $.ajax({
        type: "post",
        url: 'http://192.168.1.89/mailnew/php/examples/simple/main/main/process/ajaxrequests/ajaxReq-getOutgoingStats_v2.php',
        cache: false,
        data: {
            ispolOnly: ispolOnly
        },
        success: reqField_getOutgoingStats_v2[responseHandler]
    });
    // Callback handler that will be called on success
    request_getOutgoingStats.done(function (response, textStatus, jqXHR) { });
    request_getOutgoingStats.fail(function (jqXHR, textStatus, errorThrown) {
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
    // Callback handler that will be called regardless
    // if the request_addItem failed or succeeded
    request_getOutgoingStats.always(function () { });
}
