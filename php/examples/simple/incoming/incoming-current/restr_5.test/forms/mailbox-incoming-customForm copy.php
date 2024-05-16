<?php
// ----- ----- ----- ----- -----
// Форма редактирования входящего письма
// :::
?>
<link rel="stylesheet"
      href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR5; ?>/css/mailbox-incoming-editorform.css">
<link href="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/_assets/libs/Other/Bootstrap-fileinput/css/fileinput.min.css"
      rel="stylesheet" />
<script src="<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/_assets/libs/Other/Bootstrap-fileinput/js/fileinput.min.js">
</script>
<script type="text/javascript" src="<?php echo __ROOT; ?>/_assets/js/selectize/js/selectize.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo __ROOT; ?>/_assets/js/selectize/css/selectize.css" />

<style>
@font-face {
    font-family: 'Stolzl';
    src: url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Light.eot');
    src: local('Stolzl Light'), local('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Light'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Light.eot?#iefix') format('embedded-opentype'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Light.woff2') format('woff2'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Light.woff') format('woff'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Light.ttf') format('truetype');
    font-weight: 300;
    font-style: normal;
}

@font-face {
    font-family: 'Stolzl';
    src: url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Thin.eot');
    src: local('Stolzl Thin'), local('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Thin'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Thin.eot?#iefix') format('embedded-opentype'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Thin.woff2') format('woff2'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Thin.woff') format('woff'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Thin.ttf') format('truetype');
    font-weight: 100;
    font-style: normal;
}

@font-face {
    font-family: 'Stolzl Book';
    src: url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Book.eot');
    src: local('Stolzl Book'), local('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Book'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Book.eot?#iefix') format('embedded-opentype'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Book.woff2') format('woff2'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Book.woff') format('woff'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Book.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
}

@font-face {
    font-family: 'Stolzl';
    src: url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Regular.eot');
    src: local('Stolzl Regular'), local('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Regular'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Regular.eot?#iefix') format('embedded-opentype'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Regular.woff2') format('woff2'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Regular.woff') format('woff'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Regular.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
}

@font-face {
    font-family: 'Stolzl';
    src: url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Bold.eot');
    src: local('Stolzl Bold'), local('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Bold'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Bold.eot?#iefix') format('embedded-opentype'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Bold.woff2') format('woff2'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Bold.woff') format('woff'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Bold.ttf') format('truetype');
    font-weight: bold;
    font-style: normal;
}

@font-face {
    font-family: 'Stolzl';
    src: url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Medium.eot');
    src: local('Stolzl Medium'), local('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Medium'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Medium.eot?#iefix') format('embedded-opentype'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Medium.woff2') format('woff2'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Medium.woff') format('woff'),
        url('<?php echo __ROOT; ?>/_assets/fonts/Stolzl-Medium.ttf') format('truetype');
    font-weight: 500;
    font-style: normal;
}

#DTE_Field_mailbox_incoming_test-inbox_docType,
#DTE_Field_mailbox_incoming_test-inbox_docRecipient_kodzayvtel {
    height: 28px;
}

span.select2.select2-container.select2-container--default.select2-container--focus {
    width: 100%;
}

span.select2-container>span>span.select2-selection {
    height: 28px;
    text-align: left;
    font-size: 13px;
}

.select2-results__option {
    padding: 4px 8px;
    line-height: 1.2em;
}

#select2-DTE_Field_mailbox_incoming-inbox_docSender_kodzakaz-container {
    line-height: 30px;
    text-align: left;
}

.select2-container--default .select2-results>.select2-results__options {
    max-height: 200px;
    overflow-y: auto;
    font-size: 13px;
    line-height: 1em;
}

span.select2-container>span.selection>span.select2-selection>span.select2-selection__arrow {
    height: 28px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    max-width: 920px;
}

.select2-container--default .select2-selection--single {
    border: 1px solid #0446f9;
}

.select2-container--open .select2-dropdown--below {
    border-color: #0446f9;
}

#editorform .popoverDivControl {
    font-family: "Play", sans-serif;
    text-align: left;
    font-size: 1em;
    margin-bottom: 10px;
    padding: 8px 5px;
    border: 1px #ff0000 dashed;
}

#editorform .popoverDivControl label {
    line-height: normal;
}

#editorform .popoverDivControl>label>div {
    margin-top: 3px;
    margin-left: 5px;
    font-weight: 600;
}

#editorform .popover {
    min-width: 420px;
    font-size: 12px;
    font-family: "Stolzl", Arial, Helvetica Neue, Helvetica, sans-serif;
    /* font-family: "Play", sans-serif; */
}

#editorform .popover-title {
    font-size: 1.2em;
    color: #fff;
    background-color: #ff0000;
    font-weight: 400;
    letter-spacing: 0.1em;
    padding: 10px 14px;
}

#editorform .popover-content {}


#editorform .popover-content p {
    color: #666;
    font-size: 0.9em;
    font-weight: 400;
    line-height: 1.4em;
    margin-bottom: 3px;
    text-align: justify;
}

#editorform .popover-content p b {
    color: #111;
}

#editorform .popover-content div.warning {
    margin-top: 10px;
    margin-bottom: 10px;
    border: 2px #02546e solid;
    border-radius: 5px;
    padding: 5px 10px;
    text-align: justify;
}

#editorform .popover-content div.warning p {
    color: #000;
    line-height: 1.25em;
}

#editorform .popover-content h3,
#editorform .popover-content h4 {
    color: #111;
    margin-top: 12px;
    margin-bottom: 3px;
}

#editorform .popover-content h3:first-child,
#editorform .popover-content h4:first-child {
    margin-top: 0;
    margin-bottom: 3px;
}

#editorform .popover-content h3 {
    font-size: 1.2em;
}

#editorform .popover-content h4 {
    font-size: 1.1em;
}

#editorform .popover-content div.knoweledge-base-link {
    margin-top: 10px;
    text-align: right;
}

#editorform .popover-content div.knoweledge-base-link a {
    color: #1a73e8;
    font-size: 0.9em;
    font-weight: 400;
    text-decoration: underline;
}

#editorform .popover-content div.knoweledge-base-link a:hover {
    text-decoration: none;
}
</style>

<div id="editorform">
    <div id="editorform-editor-tabs" style="width:100%">
        <ul id="editorform-editor-tabs-menu" class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#doc-editor-tab-1" title=""><span
                          id="doc-editor-menu-tab-1-errmsg"></span>Регистрация</a></li>
            <li><a data-toggle="tab" href="#doc-editor-tab-2" title=""><span
                          id="doc-editor-menu-tab-2-errmsg"></span>Файл</a>
            </li>
            <li><a data-toggle="tab" href="#doc-editor-tab-3" title=""><span
                          id="doc-editor-menu-tab-3-errmsg"></span>Ответственный</a></li>
            <li><a data-toggle="tab" href="#doc-editor-tab-6" title=""><span
                          id="doc-editor-menu-tab-6-errmsg"></span>Исполнение</a></li>
            <li><a data-toggle="tab" href="#doc-editor-tab-4" title=""><span
                          id="doc-editor-menu-tab-4-errmsg"></span>Отправитель</a></li>
            <li><a data-toggle="tab" href="#doc-editor-tab-5" title=""><span
                          id="doc-editor-menu-tab-5-errmsg"></span>Дополнительно</a></li>
            <li><a data-toggle="tab" href="#doc-editor-tab-7" title=""><span
                          id="doc-editor-menu-tab-7-errmsg"></span>Связи</a></li>
        </ul>
        <div class="tab-content" style="padding:5px">
            <div class="popoverDivControl checkbox" style="">
                <label>
                    <input id="popoverCheckboxEnable" type="checkbox" value="">
                    <div>Включить/отключить всплывающие подсказки для некоторых элементов формы<sup
                             style='color:red; padding:0 3px'><i class='fa fa-exclamation-circle'
                               aria-hidden='true'></i></sup>
                    </div>
                </label>
            </div>
            <div id="doc-editor-tab-1" class="tab-pane fade in active">
                <div class="Section">
                    <div class="Block100">
                        <legend>Регистрация документа в АТГС</legend>
                        <div class="Block15">
                            <fieldset class="field100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docID">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="Block25">
                            <fieldset class="field100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDate">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="Block30">
                            <fieldset class="field100">
                                <editor-field
                                              name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docRecipient_kodzayvtel">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="Block30">
                            <fieldset class="field100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docType">
                                </editor-field>
                            </fieldset>
                        </div>
                    </div>
                    <div class="Block100">
                        <div class="Block100">
                            <fieldset class="field100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docAbout">
                                </editor-field>
                            </fieldset>
                        </div>
                    </div>
                    <div class="Block100 docType2-section-only">
                        <legend>Ответный документ</legend>





                        <div class="Block100" style="margin-bottom:15px">
                            <fieldset class="field100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel">
                                </editor-field>
                            </fieldset>
                            <div class="Block100 fieldset-table-row rowID-rel-only" style="padding:0; margin-top:-20px">
                                <div class="fieldset-table-cell" style="padding-bottom:3px">
                                    <fieldset>
                                        <editor-field name="enbl_outbox_docType_change">
                                        </editor-field>
                                    </fieldset>
                                </div>
                                <div class="fieldset-table-cell" style="width:100%">
                                    <div>
                                        <div class="chekbox-inline-text text-danger"
                                             style="font-family:'Play',sans-serif; font-style:normal; font-weight:normal; font-size:1em">
                                            <b>Изменить тип указанного исходящего письма на "Запрос ответа" и связать с
                                                текущим входящим
                                                документом (исходящий запрос - текущий ответ)</b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="outbox_rowID_rel-message" class="rowID-rel-only" data-dte-e="msg-message"
                                 style="display:block; margin-top:-15px; margin-left:15px; margin-right:15px; line-height:1.2em; padding-left:28px;">
                                Если данный чекбокс установлен, то тип входящего письма (в поле выше) будет изменен на
                                "Запрос ответа" и
                                будет прописана соответствующая однозначная связь с текущим документом. Эта связь
                                отображается в столбце
                                <b>О</b><sub>o</sub> таблицы как входящих так и исходящих документов.
                            </div>
                        </div>





                        <div class="Block100" style="padding-left:15px; padding-right:15px">
                            <div class="Block100 fieldset-table-row"
                                 style="color:#FAFAFA; background-color:#02546E; margin-bottom:5px; padding:0">
                                <div class="fieldset-table-cell" style="padding-bottom:0">
                                    <fieldset>
                                        <editor-field name="enbl_outbox_rowIDadd_rel">
                                        </editor-field>
                                    </fieldset>
                                </div>
                                <div class="fieldset-table-cell" style="width:100%">
                                    <div>
                                        <div class="">
                                            <b>Дополнительные исходящие письма, на которые текущий входящий документ
                                                можно считать ответным</b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="outbox_rowIDadd_rel-message" data-dte-e="msg-message"><span
                                      class="glyphicon glyphicon-exclamation-sign"
                                      style="padding-right:5px"></span>Имейте в виду, снятие
                                этого чекбокса очистит список уже связанных
                                исходящих документов (см. ниже)</div>
                        </div>
                    </div>
                    <div class="Block100 docType2-section-only">
                        <div class="Block100 rowIDadd-enbl">
                            <fieldset class="field100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDList_rel">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field100" style="padding:0 15px 5px">
                                <button id="mailbox-rel-links-btnAdd"
                                        class="btn btn-xs btn-primary doc-links-buttons">Добавить</button>
                                <button id="mailbox-rel-links-btnRemove"
                                        class="btn btn-xs btn-danger doc-links-buttons">Удалить</button>
                            </fieldset>
                        </div>
                        <div class="Block100 rowIDadd-enbl">
                            <fieldset class="field100" style="padding:0 15px">
                                <select id="linked-mail-listDocs" class="form-control" size="5" multiple>
                                </select>
                            </fieldset>
                            <fieldset class="field100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDadd_rel">
                                </editor-field>
                            </fieldset>
                        </div>
                    </div>
                    <div class="Block100 docType3-section-only">
                        <legend>Документы, которые являются ответом на текущий запрос</legend>
                        <div class="Block100">
                        </div>
                    </div>
                </div>
            </div>
            <div id="doc-editor-tab-2" class="tab-pane fade in">
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
                                <div class="field100" id="uploadFiles-result"
                                     style="overflow-wrap: anywhere; text-align:left; line-height:1.5em; font-size: 0.95em">
                                </div>
                                <div class="field100" id="listFiles-result"
                                     style="overflow-wrap: anywhere; text-align:left; line-height:1.5em; font-size: 0.95em">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="doc-editor-tab-3" class="tab-pane fade in">
                <div class="Section">
                    <div class="Block100">
                        <legend>Ответственный по документу</legend>
                        <div class="Block20">
                            <fieldset class="field100">
                                <editor-field name="toSendEmail"></editor-field>
                            </fieldset>
                            <fieldset class="field100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.toSendEmail">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="Block40">
                            <fieldset class="field100">
                                <editor-field
                                              name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractor_kodzayvispol">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="Block40">
                            <div class="Block100" style="padding-top:7px">
                                <span
                                      style="text-align:left; font-family: 'Jura', sans-serif; text-transform:none; font-style:normal; font-weight:700; font-size:1.0em; color:#000; ">Выбраны
                                    ответственными</span>
                            </div>
                            <div class="Block100"
                                 style="min-height:137px; border:1px #ccc solid; border-radius:4px; padding:2px 4px; font-size:1.0em">
                                <span id="ispol-selected-str"></span>
                            </div>
                        </div>
                        <div class="Block20"></div>
                        <div class="Block80">
                            <div class="checkbox" style="padding-left:15px; padding-right:15px; padding-top:0">
                                <label class="" style="line-height:1.7em; font-weight:700"><input
                                           id="ispol-selected-clear" type="checkbox" value="">Без ответственных ( "---"
                                    ) /
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
            <div id="doc-editor-tab-6" class="tab-pane fade in">
                <div class="Section">
                    <div class="Block100">
                        <legend>Контроль исполнения</legend>
                        <div class="Block60">
                            <div class="Block100 fieldset-table-row"
                                 style="background-color: #F1F1F1; margin-bottom: 5px;">
                                <div class="fieldset-table-cell" style="padding-bottom:1px">
                                    <fieldset>
                                        <editor-field
                                                      name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive">
                                        </editor-field>
                                    </fieldset>
                                </div>
                                <div class="fieldset-table-cell" style="width:100%">
                                    <div>
                                        <div class="chekbox-inline-text">Поставить письмо на контроль исполнения</div>
                                    </div>
                                </div>
                            </div>
                            <div class="Block100 fieldset-table-row"
                                 style="background-color: #F1F1F1; margin-bottom: 5px;">
                                <div class="fieldset-table-cell" style="padding-bottom:1px">
                                    <fieldset>
                                        <editor-field
                                                      name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline">
                                        </editor-field>
                                    </fieldset>
                                </div>
                                <div class="fieldset-table-cell" style="width:100%">
                                    <div>
                                        <div class="chekbox-inline-text">Использовать дедлайн</div>
                                    </div>
                                </div>
                            </div>
                            <div class="Block40 blockDeadline">
                                <fieldset class="field100">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDateDeadline">
                                    </editor-field>
                                </fieldset>
                            </div>
                            <div class="Block60 blockDeadline">
                                <div class="Block100 fieldset-table-row">
                                    <div class="fieldset-table-cell" style="padding-bottom:3px">
                                        <fieldset>
                                            <editor-field
                                                          name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder1">
                                            </editor-field>
                                        </fieldset>
                                    </div>
                                    <div class="fieldset-table-cell" style="width:100%">
                                        <div>
                                            <div class="chekbox-inline-text"><b>Напоминание исполнителю(ям) #1 (когда до
                                                    дедлайна остается менее 3-х суток )</b>
                                            </div>
                                            <div id="controlIspolMailReminder1-Msg" class="small" style="color:#1a73e8">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="Block100 fieldset-table-row">
                                    <div class="fieldset-table-cell" style="padding-bottom:3px">
                                        <fieldset>
                                            <editor-field
                                                          name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder2">
                                            </editor-field>
                                        </fieldset>
                                    </div>
                                    <div class="fieldset-table-cell" style="width:100%">
                                        <div>
                                            <div class="chekbox-inline-text"><b>Напоминание исполнителю(ям) #2 (когда до
                                                    дедлайна остается менее 3-х суток )</b>
                                            </div>
                                            <div id="controlIspolMailReminder2-Msg" class="small" style="color:#1a73e8">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="Block40">
                            <div class="Block100 fieldset-table-row"
                                 style="background-color: #F1F1F1; margin-bottom: 5px;">
                                <div class="fieldset-table-cell ispolStatus-visible" style="padding-bottom:1px">
                                    <fieldset>
                                        <editor-field name="ispolStatus">
                                        </editor-field>
                                    </fieldset>
                                </div>
                                <div class="fieldset-table-cell ispolStatus-visible" style="width:100%">
                                    <div>
                                        <div class="chekbox-inline-text ispolStatus-chkText"><b>Я выполнил документ</b>
                                        </div>
                                    </div>
                                </div>
                                <div id="ispolStatus-msg" class="text-danger small"></div>
                            </div>
                            <div class="Block100"
                                 style="border: 1px #02546e dashed; border-radius: 5px; margin-bottom: 5px;">
                                <div class="Block100 fieldset-table-row">
                                    <div class="fieldset-table-cell" style="padding-bottom:3px">
                                        <fieldset>
                                            <editor-field
                                                          name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailToRukOk">
                                            </editor-field>
                                        </fieldset>
                                    </div>
                                    <div class="fieldset-table-cell" style="width:100%">
                                        <div>
                                            <div class="chekbox-inline-text" style=""><b>Уведомить
                                                    руководителя на email об
                                                    исполнении</b></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="Block100 fieldset-table-row">
                                    <div class="fieldset-table-cell" style="padding-bottom:3px">
                                        <fieldset>
                                            <editor-field
                                                          name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailToRukAlarm">
                                            </editor-field>
                                        </fieldset>
                                    </div>
                                    <div class="fieldset-table-cell" style="width:100%">
                                        <div>
                                            <div class="chekbox-inline-text" style=""><b>Уведомить
                                                    руководителя на email о
                                                    просрочке (более суток)</b></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="Block100"
                                 style="border: 1px #02546e dashed; border-radius: 5px; margin-bottom: 5px; padding: 5px 0;">
                                <div class="Block100 fieldset-table-row">
                                    <div class="fieldset-table-cell" style="padding-bottom:3px">
                                        <fieldset>
                                            <editor-field
                                                          name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolWeekStatMail">
                                            </editor-field>
                                        </fieldset>
                                    </div>
                                    <div class="fieldset-table-cell" style="width:100%">
                                        <div>
                                            <div class="chekbox-inline-text" style=""><b>Получать на
                                                    еженедельную рассылку (пн,
                                                    10:00) о невыполненных документах, стоящих на контроле исполнения
                                                    (КИ)</b></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="doc-editor-tab-4" class="tab-pane fade in">
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
            <div id="doc-editor-tab-5" class="tab-pane fade in">
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
            <div id="doc-editor-tab-7" class="tab-pane fade in">
                <div class="Section">
                    <ul class="nav nav-pills" style="margin-bottom:10px">
                        <li class="active"><a data-toggle="pill" href="#pill-incoming-tab">Входящие</a></li>
                        <li><a data-toggle="pill" href="#pill-outgoing-tab">Исходящие</a></li>
                        <li><a data-toggle="pill" href="#pill-dognet-tab">Договоры</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="pill-incoming-tab" class="tab-pane fade in active">
                            <div class="Block100" style="margin-bottom: 20px">
                                <legend>Связь с входящими письмами</legend>
                                <div class="Block100">
                                    <fieldset class="field100">
                                        <editor-field
                                                      name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docIDs_links">
                                        </editor-field>
                                    </fieldset>
                                </div>
                                <div class="Block100">
                                    <fieldset class="field100" style="padding:0 15px 5px">
                                        <button id="inbox-doc-links-btnAdd"
                                                class="btn btn-xs btn-primary doc-links-buttons">Добавить</button>
                                        <button id="inbox-doc-links-btnRemove"
                                                class="btn btn-xs btn-primary doc-links-buttons">Удалить
                                            связь</button>
                                    </fieldset>
                                </div>
                                <div class="Block100">
                                    <fieldset class="field100" style="padding:0 15px">
                                        <select id="linked-mail-incomingDocs" class="form-control" size="5" multiple>
                                        </select>
                                    </fieldset>
                                    <fieldset class="field100">
                                        <editor-field
                                                      name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_rowIDs_links">
                                        </editor-field>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div id="pill-outgoing-tab" class="tab-pane fade">
                            <div class="Block100" style="margin-bottom: 20px">
                                <legend>Связь с исходящими письмами</legend>
                                <div class="Block100">
                                    <fieldset class="field100">
                                        <editor-field
                                                      name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_docIDs_links">
                                        </editor-field>
                                    </fieldset>
                                </div>
                                <div class="Block100">
                                    <fieldset class="field100" style="padding:0 15px 5px">
                                        <button id="outbox-doc-links-btnAdd"
                                                class="btn btn-xs btn-primary doc-links-buttons">Добавить</button>
                                        <button id="outbox-doc-links-btnRemove"
                                                class="btn btn-xs btn-primary doc-links-buttons">Удалить
                                            связь</button>
                                    </fieldset>
                                </div>
                                <div class="Block100">
                                    <fieldset class="field100" style="padding:0 15px">
                                        <select id="linked-mail-outgoingDocs" class="form-control" size="5" multiple>
                                        </select>
                                    </fieldset>
                                    <fieldset class="field100">
                                        <editor-field
                                                      name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowIDs_links">
                                        </editor-field>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div id="pill-dognet-tab" class="tab-pane fade">
                            <div class="Block100" style="margin-bottom: 20px">
                                <legend>Связь с договорами</legend>
                                <div class="Block100">
                                    <fieldset class="field100">
                                        <editor-field
                                                      name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.dognet_docIDs_links">
                                        </editor-field>
                                    </fieldset>
                                </div>
                                <div class="Block100">
                                    <fieldset class="field100" style="padding:0 15px 5px">
                                        <button id="dognet-doc-links-btnAdd"
                                                class="btn btn-xs btn-primary doc-links-buttons">Добавить</button>
                                        <button id="dognet-doc-links-btnRemove"
                                                class="btn btn-xs btn-primary doc-links-buttons">Удалить
                                            связь</button>
                                    </fieldset>
                                </div>
                                <div class="Block100">
                                    <fieldset class="field100" style="padding:0 15px">
                                        <select id="linked-dognet-docs" class="form-control" size="5" multiple>
                                        </select>
                                    </fieldset>
                                    <fieldset class="field100">
                                        <editor-field
                                                      name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.dognet_rowIDs_links">
                                        </editor-field>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Добавить элемент в конец select
</script>