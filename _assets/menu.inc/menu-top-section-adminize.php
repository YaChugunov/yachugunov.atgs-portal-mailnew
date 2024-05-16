<?php
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
// ::: АДМИНКА ::: BEGIN
?>
<li class="dropdown" style="color:#111; font-size:0.9em">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
        aria-expanded="false">Администрирование&nbsp;<span class="caret"></span></a>
    <ul class="dropdown-menu">
        <li><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/_adminize?tools=common">Общие установки</a></li>
        <li><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/_adminize?tools=access">Права и доступ</a></li>
        <li><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/_adminize?tools=users">Пользователи</a></li>
        <li><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/_adminize?tools=logs">Логи</a></li>
        <li><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/_adminize?tools=mailing">Рассылки</a></li>
        <li class="dropdown-submenu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Сервисы</a>
            <ul class="dropdown-menu">
                <li><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/_adminize?tools=services-mail">Почта</a></li>
            </ul>
        </li>
        <li class="dropdown-submenu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Пользователи</a>
            <ul class="dropdown-menu">
                <li><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/_adminize?tools=pwds">Работа с паролями</a>
                </li>
                <li><a href="#">Раздел 2</a></li>
                <li class="divider"></li>
                <li><a href="#">Раздел 0</a></li>
            </ul>
        </li>
    </ul>
</li>
<?php
// ::: АДМИНКА ::: END
// ----- ----- ----- ----- ----- ----- ----- ----- ----- -----
?>