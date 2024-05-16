<?php
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
?>
<script type="text/javascript" language="javascript" class="">
    //
</script>

<style>
    #mailnew-help-top,
    #mailnew-help-center,
    #mailnew-help-bottom {
        font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
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
        <div id="mailnew-help-top" class="">

        </div>



        <div id="mailnew-help-center" class="">
            <?php
            if (isset($_GET['type']) && !empty($_GET['type'])) {
                if ($_GET['type'] == 'help') {
                    include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_HELP_WORKPATH . "/mailbox-main-help.php";
                } else {
                    include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_MAIN_WORKPATH  . __MAIL_RESTR . "/mailbox-main-main.php";
                }
            } else {
                include __DIR_ROOT . __SERVICENAME_MAILNEW . __MAIL_MAIN_WORKPATH . "/mailbox-main.php";
            }
            ?>
        </div>



        <div id="mailnew-help-bottom" class="">

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
        //$(document).ready(function() {

        // $('input').inputmask();

    });
</script>