<?php
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
$_QRY = mysqli_fetch_assoc(mysqlQuery("SELECT update_id FROM mailbox_updates WHERE active='1' ORDER BY id DESC LIMIT 1"));
$updateID = !empty($_QRY) ? $_QRY['update_id'] : "";
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
?>
<link rel="stylesheet" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Other/Bxslider/4.2.12/jquery.bxslider.css">
<script src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Other/Bxslider/4.2.12/jquery.bxslider.min.js">
</script>


<script type="text/javascript" language="javascript" class="">
    //
    var sessionID = '<?php echo session_id(); ?>';
    var userID_session = '<?php echo $_SESSION['id']; ?>';
    var userLogin_session = '<?php echo $_SESSION['login']; ?>';
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
    function ajaxRequest_checkUpdateOnReadAsync(updateid, userid, action) {
        result = false;
        $.ajax({
            async: false,
            cache: false,
            type: "post",
            url: "<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>/_assets/user-phpscript/ajaxReq-checkUpdateOnRead.php",
            data: {
                updateid: updateid,
                userid: userid,
                action: action
            },
            success: function(response) {
                console.log('ajaxRequest_checkUpdateOnReadAsync', response);
                if (response === 'ok,1' || response === 'ok') {
                    result = 1;
                } else if (response === 'ok,0') {
                    result = 2;
                } else if (response === 'none') {
                    result = 0;
                } else {
                    result = -1;
                }
            }
        });
        return result;
    }
    /**
     * Returns a random number between min (inclusive) and max (exclusive)
     */
    function getRandomArbitrary(min, max) {
        return Math.random() * (max - min) + min;
    }
</script>

<style>
    .slider .bx-wrapper {
        position: relative;
        box-shadow: none;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
    }

    .slider-title {
        height: 2rem;
        margin-bottom: 2rem;
    }

    .slider-title h1 {
        font-family: 'Oswald', sans-serif;
        font-size: 2.5rem;
        margin-bottom: 0;
        margin-top: 0;
    }

    .slider-items .item h3 {
        font-family: "Stolzl", Arial, Helvetica Neue, Helvetica, sans-serif;
        font-size: 1.5rem;
    }

    .slider-items .item p.lead {
        font-family: "Stolzl", Arial, Helvetica Neue, Helvetica, sans-serif;
        font-size: 1.0rem;
        font-weight: 500;
    }

    .slider-items .item p.lead a:hover {
        text-decoration: none;
    }

    .slider-items .item p,
    .slider-items .item ul li {
        font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
        font-size: 0.8rem;
    }

    .slider-title-control {
        border: 5px solid #fff;
        height: 2rem;
    }

    .slider-title-control a:hover {
        filter: brightness(90%);
    }

    .slider-title-control button:focus {
        box-shadow: none;
    }

    .slider {
        min-height: 5rem;
        overflow: hidden;
    }

    .slider div {
        margin: 0;
        padding: 0;
    }

    .slider div img {
        width: 100%;
        height: auto;
    }
</style>

<div class="modal fade" id="modal-updateMessage" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modal-updateMessage-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow border-0 bg-white text-dark" style="font-family:'Stolzl Book',sans-serif">
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="slider-title d-flex justify-content-start align-items-center my-3 mb-5">
                        <h1>Обновление сервиса</h1>
                        <div class="slider-title-control d-flex mb-2 ml-auto invisible">
                            <div class="slider-title-prev invisible mr-1" style="height:2rem"></div>
                            <div class="slider-title-next invisible" style="height:2rem"></div>
                        </div>
                    </div>
                    <div class="slider">
                        <div class="slider-items">
                            <!-- --- --- --- -->
                            <div class="item" id="slider-item-0">
                                <div class="progress my-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="">
                                    </div>
                                </div>
                                <div id="updateComplete" class=""></div>
                            </div>
                            <!-- --- --- --- -->
                            <div class="item" id="slider-item-1">
                                <h3 class="mb-3 text-primary">Итак, что нового?</h3>
                                <div class="p-1">
                                    <p class="lead"><i class="fa-solid fa-circle-check fa-xl mr-2 text-primary text-justify"></i>От
                                        пушей
                                        не отказываемся, а дорабатываем</p>
                                    <p class="text-left">Доработан
                                        механизм
                                        формирования уведомлений пользователя и
                                        push-сообщений. Механизм пока не идеален, но
                                        стал лучше и стабильнее. Дальше будет лучше и информативнее.</p>
                                </div>
                                <div class="p-1">
                                    <p class="lead"><i class="fa-solid fa-gear fa-xl mr-2 text-primary text-justify"></i>Больше
                                        настроек</p>
                                    <p class="text-left">Расширился
                                        и видоизменился раздел пользовательских настроек сервиса. Например, теперь можно
                                        создать
                                        предустановки для Исходящих, что позволит создавать документ буквально в два
                                        клика, а также подключить еженедельную рассылку со списком входящих "горящих" и
                                        "подгорающих" документов, в которых вы ответственный.</p>
                                </div>
                            </div>
                            <!-- --- --- --- -->
                            <div class="item" id="slider-item-2">
                                <h3 class="mb-3 text-primary">Итак, что нового?</h3>
                                <div class="p-1">
                                    <p class="lead"><i class="fa-solid fa-floppy-disk fa-xl mr-2 text-primary text-justify"></i>Сохраняем
                                        состояние</p>
                                    <p class="text-left">Теперь при
                                        сохранении формы или обновлении страницы сохраняется состояние списка
                                        документов:
                                    </p>
                                    <ul>
                                        <li>выбранная ранее страница списка</li>
                                        <li>выбранная строка</li>
                                        <li>количество отображаемых записей на одной странице*</li>
                                        <li>состояние фильтров</li>
                                    </ul>
                                    <p class="text-left">*У выбранной страницы будет приоритет, т.е. если вы после
                                        выбора строки перешли
                                        на другую страницу списка ничего не выделив после этого, то при перезагрузке
                                        страницы вы окажетесь на этой странице, а не на странице, где вы ранее выделили
                                        строку.</p>
                                </div>
                            </div>
                            <!-- --- --- --- -->
                            <div class="item" id="slider-item-3">
                                <h3 class="mb-3 text-primary">Итак, что нового?</h3>
                                <div class="p-1">
                                    <p class="lead"><i class="fa-solid fa-lightbulb fa-xl mr-2 text-primary text-justify"></i>Стало
                                        информативнее и интерактивнее</p>
                                    <div class="row">
                                        <div class="col-12">
                                            <img class="img-fluid img-thumbnail mb-3" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/updates/202308-v2.1.202308.01/img/img-01-stats.fullblock.png" alt="">
                                            <p class="text-justify">Теперь блок со статистической информацией стал
                                                информативнее и функциональнее. Часть параметров теперь является
                                                комбинированным или простым фильтром, т.е. кликнув на нем вы сразу
                                                фильтруете список документов по соответствующим параметрам.</p>
                                            <p class="text-justify">Кроме того, отображаемая информация может быть как
                                                общей по разделу, так и <b>только касающейся вас как ответственного (или
                                                    исполнителя)</b>. Режим отображения зависит от состояния чекбокса
                                                "Показывать только документы, где я исполнитель".</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- --- --- --- -->
                            <div class="item" id="slider-item-4">
                                <h3 class="mb-3 text-primary">Итак, что нового?</h3>
                                <div class="p-1">
                                    <p class="lead"><i class="fa-solid fa-message fa-xl mr-2 text-primary text-justify"></i>Подсказок
                                        много не бывает</p>
                                    <div class="row">
                                        <div class="col-6">
                                            <img class="img-fluid px-3" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/updates/202308-v2.1.202308.01/img/img-03-stats.block.with.popover.png" alt="">
                                        </div>
                                        <div class="col-6">
                                            <img class="img-fluid px-3" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/updates/202308-v2.1.202308.01/img/img-05-table.header.with.popover.png" alt="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <p class="text-justify">Стало гораздо больше всплывающих подсказок. Привел в
                                                порядок всплывающие
                                                подсказки в формах редактирования - добавил новые, поправил старые,
                                                сделал
                                                их более информативными. Также
                                                добавил подсказки к шапке таблицы списка с документами и прочим
                                                вспомогательным
                                                элементам страницы. Планирую добавлять все новые подсказки и дальше.</p>
                                            <p class="text-justify">Наводите мышку и ждите, вдруг что-нибудь да
                                                всплывет. :)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- --- --- --- -->
                            <div class="item" id="slider-item-5">
                                <h3 class="mb-3 text-primary">Итак, что нового?</h3>
                                <div class="p-1">
                                    <p class="lead"><i class="fa-solid fa-signal fa-xl mr-2 text-primary text-justify"></i>Повышаем
                                        функциональность</p>
                                    <div class="row">
                                        <div class="col-12 pb-1">
                                            <p class="text-justify">Теперь при создании ответного письма при выборе
                                                письма-запроса
                                                организация из него будет автоматически подставляться в
                                                организацию-получателя
                                                создаваемого
                                                документа. Работает как для входящих так и для исходящих.</p>
                                        </div>
                                        <div class="col-12 px-1">
                                            <img class="img-fluid img-thumbnail mb-3" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/updates/202308-v2.1.202308.01/img/img-04-form.check.on.ispol.png" alt="">
                                            <p class="text-justify">Также при создании ответного письма при выборе
                                                документа-запроса сервис автоматически проверит являетесь ли вы
                                                ответственным/исполнителем по выбранному документу-запросу и стоит ли
                                                оно на
                                                контроле исполнения. Если являетесь и контроль включен, то появится
                                                возможность
                                                отметить этот документ как полностью исполненный (за всех ответственных)
                                                сразу
                                                при формировании ответа. Вам не придется переходить в противоположный
                                                раздел и
                                                отмечать этот документ исполненным.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- --- --- --- -->
                            <div class="item" id="slider-item-6">
                                <h3 class="mb-3 text-primary">Итак, что нового?</h3>
                                <div class="p-1">
                                    <p class="lead"><i class="fa-solid fa-user-check fa-xl mr-2 text-primary text-justify"></i>За
                                        себя и
                                        того парня</p>
                                    <p class="text-justify">Появилась возможность отметить документ исполненным (и
                                        наоборот)
                                        не только за себя, но и за своих коллег по несчастью (остальных ответственных),
                                        что
                                        позволит каждому из ответственных отмечать документ как исполненный не взирая,
                                        что
                                        думают по этому поводу другие ответственные. При отметке об исполнении можно
                                        будет
                                        тут же оставить комментарий, который будет доступен в общем чате с комментариями
                                        по
                                        документу.</p>
                                    <p class="lead"><i class="fa-solid fa-comment-dots fa-xl mr-2 text-primary text-justify"></i>Всё
                                        в
                                        чат</p>
                                    <p class="text-justify">Теперь текстовые поля из формы, как то: дополнительная
                                        информация, комментарий исполнителя,а также комментарий к отметке об исполнении,
                                        все
                                        они валятся в чат по документу. Там же их можно и отредактировать и удалить при
                                        необходимости. Тут важно помнить, что операции с комментариями в чате, попавшими
                                        в
                                        него из форм редактирования документа, не влияют на эти комментарии в самом
                                        документе, они останутся без изменений.</p>
                                </div>
                            </div>
                            <!-- --- --- --- -->
                            <div class="item" id="slider-item-7">
                                <h3 class="mb-3 text-primary">Итак, что нового?</h3>
                                <div class="p-1">
                                    <p class="lead"><i class="fa-solid fa-address-card fa-xl mr-2 text-primary text-justify"></i>Номер
                                        письма имеет значение</p>
                                    <p class="text-justify">Номер письма организации-контрагента теперь также является
                                        инструментом поиска. По крайней мере во входящих, где этот номер всегда вносится
                                        в систему. По номеру письма контрагента можно искать документ как и в общей
                                        таблице документов, так и осуществлять поиск при формировании ответного письма.
                                    </p>
                                    <p class="lead"><i class="fa-solid fa-bug fa-xl mr-2 text-primary text-justify"></i>И
                                        прочее</p>
                                    <p class="text-justify">Мелкие правки и доработки, которые не заслуживают отдельного
                                        абзаца. Например, с удивлением обнаружил, что ссылки из письма ведут на Профиль,
                                        а оттуда через ссылку внизу страницы попасть можно только в Архив. Это же
                                        неудобно для писем текущего года. И никто не сказал. Теперь же при переходе
                                        система проверяет год письма и если год текущий вам будет предложено перейти не
                                        в архивный, а в текущий раздел.</p>
                                    <p class="text-justify">Подчистил мелкие ошибки, затерявшиеся во времени и
                                        неожиданно напомнившие о себе вновь. Процесс бесконечный в виду постоянного
                                        развития сервиса. Работаем.</p>

                                </div>
                            </div>
                            <!-- --- --- --- -->
                            <div class="item" id="slider-item-7">
                                <h3 class="mb-3 text-warning">Что ждать дальше?</h3>
                                <div class="p-1">
                                    <p class="lead"><i class="fa-solid fa-clock fa-xl mr-2 text-warning text-justify"></i>Контроль
                                        ответных писем</p>
                                    <p class="text-justify">Дорабатываю механизм контроля исполнения в части ответов на
                                        запросы, просто ответных писем, ответа на два и более входящих. Реализую
                                        подсветку "зеленым" исполненного документа и контроль связанных писем не только
                                        через основной документ, но и через дополнительные и прочее. Эти обновления
                                        будут сделаны в срочном рабочем порядке в первую очередь вне очередного пакета
                                        обновлений в виду актуальности этих вопросов.</p>
                                    <p class="lead"><i class="fa-solid fa-clock fa-xl mr-2 text-warning text-justify"></i>Связи</p>
                                    <p class="text-justify">Дорабатываю механизм формирования горизонтальных связей
                                        между
                                        сервисами. Профиль наше всё.</p>
                                    <p class="lead"><i class="fa-solid fa-clock fa-xl mr-2 text-warning text-justify"></i>Главная
                                        страница</p>
                                    <p class="text-justify">К следующем пакету обновлений соберу главную страницу с
                                        наиболее полезной информацией для ответственных. По аналогии с главной страницей
                                        сервиса Договор.</p>
                                </div>
                            </div>
                            <!-- --- --- --- -->
                            <div class="item" id="slider-item-7">
                                <h3 class="mb-3 text-warning">Что ждать дальше?</h3>
                                <div class="p-1">
                                    <p class="lead"><i class="fa-solid fa-clock fa-xl mr-2 text-warning text-justify"></i>Больше
                                        рассылок</p>
                                    <p class="text-justify">Планирую запустить подписку на уведомления по конкретному
                                        документу не зависимо от того, являетесь ли вы ответственным по нему или нет.
                                        Функция давно была заложена, но так и не доработана. Сделаем.</p>
                                    <p class="lead"><i class="fa-solid fa-clock fa-xl mr-2 text-warning text-justify"></i>Светлая
                                        тема</p>
                                    <p class="text-justify">Куда ж без нее.</p>
                                    <p class="lead"><i class="fa-solid fa-clock fa-xl mr-2 text-warning text-justify"></i>Что-то еще
                                    </p>
                                    <p class="text-justify">"У самурая нет цели, есть только путь..."</p>
                                </div>
                            </div>
                            <!-- --- --- --- -->
                            <div class="item" id="slider-item-7">
                                <h3 class="mb-3 text-dark">На связи</h3>
                                <div class="p-1">
                                    <p class="lead"><i class="fa-solid fa-pen-to-square fa-xl mr-2 text-dark"></i>Подробное описание
                                        всех обновлений</p>
                                    <p class="text-justify">Когда наконец дойдут руки, то открою специальный раздел типа
                                        Updates.Log или Updates.Tracker, куда буду подробно (в разумных пределах)
                                        записывать все обновления и изменения сервиса, в том числе с ссылками на
                                        "инициаторов" обновлений, чтобы последние могли отслеживать реализована ли их
                                        просьба или нет. К следующему пакету думаю раздел будет открыт.</p>

                                    <p class="lead"><i class="fa-brands fa-vk fa-xl mr-2 text-dark"></i><a href="https://vk.com/atgsportal" target="_blank">VK-сообщество
                                            АТГС.Портал</a></p>
                                    <p class="text-justify">Тут больше лонгридов, видеоманулы, инструкции, анонсы
                                        обновлений и фидбэк по текущей версии. Дальше будет чаще, все не успеваю. Либо
                                        мануалы, либо обновления писать.</p>

                                    <p class="lead"><i class="fa-brands fa-telegram fa-xl mr-2 text-dark"></i><a href="https://t.me/+bPuTlfJa87kzYmVi" target="_blank">Телеграм-канал
                                            АТГС.Портал</a></p>
                                    <p class="text-justify">Тут посты покороче, но почаще. Объявления и комментарии по
                                        текучке.</p>
                                </div>
                            </div>
                            <!-- --- --- --- -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-top-0 pt-0 invisible d-flex justify-content-start">
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input small" id="updates-doNotShowAgain" style="margin-top: 6px">
                    <label class="form-check-label small text-dark pl-2" for="updates-doNotShowAgain"><b>Больше не
                            показывать</b></label>
                </div>
                <button type="button" class="btn btn-danger text-white btn-xl ml-auto" data-dismiss="modal" data-userid="" data-updateid="" onclick="ajaxRequest_checkUpdateOnReadAsync(<?php echo $updateID; ?>, <?php echo $_SESSION['id']; ?>, $('#updates-doNotShowAgain').prop('checked') ? 'mark' : '')">Начать
                    работу</button>
            </div>
        </div> <!-- end of modal content -->
    </div>
</div> <!-- end of modal -->

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        var updateID = '<?php echo $updateID; ?>';
        var userID_session = '<?php echo $_SESSION['id']; ?>';
        var userLogin_session = '<?php echo $_SESSION['login']; ?>';
        var progressOn = ajaxRequest_checkUpdateOnReadAsync(updateID, userID_session, '');
        var slideCount = $(".slider-items div.item").length - 1;
        console.log('CHECK > progressOn', progressOn);
        console.log('CHECK > slideCount', slideCount);

        if (userID_session === '999' && userLogin_session === 'yachugunov') {
            // if (userID_session === '999' && userLogin_session === 'yachugunov' && updateID !== "") {
            // if (updateID !== "") {
            var checkupdate = ajaxRequest_checkUpdateOnReadAsync(updateID, userID_session, 'check');
            console.log('CHECK > checkupdate', checkupdate);
            if (checkupdate > 0) {
                $("#modal-updateMessage").modal("show");
                var slider = $("#modal-updateMessage .slider div.slider-items").bxSlider({
                    controls: true,
                    mode: 'fade',
                    prevSelector: '.slider-title-prev',
                    nextSelector: '.slider-title-next',
                    prevText: '<button type="button" style="width:5rem" class="btn btn-outline-danger btn-sm">Назад</button>',
                    nextText: '<button type="button" style="width:5rem" class="btn btn-outline-danger btn-sm">Вперед</button>',
                    pager: false,
                    auto: false,
                    pause: 0,
                    minSlides: 1,
                    maxSlides: 1,
                    adaptiveHeight: true,
                    infiniteLoop: false,
                    touchEnabled: false,
                    // slideMargin: 20,
                    // slideWidth: 279,
                    onSlideNext: function($slideElemen, oldIndex, newIndex) {
                        if (newIndex > 1) {
                            // $('.slider-title-control').addClass('invisible');
                            $('.slider-title-control>div.slider-title-prev').removeClass(
                                'invisible d-none');
                            $('.slider-title-control>div.slider-title-next').removeClass(
                                'invisible d-none');
                            $('.modal-footer').css('padding', '15px 31px');
                        }
                        if (newIndex >= slideCount) {
                            // $('.slider-title-control').addClass('invisible');
                            $('.modal-footer').removeClass('invisible');
                            $('.slider-title-control>div.slider-title-next').addClass('d-none');
                            $('.slider-title-control>div.slider-title-prev').removeClass(
                                'invisible d-none');
                            $('.modal-footer').css('padding', '15px 31px');
                        }
                    },
                    onSlidePrev: function($slideElemen, oldIndex, newIndex) {
                        if (newIndex < slideCount) {
                            // $('.slider-title-control').addClass('invisible');
                            $('.slider-title-control>div.slider-title-next').removeClass(
                                'invisible d-none');
                            $('.modal-footer').css('padding', '15px 31px');
                        }
                        if (newIndex < 2) {
                            // $('.slider-title-control').addClass('invisible');
                            $('.slider-title-control>div.slider-title-prev').addClass('d-none');
                            $('.slider-title-control>div.slider-title-next').removeClass(
                                'invisible d-none');
                            $('.modal-footer').css('padding', '15px 31px');
                        }
                    },
                });
                // if (!ajaxRequest_checkToShowUpdateMessageAsync(updateid, userid)) {
                // $('#modal-updateMessage .modal-message').html('Обновление');
                // $('#modal-updateMessage .modal-message').html('Обновление');
                // }
                $('#modal-updateMessage').on('shown.bs.modal', function(e) {
                    slider.reloadSlider();
                    if (progressOn === 1) {
                        var progress = 0;
                        // повторить с интервалом 2 секунды
                        let timerId = setInterval(() => {
                            randVal = getRandomArbitrary(1, 10);
                            progress += randVal;
                            if (progress >= 100) {
                                $('.slider .progress-bar').attr('aria-valuenow', 100);
                                $('.slider .progress-bar').css('width', '100%');
                                $('.slider .progress-bar').removeClass(
                                    'progress-bar-striped progress-bar-animated, progress-bar-striped progress-bar-animated'
                                );
                                $('.slider .progress-bar').text('Обновлено');
                                $('.slider-title h1').html(
                                    '<span class="text-dark">Сервис обновлен</span>');
                                $('#updateComplete').html(
                                    'Почта обновилась до версии <b>2.1.202308.01</b>');
                                $('.slider-title-control').removeClass('invisible');
                                $('.slider-title-control>div.slider-title-prev').addClass('d-none');
                                $('.slider-title-control>div.slider-title-next').removeClass(
                                    'invisible d-none');
                                clearInterval(timerId);
                                $('.slider .progress').remove();
                                // slider.goToNextSlide();
                            } else {
                                $('.slider .progress-bar').attr('aria-valuenow', Math.round(
                                    progress));
                                $('.slider .progress-bar').css('width', Math.round(progress) + '%');
                                $('.slider .progress-bar').text(Math.round(progress) + '%');
                            }
                        }, 1000);

                        // остановить вывод через 20 секунд
                        setTimeout(() => {
                            if (progress < 100) {
                                $('.slider .progress-bar').attr('aria-valuenow', 100);
                                $('.slider .progress-bar').css('width', '100%');
                                $('.slider .progress-bar').removeClass(
                                    'progress-bar-striped progress-bar-animated, progress-bar-striped progress-bar-animated'
                                );
                                $('.slider .progress-bar').text('Обновлено');
                                $('.slider-title h1').html(
                                    '<span class="text-dark">Сервис обновлен</span>');
                                $('#updateComplete').html(
                                    'Ваша Почта обновилась до версии <b>2.1.202308.01</b>');
                                $('.slider-title-control, .modal-footer>button').removeClass(
                                    'invisible');
                                $('.slider-title-control>div.slider-title-prev').addClass('d-none');
                                $('.slider-title-control>div.slider-title-next').removeClass(
                                    'invisible d-none');
                                clearInterval(timerId);
                                $('.slider .progress').remove();
                                // slider.goToNextSlide();
                            }
                        }, 30000);
                    } else {
                        $('.slider-title-control, .modal-footer>button').removeClass(
                            'invisible');
                        $('.slider .progress').remove();
                        $('.slider-title-control>div.slider-title-next').removeClass('invisible');
                        slider.goToNextSlide();
                    }
                });
            }
        }

    });
</script>