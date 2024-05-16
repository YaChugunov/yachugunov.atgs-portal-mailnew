<?php
if (__UI_PERSONAL_OUT_DASHBOARDSHOW == '1') {
    if (isset($_GET['type']) && !empty($_GET['type'])) {
        if ($_GET['type'] == 'out' && isset($_GET['mode']) && $_GET['mode'] == "thisyear") {
?>

<!-- <p class="">
        <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="collapse" data-target="#mail-outgoing-deck-block" aria-expanded="false" aria-controls="mail-outgoing-deck-block">
            Показать / скрыть доску
        </button>
    </p> -->

<style>
div.outStats {
    font-size: 0.85rem;
}

#mail-outgoing-deck-block div.border-danger div.filterLinked {
    color: #DC3545;
}

#mail-outgoing-deck-block div.border-danger div.filterLinked:hover {
    cursor: pointer;
    border-bottom: 1px dashed #DC3545;
}

#mail-outgoing-deck-block div.border-warning div.filterLinked {
    color: #FFC107;
}

#mail-outgoing-deck-block div.border-warning div.filterLinked:hover {
    cursor: pointer;
    border-bottom: 1px dashed #FFC107;
}

#mail-outgoing-deck-block div.border-success div.filterLinked {
    color: #28A745;
}

#mail-outgoing-deck-block div.border-success div.filterLinked:hover {
    cursor: pointer;
    border-bottom: 1px dashed #28A745;
}

div.mail-outgoing-popover .popover-body {
    font-size: 12px;
    text-align: left !important;
}

div.mail-outgoing-deck-block .popover-body h3,
#divOnlyIspolMe .popover-body h3 {
    font-family: "Stolzl", Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
}

div.mail-outgoing-deck-block .popover-body p,
#divOnlyIspolMe .popover-body p {
    font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 0.8rem;
    color: #666;
    line-height: 1.15rem;
    margin-bottom: 0.25rem;
}

div.mail-outgoing-deck-block .popover-body p u,
#divOnlyIspolMe .popover-body p u {
    color: #111 !important;
    font-weight: 700;
    ;
    text-decoration: none;
}

div.mail-outgoing-outTable-tooltip p,
div.mail-outgoing-insideTable-tooltip * {
    font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 0.75rem;
    color: #FFF !important;
    line-height: 1.15rem;
    margin-bottom: 0.25rem;
}
</style>

<div id="mail-outgoing-deck-block" class="collapse show w-100">
    <div class="card-deck mb-3">
        <div class="card border-danger mb-3">
            <div class="card-header text-white bg-danger">
                <div class="d-flex flex-row align-items-center">
                    <div>Алярм, коллеги! Дедлайн!</div>
                    <div data-toggle="popover"
                         data-content='<p>Если вы видите эту пульсируюущую иконку, это означает, что установлен чекбокс <u>Показывать только документы, где я исполнитель</u> и статистическая информация в этом блоке относится только к Вам как исполнителю.</p>'
                         class="ml-auto"><i style="color:#fff !important"
                           class="fa-solid fa-circle-user fa-beat fa-xl"></i></div>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title">Ой</h5>
                <div class="outStats d-flex flex-column">
                    <div class="outStats-DL3days d-flex flex-row justify-content-between">
                        <div class="filterLinked" filterID="filterIspolDL" filterVal="less3" data-toggle="popover"
                             data-content='<h3 class="text-danger">Комбинированный фильтр</h3><p>В блоке фильтров будет выставлено 3 фильтра: <u>дедлайн</u> (менее 3-х дней), <u>контроль исполнения</u> (только на контроле), <u>исполнение</u> (не исполнен).</p>'>
                            До дедлайна менее 3-х дней
                        </div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-DL3days"></div>
                    </div>
                    <div class="outStats-DL1day d-flex flex-row justify-content-between">
                        <div class="filterLinked" filterID="filterIspolDL" filterVal="less1" data-toggle="popover"
                             data-content='<h3 class="text-danger">Комбинированный фильтр</h3><p>В блоке фильтров будет выставлено 3 фильтра: <u>дедлайн</u> (менее суток), <u>контроль исполнения</u> (только на контроле), <u>исполнение</u> (не исполнен).</p>'>
                            До дедлайна менее суток
                        </div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-DL1day"></div>
                    </div>
                    <div class="outStats-DLexpired d-flex flex-row justify-content-between">
                        <div class="filterLinked" filterID="filterIspolDL" filterVal="expired" data-toggle="popover"
                             data-content='<h3 class="text-danger">Комбинированный фильтр</h3><p>В блоке фильтров будет выставлено 3 фильтра: <u>дедлайн</u> (срок истёк), <u>контроль исполнения</u> (только на контроле), <u>исполнение</u> (не исполнен).</p>'>
                            Истек срок дедлайна</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-DLexpired"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-warning mb-3">
            <div class="card-header text-white bg-warning">
                <div class="d-flex flex-row align-items-center">
                    <div>Атансьон. Пора взбодриться.</div>
                    <div data-toggle="popover"
                         data-content='<p>Если вы видите эту пульсируюущую иконку, это означает, что установлен чекбокс <u>Показывать только документы, где я исполнитель</u> и статистическая информация в этом блоке относится только к Вам как исполнителю.</p>'
                         class="ml-auto"><i style="color:#fff !important"
                           class="fa-solid fa-circle-user fa-beat fa-xl"></i></div>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title">Не все так страшно (пока)</h5>
                <div class="outStats d-flex flex-column">
                    <div class="outStats-noispol d-flex flex-row justify-content-between">
                        <div class="filterLinked" filterID="filterIspol" filterVal="---" data-toggle="popover"
                             data-content='<h3 class="text-warning">Обычный фильтр</h3><p>В блоке фильтров будет выставлен фильтр <u>ответственный</u> в значение "---", которое означет отсутствие ответственного.</p>'>
                            Документов без ответственного
                        </div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-noispol"></div>
                    </div>
                    <div class="outStats-noattach d-flex flex-row justify-content-between">
                        <div class="">Документов без файлов</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-noattach"></div>
                    </div>
                    <div class="outStats-CTRLnotexec d-flex flex-row justify-content-between">
                        <div class="filterLinked" filterID="filterCheckout" filterVal="0" data-toggle="popover"
                             data-content='<h3 class="text-warning">Комбинированный фильтр</h3><p>В блоке фильтров будет выставлено 2 фильтра: <u>контроль исполнения</u> (только на контроле), <u>исполнение</u> (не исполнен).</p>'>
                            Документов не исполнено (пока)
                        </div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-CTRLnotexec"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-success mb-3">
            <div class="card-header text-white bg-success">
                <div class="d-flex flex-row align-items-center">
                    <div>На чилле...</div>
                    <div data-toggle="popover"
                         data-content='<p>Если вы видите эту пульсируюущую иконку, это означает, что установлен чекбокс <u>Показывать только документы, где я исполнитель</u> и статистическая информация в этом блоке относится только к Вам как исполнителю.</p>'
                         class="ml-auto"><i style="color:#fff !important"
                           class="fa-solid fa-circle-user fa-beat fa-xl"></i></div>
                </div>
            </div>
            <div class="card-body text-dark">
                <h5 class="card-title">Просто циферки</h5>
                <div class="outStats d-flex flex-column">
                    <div class="outStats-total d-flex flex-row justify-content-between">
                        <div class="">Всего документов</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-total"></div>
                    </div>
                    <div class="outStats-CTRLon d-flex flex-row justify-content-between">
                        <div class="filterLinked" filterID="filterInControl" filterVal="1" data-toggle="popover"
                             data-content='<h3 class="text-success">Обычный фильтр</h3><p>В блоке фильтров будет выставлен фильтр контроль исполнения (активен).</p>'>
                            Документов на контроле</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-CTRLon"></div>
                    </div>
                    <div class="outStats-DLon d-flex flex-row justify-content-between">
                        <div class="filterLinked" filterID="filterIspolDL" filterVal="more3" data-toggle="popover"
                             data-content='<h3 class="text-success">Комбинированный фильтр</h3><p>В блоке фильтров будет выставлено 3 фильтра: <u>дедлайн</u> (более 3-х суток), <u>контроль исполнения</u> (только на контроле), <u>исполнение</u> (не исполнен).</p>'>
                            Документов с дедлайном
                            более 3-х дней
                        </div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-DLon"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p class="small text-right mb-0" style="color:#AAAAAA; font-size:0.7rem"><span class="px-1 text-dark"><sup><i
                   class="fa-solid fa-star-of-life fa-xs"></i></sup></span>Вывод блока с информационными карточками
        можно
        скрыть/показать в разделе Настройки.</p>
    <p class="small text-right mb-0" style="color:#AAAAAA; font-size:0.7rem"><span class="px-1 text-dark"><sup><i
                   class="fa-solid fa-star-of-life fa-xs"></i><i
                   class="fa-solid fa-star-of-life fa-xs"></i></sup></span>Раздел будет пополняться новой статистической
        информацией с возмодностью быстрого перехода к ее детализации.</p>
</div>

<?php
        }
    }
}
?>

<script type="text/javascript" language="javascript" class="">
$(window).on("load", function() {


});
</script>