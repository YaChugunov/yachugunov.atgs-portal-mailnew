<?php
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
# 
?>
<script type="text/javascript" language="javascript" class="">
//
</script>

<style>
#mailnew-main-profile-top,
#mailnew-main-profile-center,
#mailnew-main-profile-bottom {
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
<div id="mailnew-main-profile-top" class="">

</div>



<div id="mailnew-main-profile-center" class="">
    <div class="my-5">
        <p class="lead text-center text-dark">Если раздел Помощи действительно понадобится, он тут появится. Но
            думаю мы вполне обойдемся разделом Вики и FAQ (см. ссылки подвале сайта), а также VK-сообществом и
            телеграм-каналом.</p>
        <p class="text-center text-secondary">Следите за обновлениями.</p>
    </div>
</div>



<div id="mailnew-main-profile-bottom" class="">

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


});
</script>