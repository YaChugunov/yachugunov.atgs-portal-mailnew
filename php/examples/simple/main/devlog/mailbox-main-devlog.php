<?php
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
?>

<script type="text/javascript" language="javascript" class="">
    function showInModal(modalID, contentTitle, contentBody) {
        $('#' + modalID + ' .changeDetails-title').text(contentTitle);
        $('#' + modalID).modal('show');
    }

    function showUpdate2(updatePath, updateFilename, userID) {
        $.getJSON('<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>' + updatePath + '/' + updateFilename, function(data) {
            // var jsonObj = jQuery.parseJSON(data);
            var jsonObj = data;
            for (i = 0; i < jsonObj.length; i++) {
                var res = '';
                var update = jsonObj[i].update;
                var updateID = update[0].updateID;
                var updateTestMode = update[0].testMode;
                // if (updateTestMode !== "1" || (userID === "999" && updateTestMode === "1")) {
                var updateIDStr = (checkVal(update[0].updateID) === 1 && update[0].releaseStatus != '0') ?
                    '<span class="mr-1">Update ID:</span><span class="mr-3 text-dark">' + update[0]
                    .updateIDStr +
                    '</span>' : "";
                var releaseDate = (checkVal(update[0].releaseDate) === 1 && update[0].releaseStatus === '1') ?
                    update[0].releaseDate : "";
                var releaseDateStr = (checkVal(update[0].releaseDate) === 1 && update[0].releaseStatus ===
                        '1') ?
                    '<span class="mr-1">Update release date:</span><span class="mr-3 text-dark">' + update[0]
                    .releaseDate + '</span>' : "";
                var versionAfterUpdateStr = (checkVal(update[0].versionAfterUpdate) === 1 && update[0]
                        .releaseStatus === '1') ?
                    '<span class="mr-1">Service version after update:</span><span class="mr-3 text-dark">' +
                    update[
                        0].versionAfterUpdate + '</span>' : "";

                if (update[0].releaseStatus === '0') {
                    var releaseStatusStr = '<sup class="text-warning ml-1">В планах</sup>';
                } else if (update[0].releaseStatus === '1') {
                    var releaseStatusStr =
                        '<sup class="text-success ml-1"><i class="fa-solid fa-square-check"></i></sup>';
                } else if (update[0].releaseStatus === '2') {
                    var releaseStatusStr = '<sup class="text-secondary ml-1">Предрелиз</sup>';
                } else if (update[0].releaseStatus === '3') {
                    var releaseStatusStr = '<sup class="text-danger ml-1">Сейчас в работе</sup>';
                } else {
                    var releaseStatusStr = '';
                }
                var updateTitle = update[0].updateTitle;
                var updateDesc = update[0].updateDesc;
                var versionAfterUpdate = update[0].versionAfterUpdate;
                var showUpdateDetails = (update[0].showInDetails === '1' || (userID === "999" && updateTestMode ===
                        "1")) ?
                    '<a class="" data-toggle="collapse" href="#update' + updateID +
                    '" aria-expanded="false" aria-controls="update' + updateID +
                    '" onclick="">Подробная информация об обновлении</a>' : '';
                var showUpdatePromo = (update[0].showInPromo === '1' || (userID === "999" &&
                        updateTestMode === "1")) ?
                    '<span class="mx-3">&bull;</span><a class="showPromo" onclick="showPromo(\'' +
                    updateID +
                    '\', \'modal-updatePromo\', \'/updates\', \'updates.all.fullLog.json\')">Посмотреть промо обновления</a>' :
                    '';
                //
                res += '<div class="update mb-5">';
                res += '<h3 class="title">' + updateTitle +
                    releaseStatusStr + '</h3>';
                if (update[0].releaseStatus !== '0') {
                    res += '<div class="update-info mb-3">' + updateIDStr + releaseDateStr +
                        versionAfterUpdateStr +
                        '</div>';
                }
                res += '<p class="desc">' + updateDesc + '</p>';
                res += '<p class="mb-3">' +
                    showUpdateDetails + showUpdatePromo + '</p>';
                res +=
                    '<div class="collapse fade" style="margin-left:1.5rem" id="update' + updateID + '">';
                //
                var updateData = update[0].updateData;
                $.each(updateData, function(key, val) {
                    var updateDataTitle = updateData[key].title;
                    var changeID = updateData[key].changeID;
                    var changeIDText = updateData[key].changeIDText;
                    var changeIDStr = (checkVal(updateData[key].changeID) === 1) ?
                        '<span class="mr-1">Release ID:</span><span class="mr-3 text-dark">' +
                        updateData[
                            key].changeIDText + '</span>' : "";
                    var releaseDate = (checkVal(updateData[key].releaseDate) === 1) ? updateData[key]
                        .releaseDate : "";
                    var releaseDateStr = (checkVal(updateData[key].releaseDate) === 1) ?
                        '<span class="mr-1">Release date:</span><span class="mr-3 text-dark">' +
                        updateData[
                            key].releaseDate + '</span>' : "";
                    if (updateData[key].releaseStatus === '0') {
                        var releaseStatusStr = '<span class="text-warning ml-1">В планах</span>';
                    } else if (updateData[key].releaseStatus === '1') {
                        var releaseStatusStr = '<span class="text-success ml-1">Реализовано</span>';
                    } else if (updateData[key].releaseStatus === '2') {
                        var releaseStatusStr = '<span class="text-secondary ml-1">Предрелиз</span>';
                    } else if (updateData[key].releaseStatus === '3') {
                        var releaseStatusStr = '<span class="text-danger ml-1">Сейчас в работе</span>';
                    } else {
                        var releaseStatusStr = '';
                    }
                    var icon = (checkVal(updateData[key].icon) === 1) ? '<span class="mr-3">' +
                        updateData[
                            key].icon + '</span>' : "";
                    var updateDataBlockHTML_1 = updateData[key].blockHTML_1;
                    var updateDataBlockHTML_2 = updateData[key].blockHTML_2;
                    var updateDataBlockHTML_3 = updateData[key].blockHTML_3;
                    var updateDataRef = updateData[key].ref;
                    var blockDetails = updateData[key].blockDetails;
                    var blockDetailsLink = (updateData[key].blockDetails !== "") ?
                        '<a class="card-link" data-toggle="collapse" href="#change' + changeID +
                        '" aria-expanded="false" aria-controls="change' + changeID +
                        '" onclick="">Детали</a>' : '';

                    var blockDev = updateData[key].blockDev;
                    var blockDevStr = checkVal(blockDev) ? blockDev.replace(/["']/g, "&quot;") : "";
                    // var blockDevLink = (updateData[key].blockDev !== "" && userID === "999") ?
                    //     '<span class="mx-2 text-secondary">&bull;</span><a class="card-link" data-toggle="collapse" href="#changeDev' +
                    //     changeID + '" aria-expanded="false" aria-controls="changeDev' + changeID +
                    //     '" onclick="">Для разработчика</a>' :
                    //     '';

                    var blockDevLink = (updateData[key].blockDev !== "" && userID === "999") ?
                        '<span class="mx-2 text-secondary">&bull;</span><a data-changeID="' + changeID +
                        '" class="card-link" onclick="">Для разработчика</a>' : '';
                    res += '<div class="item card border-0 shadow">';
                    res += '<div class="card-body">';
                    res += '<h4 class="item-title">' + updateDataTitle + '</h4>';
                    res += '<div class="item-info mb-3">' + icon + changeIDStr + releaseDateStr +
                        releaseStatusStr + '</span></div>';
                    res += '<div class="mb-2">' + updateDataBlockHTML_1 + updateDataBlockHTML_2 +
                        updateDataBlockHTML_3 + '</div>';
                    res += updateDataRef !== '' ?
                        '<div class="reference"><p class="small text-right text-secondary"><span class="mr-1">Reference:</span>' +
                        updateDataRef + '</p></div>' : '';
                    res += blockDetails !== '' ? '<p class="small text-left">' + blockDetailsLink +
                        blockDevLink + '</p>' : '';
                    if (checkVal(updateData[key].changeID) === 1 && updateData[key].blockDetails !==
                        "") {
                        res += '<div class="collapse" id="change' + changeID +
                            '"><div class="item-details">' + blockDetails + '</div></div>';
                    }
                    if (checkVal(updateData[key].changeID) === 1 && updateData[key].blockDev !== "") {
                        res += '<div class="collapse" id="changeDev' + changeID +
                            '"><div class="item-details">' + blockDev + '</div></div>';
                    }
                    res += '</div>';
                    res += '</div>';
                });
                res += '</div>';
                res += '</div>';
                $(res).appendTo('#updatesLog-block');
                // }
                console.log('updateID, updateDesc, version', updateID, updateDesc,
                    versionAfterUpdate);
                console.log('updateData', updateData);
                console.log('res', res);
            }
        });
    }

    function showPromo(parUpdateID, parModalID, parUpdatePath, parUpdateFilename) {
        var userID = "<?php echo $_SESSION['id']; ?>";
        $('#' + parModalID + ' .slider div.slider-items').empty();
        $.getJSON('<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>' + parUpdatePath + '/' + parUpdateFilename, function(
            data) {
            // var jsonObj = jQuery.parseJSON(data);
            var jsonObj = data;
            for (i = 0; i < jsonObj.length; i++) {
                var res = '';
                var update = jsonObj[i].update;
                var updateID = update[0].updateID;
                var updateTestMode = update[0].testMode;
                var updateIDStr = update[0].updateIDStr;
                var updateShowInPromo = update[0].showInPromo;
                var updateTitle = update[0].updateTitle;
                //
                if (updateID === parUpdateID && updateShowInPromo === "1" && (updateTestMode !== "1" || (
                        userID ===
                        "999" && updateTestMode === "1"))) {
                    $('#' + parModalID + ' .slider div.slider-items').empty();
                    $('#updatePromoID').html('<i class="fa-solid fa-hashtag fa-xs"></i>' + updateIDStr);
                    var updateData = update[0].updateData;
                    $.each(updateData, function(key, val) {
                        var updateDataID = updateData[key].id;
                        var promoTitle = updateData[key].promoTitle;
                        var promoHTML = updateData[key].promoHTML;
                        var promoTitle_add1 = updateData[key].promoTitle_add1;
                        var promoHTML_add1 = updateData[key].promoHTML_add1;
                        var promoTitle_add2 = updateData[key].promoTitle_add2;
                        var promoHTML_add2 = updateData[key].promoHTML_add2;
                        res += '<div class="item" id="slider-item-' + updateDataID + '">';
                        res += (updateData[key].updateTitle !== "На связи") ?
                            '<h3 class="mb-3 text-primary">' + updateTitle + '</h3>' :
                            '<h3 class="mb-3 text-dark">' + updateData[key].updateTitle + '</h3>';
                        res += '<div class="p-1">';
                        res += promoTitle;
                        res += promoHTML;
                        res += promoTitle_add1;
                        res += promoHTML_add1;
                        res += promoTitle_add2;
                        res += promoHTML_add2;
                        res += (updateData[key].updateTitle !== "На связи") ?
                            '<p class="text-right" style="font-size: 0.75rem; color: #ccc !important">Изменение / обновление<i class="fa-solid fa-hashtag fa-xs ml-1"></i>' +
                            updateData[key].changeIDText + '</p>' : '';
                        res += '</div>';
                        res += '</div>';
                    });
                    $(res).appendTo('#' + parModalID + ' .slider div.slider-items');
                }
            }

            var slideCount2 = $('#' + parModalID + ' .slider-items div.item').length - 1;
            console.log('slideCount2', slideCount2);
            var slider = $('#' + parModalID + ' .slider div.slider-items').bxSlider({
                controls: true,
                mode: 'fade',
                prevSelector: '#' + parModalID + ' .slider-title-prev',
                nextSelector: '#' + parModalID + ' .slider-title-next',
                prevText: '<button type="button" style="width:5rem" class="btn btn-outline-danger btn-sm">Назад</button>',
                nextText: '<button type="button" style="width:5rem" class="btn btn-outline-danger btn-sm">Вперед</button>',
                pager: false,
                auto: false,
                pause: 0,
                minSlides: 1,
                maxSlides: 1,
                adaptiveHeight: true,
                infiniteLoop: false,
                touchEnabled: true,
                // slideMargin: 20,
                // slideWidth: 279,
                onSlideNext: function($slideElemen, oldIndex, newIndex) {
                    if (newIndex > 0) {
                        // $('.slider-title-control').addClass('invisible');
                        $('#' + parModalID + ' .slider-title-control>div.slider-title-prev')
                            .removeClass(
                                'invisible d-none');
                        $('#' + parModalID + ' .slider-title-control>div.slider-title-next')
                            .removeClass(
                                'invisible d-none');
                        $('#' + parModalID + ' .modal-footer').css('padding', '15px 31px');
                    }
                    if (newIndex >= slideCount2) {
                        // $('.slider-title-control').addClass('invisible');
                        // $('#' + parModalID + ' .modal-footer').removeClass('invisible');
                        $('#' + parModalID + ' .slider-title-control>div.slider-title-next')
                            .addClass(
                                'd-none');
                        $('#' + parModalID + ' .slider-title-control>div.slider-title-prev')
                            .removeClass(
                                'invisible d-none');
                        $('#' + parModalID + ' .modal-footer').css('padding', '15px 31px');
                    }
                },
                onSlidePrev: function($slideElemen, oldIndex, newIndex) {
                    if (newIndex < slideCount2) {
                        // $('.slider-title-control').addClass('invisible');
                        $('#' + parModalID + ' .slider-title-control>div.slider-title-next')
                            .removeClass(
                                'invisible d-none');
                        $('#' + parModalID + ' .modal-footer').css('padding', '15px 31px');
                    }
                    if (newIndex < 1) {
                        // $('.slider-title-control').addClass('invisible');
                        $('#' + parModalID + ' .slider-title-control>div.slider-title-prev')
                            .addClass(
                                'd-none');
                        $('#' + parModalID + ' .slider-title-control>div.slider-title-next')
                            .removeClass(
                                'invisible d-none');
                        $('#' + parModalID + ' .modal-footer').css('padding', '15px 31px');
                    }
                },
            });
            //
            $('#' + parModalID).modal("show");
            //
            $('#' + parModalID).on('hidden.bs.modal', function(e) {
                console.log('bs.modal >>>', 'destroy slider');
                slider.destroySlider();
            });
            $('#' + parModalID).on('shown.bs.modal', function(e) {
                if (checkVal(slider)) {
                    setTimeout(function() {
                        // showPromo(updateID, 'modal-updatePromo', '/updates',
                        //     'updates.all.fullLog.json');
                        slider.reloadSlider();
                    }, 100);
                }
                $('#' + parModalID + ' .slider-title-control').removeClass('invisible');
                $('#' + parModalID + ' .slider-title-control>div.slider-title-prev').addClass('d-none');
                $('#' + parModalID + ' .slider-title-control>div.slider-title-next').removeClass(
                    'invisible d-none');
            });

        });

    }
</script>

<style>
    #mailnew-main-devlog-top,
    #mailnew-main-devlog-center,
    #mailnew-main-devlog-bottom {
        font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
    }

    #updatesLog-block {
        font-size: 10px;
    }

    #updatesLog-block .update {}

    #updatesLog-block .update h3.title {
        font-family: 'Oswald', sans-serif;
        font-size: 1.5rem;
        font-weight: 400;
    }

    #updatesLog-block .update h3.title sup {
        font-size: 0.8rem;
        top: -1.0rem;
        font-family: "Stolzl", Arial, Helvetica Neue, Helvetica, sans-serif;
    }

    #updatesLog-block .update p {
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        line-height: 1.35rem;
    }

    #updatesLog-block .update p.desc {
        color: #999999;
    }

    #updatesLog-block .update p a {
        color: #0178FA;
        text-decoration: underline;
    }

    #updatesLog-block .update p a:hover {
        text-decoration: none;
    }

    #updatesLog-block .update .item {
        margin-bottom: 1.25rem;
        border-radius: 0.75rem !important;
    }

    #updatesLog-block .update .item a.card-link {
        text-decoration: none;
    }

    #updatesLog-block .update .item h4 {
        font-family: "Stolzl", Arial, Helvetica Neue, Helvetica, sans-serif;
        font-size: 1.0rem;
        font-weight: 400;
        color: #111111;
    }

    #updatesLog-block .update .item p,
    #updatesLog-block .update .item ul {
        font-size: 0.85rem;
        color: #666666;
    }

    #updatesLog-block .update .item-details {
        padding: 1.0rem;
    }

    #updatesLog-block .update .item-details * {
        font-size: 0.8rem !important;
        line-height: 1.15rem !important;
        color: #999999 !important;
    }

    #updatesLog-block .update .update-info,
    #updatesLog-block .update .item-info {
        font-size: 0.70rem;
        color: #AAAAAA;
    }

    #updatesLog-block .update .item .reference p {
        font-size: 0.7rem !important;
        color: #CCCCCC;
        font-style: italic;
    }

    #updatesLog-block .collapsing {
        -webkit-transition: none;
        transition: none;
        display: none;
    }


    #updatesLog-block .slider .bx-wrapper {
        position: relative;
        box-shadow: none;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
    }

    #updatesLog-block .slider-title {
        height: 2rem;
        margin-bottom: 2rem;
    }

    #updatesLog-block .slider-title h1 {
        font-family: 'Oswald', sans-serif;
        font-size: 2.0rem;
        margin-bottom: 0;
        margin-top: 0;
    }

    #updatesLog-block .slider-items .item h3 {
        font-family: "Stolzl", Arial, Helvetica Neue, Helvetica, sans-serif;
        font-size: 1.5rem;
    }

    #updatesLog-block .slider-items .item p.lead {
        font-family: "Stolzl", Arial, Helvetica Neue, Helvetica, sans-serif;
        font-size: 1.0rem;
        font-weight: 500;
    }

    #updatesLog-block .slider-items .item p.lead a:hover {
        text-decoration: none;
    }

    #updatesLog-block .slider-items .item p,
    #updatesLog-block .slider-items .item ul li {
        font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
        font-size: 0.8rem;
    }

    #updatesLog-block .slider-title-control {
        border: 5px solid #fff;
        height: 2rem;
    }

    #updatesLog-block .slider-title-control a:hover {
        filter: brightness(90%);
    }

    #updatesLog-block .slider-title-control button:focus {
        box-shadow: none;
    }

    #updatesLog-block .slider {
        min-height: 5rem;
        overflow: hidden;
    }

    #updatesLog-block .slider div {
        margin: 0;
        padding: 0;
    }

    #updatesLog-block .slider div img {
        width: 100%;
        height: auto;
    }

    #updatesLog-block a.showPromo:hover {
        cursor: pointer;
    }
</style>


<?php
if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization_defaultDB($_SESSION['login'], $_SESSION['password']) == -1) {
        // Редирект на главную страницу
?>
        <meta http-equiv="refresh" content="0; url=<?php echo __ROOT; ?>">
    <?php
    } else {
        // При удачном входе пользователю выдается все, что расположено НИЖЕ звездочек
        // ************************************************************************************
    ?>
        <div id="mailnew-main-devlog-top" class="">

        </div>



        <div id="mailnew-main-devlog-center" class="">
            <div class="my-5">
                <div class="" id="updatesLog-block">

                </div>
            </div>
        </div>



        <div id="mailnew-main-devlog-bottom" class="">

        </div>


        <div class="modal fade" id="modal-updatePromo" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modal-updatePromo-label" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content shadow border-0 bg-white text-dark" style="font-family:'Stolzl Book',sans-serif">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="slider-title d-flex justify-content-start align-items-center my-3 mb-5">
                                <h1>Промо обновления<span id="updatePromoID" class="ml-2"></span></h1>
                                <div class="slider-title-control d-flex mb-2 ml-auto invisible">
                                    <div class="slider-title-prev invisible mr-1" style="height:2rem"></div>
                                    <div class="slider-title-next invisible" style="height:2rem"></div>
                                </div>
                            </div>
                            <div class="slider">
                                <div class="slider-items">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center border-top-0 pt-0 d-flex">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-changeDetails" data-keyboard="false" data-backdrop="true" tabindex="-1" role="dialog" aria-labelledby="modal-changeDetails-label" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content shadow border-0 bg-white text-dark" style="font-family:'Stolzl Book',sans-serif">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <h3 class="changeDetails-title"></h3>
                            <div class="changeDetails-text"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-top-0 pt-0 d-flex">
                    <button type="button" class="btn btn-outline-dark btn-sm" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>

    <?php
        // ************************************************************************************
        // При удачном входе пользователю выдается все, что расположено ВЫШЕ звездочек
    }
} else {
    # Редирект на главную страницу
    ?>
    <meta http-equiv="refresh" content="0; url=<?php echo __ROOT; ?>">
<?php
}
?>
<script type="text/javascript" language="javascript" class="init">
    $(window).on("load", function() {

        showUpdate2('/updates', 'updates.all.fullLog.json', '<?php echo $_SESSION['id']; ?>');

        $('#modal-updatePromo').on('hidden.bs.modal', function(e) {
            console.log('#modal-updatePromo', 'hidden');
        });


    });
</script>