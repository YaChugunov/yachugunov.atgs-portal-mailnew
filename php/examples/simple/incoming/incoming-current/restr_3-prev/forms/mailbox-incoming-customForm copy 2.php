<!-- HTML -->
<?php
// ----- ----- ----- ----- -----
// Форма редактирования входящего письма
// :::
?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/common-customform.css">
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-incoming-customForm.css">


<div id="customForm-mail-main-inbox" class="editorform">
    <div id="editorform-editor-tabs" class="editorform-tabs w-100">
        <ul id="editorform-editor-tabs-menu" class="nav nav-tabs editorform-tabs-menu">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#doc-editor-tab-1" title=""><span id="doc-editor-menu-tab-1-errmsg"></span>Регистрация</a></li>
            <li class="nav-item tab-outgoing-rel hide"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-8" title=""><span id="doc-editor-menu-tab-8-errmsg"></span>Ответное</a>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-2" title=""><span id="doc-editor-menu-tab-2-errmsg"></span>Файл</a>
            </li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-3" title=""><span id="doc-editor-menu-tab-3-errmsg"></span>Ответственный</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-6" title=""><span id="doc-editor-menu-tab-6-errmsg"></span>Исполнение</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-4" title=""><span id="doc-editor-menu-tab-4-errmsg"></span>Отправитель</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-5" title=""><span id="doc-editor-menu-tab-5-errmsg"></span>Дополнительно</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-7" title=""><span id="doc-editor-menu-tab-7-errmsg"></span>Связи</a></li>
        </ul>
        <div class="tab-content p-2">
            <!-- 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
            -->
            <div class="under-tab-pane-block p-2 mx-3 mt-1 mb-3">
                <div class="popoverDivControl align-items-center custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="popoverCheckboxEnable">
                    <label class="custom-control-label" for="popoverCheckboxEnable">Включить/отключить всплывающие
                        подсказки для некоторых элементов формы<sup class="text-danger px-1"><i class='fa fa-exclamation-circle' aria-hidden='true'></i></sup></label>
                </div>
            </div>
            <!-- 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
            -->
            <div id="doc-editor-tab-1" class="tab-pane fade show active">
                <div class="section d-flex flex-column">
                    <div class="title">Регистрация документа в АТГС</div>
                    <div class="block d-flex flex-row">
                        <fieldset class="field w-25">
                            <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docID"></editor-field>
                        </fieldset>
                        <fieldset class="field">
                            <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDate"></editor-field>
                        </fieldset>
                        <fieldset class="field">
                            <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docRecipient_kodzayvtel">
                            </editor-field>
                        </fieldset>
                        <fieldset class="field">
                            <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docType"></editor-field>
                        </fieldset>
                    </div>
                    <div class="block d-flex">
                        <fieldset class="field w-100">
                            <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docAbout"></editor-field>
                        </fieldset>
                    </div>
                </div>
            </div>
            <!-- 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
            -->
            <div id="doc-editor-tab-8" class="tab-pane fade">
                <div class="section d-flex flex-column">
                    <div class="title">Ответный документ</div>
                    <div id="docType2-section-rowID-main" class="block d-flex flex-column docType2-section-only">
                        <div class="block mb-1">
                            <fieldset class="field w-100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel">
                                </editor-field>
                            </fieldset>
                            <div class="w-100 mt-2 rowID-rel-only">
                                <div class="block d-flex">
                                    <fieldset class="field checkbox-inline w-100">
                                        <editor-field name="enbl_outbox_docType_change"></editor-field>
                                    </fieldset>
                                </div>
                            </div>
                            <div id="outbox-rowID-rel-alert" class="alert alert-warning alert-fadein fade show small mx-3 mb-0 rowID-rel-only-alert" style="display:none">Если данный чекбокс установлен, то тип входящего письма (в поле
                                выше) будет изменен на
                                "Запрос ответа" и будет прописана соответствующая однозначная связь с текущим
                                документом. Эта связь
                                отображается в столбце <b>О</b><sub>o</sub> таблицы как входящих так и исходящих
                                документов.</div>
                        </div>

                        <div class="block mb-1">
                            <div class="w-100 mt-2">
                                <div class="block d-flex">
                                    <fieldset class="field checkbox-inline w-100">
                                        <editor-field name="enbl_outbox_rowIDadd_rel"></editor-field>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div id="outbox-rowIDadd-rel-alert" data-dte-e="msg-message" class="alert alert-warning alert-fadein fade show small mx-3 mb-0 rowID-rel-only-alert" style="display:none">
                            Имейте в виду, снятие этого чекбокса очистит список уже связанных исходящих документов (см.
                            ниже)</div>

                    </div>
                    <div id="docType2-section-rowID-add" class="block d-flex flex-column docType2-section-only">
                        <div class="block mb-1 rowIDadd-enbl">
                            <div class="title">Дополнительные ответные документы</div>
                            <fieldset class="field w-100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="block mb-1 mx-3 rowIDadd-enbl">
                            <fieldset class="field mb-1 w-100">
                                <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                    <div class="btn-group" role="group" aria-label="First group">
                                        <button type="button" id="mailbox-rel-links-btnAdd" class="btn btn-default ml-0 doc-links-buttons">Добавить</button>
                                        <button type="button" id="mailbox-rel-links-btnRemove" class="btn btn-danger doc-links-buttons">Удалить</button>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="field w-100">
                                <select id="linked-mail-listDocs" class="form-control" size="5" multiple>
                                </select>
                            </fieldset>
                            <fieldset class="field w-100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel">
                                </editor-field>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
            -->
            <div id="doc-editor-tab-2" class="tab-pane fade">
                <div class="Section">
                    <div class="Block100 fileDocSection">
                        <div style="width:100%; text-align:center">
                            <fieldset class="field100 msgField">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.msgDocFileID">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field50">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileIDtmp">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field50">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileIDadd">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="Block50" id="mainFile">
                            <legend>Основной файл</legend>
                            <fieldset class="field100 docFileID">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="Block50" id="addFiles">
                            <legend>Дополнительные файлы</legend>
                            <fieldset class="field100" style="display:none">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail"></editor-field>
                            </fieldset>
                            <fieldset class="field100 docFileID" style="background-color:#f1f1f1; padding:10px">
                                <form id="form-js-file"><input type="file" multiple id="js-file"></form>
                            </fieldset>
                            <div class="lnkDocFileID">
                                <div class="field100" id="uploadFiles-result" style="overflow-wrap: anywhere; text-align:left; line-height:1.5em; font-size: 0.95em">
                                </div>
                                <div class="field100" id="listFiles-result" style="overflow-wrap: anywhere; text-align:left; line-height:1.5em; font-size: 0.95em">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
            -->
            <div id="doc-editor-tab-3" class="tab-pane fade">
                <div class="section d-flex flex-column">
                    <div class="block d-flex flex-column">
                        <div class="title">Ответственный по документу</div>
                        <div class="block mb-1 w-50">
                            <fieldset class="field w-100">
                                <editor-field name="toSendEmail"></editor-field>
                            </fieldset>
                            <fieldset class="field w-100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.toSendEmail">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="block mb-1 w-50">
                            <fieldset class="field w-100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractor_kodzayvispol">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="block mb-1 w-50">
                            <div class="block" style="padding-top:7px">
                                <span style="text-align:left; font-family: 'Jura', sans-serif; text-transform:none; font-style:normal; font-weight:700; font-size:1.0em; color:#000; ">Выбраны
                                    ответственными</span>
                            </div>
                            <div class="block" style="min-height:137px; border:1px #ccc solid; border-radius:4px; padding:2px 4px; font-size:1.0em">
                                <span id="ispol-selected-str"></span>
                            </div>
                        </div>
                        <div class="block"></div>
                        <div class="block">
                            <div class="checkbox" style="padding-left:15px; padding-right:15px; padding-top:0">
                                <label class="" style="line-height:1.7em; font-weight:700"><input id="ispol-selected-clear" type="checkbox" value="">Без ответственных ( "---" ) /
                                    Очистить список, если ответственные уже были выбраны</label>
                            </div>
                        </div>
                    </div>
                    <div class="Block100">
                        <fieldset class="field100">
                            <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractorComment">
                            </editor-field>
                        </fieldset>
                    </div>
                </div>
            </div>
            <!-- 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
            -->
            <div id="doc-editor-tab-6" class="tab-pane fade">
                <div class="section d-flex flex-column">
                    <div class="title">Контроль исполнения</div>
                    <div class="block d-flex flex-row">
                        <div class="block flex-column w-50">
                            <fieldset class="field checkbox-inline">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field checkbox-inline w-100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field checkbox-inline w-100 blockDeadline">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDateDeadline">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="block flex-column w-50 blockDeadline">
                            <div class="block mb-1">
                                <fieldset class="field checkbox-inline w-100">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder1"></editor-field>
                                </fieldset>
                                <fieldset class="field checkbox-inline w-100">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder2"></editor-field>
                                </fieldset>
                            </div>
                            <div class="block pb-1 ispolStatus-visible">
                                <fieldset class="field checkbox-inline w-100">
                                    <editor-field name="ispolStatus"></editor-field>
                                </fieldset>
                            </div>
                            <div id="ispolStatus-msg" class="text-danger small"></div>
                        </div>
                    </div>
                    <div class="block d-flex flex-column" style="border: 1px #02546e dashed">
                        <div class="block w-100">
                            <fieldset class="field checkbox-inline w-100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailToRukOk">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field checkbox-inline w-100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailToRukAlarm">
                                </editor-field>
                            </fieldset>
                        </div>
                    </div>
                    <div class="block d-flex flex-column" style="border: 1px #02546e dashed">
                        <div class="block w-100">
                            <fieldset class="field checkbox-inline w-100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolWeekStatMail">
                                </editor-field>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
            -->
            <div id="doc-editor-tab-4" class="tab-pane fade">
                <div class="Section">
                    <div class="Block100 senderDocSection">
                        <legend>Отправитель документа</legend>
                        <div class="Block40">
                            <fieldset class="field60 docSourceID">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSourceID">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field40 docSourceDate">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSourceDate">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="Block60 sDS1">
                            <fieldset class="field100 docSender">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="Block100 sDS2">
                            <fieldset class="field20 enblSenderManual">
                                <editor-field name="enblSenderManual"></editor-field>
                            </fieldset>
                            <fieldset class="field40 docSenderOrg">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field40 docSenderName">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSenderName">
                                </editor-field>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
            -->
            <div id="doc-editor-tab-5" class="tab-pane fade">
                <div class="Section">
                    <div class="Block100 infoDocSection">
                        <legend>Дополнительная информация</legend>
                        <div class="Block100 iDS1">
                            <fieldset class="field100 docComment">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docComment">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field100 docComment">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp4"></editor-field>
                            </fieldset>
                        </div>
                        <div class="">
                            <fieldset class="">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp1"></editor-field>
                            </fieldset>
                            <fieldset class="">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp2"></editor-field>
                            </fieldset>
                            <fieldset class="">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp3"></editor-field>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
            -->
            <div id="doc-editor-tab-7" class="tab-pane fade">
                <div class="section d-flex flex-column">
                    <ul class="nav nav-pills mb-3 mx-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm btn-outline-dark p-2 active" id="pills-home-tab" data-toggle="pill" data-target="#pill-incoming-tab" type="button" role="tab" aria-controls="pill-incoming" aria-selected="true">Входящие</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm btn-outline-dark p-2" id="pills-profile-tab" data-toggle="pill" data-target="#pill-outgoing-tab" type="button" role="tab" aria-controls="pill-outgoing" aria-selected="false">Исходящие</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm btn-outline-dark p-2" id="pills-contact-tab" data-toggle="pill" data-target="#pill-dognet-tab" type="button" role="tab" aria-controls="pill-dognet" aria-selected="false">Договоры</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div id="pill-incoming-tab" class="tab-pane fade show active" role="tabpanel" aria-labelledby="pill-incoming-tab">
                            <div class="block mb-1">
                                <div class="title">Связь с входящими письмами</div>
                                <fieldset class="field w-100">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docIDs_links">
                                    </editor-field>
                                </fieldset>
                            </div>
                            <div class="block mb-1 mx-3">
                                <fieldset class="field mb-1 w-100">
                                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                        <div class="btn-group" role="group" aria-label="First group">
                                            <button type="button" id="inbox-doc-links-btnAdd" class="btn btn-default ml-0 doc-links-buttons">Добавить</button>
                                            <button type="button" id="inbox-doc-links-btnRemove" class="btn btn-danger doc-links-buttons">Удалить</button>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="field w-100">
                                    <select id="linked-mail-incomingDocs" class="form-control" size="5" multiple>
                                    </select>
                                </fieldset>
                                <fieldset class="field w-100">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_rowIDs_links">
                                    </editor-field>
                                </fieldset>
                            </div>
                        </div>
                        <div id="pill-outgoing-tab" class="tab-pane fade" role="tabpanel" aria-labelledby="pill-outgoing-tab">
                            <div class="block mb-1">
                                <div class="title">Связь с исходящими письмами</div>
                                <fieldset class="field w-100">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_docIDs_links">
                                    </editor-field>
                                </fieldset>
                            </div>
                            <div class="block mb-1 mx-3">
                                <fieldset class="field mb-1 w-100">
                                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                        <div class="btn-group" role="group" aria-label="First group">
                                            <button type="button" id="outbox-doc-links-btnAdd" class="btn btn-default ml-0 doc-links-buttons">Добавить</button>
                                            <button type="button" id="outbox-doc-links-btnRemove" class="btn btn-danger doc-links-buttons">Удалить</button>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="field w-100">
                                    <select id="linked-mail-outgoingDocs" class="form-control" size="5" multiple>
                                    </select>
                                </fieldset>
                                <fieldset class="field w-100">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDs_links">
                                    </editor-field>
                                </fieldset>
                            </div>
                        </div>
                        <div id="pill-dognet-tab" class="tab-pane fade" role="tabpanel" aria-labelledby="pill-dognet-tab">
                            <div class="block mb-1">
                                <div class="title">Связь с договорами</div>
                                <fieldset class="field w-100">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.dognet_docIDs_links">
                                    </editor-field>
                                </fieldset>
                            </div>
                            <div class="block mb-1 mx-3">
                                <fieldset class="field mb-1 w-100">
                                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                        <div class="btn-group" role="group" aria-label="First group">
                                            <button type="button" id="dognet-doc-links-btnAdd" class="btn btn-default ml-0 doc-links-buttons">Добавить</button>
                                            <button type="button" id="dognet-doc-links-btnRemove" class="btn btn-danger doc-links-buttons">Удалить</button>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="field w-100">
                                    <select id="linked-dognet-docs" class="form-control" size="5" multiple>
                                    </select>
                                </fieldset>
                                <fieldset class="field w-100">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.dognet_rowIDs_links">
                                    </editor-field>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
            -->
        </div>
    </div>
</div>

<script>
    // Добавить элемент в конец select
</script>