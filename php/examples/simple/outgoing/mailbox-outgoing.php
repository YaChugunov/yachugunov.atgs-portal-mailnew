<?php
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
?>
<script type="text/javascript" language="javascript" class="">
//
</script>

<style>
#mailnew-outgoing-top,
#mailnew-outgoing-center,
#mailnew-outgoing-bottom {
    font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
}
</style>

<script type="text/javascript" language="javascript" class="init"
        src="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH; ?>/js/outgoingTopblock-control.js">
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
                if ($_GET['type'] == 'out') {
                    if ($_GET['mode'] == 'thisyear') {
                        echo '<div id="mailnew-outgoing-top" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH  . __MAIL_RESTR . "/mailbox-outgoing-top.php";
                        echo '</div>';
                        echo '<div id="mailnew-outgoing-center" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH  . __MAIL_RESTR . "/mailbox-outgoing-current.php";
                        echo '</div>';
                        echo '<div id="mailnew-outgoing-bottom" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_CURRENT_WORKPATH  . __MAIL_RESTR . "/mailbox-outgoing-bottom.php";
                        echo '</div>';
                    } elseif ($_GET['mode'] == 'archive') {
                        echo '<div id="mailnew-outgoing-top" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_ARCHIVE_WORKPATH  . __MAIL_RESTR . "/mailbox-outgoing-top.php";
                        echo '</div>';
                        echo '<div id="mailnew-outgoing-center" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_ARCHIVE_WORKPATH  . __MAIL_RESTR . "/mailbox-outgoing-archive.php";
                        echo '</div>';
                        echo '<div id="mailnew-outgoing-bottom" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_ARCHIVE_WORKPATH  . __MAIL_RESTR . "/mailbox-outgoing-bottom.php";
                        echo '</div>';
                    } elseif ($_GET['mode'] == 'profile') {
                        echo '<div id="mailnew-outgoing-top" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH  . __MAIL_RESTR . "/mailbox-outgoing-top.php";
                        echo '</div>';
                        echo '<div id="mailnew-outgoing-center" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH  . __MAIL_RESTR . "/mailbox-outgoing-profile.php";
                        echo '</div>';
                        echo '<div id="mailnew-outgoing-bottom" class="">';
                        include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_OUTGOING_PROFILE_WORKPATH  . __MAIL_RESTR . "/mailbox-outgoing-bottom.php";
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
    $("#mail-outgoing-deck-block div.outStats").on("click", "div.filterLinked", function() {
        $('#mail-outgoing-filters-block *').filter('input, select').each(function(index, element) {
            var itemID = $(element).attr('id');
            var tagName = $(element).prop('tagName').toLowerCase();
            if (!$(element).prop('disabled')) {
                sessionStorage.removeItem('outbox_' + itemID);
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
    $('#mail-outgoing-deck-block div.incStats div.filterLinked, #mail-outgoing-deck-block *[data-toggle="popover"]')
        .popover({
            html: true,
            trigger: 'hover',
            placement: 'top',
            customClass: 'mail-outgoing-deck-block',
        });
    $('#mail-main-outbox table th').tooltip({
        html: true,
        trigger: 'hover',
        placement: 'top',
        customClass: 'mail-outgoing-insideTable-tooltip'
    });
    $('#divOnlyIspolMe *[data-toggle="popover"]')
        .popover({
            html: true,
            trigger: 'hover',
            placement: 'top',
            customClass: 'mail-outgoing-deck-block',
        });
});
</script>