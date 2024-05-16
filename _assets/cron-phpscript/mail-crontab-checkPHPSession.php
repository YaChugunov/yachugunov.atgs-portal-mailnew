#!/usr/bin/php
<?php
if (session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
?>
    <script type="text/javascript" language="javascript" class="">
        console.log("CRONTAB: session is none");
    </script>
<?php
    echo "CRONTAB: session is none";
} else {
?>
    <script type="text/javascript" language="javascript" class="">
        console.log("CRONTAB: session exist");
    </script>
<?php
    echo "CRONTAB: session exist";
}
?>