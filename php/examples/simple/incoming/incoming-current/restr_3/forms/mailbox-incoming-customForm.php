<!-- HTML -->
<?php
// ----- ----- ----- ----- -----
// Форма редактирования входящего письма
// :::
?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH . __MAIL_RESTR3; ?>/css/mailbox-incoming-customForm.css">
<style>
    @-webkit-keyframes blink {
        0% {
            background: rgba(255, 0, 0, 0.5);
        }

        50% {
            background: rgba(255, 0, 0, 0);
        }

        100% {
            background: rgba(255, 0, 0, 0.5);
        }
    }

    @keyframes blink {
        0% {
            background: rgba(255, 0, 0, 0.5);
        }

        50% {
            background: rgba(255, 0, 0, 0);
        }

        100% {
            background: rgba(255, 0, 0, 0.5);
        }
    }

    fieldset.inbox_controlIspolMailSpecialNotifyDL.blink {
        -webkit-animation-direction: normal;
        -webkit-animation-duration: 5s;
        -webkit-animation-iteration-count: infinite;
        -webkit-animation-name: blink;
        -webkit-animation-timing-function: ease;
        animation-direction: normal;
        animation-duration: 5s;
        animation-iteration-count: infinite;
        animation-name: blink;
        animation-timing-function: ease;
    }
</style>

<div id="customForm-mail-main-inbox" class="editorform">
    <div id="editorform-editor-tabs" class="editorform-tabs w-100">
        <ul id="editorform-editor-tabs-menu" class="nav nav-tabs editorform-tabs-menu">
            <li class="nav-item tab1"><a class="nav-link active" data-toggle="tab" href="#doc-editor-tab-1" title="">Регистрация</a></li>
            <li class="nav-item tab8 tab-outgoing-rel hide"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-8" title="">Ответное</a></li>
            <li class="nav-item tab2"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-2" title="">Файл</a>
            </li>
            <li class="nav-item tab3"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-3" title="">Ответственный</a></li>
            <li class="nav-item tab6"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-6" title="">Исполнение</a></li>
            <li class="nav-item tab4"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-4" title="">Отправитель</a></li>
            <li class="nav-item tab5"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-5" title="">Дополнительно</a></li>
            <li class="nav-item tab7"><a class="nav-link" data-toggle="tab" href="#doc-editor-tab-7" title="">Связи</a>
            </li>
        </ul>
        <div class="tab-content p-2">
            <!-- 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
            -->
            <div class="under-tab-pane-block p-2 mx-3 mt-1 mb-3">
                <div class="popoverDivControl align-items-center custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="popoverCheckboxEnable" value="1" checked="checked">
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
                    <div class="title">Документ, на который вы создаете ответ</div>
                    <div id="docType2-section-rowID-main" class="block d-flex flex-column docType2-section-only">
                        <div class="block mb-1">
                            <fieldset class="field w-100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.outbox_rowID_rel">
                                </editor-field>
                            </fieldset>
                            <!-- -->
                            <div id="outbox-relExist-alert" class="alert alert-danger alert-fadein fade show small mx-3 mb-0 outbox-relExist-alert" style="display:none">
                                <div class="outbox-relExist-alert-msg"></div>
                            </div>
                            <!-- -->
                            <div id="outbox-setCheckout-alert" class="alert alert-warning alert-fadein fade show small mx-3 mb-0 outbox-setCheckout-alert" style="display:none">
                                <p class="mb-1">Вы в числе ответственных по этому документу, этот документ стоит
                                    на контроле исполнения и не отмечен как полностью исполненный. Установив чекбох ниже
                                    вы отметите выбранный исходящий
                                    документ как полностью исполненный (всеми ответственными) и закроете вопрос. Если вы
                                    не уверены, что это будет правильно, лучше тогда так не делать.</p>
                                <fieldset class="field checkbox-inline w-100">
                                    <editor-field name="set_outbox_fullCheckout">
                                    </editor-field>
                                </fieldset>
                            </div>
                            <!-- -->
                            <div class="w-100 mt-2 rowID-rel-only">
                                <div class="block d-flex">
                                    <fieldset class="field checkbox-inline w-100">
                                        <editor-field name="enbl_outbox_docType_change"></editor-field>
                                    </fieldset>
                                </div>
                            </div>
                            <div id="outbox-rowID-rel-alert" class="alert alert-warning alert-fadein fade show small mx-3 mb-0 rowID-rel-only-alert" style="display:none">Если данный чекбокс установлен, то тип исходящего документа (в
                                поле
                                выше) будет изменен на
                                "Запрос ответа" и будет прописана соответствующая однозначная связь с текущим
                                документом. Эта связь
                                отображается в столбце <b>ЗО</b><sub>o</sub> таблицы как входящих так и исходящих
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
                        <div id="outbox-rowIDadd-rel-alert" data-dte-e="msg-message" class="alert alert-warning alert-fadein fade show small mx-3 mb-0 rowID-rel-only-alert" style="display:none">Дополнительные исходящие документы, на которые текущий документ также
                            определен как ответный. Имейте в виду, снятие этого чекбокса очистит список уже связанных
                            исходящих документов (см. ниже)</div>

                    </div>
                    <div id="docType2-section-rowID-add" class="block d-flex flex-column docType2-section-only">
                        <div class="block mb-1 rowIDadd-enbl">
                            <div class="title">Дополнительные документы</div>
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
                <div class="section d-flex flex-column">
                    <div class="d-flex flex-column">
                        <fieldset class="field w-100">
                            <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.msgDocFileID">
                            </editor-field>
                        </fieldset>
                    </div>
                    <div class="d-flex flex-row divUploads">
                        <div class="block d-flex flex-column w-50">
                            <div class="title show">Основной файл</div>
                            <fieldset class="field">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileIDtmp">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileIDadd">
                                </editor-field>
                            </fieldset>
                            <div class="mainFile" id="mainFile">
                                <fieldset class="field docFileID">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docFileID">
                                    </editor-field>
                                </fieldset>
                            </div>
                            <!-- <div class="mx-3 mt-1 mb-3 rounded-sm" style="border: 1px dashed #ccc">
                                <svg class="bd-placeholder-img card-img-top" width="100%" height="180"
                                     xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Image cap"
                                     preserveAspectRatio="xMidYMid slice" focusable="false">
                                    <title>Зона для загрузки основного файла методом Drag&drop</title>
                                    <rect width="100%" height="100%" fill="#FAFAFA"></rect><text x="50%" y="50%"
                                          fill="#999999" dy=".3em" text-anchor="middle">Drag & Drop zone (скоро)</text>
                                </svg>
                            </div> -->
                        </div>
                        <div class="block d-flex flex-column w-50">
                            <div class="title show">Дополнительные файлы</div>
                            <fieldset class="field">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.koddocmail"></editor-field>
                            </fieldset>
                            <div class="addFiles" id="addFiles">
                                <fieldset class="field docFileID">
                                    <form id="form-js-file">
                                        <input type="file" class="" multiple id="js-file">
                                    </form>
                                </fieldset>
                                <div class="lnkDocFileID">
                                    <div class="field" id="listFiles-result">
                                    </div>
                                    <div class="field" id="uploadFiles-result">
                                        <table class="table table-striped table-listattachments-inform">
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="mx-3 mt-1 mb-3 rounded-sm" style="border: 1px dashed #ccc">
                                <svg class="bd-placeholder-img card-img-top" width="100%" height="180"
                                     xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Image cap"
                                     preserveAspectRatio="xMidYMid slice" focusable="false">
                                    <title>Зона для загрузки дополнительных файлов методом Drag&drop</title>
                                    <rect width="100%" height="100%" fill="#FAFAFA"></rect><text x="50%" y="50%"
                                          fill="#999999" dy=".3em" text-anchor="middle">Drag & Drop zone (скоро)</text>
                                </svg>
                            </div> -->
                        </div>
                    </div>
                    <p class="small text-right" style="color:#AAAAAA; font-size:0.7rem; margin: 5px 20px"><span class="px-1 text-dark"><sup><i class="fa-solid fa-star-of-life fa-xs"></i></sup></span>В
                        ближайшее время будет подключен метод Drag & Drop загрузки как основного так и дополнительных
                        файлов. Следите за обновлениями.</p>
                </div>
            </div>
            <!-- 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
              ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
            -->
            <div id="doc-editor-tab-3" class="tab-pane fade">
                <div class="section d-flex flex-column">
                    <div class="title">Ответственный по документу</div>
                    <div class="block d-flex flex-row">
                        <div class="w-50">
                            <div class="block d-flex flex-column">
                                <fieldset class="field">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docContractor_kodzayvispol">
                                    </editor-field>
                                </fieldset>
                                <fieldset class="field">
                                    <editor-field name="selectedContractorIDs"></editor-field>
                                </fieldset>
                            </div>
                        </div>
                        <div class="w-50 pr-3">
                            <div class="block d-flex flex-column">
                                <fieldset class="field">
                                    <div class="DTE_Field form-group row">
                                        <label class="w-100" data-dte-e="label" for="ispol-selected-str">Выбраны
                                            ответственными</label>
                                    </div>
                                </fieldset>
                                <div class="block py-3 px-2 bg-light text-dark rounded-lg" id="ispol-selected-str">
                                </div>
                                <fieldset class="field checkbox-inline w-100">
                                    <editor-field name="toSendEmail"></editor-field>
                                </fieldset>
                                <fieldset class="field checkbox-inline w-100">
                                    <editor-field name="ispolSelectedClear"></editor-field>
                                </fieldset>
                                <fieldset class="field">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.toSendEmail">
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
            <div id="doc-editor-tab-6" class="tab-pane fade">
                <div class="section d-flex flex-column">
                    <div class="title">Контроль исполнения</div>
                    <div class="block d-flex flex-row">
                        <div class="block d-flex flex-column w-50">
                            <fieldset class="field checkbox-inline">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolActive">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field checkbox-inline">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolUseDeadline">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field textbox-inline input-fitcontent">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docDateDeadline">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="block d-flex flex-column w-50">
                            <fieldset class="field checkbox-inline ispolStatus-visible">
                                <editor-field name="ispolStatus"></editor-field>
                            </fieldset>
                            <fieldset class="field checkbox-inline ispolStatus-visible">
                                <editor-field name="ispolStatusOtherOn"></editor-field>
                            </fieldset>
                            <fieldset class="field checkbox-inline ispolStatus-visible">
                                <editor-field name="ispolStatusOtherOff"></editor-field>
                            </fieldset>
                        </div>
                    </div>
                    <div class="block d-flex flex-row">
                        <div class="block d-flex flex-column w-100">
                            <fieldset class="field ispolStatus-visible">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolCheckoutComment">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field ispolStatus-visible">
                                <editor-field name="inbox_controlIspolCheckoutComment_old">
                                </editor-field>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="section d-flex flex-column">
                    <div class="block d-flex flex-row">
                        <div class="block d-flex flex-column w-50">
                            <div class="title">Уведомления ответственного(ых)</div>
                            <fieldset class="field checkbox-inline">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder1">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field checkbox-inline">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailReminder2">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field checkbox-inline">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyDL">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field checkbox-inline">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailNotifyCheckout">
                                </editor-field>
                            </fieldset>
                            <div id="ispolStatus_msg_1" class="field"></div>
                        </div>
                        <div class="block d-flex flex-column w-50">
                            <div class="title">Дополнительные уведомления</div>
                            <fieldset class="field checkbox-inline">
                                <editor-field name="checkbox_controlIspolMailUserListNotifyCheckout">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field checkbox-inline">
                                <editor-field name="checkbox_controlIspolMailUserListNotifyDL">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field checkbox-inline inbox_controlIspolMailSpecialNotifyDL">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailSpecialNotifyDL">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field w-100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailUserListNotifyCheckout">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field w-100">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_controlIspolMailUserListNotifyDL">
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
                <div class="section d-flex flex-column">
                    <div class="title">Отправитель документа</div>
                    <div class="block d-flex flex-row">
                        <div class="block flex-column">
                            <fieldset class="field docSourceID">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSourceID">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field docSourceDate">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSourceDate">
                                </editor-field>
                            </fieldset>
                        </div>
                        <div class="block w-75 flex-column">
                            <fieldset class="field docSender">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender_kodzakaz">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field docSenderOrg">
                                <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docSender">
                                </editor-field>
                            </fieldset>
                            <fieldset class="field checkbox-inline enblSenderManual">
                                <editor-field name="enblSenderManual"></editor-field>
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
                <div class="section d-flex flex-column">
                    <div class="title">Дополнительная информация</div>
                    <div class="block d-flex flex-column">
                        <fieldset class="field w-100">
                            <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_docComment">
                            </editor-field>
                        </fieldset>
                        <fieldset class="field docComment">
                            <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp4"></editor-field>
                        </fieldset>
                    </div>
                    <div class="block">
                        <fieldset class="field">
                            <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp1"></editor-field>
                        </fieldset>
                        <fieldset class="field">
                            <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp2"></editor-field>
                        </fieldset>
                        <fieldset class="field">
                            <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.inbox_tmp3"></editor-field>
                        </fieldset>
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
                            <button class="nav-link btn-sm btn-outline-dark p-2 active" data-toggle="pill" data-target="#pill-incoming-tab" type="button" role="tab" aria-controls="pill-incoming" aria-selected="true">Входящие</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm btn-outline-dark p-2" data-toggle="pill" data-target="#pill-outgoing-tab" type="button" role="tab" aria-controls="pill-outgoing" aria-selected="false">Исходящие</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm btn-outline-dark p-2" data-toggle="pill" data-target="#pill-dognet-tab" type="button" role="tab" aria-controls="pill-dognet" aria-selected="false">Договоры</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm btn-outline-dark p-2" data-toggle="pill" data-target="#pill-sp-tab" type="button" role="tab" aria-controls="pill-sp" aria-selected="false">Контрагенты</button>
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
                        <div id="pill-sp-tab" class="tab-pane fade" role="tabpanel" aria-labelledby="pill-sp-tab">
                            <div class="block mb-1">
                                <div class="title">Связь с контрагентами</div>
                                <fieldset class="field w-100">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.sp_docIDs_links">
                                    </editor-field>
                                </fieldset>
                            </div>
                            <div class="block mb-1 mx-3">
                                <fieldset class="field mb-1 w-100">
                                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                        <div class="btn-group" role="group" aria-label="First group">
                                            <button type="button" id="sp-doc-links-btnAdd" class="btn btn-default ml-0 doc-links-buttons">Добавить</button>
                                            <button type="button" id="sp-doc-links-btnRemove" class="btn btn-danger doc-links-buttons">Удалить</button>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="field w-100">
                                    <select id="linked-sp-docs" class="form-control" size="5" multiple>
                                    </select>
                                </fieldset>
                                <fieldset class="field w-100">
                                    <editor-field name="<?php echo __MAIL_INCOMING_TABLENAME; ?>.sp_rowIDs_links">
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