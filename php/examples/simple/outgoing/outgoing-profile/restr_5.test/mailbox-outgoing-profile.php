<?php
?>
<script type="text/javascript" language="javascript" class="">
    //
    var userID_session = '<?php echo $_SESSION['id']; ?>';
    var userLogin_session = '<?php echo $_SESSION['login']; ?>';


    function toggleTextOnClick(objElement) {
        $(objElement).toggle(function() {
            $(objElement).html('(кликните, чтобы скрыть текст)');
        }, function() {
            $(objElement).html('(кликните, чтобы посмотреть текст)');
        });
    }

    function showHideElement(objElement) {
        $(objElement).toggle(function() {
            $(objElement).css({
                display: "block"
            });
        }, function() {
            $(objElement).css({
                display: "none"
            });
        });
    }

    function close_window() {
        close();
    }
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_profileFilesList = {
        profileFileList: function(response) {
            console.log('profileFileList output', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                $('#docMain-output-mainFile').html('<p><span class="nodata">Нет данных</span></p>');
                $('#docMain-output-addFiles').html('<p><span class="nodata">Нет данных</span></p>');
                $('#docMain-output-tags').html('<p><span class="nodata">Нет данных</span></p>');
                x = response.split('///-///');
                mainfiles = x[0];
                addfiles = x[1];
                x0 = mainfiles.split('|||');
                x1 = addfiles.split('|||');
                //
                if (x0 !== "" && typeof x0 !== "undefined" && x0 !== null) {
                    var res_attached = "";
                    for (i = 0; i < x0.length; i++) {
                        res_attached += x0[i];
                        console.log('profileFileList response ' + i + ':', x0[i]);
                    }
                    if (res_attached !== "" && typeof res_attached !== "undefined" && res_attached !== null) {
                        $('#docMain-output-mainFile').html(res_attached);
                    }
                } else {
                    $('#docMain-output-mainFile').html(
                        '<span style="color:red">Нет основного файла</span>');
                }
                //
                if (x1 !== "" && typeof x1 !== "undefined" && x1 !== null) {
                    var res_attached = "";
                    for (i = 0; i < x1.length; i++) {
                        res_attached += x1[i];
                        console.log('profileFileList response ' + i + ':', x1[i]);
                    }
                    if (res_attached !== "" && typeof res_attached !== "undefined" && res_attached !== null) {
                        $('#docMain-output-addFiles').html(res_attached);
                    }
                } else {
                    $('#docMain-output-addFiles').html(
                        '<span style="color:red">Нет дополнительных прикрепленных файлов</span>');
                }
            }
        }
    };

    function ajaxRequest_profileFilesList(koddocmail, responseHandler) {
        request_profileFileList = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-docMain-files.php',
            cache: false,
            data: {
                koddocmail: koddocmail
            },
            success: reqField_profileFilesList[responseHandler]
        });
        // Callback handler that will be called on success
        request_profileFileList.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
            // console.log('request_profileFileList:', 'success', response)
        });
    }
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_profileContragentInfo = {
        profileContragentInfo: function(response) {
            console.log('profileContragentInfo output', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                $('#docMain-output-contragent').html('<p><span class="nodata">Нет данных</span></p>');
                let out = "";
                let arrTitles = ["ID контрагента", "Организация-контрагент", "Адрес", "Контактное лицо", "", "ИНН"];
                arrVals = response.split('///');
                for (i = 0; i < arrVals.length; i++) {
                    if (arrVals[i] !== "" && typeof arrVals[i] !== "undefined" && arrVals[i] !== null) {
                        if (arrTitles[i] === "") {
                            out += "<p style='margin-top:-0.95rem'><span class='text'>" + arrVals[i] +
                                "</span></p>";
                        } else {
                            out += "<p><span class='title'>" + arrTitles[i] + "</span><br><span class='text'>" +
                                arrVals[i] +
                                "</span></p>";
                        }
                    };
                }
                $('#docMain-output-contragent').html(out);
                //
            }
        }
    };

    function ajaxRequest_profileContragentInfo(koddocmail, responseHandler) {
        request_profileContragentInfo = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-Zak-info.php',
            cache: false,
            data: {
                koddocmail: koddocmail
            },
            success: reqField_profileContragentInfo[responseHandler]
        });
        // Callback handler that will be called on success
        request_profileContragentInfo.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
            $('#btn-contragent-lastInc').attr('kodcontragent', arrVals[0]);
            $('#btn-contragent-lastInc').attr('koddocmail', koddocmail);
            $('#btn-contragent-lastOut').attr('kodcontragent', arrVals[0]);
            $('#btn-contragent-lastOut').attr('koddocmail', koddocmail);
            $('#btn-contragent-dogs').attr('kodcontragent', arrVals[0]);
            $('#btn-contragent-chfzadol').attr('kodcontragent', arrVals[0]);
            $('#btn-contragent-unoplav').attr('kodcontragent', arrVals[0]);
            // console.log('request_profileContragentInfo:', 'success', response)
        });
        request_profileContragentInfo.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_profileContragentInfo.always(function() {});
    }
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_contragentLastInc = {
        contragentLastInc: function(response) {
            console.log('contragentLastInc output', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                $('#contragent-display-block').html('');
                let out = res;
                $('#contragent-display-block').html(out);
                //
                document.querySelector('#contragent-display-block>div').classList.remove('hidden');
            }
        }
    };

    function ajaxRequest_contragentLastInc(kodcontragent, responseHandler) {
        request_contragentLastInc = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byZak-lastInc.php',
            cache: false,
            data: {
                kodcontragent: kodcontragent
            },
            success: reqField_contragentLastInc[responseHandler]
        });
        // Callback handler that will be called on success
        request_contragentLastInc.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
        });
        request_contragentLastInc.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_contragentLastInc.always(function() {});
    }
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_contragentLastOut = {
        contragentLastOut: function(response) {
            console.log('contragentLastOut output', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                $('#contragent-display-block').html('');
                let out = res;
                $('#contragent-display-block').html(out);
                //
            }
            document.querySelector('#contragent-display-block>div').classList.remove('hidden');
        }
    };

    function ajaxRequest_contragentLastOut(kodcontragent, responseHandler) {
        request_contragentLastOut = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byZak-lastOut.php',
            cache: false,
            data: {
                kodcontragent: kodcontragent
            },
            success: reqField_contragentLastOut[responseHandler]
        });
        // Callback handler that will be called on success
        request_contragentLastOut.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
        });
        request_contragentLastOut.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_contragentLastOut.always(function() {});
    }

    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_contragentDogs = {
        contragentDogs: function(response) {
            console.log('contragentDogs output', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                $('#contragent-display-block').html('');
                let out = res;
                $('#contragent-display-block').html(out);
                //
            }
            document.querySelector('#contragent-display-block>div').classList.remove('hidden');
        }
    };

    function ajaxRequest_contragentDogs(kodcontragent, responseHandler) {
        request_contragentDogs = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byZak-dogs.php',
            cache: false,
            data: {
                kodcontragent: kodcontragent
            },
            success: reqField_contragentDogs[responseHandler]
        });
        // Callback handler that will be called on success
        request_contragentDogs.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
        });
        request_contragentDogs.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_contragentDogs.always(function() {});
    }

    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_contragentChfzadol = {
        contragentChfzadol: function(response) {
            console.log('contragentChfzadol output', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                $('#contragent-display-block').html('');
                let out = res;
                $('#contragent-display-block').html(out);
                //
            }
            document.querySelector('#contragent-display-block>div').classList.remove('hidden');
        }
    };

    function ajaxRequest_contragentChfzadol(kodcontragent, responseHandler) {
        request_contragentChfzadol = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byZak-zadolChf.php',
            cache: false,
            data: {
                kodcontragent: kodcontragent
            },
            success: reqField_contragentChfzadol[responseHandler]
        });
        // Callback handler that will be called on success
        request_contragentChfzadol.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
        });
        request_contragentChfzadol.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_contragentChfzadol.always(function() {});
    }

    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_contragentUnoplav = {
        contragentUnoplav: function(response) {
            console.log('contragentUnoplav output', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                $('#contragent-display-block').html('');
                let out = res;
                $('#contragent-display-block').html(out);
                //
            }
            document.querySelector('#contragent-display-block>div').classList.remove('hidden');
        }
    };

    function ajaxRequest_contragentUnoplav(kodcontragent, responseHandler) {
        request_contragentUnoplav = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byZak-unOplav.php',
            cache: false,
            data: {
                kodcontragent: kodcontragent
            },
            success: reqField_contragentUnoplav[responseHandler]
        });
        // Callback handler that will be called on success
        request_contragentUnoplav.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
        });
        request_contragentUnoplav.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_contragentUnoplav.always(function() {});
    }

    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_profileDocInfo = {
        profileDocInfo: function(response) {
            console.log('profileDocInfo output', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                $('#docMain-output-commoninfo').html('<p><span class="nodata">Нет данных</span></p>');
                let out = "";
                let arrTitles = ["Входящий номер АТГС", "Дата и время регистрации", "Тип документа (письма)",
                    "Описание",
                    "Ответственный(ые)", "Получатель", "Исходящий номер контрагента", "Дата исходящего"
                ];
                arrVals = response.split('///');
                for (i = 0; i < arrVals.length; i++) {
                    if (arrVals[i] !== "" && typeof arrVals[i] !== "undefined" && arrVals[i] !== null) {
                        if (arrTitles[i] === "") {
                            out += "<p style='margin-top:-0.95rem'><span class='text'>" + arrVals[i] +
                                "</span></p>";
                        } else {
                            out += "<p><span class='title'>" + arrTitles[i] + "</span><br><span class='text'>" +
                                arrVals[i] +
                                "</span></p>";
                        }
                    };
                }
                $('#docMain-output-commoninfo').html(out);
                //
            }
        }
    };

    function ajaxRequest_profileDocInfo(koddocmail, responseHandler) {
        request_profileDocInfo = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-docMain-info.php',
            cache: false,
            data: {
                koddocmail: koddocmail
            },
            success: reqField_profileDocInfo[responseHandler]
        });
        // Callback handler that will be called on success
        request_profileDocInfo.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
            $('#btn-commoninfo-linkedInc').attr('koddocmail', koddocmail);
            $('#btn-commoninfo-linkedOut').attr('koddocmail', koddocmail);
            $('#btn-commoninfo-linkedDog').attr('koddocmail', koddocmail);
            $('#btn-commoninfo-linkedContr').attr('koddocmail', koddocmail);
            // console.log('request_profileDocInfo:', 'success', response)
        });
        request_profileDocInfo.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_profileDocInfo.always(function() {});
    }
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_docLinkedInc = {
        docLinkedInc: function(response) {
            console.log('docLinkedInc output', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                $('#docLinked-display-block').html('');
                $('#docLinked-display-subblock').html('');
                let out = res;
                $('#docLinked-display-block').html(out);
                //
                document.querySelector('#docLinked-display-block>div').classList.remove('hidden');
            }
        }
    };

    function ajaxRequest_docLinkedInc(koddocmail, responseHandler) {
        request_docLinkedInc = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byDocMain-linkedInc.php',
            cache: false,
            data: {
                koddocmail: koddocmail
            },
            success: reqField_docLinkedInc[responseHandler]
        });
        // Callback handler that will be called on success
        request_docLinkedInc.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
        });
        request_docLinkedInc.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_docLinkedInc.always(function() {});
    }
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_docLinkedOut = {
        docLinkedOut: function(response) {
            console.log('docLinkedOut output', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                $('#docLinked-display-block').html('');
                $('#docLinked-display-subblock').html('');
                let out = res;
                $('#docLinked-display-block').html(out);
                //
                document.querySelector('#docLinked-display-block>div').classList.remove('hidden');
            }
        }
    };

    function ajaxRequest_docLinkedOut(koddocmail, responseHandler) {
        request_docLinkedOut = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byDocMain-linkedOut.php',
            cache: false,
            data: {
                koddocmail: koddocmail
            },
            success: reqField_docLinkedOut[responseHandler]
        });
        // Callback handler that will be called on success
        request_docLinkedOut.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
        });
        request_docLinkedOut.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_docLinkedOut.always(function() {});
    }
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_docLinkedDog = {
        docLinkedDog: function(response) {
            console.log('docLinkedDog output', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                $('#docLinked-display-block').html('');
                $('#docLinked-display-subblock').html('');
                let out = res;
                $('#docLinked-display-block').html(out);
                //
                document.querySelector('#docLinked-display-block>div').classList.remove('hidden');
            }
        }
    };

    function ajaxRequest_docLinkedDog(koddocmail, responseHandler) {
        request_docLinkedDog = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byDocMain-linkedDog.php',
            cache: false,
            data: {
                koddocmail: koddocmail
            },
            success: reqField_docLinkedDog[responseHandler]
        });
        // Callback handler that will be called on success
        request_docLinkedDog.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
        });
        request_docLinkedDog.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_docLinkedDog.always(function() {});
    }
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_docLinkedContr = {
        docLinkedContr: function(response) {
            console.log('docLinkedContr output', response);
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                $('#docLinked-display-block').html('');
                $('#docLinked-display-subblock').html('');
                let out = res;
                $('#docLinked-display-block').html(out);
                //
                document.querySelector('#docLinked-display-block>div').classList.remove('hidden');
            }
        }
    };

    function ajaxRequest_docLinkedContr(koddocmail, responseHandler) {
        request_docLinkedContr = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byDocMain-linkedContr.php',
            cache: false,
            data: {
                koddocmail: koddocmail
            },
            success: reqField_docLinkedContr[responseHandler]
        });
        // Callback handler that will be called on success
        request_docLinkedContr.done(function(response, textStatus, jqXHR) {
            res = response.replace(new RegExp("\\r?\\n", "g"), "");
        });
        request_docLinkedContr.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_docLinkedContr.always(function() {});
    }
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_byLinkedDoc2Inc = {
        byLinkedDoc2Inc: function(response) {
            console.log('byLinkedDoc2Inc response', response);
        }
    };

    function ajaxRequest_byLinkedDoc2Inc(str, koddoc, byWhat, responseHandler) {
        request_byLinkedDoc2Inc = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byLinkedDog-linkedInc.php',
            cache: false,
            data: {
                str: str,
                koddoc: koddoc
            },
            success: reqField_byLinkedDoc2Inc[responseHandler]
        });
        // Callback handler that will be called on success
        request_byLinkedDoc2Inc.done(function(response, textStatus, jqXHR) {
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                if (byWhat === 'byDoc') {
                    $('#docLinked-display-subblock').html('');
                    let out = res;
                    $('#docLinked-display-subblock').html(out);
                    //
                    document.querySelector('#docLinked-display-subblock>div').classList.remove('hidden');
                } else {
                    $('#contragent-display-subblock').html('');
                    let out = res;
                    $('#contragent-display-subblock').html(out);
                    //
                    document.querySelector('#contragent-display-subblock>div').classList.remove('hidden');
                }
            }
        });
        request_byLinkedDoc2Inc.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_byLinkedDoc2Inc.always(function() {});
    }


    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_byLinkedDoc2Out = {
        byLinkedDoc2Out: function(response) {
            console.log('byLinkedDoc2Out response', response);
        }
    };

    function ajaxRequest_byLinkedDoc2Out(str, koddoc, byWhat, responseHandler) {
        request_byLinkedDoc2Out = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byLinkedDog-linkedOut.php',
            cache: false,
            data: {
                str: str,
                koddoc: koddoc
            },
            success: reqField_byLinkedDoc2Out[responseHandler]
        });
        // Callback handler that will be called on success
        request_byLinkedDoc2Out.done(function(response, textStatus, jqXHR) {
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                if (byWhat === 'byDoc') {
                    $('#docLinked-display-subblock').html('');
                    let out = res;
                    $('#docLinked-display-subblock').html(out);
                    //
                    document.querySelector('#docLinked-display-subblock>div').classList.remove('hidden');
                } else {
                    $('#contragent-display-subblock').html('');
                    let out = res;
                    $('#contragent-display-subblock').html(out);
                    //
                    document.querySelector('#contragent-display-subblock>div').classList.remove('hidden');
                }
            }
        });
        request_byLinkedDoc2Out.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_byLinkedDoc2Out.always(function() {});
    }


    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_byLinkedDocFiles = {
        byLinkedDocFiles: function(response) {
            console.log('byLinkedDocFiles response', response);
        }
    };

    function ajaxRequest_byLinkedDocFiles(str, byWhat, responseHandler) {
        request_byLinkedDocFiles = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byLinkedDog-files.php',
            cache: false,
            data: {
                str: str
            },
            success: reqField_byLinkedDocFiles[responseHandler]
        });
        // Callback handler that will be called on success
        request_byLinkedDocFiles.done(function(response, textStatus, jqXHR) {
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                if (byWhat === 'byDoc') {
                    $('#docLinked-display-subblock').html('');
                    let out = res;
                    $('#docLinked-display-subblock').html(out);
                    //
                    document.querySelector('#docLinked-display-subblock>div').classList.remove('hidden');
                } else {
                    $('#contragent-display-subblock').html('');
                    let out = res;
                    $('#contragent-display-subblock').html(out);
                    //
                    document.querySelector('#contragent-display-subblock>div').classList.remove('hidden');
                }
            }
        });
        request_byLinkedDocFiles.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_byLinkedDocFiles.always(function() {});
    }


    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
    // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
    //
    var reqField_byLinkedDocSums = {
        byLinkedDocSums: function(response) {
            console.log('byLinkedDocSums response', response);
        }
    };

    function ajaxRequest_byLinkedDocSums(koddoc, byWhat, responseHandler) {
        request_byLinkedDocSums = $.ajax({
            type: "post",
            url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/process/ajaxrequests/ajaxReq-profilingDoc-byLinkedDog-sums.php',
            cache: false,
            data: {
                koddoc: koddoc
            },
            success: reqField_byLinkedDocSums[responseHandler]
        });
        // Callback handler that will be called on success
        request_byLinkedDocSums.done(function(response, textStatus, jqXHR) {
            if (response !== "" && typeof response !== "undefined" && response !== null) {
                res = response.replace(new RegExp("\\r?\\n", "g"), "");
                if (byWhat === 'byDoc') {
                    $('#docLinked-display-subblock').html('');
                    let out = res;
                    $('#docLinked-display-subblock').html(out);
                    //
                    document.querySelector('#docLinked-display-subblock>div').classList.remove('hidden');
                } else {
                    $('#contragent-display-subblock').html('');
                    let out = res;
                    $('#contragent-display-subblock').html(out);
                    //
                    document.querySelector('#contragent-display-subblock>div').classList.remove('hidden');
                }
            }
        });
        request_byLinkedDocSums.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        // Callback handler that will be called regardless
        // if the request_addItem failed or succeeded
        request_byLinkedDocSums.always(function() {});
    }
</script>
<?php

?>
<style>

</style>

<?php
// if ( ($_GET['userid']==$_SESSION['id']) || (checkIsItSuperadmin_defaultDB($_SESSION['id'])==1) ) {
if ($noerrors) {
?>
    <link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH . __MAIL_RESTR; ?>/css/mailbox-outgoing-profile.css">

    <div id="profile-main" class="container mt-5">

        <div class="row">
            <div class="col-md-12">

            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex flex-row mb-3">
                    <div class="docMain commonInfo" style="flex-basis:65%">
                        <h4 class="mb-3">Информация о документе</h4>
                        <div id="docMain-output-commoninfo"></div>
                    </div>
                    <div class="docMain files " style="flex-basis:35%">
                        <h4 class="mb-3">Файлы и теги</h4>
                        <div class="d-flex flex-column">
                            <div class="docMain filesList">
                                <p class="mb-2"><span class="title">Основной файл</span></p>
                                <div id="docMain-output-mainFile"></div>
                            </div>
                            <div class="docMain filesList">
                                <p class="mb-2"><span class="title">Дополнительные файлы</p>
                                <div id="docMain-output-addFiles"></div>
                            </div>
                            <div class="docMain tags">
                                <p class="mb-2"><span class="title">Теги по документу</p>
                                <div id="docMain-output-tags"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column mb-4">
                    <div id="commoninfo-ctrl-block" class="mt-3 mb-3">
                        <p class="mb-2"><span class="title">Исходящие связи документа</span></p>
                        <button id="btn-commoninfo-linkedInc" koddocmail="" type="button" class="btn btn-outline-secondary btn-sm">Связанные входящие</button>
                        <button id="btn-commoninfo-linkedOut" koddocmail="" type="button" class="btn btn-outline-secondary btn-sm">Связанные исходящие</button>
                        <button id="btn-commoninfo-linkedDog" koddocmail="" type="button" class="btn btn-outline-success btn-sm">Связанные договоры</button>
                        <button id="btn-commoninfo-linkedContr" koddocmail="" type="button" class="btn btn-outline-success btn-sm">Связанные контрагенты</button>
                    </div>
                    <div id="docLinked-display-block" class=""></div>
                    <div id="docLinked-display-subblock" class=""></div>
                </div>

                <div class="d-flex flex-row mb-4">
                    <div class="docMain commonInfo" style="flex-basis:100%">
                        <h4 class="mb-3">Информация о контрагенте</h4>
                        <div id="docMain-output-contragent" class="mt-3 mb-3"></div>
                        <div id="contragent-ctrl-block" class="mt-3 mb-3">
                            <p class="mb-2"><span class="title">Посмотреть по этому контрагенту</span></p>
                            <button id="btn-contragent-lastInc" koddocmail="" kodcontragent="" type="button" class="btn btn-outline-secondary btn-sm">Последние 10 во Входящих</button>
                            <button id="btn-contragent-lastOut" koddocmail="" kodcontragent="" type="button" class="btn btn-outline-secondary btn-sm">Последние 10 в Исходящих</button>
                            <button id="btn-contragent-dogs" kodcontragent="" type="button" class="btn btn-outline-success btn-sm">Текущие договоры</button>
                            <button id="btn-contragent-chfzadol" kodcontragent="" type="button" class="btn btn-outline-info btn-sm">Задолженность по счетам-фактурам</button>
                            <button id="btn-contragent-unoplav" kodcontragent="" type="button" class="btn btn-outline-info btn-sm">Незакрытые авансы</button>
                        </div>
                        <div id="contragent-display-block" class=""></div>
                        <div id="contragent-display-subblock" class=""></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="my-2">&nbsp;</div>

    </div>
<?php
} else {
?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-sm-12">
                <p class="text-danger text-center">Произошла какая-то ошибка. Доступ к данным невозможен.
                </p>
            </div>
        </div>
    </div>
<?php
}
?>
<div class="container mt-5">
    <div class="col-sm-12">
        <div class="text-center profiling-link"><a href="index.php?type=out&mode=archive&rel=<?php echo $_reqDB_docmail['koddocmail']; ?>">Вернуться к
                документу в исходящих</a></div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:none">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ок</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" language="javascript" class="init">
    $(window).on("load", function() {

        var koddocmail = <?php echo $_GET['uid']; ?>;
        console.log('koddocmail', koddocmail);
        ajaxRequest_profileDocInfo(koddocmail, 'profileDocInfo');
        ajaxRequest_profileContragentInfo(koddocmail, 'profileContragentInfo');
        ajaxRequest_profileFilesList(koddocmail, 'profileFileList');

        $("#btn-contragent-lastInc").click(function() {
            console.log('#btn-contragent-lastInc', 'clicked');
            let kodcontragent = $(this).attr('kodcontragent');
            ajaxRequest_contragentLastInc(kodcontragent, 'contragentLastInc');
        });

        $("#btn-contragent-lastOut").click(function() {
            console.log('#btn-contragent-lastOut', 'clicked');
            let kodcontragent = $(this).attr('kodcontragent');
            ajaxRequest_contragentLastOut(kodcontragent, 'contragentLastOut');
        });

        $("#btn-contragent-dogs").click(function() {
            console.log('#btn-contragent-dogs', 'clicked');
            let kodcontragent = $(this).attr('kodcontragent');
            ajaxRequest_contragentDogs(kodcontragent, 'contragentDogs');
        });

        $("#btn-contragent-chfzadol").click(function() {
            console.log('#btn-contragent-chfzadol', 'clicked');
            let kodcontragent = $(this).attr('kodcontragent');
            ajaxRequest_contragentChfzadol(kodcontragent, 'contragentChfzadol');
        });

        $("#btn-contragent-unoplav").click(function() {
            console.log('#btn-contragent-unoplav', 'clicked');
            let kodcontragent = $(this).attr('kodcontragent');
            ajaxRequest_contragentUnoplav(kodcontragent, 'contragentUnoplav');
        });

        $("#btn-commoninfo-linkedInc").click(function() {
            console.log('#btn-commoninfo-linkedInc', 'clicked');
            let koddocmail = $(this).attr('koddocmail');
            ajaxRequest_docLinkedInc(koddocmail, 'docLinkedInc');
        });

        $("#btn-commoninfo-linkedOut").click(function() {
            console.log('#btn-commoninfo-linkedOut', 'clicked');
            let koddocmail = $(this).attr('koddocmail');
            ajaxRequest_docLinkedOut(koddocmail, 'docLinkedOut');
        });

        $("#btn-commoninfo-linkedDog").click(function() {
            console.log('#btn-commoninfo-linkedDog', 'clicked');
            let koddocmail = $(this).attr('koddocmail');
            ajaxRequest_docLinkedDog(koddocmail, 'docLinkedDog');
        });

        $("#btn-commoninfo-linkedContr").click(function() {
            console.log('#btn-commoninfo-linkedContr', 'clicked');
            let koddocmail = $(this).attr('koddocmail');
            ajaxRequest_docLinkedContr(koddocmail, 'docLinkedContr');
        });

        $("#btn-linkedDoc-linkedInc").click(function() {
            console.log('#btn-linkedDoc-linkedInc', 'clicked');
        });

    });
</script>