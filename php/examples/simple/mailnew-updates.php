<?php
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
if ($_SESSION['id'] !== '999') {
    $_QRY = mysqli_fetch_assoc(mysqlQuery("SELECT update_id, update_ver FROM mailbox_updates WHERE active='1' ORDER BY id DESC LIMIT 1"));
    $updateID = !empty($_QRY) ? $_QRY['update_id'] : "";
    $updateVersion = !empty($_QRY) ? $_QRY['update_ver'] : "";
} else {
    $_QRY = mysqli_fetch_assoc(mysqlQuery("SELECT update_id, update_ver FROM mailbox_updates WHERE testmode='1' ORDER BY id DESC LIMIT 1"));
    $updateID = !empty($_QRY) ? $_QRY['update_id'] : "";
    $updateVersion = !empty($_QRY) ? $_QRY['update_ver'] : "";
}
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
?>
<link rel="stylesheet"
      href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Other/Bxslider/4.2.12/jquery.bxslider.css">
<script
        src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Other/Bxslider/4.2.12/jquery.bxslider.min.js">
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

// 
// ###### ## ##### ## ###### ## ##### ## ###### ## ##### ## ###### ## #####
// Функция вывода промо обновления
//
function showUpdateInModal(parUpdateID, parProgressOn, parModalID, parUpdatePath, parUpdateFilename) {

    $.getJSON('<?php echo __ROOT . __SERVICENAME_MAILNEW; ?>' + parUpdatePath + '/' + parUpdateFilename, function(
        data) {
        // var jsonObj = jQuery.parseJSON(data);
        var jsonObj = data;
        for (i = 0; i < jsonObj.length; i++) {
            var res = '';
            var update = jsonObj[i].update;
            var updateID = update[0].updateID;
            var versionAfterUpdate = update[0].versionAfterUpdate;
            var updateSelector = update[0].updateSelector;
            var updateIDStr = update[0].updateIDStr;
            var updateReleaseDate = update[0].releaseDate;
            var updateReleaseStatus = update[0].releaseStatus;
            //
            console.log('showUpdateInModal >>>', 'parUpdateID', parUpdateID, 'parUpdateID', parUpdateID,
                'updateReleaseStatus', updateReleaseStatus);
            if (updateSelector === parUpdateID && parUpdateID !== "" && updateReleaseStatus === "1") {
                // res += '<div class="item" id="slider-item-' + updateDataID + '">';
                // res += '</div>';
                var updateData = update[0].updateData;
                $.each(updateData, function(key, val) {
                    if (updateData[key].showInPromo === "1") {
                        var updateDataID = updateData[key].id;
                        var updateTitle = updateData[key].updateTitle;
                        var promoTitle = updateData[key].promoTitle;
                        var promoHTML = updateData[key].promoHTML;
                        var promoTitle_add1 = updateData[key].promoTitle_add1;
                        var promoHTML_add1 = updateData[key].promoHTML_add1;
                        var promoTitle_add2 = updateData[key].promoTitle_add2;
                        var promoHTML_add2 = updateData[key].promoHTML_add2;
                        res += '<div class="item" id="slider-item-' + updateDataID + '">';
                        res += (updateData[key].updateTitle !== "На связи") ?
                            '<h3 class="mb-3 text-primary">' + updateTitle +
                            '<i class="fa-solid fa-hashtag fa-xs ml-2"></i>' + updateIDStr + '</h3>' :
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
                    }
                });
                $(res).appendTo('#' + parModalID + ' .slider div.slider-items');
            }
        }

        var slideCount2 = $('#' + parModalID + ' .slider-items div.item').length - 1;
        console.log('slideCount2', slideCount2);
        if (slideCount2 > 0) {
            $('#' + parModalID).modal("show");
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
                    if (newIndex > 1) {
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
                        $('#' + parModalID + ' .modal-footer').removeClass('invisible');
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
                    if (newIndex < 2) {
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

            $('#' + parModalID).on('shown.bs.modal', function(e) {
                if (checkVal(slider)) {
                    slider.reloadSlider();
                }
                if (parProgressOn === 1) {
                    var progress = 0;
                    // повторить с интервалом 1 секунды
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
                                'Ваша Почта обновилась до версии <b><?php echo $updateVersion; ?></b>'
                            );
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
                                'Ваша Почта обновилась до версии <b><?php echo $updateVersion; ?></b>'
                            );
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
                $('#' + parModalID + ' .slider-title-control').removeClass('invisible');
                $('#' + parModalID + ' .slider-title-control>div.slider-title-prev').addClass('d-none');
                $('#' + parModalID + ' .slider-title-control>div.slider-title-next').removeClass(
                    'invisible d-none');
            });
        }
    });
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

<div class="modal fade" id="modal-updateShow" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="modal-updateShow-label" aria-hidden="true">
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
                        <!-- --- --- --- -->
                        <div class="slider-items">
                            <div class="item" id="slider-item-0">
                                <div class="progress my-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                                         role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"
                                         style="">
                                    </div>
                                </div>
                                <div id="updateComplete" class=""></div>
                            </div>
                        </div>
                        <!-- --- --- --- -->
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-top-0 pt-0 invisible d-flex justify-content-start">
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input small" id="updates-doNotShowAgain"
                           style="margin-top: 6px">
                    <label class="form-check-label small text-dark pl-2" for="updates-doNotShowAgain"><b>Больше не
                            показывать</b></label>
                </div>
                <button type="button" class="btn btn-danger text-white btn-xl ml-auto" data-dismiss="modal"
                        data-userid="" data-updateid=""
                        onclick="ajaxRequest_checkUpdateOnReadAsync(<?php echo $updateID; ?>, <?php echo $_SESSION['id']; ?>, $('#updates-doNotShowAgain').prop('checked') ? 'mark' : '')">Начать
                    работу</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" language="javascript">
$(document).ready(function() {
    var updateID = '<?php echo $updateID; ?>';
    var userID_session = '<?php echo $_SESSION['id']; ?>';
    var userLogin_session = '<?php echo $_SESSION['login']; ?>';
    var progressOn = ajaxRequest_checkUpdateOnReadAsync(updateID, userID_session, '');
    var checkupdate = ajaxRequest_checkUpdateOnReadAsync(updateID, userID_session, 'check');
    console.log('checkUpdateOnReadAsync (showUpdateInModal) >>>', 'checkupdate', checkupdate);
    if (checkupdate > 0) {
        showUpdateInModal(updateID, progressOn, 'modal-updateShow', '/updates', 'updates.all.fullLog.json');
        console.log('showUpdateInModal >>>', 'updateID', updateID);
    }

});
</script>