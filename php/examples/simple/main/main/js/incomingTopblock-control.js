// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
// MESSAGES : получение всего списка сообщений пользователя, с выделением непрочитанных
// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
var reqField_getIncomingStats = {
    getIncomingStats: function (response) {
        res = response.replace(new RegExp("\\r?\\n", "g"), "");
        console.log('getIncomingStats!', res);
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
            $('#incStats-total').text(total);
            $('#incStats-noispol').text(noispol);
            $('#incStats-noattach').text(noattach);
            $('#incStats-CTRLon').text(CTRLon);
            $('#incStats-CTRLnotexec').text(CTRLnotexec);
            $('#incStats-DLon').text(DLon);
            $('#incStats-DL3days').text(DL3days);
            $('#incStats-DL1day').text(DL1day);
            $('#incStats-DLexpired').text(DLexpired);
        } else {
        }
    }
};
const ajaxRequest_getIncomingStats = async (responseHandler) => {
    request_getIncomingStats = $.ajax({
        type: "post",
        url: 'http://192.168.1.89/mailnew/php/examples/simple/main/main/process/ajaxrequests/ajaxReq-getIncomingStats.php',
        cache: false,
        data: {},
        success: reqField_getIncomingStats[responseHandler]
    });
    // Callback handler that will be called on success
    request_getIncomingStats.done(function (response, textStatus, jqXHR) { });
    request_getIncomingStats.fail(function (jqXHR, textStatus, errorThrown) {
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
    // Callback handler that will be called regardless
    // if the request_addItem failed or succeeded
    request_getIncomingStats.always(function () { });
}
// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
// MESSAGES : получение всего списка сообщений пользователя, с выделением непрочитанных
// ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
var reqField_getIncomingStats_v2 = {
    getIncomingStats: function (response) {
        res = response.replace(new RegExp("\\r?\\n", "g"), "");
        console.log('getIncomingStats!', res);
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
            $('#incStats-total').text(total);
            $('#incStats-noispol').text(noispol);
            $('#incStats-noattach').text(noattach);
            $('#incStats-CTRLon').text(CTRLon);
            $('#incStats-CTRLnotexec').text(CTRLnotexec);
            $('#incStats-DLon').text(DLon);
            $('#incStats-DL3days').text(DL3days);
            $('#incStats-DL1day').text(DL1day);
            $('#incStats-DLexpired').text(DLexpired);
        } else {
        }
    }
};
const ajaxRequest_getIncomingStats_v2 = async (ispolOnly, responseHandler) => {
    request_getIncomingStats = $.ajax({
        type: "post",
        url: 'http://192.168.1.89/mailnew/php/examples/simple/main/main/process/ajaxrequests/ajaxReq-getIncomingStats_v2.php',
        cache: false,
        data: {
            ispolOnly: ispolOnly
        },
        success: reqField_getIncomingStats_v2[responseHandler]
    });
    // Callback handler that will be called on success
    request_getIncomingStats.done(function (response, textStatus, jqXHR) { });
    request_getIncomingStats.fail(function (jqXHR, textStatus, errorThrown) {
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
    // Callback handler that will be called regardless
    // if the request_addItem failed or succeeded
    request_getIncomingStats.always(function () { });
}
