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
<script type="text/javascript" language="javascript" class="init"
        src="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH; ?>/js/outgoingTopblock-control.js">
</script>

<style>
div.outStats {
    font-size: 0.85rem;
}
</style>
<div id="mail-outgoing-deck-block" class="collapse show w-100">
    <div class="card-deck mb-3">
        <div class="card border-danger mb-3">
            <div class="card-header text-white bg-danger"><strong>Алярм, коллеги! У нас дедлайн!</strong></div>
            <div class="card-body">
                <h5 class="card-title">Ой</h5>
                <div class="outStats d-flex flex-column">
                    <div class="outStats-DL3days d-flex flex-row justify-content-between">
                        <div class="">До дедлайна менее 3-х дней</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-DL3days"></div>
                    </div>
                    <div class="outStats-DL1day d-flex flex-row justify-content-between">
                        <div class="">До дедлайна менее суток</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-DL1day"></div>
                    </div>
                    <div class="outStats-DLexpired d-flex flex-row justify-content-between">
                        <div class="">Истек срок дедлайна</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-DLexpired"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-warning mb-3">
            <div class="card-header text-white bg-warning"><strong>Атансьон. Пора взбодриться.</strong></div>
            <div class="card-body">
                <h5 class="card-title">Не все так страшно (пока)</h5>
                <div class="outStats d-flex flex-column">
                    <div class="outStats-noispol d-flex flex-row justify-content-between">
                        <div class="">Документов без ответственного</div>
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
                        <div class="">Документов не исполнено (пока)</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-CTRLnotexec"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-success mb-3">
            <div class="card-header text-white bg-success">На чилле...</div>
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
                        <div class="">Документов на контроле</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="outStats-CTRLon"></div>
                    </div>
                    <div class="outStats-DLon d-flex flex-row justify-content-between">
                        <div class="">Документов с дедлайном</div>
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