<?php
if (__UI_PERSONAL_OUT_LEGENDSHOW == '1') {
    if (isset($_GET['type']) && !empty($_GET['type'])) {
        if ($_GET['type'] == 'out') {
?>

<div class="mt-3">

    <ul class="list-group list-group-horizontal-lg table-legend">
        <li class="list-group-item border-light flex-fill text-justify w-50">
            <div class="text-left"><span class="text-info pr-2"><b>Исполнитель</b></span>Если рядом с именем исполнителя
                Вы видите один из ниже приведенных символов, то это значит, что Вы являетесь исполнителем по документу,
                а именно</div>
            <div class="ml-3">
                <span><i class="fa-solid fa-circle fa-2xs text-dark pr-2"></i>Вы исполнитель по документу при
                    <u>неактивном</u> режиме контроля исполнения.</span>
                <br>
                <span><i class="fa-solid fa-circle fa-2xs fa-beat-fade text-danger pr-2"></i>Вы как исполнитель <u>не
                        исполнили</u> документ (не поставили соответствующую отметку) при <u>активном</u> режиме
                    контроля исполнения.</span>
                <br>
                <span><i class="fa-solid fa-circle fa-2xs fa-beat-fade text-success pr-2"></i>Вы как исполнитель
                    <u>исполнили</u> документ (поставили соответствующую отметку) при <u>активном</u> режиме контроля
                    исполнения.</span>
            </div>
            <div class="text-left"><span class=""><i class="fa-regular fa-clock pr-1"
                       style="color:#111111"></i></span><span class="text-info pr-2"><b>(Д) - Дедлайн</b></span>По
                документу включен дедлайн. Дедлайн возможен только при активном режиме контроля исполнения (КИ).
                Информация доступна по наведению мышки.</div>
            <div class="text-left"><span class="text-info pr-2"><b>(КИ) - Контроль исполнения</b></span>Данный столбец
                отражает текущий статус исполнения документа в режиме контроля исполнения, а именно</div>
            <div class="ml-3">
                <span><i class="fa-solid fa-circle-exclamation text-info pr-2"></i>Контроль исполнения активен, но без
                    дедлайна. Документ не выполнен полностью, т.е. исполнитель не поставил отметку об исполнении.
                    Информация доступна по наведению мышки.</span>
                <br>
                <span><i class="fa-solid fa-fire fa-beat-fade text-danger pr-2"></i>Контроль исполнения активен и
                    дедлайн по документу просрочен. Документ не выполнен полностью, т.е. исполнитель не поставил отметку
                    об исполнении. Информация доступна по наведению мышки.</span>
                <br>
                <span><i class="fa-solid fa-circle-check text-success pr-2"></i>Документ выполнен. Исполнитель поставил
                    отметку. Информация доступна по наведению мышки.</span>
                <br>
                <div class="text-dark">
                    <div class="d-flex justify-content-center float-left"><span class="spanAlarm"><span
                                  class="spanText">0</span></span><span class="spanWarning"><span
                                  class="spanText">2</span></span><span class="spanOk"><span
                                  class="spanText">5</span></span><span class="spanOk"><span
                                  class="spanText">9+</span></span></div>
                    <div><span class="pl-2">Полные дни до наступления дедлайна - менее суток (2-е предупреждение) /
                            менее 3-х дней (1-е предупреждение) / количество дней (от 3 до 9) / более 9 дней. Информация
                            доступна по наведению мышки.</span></div>
                </div>
            </div>
        </li>
    </ul>
    <ul class="list-group list-group-horizontal-lg table-legend">
        <li class="list-group-item border-light flex-fill text-justify">
            <span><i class="fa-solid fa-ellipsis-vertical pr-2"></i></span><span class="">Кликом мышки открыть или
                закрыть детальную информацию по документу</span><span class="text-danger px-1">&bull;</span><span
                  class="text-info pr-2"><b>Тема документа</b></span><i class="fa-solid fa-tags pr-1"
               style="color:#111111"></i><i class="fa-solid fa-tags pr-2" style="color:#E0E0E0"></i><span class="">Теги
                по документу по наведению мышки (теги добавлены / теги не добавлены)</span><span
                  class="text-danger px-1">&bull;</span><span
                  class="text-info pr-2"><b>Организация-отправитель</b></span><i class="fa-solid fa-lightbulb pr-1"
               style="color:#111111"></i><i class="fa-solid fa-lightbulb pr-2" style="color:#E0E0E0"></i><span
                  class="">Информация по документу от контрагента по наведению мышки - дата регистрации, номер
                (информация есть / информации нет)</span><span class="text-danger px-1">&bull;</span><span
                  class="text-info pr-2"><b>(Ф<sub>о</sub>&nbsp;Ф<sub>д</sub>) - Файлы</b></span><i
               class="fa-solid fa-file-pdf pr-1" style="color:#111111"></i><i class="fa-solid fa-file pr-1"
               style="color:#111111"></i><i class="fa-regular fa-file-lines pr-2" style="color:#111111"></i><span
                  class="">Основной файл и список дополнительных файлов во всплывающем окне, прикрепленные к документу
                (основной PDF-файл / другой тип файла / список дополнительных файлов)</span><span
                  class="text-danger px-1">&bull;</span><span
                  class="text-info pr-2"><b>(ЗО<sub>о</sub>&nbsp;ЗО<sub>д</sub>) - Запрос-Ответ</b></span><i
               class="fa-solid fa-1" style="color:#111111"></i><i class="fa-solid fa-2" style="color:#111111"></i><i
               class="fa-solid fa-3 pr-1" style="color:#111111"></i><i class="fa-regular fa-file-lines pr-2"
               style="color:#111111"></i><span class="">Ссылка на основной документ в связке Запрос-Ответ, а также по
                клику во всплывающем окне посмотреть список дополнительных документов, для которых текущий документ
                является ответным (для документа типа "Ответный")</span><span
                  class="text-danger px-1">&bull;</span><span class="text-info pr-2"><b>(СВ) - Связи</b></span><i
               class="fa-solid fa-chain pr-2" style="color:#111111"></i><span class="">По клику во всплывающем окне
                посмотреть все дополнительные исходящие связи с другими документами входящей и исходящей почты, а также
                договоров</span><span class="text-danger px-1">&bull;</span><span class="text-info pr-2"><b>(Ж) - Журнал
                    действий</b></span><i class="fa-regular fa-rectangle-list pr-2" style="color:#111111"></i><span
                  class="">По клику во всплывающем окне посмотреть журнал действий пользователя по документу</span><span
                  class="text-danger px-1">&bull;</span><span class="text-info pr-2"><b>(К) - Комментарии
                    пользователей</b></span><i class="fa-solid fa-comments pr-1" style="color:#111111"></i><i
               class="fa-regular fa-comments pr-2" style="color:#E0E0E0"></i><span class="">По клику во всплывающем окне
                посмотреть комментарии пользователей к документу или добавить свой комментарий (комментарии есть /
                комментариев нет)</span>
        </li>
    </ul>
    <p class="small text-right mb-3" style="color:#AAAAAA; font-size:0.7rem"><span class="px-1 text-dark"><sup><i
                   class="fa-solid fa-star-of-life fa-xs"></i></sup></span>Вывод блока с расшифровкой символов и
        обозначений можно включить/отключить в разделе
        Настройки</p>
</div>

<?php
        }
    }
}
?>

<script>
$(document).ready(function() {

    $('#mail-main-outbox table th').tooltip({
        html: true,
        trigger: 'hover',
        placement: 'top',
        customClass: 'tooltip-outgoing'
    });
    $('#divOnlyIspolMe *[data-toggle="popover"]')
        .popover({
            html: true,
            trigger: 'hover',
            placement: 'top',
            customClass: 'mail-popover',
        });

});
</script>