<?php

?>
<link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Bootstrap/Bootstrap-4.6.2/css/bootstrap.css"
      rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"
        integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" language="javascript" class="init"
        src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/_assets/libs/Bootstrap/Bootstrap-4.6.2/js/bootstrap.bundle.js">
</script>

<div class="d-flex align-items-center flex-column justify-content-center h-100 bg-dark" id="header">
    <h1 class="text-uppercase text-white">Скоро и так тоже можно будет</h1>
    <h3 class="text-uppercase text-secondary">но не сейчас</h3>
    <button type="button" class="btn btn-outline-primary mt-5" action="action"
            onclick="window.history.go(-1); return false;">Вернуться</button>
</div>

<?php

?>