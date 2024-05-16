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
                <p class="lead text-center text-dark">В данной версии сервиса в Профиль можно попасть только через
                    документ входящей или исходящей Почты.</p>
                <p class="text-center text-secondary">Следите за обновлениями. В будущем Профиль станет полностью
                    самостоятельным сервисом.</p>
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