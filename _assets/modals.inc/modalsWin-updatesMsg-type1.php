<?php
/*

ВСПЛЫВАЮЩЕЕ ОКНО : ОБНОВЛЕНИЯ
-----

*/

?>

<!-- CSS-таблица модального окна -->
<style>
#modalsWin-updatesMsg-type1 div.modal-dialog {
    width: 50%;
    min-width: 640px;
    max-width: 800px;
}

#modalsWin-updatesMsg-type1 div.modal-content {
    background-color: #FFF;
}

#modalsWin-updatesMsg-type1 div.updatesMsg-content {
    margin-top: 20px;
    margin-bottom: 20px;
}

#modalsWin-updatesMsg-type1 div.updatesMsg-content img {
    padding: 10px;
}

#modalsWin-updatesMsg-type1 div.updatesMsg-author {
    text-align: right;
    font-family: 'Stolzl Book', Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 0.85em;
    color: #ccc;
}

#modalsWin-updatesMsg-type1 div.updatesMsg-date {
    margin-bottom: 10px;
    text-align: right;
    font-family: 'Stolzl Book', Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 0.85em;
    color: #ccc;
}

#modalsWin-updatesMsg-type1 div.updatesMsg-content p,
#modalsWin-updatesMsg-type1 div.updatesMsg-content ul li {
    font-family: 'Stolzl Book', Arial, Helvetica Neue, Helvetica, sans-serif;
}

#modalsWin-updatesMsg-type1 div.updatesMsg-content h1,
#modalsWin-updatesMsg-type1 div.updatesMsg-content h2,
#modalsWin-updatesMsg-type1 div.updatesMsg-content h3,
#modalsWin-updatesMsg-type1 div.updatesMsg-content h4 {
    font-family: "Oswald", sans-serif;
    /* font-family: "HeliosCond", sans-serif; */
    /* font-family: 'Stolzl Book', Arial, Helvetica Neue, Helvetica, sans-serif; */
}

#modalsWin-updatesMsg-type1 div.updatesMsg-content h1 {
    font-size: 2.25em;
    margin-top: 15px;
    margin-bottom: 5px;
    font-weight: 400;
    color: #000;
}

#modalsWin-updatesMsg-type1 div.updatesMsg-content h3 {
    font-size: 1.45em;
    margin-top: 15px;
    margin-bottom: 5px;
    font-weight: 400;
    color: #000;
}

#modalsWin-updatesMsg-type1 div.modal-header h1.modal-title {
    font-size: 1.85em;
    font-family: "Oswald", sans-serif;
    font-weight: 400;
    float: left;
    color: #fff;
    letter-spacing: 0.02em;
}

#modalsWin-updatesMsg-type1 div.modal-body {
    padding: 0;
}

#modalsWin-updatesMsg-type1 div.modal-footer {
    padding: 10px 15px;
}

#modalsWin-updatesMsg-type1 div.modal-header {
    background-color: #8b50a4;
}

#modalsWin-updatesMsg-type1 div.modal-header .close {
    color: #f1f1f1;
    margin-top: -15px;
    margin-right: -8px;
}

#modalsWin-updatesMsg-type1 div.ajaxResponse-errMessage {
    float: left;
    text-align: left;
    color: red;
    font-size: smaller;
    padding-right: 10px;
}
</style>

<!-- HTML-код модального окна -->
<div id="modalsWin-updatesMsg-type1" class="modal fade" data-backdrop="false" tabindex="-1" role="dialog"
    aria-labelledby="modalsWin-updatesMsg-label" aria-hidden="true" style="z-index:9999">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="modalsWin-updatesMsg-label"><span id="updatesMsg-header"></span><span
                        id="outAdmin_toName"></span></h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="updatesMsg-content" class="updatesMsg-content"></div>
                    <div class="updatesMsg-author">Ярослав Чугунов</div>
                    <div id="updatesMsg-date" class="updatesMsg-date"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="updatesMsg-ajaxResponse" class="ajaxResponse-errMessage"></div>
                <div style="float:right">
                    <form id="updatesMsg-cmdRead" metod="">
                        <div class="btn-group">
                            <input class="btn btn-sm btn-default" style="width:100%" value="Прочитано" type="submit">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>