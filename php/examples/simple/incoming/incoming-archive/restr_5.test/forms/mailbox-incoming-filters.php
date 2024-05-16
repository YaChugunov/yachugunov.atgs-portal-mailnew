<?php
// ----- ----- ----- ----- -----
// Подключаем форму поиска
// :::
?>
<link rel="stylesheet" href="<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH . __MAIL_RESTR5; ?>/css/mailbox-incoming-filters.css">

<style>
    .inbox-current-filters {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        padding-left: 10px;
        padding-right: 10px;
        margin-bottom: 10px;
    }

    .inbox-current-filters input,
    .inbox-current-filters select {
        width: 100%;
        padding: 2px 5px;
        font-weight: 400;
        font-size: 0.9em;
        color: #333;
        height: 28px;
        border-color: #adf;
    }

    .inbox-current-filters button {
        height: 28px;
        font-size: 0.9em;
        font-weight: 700;
        padding: 2px 10px;
        border: 1px #fff solid;
    }

    .inbox-current-filters button:hover {
        background-color: #ccc;
        border: 1px #fff solid;
    }

    .inbox-current-filters .filter-item {
        margin-left: 2px;
        margin-right: 2px;
    }

    .inbox-current-filters .filter-item label {
        font-size: 0.9em;
        margin-bottom: 1px;
    }

    div.selector-year {
        font-family: 'Stolzl', sans-serif;
    }

    div.selector-year h3 {
        font-size: 1.65rem;
        color: #e0550b;
    }

    div.input-year {
        font-family: 'Stolzl', sans-serif;
        font-size: 1rem;
    }

    div.input-year input::placeholder {
        color: #CCCCCC !important;
    }

    div.input-year input {
        font-family: 'Stolzl', sans-serif;
        font-size: 1.25rem;
        font-weight: 400;
        width: 8rem;
        height: 2.4rem;
        color: #DC3545 !important;
        text-align: center;
    }
</style>

<div class="row align-items-center justify-content-between filters-block">
    <div class="col">
        <p class="mail-incoming-filters-button">
            <button class="btn btn-sm btn-outline-secondary columnSearch_btnClear btnTop" type="button">
                Очистить фильтры
            </button>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="collapse" data-target="#mail-incoming-filters-block" aria-expanded="false" aria-controls="mail-incoming-filters-block">
                Показать / скрыть набор фильтров
            </button>
            <i class="fa-solid fa-circle fa-xs" style="color: #e0550b;"></i>
        </p>
    </div>
    <div class="col">
        <div class="selector-year d-flex flex-column justify-content-center">
            <div class="input-year d-flex flex-row align-items-center justify-content-end w-100">
                <div class="text-nowrap mr-3">Год архива</div>
                <input type="text" class="form-control text-danger" id="filterArchiveYear" placeholder="ГГГГ">
                <div class="align-self-center px-2" data-toggle="popover" data-content="<p>Для просмотра архива за определенный год, введите год и нажмите Enter. Для просмотре всего архива - очистите поле и также нажмите Enter.</p>">
                    <i class="fa-solid fa-circle-info text-dark"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section d-flex flex-column mb-3 filters-block">
    <div id="mail-incoming-filters-block" class="collapse in w-100">
        <div class="card card-body bg-light border-0 w-100">

            <div class="block d-flex flex-row">
                <!-- Блок -->
                <div class="form-group w-25 mb-3 mx-1">
                    <label for="filterNumber">Номер документа</label>
                    <input type="text" class="form-control" id="filterNumber" name="filterNumber" aria-describedby="filterNumberHelp" placeholder="Номер или часть номера документа">
                    <!-- <small id="filterNumberHelp" class="form-text text-muted">Без префикса '1-2/'</small> -->
                </div>
                <!-- Блок -->
                <div class="form-group w-25 mb-3 mx-1">
                    <label for="filterKOD">ID документа</label>
                    <input type="text" class="form-control" id="filterKOD" name="filterKOD" aria-describedby="filterKODHelp" placeholder="Целиком или часть ID документа">
                </div>
                <!-- Блок -->
                <div class="form-group w-25 mb-3 mx-1">
                    <label for="filterSourceID">Исходящий номер контрагента</label>
                    <input type="text" class="form-control" id="filterSourceID" name="filterSourceID" aria-describedby="filterSourceIDHelp" placeholder="Часть исходящего номера контрагента">
                </div>
            </div>
            <div class="block d-flex flex-row">
                <!-- 
                  < БЛОК >
                  Фильтр по типу документа 
                -->
                <div class="form-group flex-fill mb-3 mx-1">
                    <label for="filterType">Тип документа</label>
                    <select class="form-control" id="filterType" name="filterType">
                        <option value="">Все типы</option>
                        <?php
                        $_reqFilterType = mysqlQuery(" SELECT type_name_short, type_name_full FROM mailbox_sp_doctypes_incoming WHERE status = '1' AND type_name_full != '' ORDER BY id ASC ");
                        while ($_rowFilterType = mysqli_fetch_assoc($_reqFilterType)) {
                        ?>
                            <option value='<?php echo $_rowFilterType["type_name_short"]; ?>'>
                                <?php echo $_rowFilterType["type_name_full"]; ?>
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <!-- 
                  < БЛОК >
                  Фильтр по получателю 
                -->
                <div class="form-group flex-fill mb-3 mx-1">
                    <label for="filterRecipient">Получатель</label>
                    <select class="form-control" id="filterRecipient" name="filterRecipient">
                        <option value="">Все получатели</option>
                        <?php
                        $_QRY3 = mysqlQuery(" SELECT namezayvfio FROM mailbox_sp_users WHERE status_zayvtel = '1' AND namezayvfio != '' ORDER BY namezayvfio ASC ");
                        while ($_ROW3 = mysqli_fetch_assoc($_QRY3)) {
                        ?>
                            <option value='<?php echo $_ROW3["namezayvfio"]; ?>'>
                                <?php echo $_ROW3["namezayvfio"]; ?>
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <!-- 
                  < БЛОК >
                  Фильтр по ответственному 
                -->
                <div class="form-group flex-fill mb-3 mx-1">
                    <label for="filterIspol">Ответственный</label>
                    <select class="form-control" id="filterIspol" name="filterIspol">
                        <option value="">Все ответственные</option>
                        <?php
                        $_QRY3 = mysqlQuery(" SELECT namezayvfio FROM mailbox_sp_users WHERE status_ispol = '1' AND namezayvfio != '' ORDER BY namezayvfio ASC ");
                        while ($_ROW3 = mysqli_fetch_assoc($_QRY3)) {
                        ?>
                            <option value='<?php echo $_ROW3["namezayvfio"]; ?>'>
                                <?php echo $_ROW3["namezayvfio"]; ?>
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <!-- 
                  < БЛОК >
                  Фильтр по подразделению 
                -->
                <div class="form-group mb-3 mx-1">
                    <label for="filterDept">Подразделение (ответственного)</label>
                    <select class="form-control" id="filterDept" name="filterDept">
                        <option value="">Все подразделения</option>
                        <?php
                        $_QRY3 = mysqlQuery(" SELECT koddept, dept_title_short,dept_title_full FROM dept_list WHERE id<>'99' ORDER BY id ASC ");
                        while ($_ROW3 = mysqli_fetch_assoc($_QRY3)) {
                        ?>
                            <option value='<?php echo $_ROW3["koddept"]; ?>'>
                                <?php echo $_ROW3["dept_title_full"]; ?>
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="block d-flex flex-row">
                <!-- Блок -->
                <div class="form-group flex-fill mb-3 mx-1">
                    <label for="filterIspolDL">Дедлайн</label>
                    <select class="form-control" id="filterIspolDL" name="filterIspolDL">
                        <option value="">Не важно</option>
                        <option value="nodl">Без дедлайна</option>
                        <option value="more3">До дедлайна более 3-х суток</option>
                        <option value="less3">До дедлайна менее 3-х суток</option>
                        <option value="less1">До дедлайна менее суток</option>
                        <option value="expired">Срок дедлайна истёк</option>
                    </select>
                </div>
                <!-- Блок -->
                <div class="form-group flex-fill mb-3 mx-1">
                    <label for="filterInControl">Контроль (КИ)</label>
                    <select class="form-control" id="filterInControl" name="filterInControl">
                        <option value="">Не важно</option>
                        <option value="0">Только не на контроле</option>
                        <option value="1">Только на контроле</option>
                    </select>
                </div>
                <!-- Блок -->
                <div class="form-group flex-fill mb-3 mx-1">
                    <label for="filterCheckout">Исполнения</label>
                    <select class="form-control" id="filterCheckout" name="filterCheckout">
                        <option value="">Не важно</option>
                        <option value="0">Не исполнен</option>
                        <option value="1">Исполнен</option>
                    </select>
                </div>
                <!-- Блок -->
                <div class="form-group flex-fill mb-3 mx-1">
                    <label for="filterAbout">Тема документа</label>
                    <input type="text" class="form-control" id="filterAbout" name="filterAbout" placeholder="Что-нибудь из темы документа">
                </div>
                <!-- Блок -->
                <div class="form-group flex-fill mb-3 mx-1">
                    <label for="filterSender">Организация-отправитель</label>
                    <input type="text" class="form-control" id="filterSender" name="filterSender" placeholder="Часть названия организации">
                </div>
            </div>
            <div class="block d-flex flex-row justify-content-end">
                <div class="form-group">
                    <button id="columnSearch_btnApply" type="button" class="btn btn-sm btn-outline-secondary">Применить
                        фильтры</button>
                    <button id="columnSearch_btnClear" type="button" class="btn btn-sm btn-outline-secondary">Очистить</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript" language="javascript" class="init">
    $(window).on("load", function() {

        $('#mailnew-incoming-center .filters-block div[data-toggle="popover"]').popover({
            html: true,
            trigger: 'hover',
            placement: 'top',
            customClass: 'mail-popover'
        })
    });
</script>