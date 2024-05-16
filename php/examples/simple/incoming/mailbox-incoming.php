<?php
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
?>
<script type="text/javascript" language="javascript" class="">
//
</script>

<style>
#mailnew-incoming-top,
#mailnew-incoming-center,
#mailnew-incoming-bottom {
    font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
}
</style>

<script type="text/javascript" language="javascript" class="init"
        src="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH; ?>/js/incomingTopblock-control.js">
</script>

<?php
if (isset($_SESSION['password']) && isset($_SESSION['login'])) {
    if (checkUserAuthorization_defaultDB($_SESSION['login'], $_SESSION['password']) == -1) {
        // Редирект на главную страницу
        echo '<meta http-equiv="refresh" content="0; url=' . __ROOT . '">';
    } else {
        // При удачном входе пользователю выдается все, что расположено НИЖЕ звездочек
        // ************************************************************************************
        if (checkUserRestrictions_defaultDB($_SESSION['id'], 'mailnew', 3, 0) == 1) {
            if (isset($_GET['mode']) && !empty($_GET['mode']) && isset($_GET['type']) && !empty($_GET['type'])) {
                if ($_GET['type'] == 'in') {
                    if ($_GET['mode'] == 'thisyear') {
                        echo '<div id="mailnew-incoming-top" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH  . __MAIL_RESTR . "/mailbox-incoming-top.php";
                        echo '</div>';
                        echo '<div id="mailnew-incoming-center" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH  . __MAIL_RESTR . "/mailbox-incoming-current.php";
                        echo '</div>';
                        echo '<div id="mailnew-incoming-bottom" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_CURRENT_WORKPATH  . __MAIL_RESTR . "/mailbox-incoming-bottom.php";
                        echo '</div>';
                    } elseif ($_GET['mode'] == 'archive') {
                        echo '<div id="mailnew-incoming-top" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH  . __MAIL_RESTR . "/mailbox-incoming-top.php";
                        echo '</div>';
                        echo '<div id="mailnew-incoming-center" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH  . __MAIL_RESTR . "/mailbox-incoming-archive.php";
                        echo '</div>';
                        echo '<div id="mailnew-incoming-bottom" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_ARCHIVE_WORKPATH  . __MAIL_RESTR . "/mailbox-incoming-bottom.php";
                        echo '</div>';
                    } elseif ($_GET['mode'] == 'profile') {
                        echo '<div id="mailnew-incoming-top" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_PROFILE_WORKPATH  . __MAIL_RESTR . "/mailbox-incoming-top.php";
                        echo '</div>';
                        echo '<div id="mailnew-incoming-center" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_PROFILE_WORKPATH  . __MAIL_RESTR . "/mailbox-incoming-profile.php";
                        echo '</div>';
                        echo '<div id="mailnew-incoming-bottom" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_PROFILE_WORKPATH  . __MAIL_RESTR . "/mailbox-incoming-bottom.php";
                        echo '</div>';
                    } else {
                        include __DIR_ROOT . __SERVICENAME_PORTALNEW . "/php/examples/simple/main/main/common-includes/errpage-wrongurls.php";
                    }
                } else {
                    include __DIR_ROOT . __SERVICENAME_PORTALNEW . "/php/examples/simple/main/main/common-includes/errpage-wrongurls.php";
                }
            } else {
                include __DIR_ROOT . __SERVICENAME_PORTALNEW . "/php/examples/simple/main/main/common-includes/errpage-wrongurls.php";
            }
        } else {
            include __DIR_ROOT . __SERVICENAME_PORTALNEW . "/php/examples/simple/main/main/common-includes/errpage-nopermissions.php";
        }
        // ************************************************************************************
        // При удачном входе пользователю выдается все, что расположено ВЫШЕ звездочек
    }
} else {
    # Редирект на главную страницу
    echo '<meta http-equiv="refresh" content="0; url=' . __ROOT . '">';
}
?>
<script type="text/javascript" language="javascript" class="init">
$(window).on("load", function() {

});

$(document).ready(function() {
    $("#mail-incoming-deck-block div.incStats").on("click", "div.filterLinked", function() {
        $('#mail-incoming-filters-block *').filter('input, select').each(function(index, element) {
            var itemID = $(element).attr('id');
            var tagName = $(element).prop('tagName').toLowerCase();
            if (!$(element).prop('disabled')) {
                sessionStorage.removeItem('inbox_' + itemID);
            }
            if (tagName === 'select') {
                if (!$(element).prop('disabled')) {
                    $('#' + itemID + ' option').prop('selected', false);
                    $('#' + itemID).val('');
                }
            }
        });
        //
        let filterID = $(this).attr('filterID');
        let filterVal = $(this).attr('filterVal');
        console.log($(this).text(), filterID, filterVal);
        $('#' + filterID + ' option[value="' + filterVal + '"]').prop('selected', true);
        if (filterID === 'filterCheckout') {
            $('#filterInControl option[value="1"]').prop('selected', true);
            $('#filterInControl').val(1);
        }
        if (filterID === 'filterIspolDL' && filterVal !== '' && filterVal !== 'more3' && filterVal !==
            'nodl') {
            $('#filterCheckout option[value="0"]').prop('selected', true);
            $('#filterCheckout').val(0);
            $('#filterInControl option[value="1"]').prop('selected', true);
            $('#filterInControl').val(1);
        }
        if (filterID === 'filterIspolDL' && filterVal == 'more3') {
            $('#filterCheckout option[value="0"]').prop('selected', true);
            $('#filterCheckout').val(0);
            $('#filterInControl option[value="1"]').prop('selected', true);
            $('#filterInControl').val(1);
        }
        $('#columnSearch_btnApply').click();
    });
    $('#mail-incoming-deck-block div.incStats div.filterLinked, #mail-incoming-deck-block *[data-toggle="popover"]')
        .popover({
            html: true,
            trigger: 'hover',
            placement: 'top',
            customClass: 'mail-popover',
        });
    $('#mail-main-inbox table th').tooltip({
        html: true,
        trigger: 'hover',
        placement: 'top',
        customClass: 'tooltip-incoming'
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