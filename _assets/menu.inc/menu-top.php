<script type="text/javascript" language="javascript" class="">
    var chksessionID = '<?php echo session_id(); ?>';
</script>

<nav id="nav-topmenu" class="navbar navbar-expand-sm bg-light fixed-top sticky-top">

    <div class="container-fluid">
        <a class="navbar-brand" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php">
            <img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/_assets/images/portal-main-logo.svg" alt="Logo" width="36" height="36" class="d-inline-block align-text-middle">
        </a>

        <div class="col-8">
            <ul class="nav justify-content-start">
                <li class="nav-item">
                    <a class="nav-link" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php">Портал</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Договор</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_MAILNEW; ?>/index.php?type=main">Почта</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Почта АТС</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Кадры</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">ИСМ/СМК</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="http://<?php echo $_SERVER['HTTP_HOST'] . __SERVICENAME_SP; ?>/index.php">Справочник</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Еда 2.0</a>
                </li>
            </ul>
        </div>
        <div class="col">
            <ul class="nav justify-content-end">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php include($_SERVER['DOCUMENT_ROOT'] . "/_assets/php/userName_echo.php"); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
        </div>
        <img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/_assets/images/portal-main-logo.svg" alt="Logo" width="32" height="32" class="d-inline-block align-text-middle">
    </div>

</nav>