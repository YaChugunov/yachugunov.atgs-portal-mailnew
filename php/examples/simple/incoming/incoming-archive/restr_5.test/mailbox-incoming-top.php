<?php
if (__UI_PERSONAL_INC_DASHBOARDSHOW == '1') {
    if (isset($_GET['type']) && !empty($_GET['type'])) {
        if ($_GET['type'] == 'in' && isset($_GET['mode']) && $_GET['mode'] == "thisyear") {
?>

<!-- <p class="">
        <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="collapse" data-target="#mail-outgoing-deck-block" aria-expanded="false" aria-controls="mail-outgoing-deck-block">
            Показать / скрыть доску
        </button>
    </p> -->
<script type="text/javascript" language="javascript" class="init"
        src="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH; ?>/js/incomingTopblock-control.js">
</script>

<style>
div.incStats {
    font-size: 0.85rem;
}
</style>
<div id="mail-incoming-deck-block" class="collapse show w-100">
    <div class="card-deck mb-3">
        <div class="card border-danger mb-3">
            <div class="card-header text-white bg-danger"><strong>Алярм, коллеги! У нас дедлайн!</strong></div>
            <div class="card-body">
                <h5 class="card-title">Ой</h5>
                <div class="incStats d-flex flex-column">
                    <div class="incStats-DL3days d-flex flex-row justify-content-between">
                        <div class="">До дедлайна менее 3-х дней</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="incStats-DL3days"></div>
                    </div>
                    <div class="incStats-DL1day d-flex flex-row justify-content-between">
                        <div class="">До дедлайна менее суток</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="incStats-DL1day"></div>
                    </div>
                    <div class="incStats-DLexpired d-flex flex-row justify-content-between">
                        <div class="">Истек срок дедлайна</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="incStats-DLexpired"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-warning mb-3">
            <div class="card-header text-white bg-warning"><strong>Атансьон. Пора взбодриться.</strong></div>
            <div class="card-body">
                <h5 class="card-title">Не все так страшно (пока)</h5>
                <div class="incStats d-flex flex-column">
                    <div class="incStats-noispol d-flex flex-row justify-content-between">
                        <div class="">Документов без ответственного</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="incStats-noispol"></div>
                    </div>
                    <div class="incStats-noattach d-flex flex-row justify-content-between">
                        <div class="">Документов без файлов</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="incStats-noattach"></div>
                    </div>
                    <div class="incStats-CTRLnotexec d-flex flex-row justify-content-between">
                        <div class="">Документов не исполнено (пока)</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="incStats-CTRLnotexec"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-success mb-3">
            <div class="card-header text-white bg-success">На чилле...</div>
            <div class="card-body text-dark">
                <h5 class="card-title">Просто циферки</h5>
                <div class="incStats d-flex flex-column">
                    <div class="incStats-total d-flex flex-row justify-content-between">
                        <div class="">Всего документов</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="incStats-total"></div>
                    </div>
                    <div class="incStats-CTRLon d-flex flex-row justify-content-between">
                        <div class="">Документов на контроле</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="incStats-CTRLon"></div>
                    </div>
                    <div class="incStats-DLon d-flex flex-row justify-content-between">
                        <div class="">Документов с дедлайном</div>
                        <div class="flex-fill px-2">
                            <div style="border-bottom:1px dashed #CCCCCC">&nbsp;</div>
                        </div>
                        <div class="card-text" id="incStats-DLon"></div>
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