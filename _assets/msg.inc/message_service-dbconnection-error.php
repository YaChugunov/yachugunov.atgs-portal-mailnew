<?php
$__title = "Еда 2.0 Портал";
$__subtitle = "Заказ обедов";
$__subsubtitle = "";
?>
<style>
    #section-nopermission>div.row {
        font-family: 'Oswald', sans-serif
    }

    #section-nopermission>div>div>div.media>div.media-body>h1 {
        color: #111;
    }

    #section-nopermission>div>div>div.media>div.media-body>h3 {
        /* 	font-family: 'Jura', sans-serif; */
        /* 	font-size: 1.0em; */
        /* 	font-weight: 200; */
        color: #950100;
        line-height: 1.2em;
        font-family: 'Oswald', sans-serif;
        margin-bottom: 0.5em;
        font-size: 1.8em;
        font-weight: 300;
        letter-spacing: 0.05em;
    }

    #section-nopermission>div>div>div.media>div.media-body>div>h4 {
        color: #666;
        font-weight: 300;
        margin-bottom: 0.75em;
    }

    #section-nopermission>div>div>div.media>div.media-body>div>p {
        color: #111;
        font-family: 'Oswald', sans-serif;
        font-weight: 500;
    }

    #section-nopermission>div>div>div.media>div.media-body>div>p>span>a {
        text-decoration: underline;
    }

    #section-nopermission>div>div>div.media>div.media-body>div>p>span>a:hover {
        text-decoration: none;
    }

    #section-nopermission>div.media>div.media-body>ul {
        font-family: 'Oswald', sans-serif;
        font-size: 1.2em;
        font-weight: 300;
        letter-spacing: normal;
        margin-top: 5px;
        margin-left: 10px;
    }

    .vh-content {
        min-height: calc(100vh - 225px);
    }
</style>

<?php
$query1 = mysqlQuery("SELECT COUNT(id) as cnt FROM service_access WHERE service_name!='allservices'");
$query2 = mysqlQuery("SELECT service_name, service_title, access, service_path FROM service_access");
$row1 = mysqli_fetch_array($query1, MYSQLI_ASSOC);
$_cnt = $row1['cnt'];
?>

<div id="section-nopermission" class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
            <div class="space50"></div>
            <div class="media">
                <div class="media-body text-center">
                    <h1 class="text-uppercase space10">Нет доступа к базе данных сервиса dinner.atgs.ru</h1>
                    <h3 class="text-uppercase" style="margin-bottom:30px">Заказы в текущий момент невозможны</h3>
                    <img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/_assets/images/avatars/admin-emo/admin-emo_oops.png" class="img-circle space20" style="max-width:none; width:10.0em">

                </div>
            </div>
            <div class="space50"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    document.getElementById("title").innerHTML = '<?php echo $__title; ?>';
    subtitle = '<?php echo $__subtitle; ?>';
    document.getElementById("subtitle").innerHTML = subtitle;
</script>