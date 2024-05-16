</div>
</div> <!-- vh-content in header -->
</div> <!-- body in header -->

<link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/css/footer.css" rel="stylesheet">

<?php
/**
 * * Выбираем тему оформления
 * @param use_lightTheme = 1 Активна светлая тема оформления
 * @param use_lightTheme = 0 Активна темная тема оформления (по умолчанию) 
 * 
 */
if ($use_lightTheme === '1') {
    $footerLogo_filename = "portalnew-footerLogo-1.png";
?>

    <style>
        #footer {
            font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 10px;
            background-color: #F1F1F1 !important;
            color: #111111;
            position: relative;
            z-index: 11;
        }

        #footer .small-text {
            font-size: 0.75rem;
        }

        #footer .small-text.devname {
            color: #999999;
            font-size: 0.65rem;
        }

        #footer .small-text.version {
            font-size: 0.85rem;
        }

        #footer .small-text.devcomp {
            color: #111111;
            font-size: 0.8rem;
        }

        #footer h3.section-title {
            font-size: 1.1rem;
            color: #000000;
            white-space: nowrap;
        }

        #footer p {
            margin-bottom: 0.25rem;
        }

        #footer p.section-text,
        #footer p.section-text a {
            font-size: 0.75rem;
            color: #111111;
        }

        #footer p.section-text i {
            color: #111111;
            padding-right: 0.75rem;
        }

        #footer div.col-copyright {
            /* border-left: 2px solid #555555; */
            /* border-right: 2px solid #555555; */
        }

        #footer div.col-copyright img {
            max-width: 100px;
            opacity: 0.85;
        }

        #footer .footer-wiki-link:hover {
            cursor: pointer;
        }

        #footer span.footer-wiki-link:hover {
            text-decoration: underline;
        }
    </style>
<?php
} else {
    $footerLogo_filename = "portalnew-footerLogo-1-inverse.png";
?>
    <style>
        #footer {
            font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 10px;
            background-color: #161617 !important;
            color: #CCCCCC;
            position: relative;
            z-index: 11;
        }

        #footer .small-text {
            font-size: 0.75rem;
        }

        #footer .small-text.devname {
            color: #AAAAAA;
            font-size: 0.65rem;
        }

        #footer .small-text.version {
            font-size: 0.85rem;
        }

        #footer .small-text.devcomp {
            color: #CCCCCC;
            font-size: 0.8rem;
        }

        #footer h3.section-title {
            font-size: 1.1rem;
            color: #F1F1F1;
            white-space: nowrap;
        }

        #footer p {
            margin-bottom: 0.25rem;
        }

        #footer p.section-text,
        #footer p.section-text a {
            font-size: 0.75rem;
            color: #AAAAAA;
        }

        #footer p.section-text i {
            color: #AAAAAA;
            padding-right: 0.75rem;
        }

        #footer div.col-copyright {
            /* border-left: 2px solid #555555; */
            /* border-right: 2px solid #555555; */
        }

        #footer div.col-copyright img {
            max-width: 100px;
            opacity: 0.35;
        }

        #footer .footer-wiki-link:hover {
            cursor: pointer;
        }

        #footer span.footer-wiki-link:hover {
            text-decoration: underline;
        }
    </style>
<?php
}
?>
<div id="footer" class="p-4 text-center">
    <div class="container-fluid">
        <div class="row">
            <div class="col text-left">
                <div class="d-flex flex-row">
                    <div class="d-flex flex-column mr-5">
                        <h3 class="section-title mb-3">Экосистема портала</h3>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portalnew/index.php" title="Портал 2023 / Главная страница">Портал</a></p>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/index.php?type=main" title="Почта">Почта</a></p>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/sp/index.php?type=main" title="Новый справочник 2023">Справочник</a></p>
                        <h3 class="section-title mt-3 mb-3">Старые сервисы</h3>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal" title="Портал">Портал (старая версия)</a></p>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dognet" title="Сервис Договор">Договор</a></p>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/hr" title="Сервис Кадры">Кадры</a></p>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/ism" title="Сервис ИСМ/СМК">ИСМ/СМК</a></p>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailATC/incoming.php?mode=thisyear" title="Входящая почта АТ Система">Входящая АТ Система</a></p>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailATC/outgoing.php?mode=thisyear" title="Исходящая почта АТ Система">Исходящая АТ Система</a></p>
                    </div>
                    <div class="d-flex flex-column mr-5">
                        <h3 class="section-title mb-3">Разделы</h3>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dognet/dognet-blankview.php?blankview_type=current" title="Бланки заявок на новый договор">Бланки заявок на новый договор</a></p>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dognet/dognet-zayvview.php?zayvview_type=current" title="Заявки ИТиТО АСУ">Заявки ИТиТО АСУ</a></p>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dognet/dognet-report.php" title="Отчёты сервиса Договор">Отчёты сервиса Договор</a></p>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/dognet/dognet-base.php?section=missions" title="Реестр договоров (для служебных заданий)">Реестр договоров (для сл. заданий)</a>
                        </p>
                        <h3 class="section-title mt-3 mb-3">Ссылки</h3>
                        <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/eda" title="Заказать обед в разделе Еда 2.0 на Портале">Еда 2.0 (Портал)</a></p>
                        <p class="section-text"><a href="http://dinner.atgs.ru" title="Заказать обед на сайте dinner.atgs.ru" target="_blank">Еда (dinner.atgs.ru)</a>
                        </p>
                        <p class="section-text"><a href="https://www.atgs.ru" title="Сайт АТГС" target="_blank">Сайт
                                АТГС</a></p>
                    </div>
                </div>
            </div>
            <div class="col-copyright col-2 d-flex flex-column justify-content-end">
                <div class="mt-auto">
                    <p class="section-text"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portalnew/?mode=changeslog">История
                            изменений и
                            обновлений</a></p>
                </div>
                <div class="small-text version mb-auto">
                    <?php echo getServiceVersion('mail'); ?>
                </div>
                <img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portalnew/_assets/images/<?php echo $footerLogo_filename; ?>" class="mx-auto d-block mb-1 w-25 rounded-circle footer-logo">
                <div class="small-text devcomp">Корпоративные web-сервисы<sup>&copy;</sup></div>
                <div class="small-text devname mt-1">Ярослав Чугунов</div>
                <div class="small-text devname">2017-<?php echo date('Y'); ?></div>
            </div>
            <div class="col text-right">
                <div class="d-flex flex-row justify-content-end">
                    <div class="d-flex flex-column mr-5">
                        <h3 class="section-title mb-3">Вики</h3>
                        <p class="section-text"><span id="searchInput-help-footer" class="footer-wiki-link">Что такое
                                Поиск 1.0</span></p>
                        <p class="section-text"><span id="messages-help-footer" class="footer-wiki-link">Что такое
                                уведомления пользователя</span></p>
                        <p class=" section-text"><span id="cloudFiles-help-footer" class="footer-wiki-link">Что такое
                                Облако</span></p>
                        <p class=" section-text"><span id="serviceSP-help-footer" class="footer-wiki-link">Что такое
                                Справочник</span></p>
                        <p class=" section-text"><span id="serviceNewmail-help-footer" class="footer-wiki-link">Почему
                                Почта называется Новая</span></p>
                        <p class=" section-text"><span id="serviceProfile-help-footer" class="footer-wiki-link">Зачем
                                нужен Профиль</span></p>
                        <h3 class=" section-title mt-3 mb-3">FAQ</h3>
                    </div>
                    <div class="d-flex flex-column">
                        <h3 class="section-title mb-3">Техническая поддержка</h3>
                        <div class="mb-3">
                            <p class="section-text"><a href="mailto:chugunov@atgs.ru">chugunov@atgs.ru</a>
                            </p>
                            <p class="section-text">+7 926 112 4469 / WhatsApp,
                                Telegram
                            </p>
                            <p class="section-text">8 495 660 0802 (513)</p>
                        </div>
                        <h3 class="section-title mt-3 mb-3">Социальные сети</h3>
                        <p class="section-text"><a href="https://vk.com/atgsportal" target="_blank">VK-сообщество</a>
                        </p>
                        <p class="section-text"><a href="https://t.me/+bPuTlfJa87kzYmVi" target="_blank">Канал в
                                Telegram</a></p>
                        <div class="d-flex flex-row justify-content-end mt-1">
                            <a href="https://vk.com/atgsportal" target="_blank"><img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portalnew/_assets/images/socials-logo/vk-logo-blue_120x120.svg" width="32" class="mr-1 rounded-circle"></a>
                            <a href="https://t.me/+YoT9ueBKP21hYjhi" target="_blank"><img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portalnew/_assets/images/socials-logo/telegram-logo.svg" width="32" class="ml-1 arounded-circle"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</div> <!-- servicename-sp in header -->
<script type="text/javascript" language="javascript" class="init">
    $(window).on("load", function() {
        $('#before-load').find('i').fadeOut().end().delay(400).fadeOut('slow');
        $('#portalnew-navbar div[data-toggle="popover"]')
            .popover({
                html: true,
                trigger: 'hover',
                placement: 'bottom',
            })
        $('#portalnew-main-input div[data-toggle="popover"]')
            .popover({
                html: true,
                trigger: 'hover',
                placement: 'top',
            })
        $('#portlnew-navbar div[data-toggle="popover"]')
            .popover({
                html: true,
                trigger: 'hover',
                placement: 'bottom',
            })
        $('#portalnew-main-bottom-blocks div[data-toggle="popover"], #portalnew-main-bottom-blocks a[data-toggle="popover"]')
            .popover({
                html: true,
                trigger: 'hover',
                placement: 'top',
            })
        $('#currentBirthdays div[data-toggle="popover"]')
            .popover({
                html: true,
                trigger: 'hover',
                placement: 'top',
            })
        $('#navbar-topmain div[data-toggle="popover"]')
            .popover({
                html: true,
                trigger: 'hover',
                placement: 'bottom',
            })



        $("#searchInput-help, #searchInput-help-footer").click(function() {
            $('#loadedData-help-output').load(
                '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH; ?>/data/help-mainSearch-loadtoModal.html',
                function() {
                    $('#loadedData-help-modal h5.modal-title').html(
                        "Инструмент глобального поиска Портала - Поиск 1.0");
                    $('#loadedData-help-modal').modal('show');
                });
        });
        $("#messages-help-footer").click(function() {
            $('#loadedData-help-output').load(
                '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH; ?>/data/help-userMessages-loadtoModal.html',
                function() {
                    $('#loadedData-help-modal h5.modal-title').html(
                        "Уведомления Портала. Стандартный список уведомлений и push");
                    $('#loadedData-help-modal').modal('show');
                });
        });
        $("#cloudFiles-help-footer").click(function() {
            $('#loadedData-help-output').load(
                '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH; ?>/data/help-cloudFiles-loadtoModal.html',
                function() {
                    $('#loadedData-help-modal h5.modal-title').html(
                        "Что такое Облако и зачем оно нужно");
                    $('#loadedData-help-modal').modal('show');
                });
        });
        $("#serviceSP-help-footer").click(function() {
            $('#loadedData-help-output').load(
                '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH; ?>/data/help-SP-loadtoModal.html',
                function() {
                    $('#loadedData-help-modal h5.modal-title').html(
                        "Справочник как единое хранилище информации о контрагентах");
                    $('#loadedData-help-modal').modal('show');
                });
        });
        $("#serviceNewmail-help-footer").click(function() {
            $('#loadedData-help-output').load(
                '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH; ?>/data/help-Newmail-loadtoModal.html',
                function() {
                    $('#loadedData-help-modal h5.modal-title').html(
                        "Что нового в Новой почте и как с этим работать.");
                    $('#loadedData-help-modal').modal('show');
                });
        });
        $("#serviceProfile-help-footer").click(function() {
            $('#loadedData-help-output').load(
                '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH; ?>/data/help-Profile-loadtoModal.html',
                function() {
                    $('#loadedData-help-modal h5.modal-title').html(
                        "Профиль. Надстройка которая расширяет возможности");
                    $('#loadedData-help-modal').modal('show');
                });
        });

        $('#navbar-topmain h1.service-title').html("Почта");
        $('#navbar-topmain h3.service-subtitle').html("Входящая и исходящая корпоративная корреспонденция");

    });
</script>
</body>

</html>