<?php
if (isset($_GET['uid'])) {
    $_reqDB_docmail = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM " . __MAIL_INCOMING_TABLENAME . " WHERE koddocmail='{$_GET['uid']}'"));
}
if (isset($_GET['type']) && !empty($_GET['type'])) {
    if ($_GET['type'] == 'in' && isset($_GET['mode']) && $_GET['mode'] == "profile") {
        $noerrors = true;
?>
<div id="profile-title" class="pt-2 text-center">
    <div class="p-3 mb-2 bg-light text-dark shadow">
        <h1 class="mb-3 text-center text-body">Профиль документа</h1>
        <h3 class="mb-0 text-center text-dark">
            <?php echo $noerrors ? "Входящая почта № 1-2/" . $_reqDB_docmail['inbox_docIDSTR'] : ""; ?></h3>
        <p class="text-center text-muted">Все в БД Портала, что как-либо связано с этим документом</p>
    </div>
</div>
<?php
    } else {
        $noerrors = false;
    }
}
?>