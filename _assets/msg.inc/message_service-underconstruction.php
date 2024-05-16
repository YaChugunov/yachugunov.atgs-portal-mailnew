<!DOCTYPE html>
<html lang="ru">
<head>

	<meta http-equiv="content-type" content="text/html; charset=UTF8"> 
	<title>АТГС.Договор</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">

<?php 	
	include('_assets/dognet_style-css-includes.header'); 
	include('_assets/dognet_script-js-includes.header');
	include('_assets/dognet_datatables-includes.header');
?> 

</head>
<body>

<div class="body" style="padding-top: 70px;">

<style>
#section-undercon > div.row { font-family:'Oswald', sans-serif }
#section-undercon > div > div > div.media > div.media-body > p {
/* 	font-family: 'Jura', sans-serif; */
/* 	font-size: 1.0em; */
/* 	font-weight: 200; */
	line-height: 1.2em;
	font-family:'Oswald', sans-serif;
	margin-bottom: 1.0em;
	font-size: 1.8em;
    font-weight: 300;
    letter-spacing: normal;
}
#section-undercon > div.media > div.media-body > ul {
	font-family: 'Oswald', sans-serif;
    font-size: 1.2em;
    font-weight: 300;
    letter-spacing: normal;	
	margin-top: 5px;
	margin-left: 10px;
}
</style>

<div id="section-undercon" class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">

			<div class="media">
				<div class="media-body text-center">
					<h1 class="space30">АТГС.Договор</h1>
					<img src="../_assets/images/avatars/admin-emo/admin-emo_attention.png" class="img-circle space20" style="max-width:none; width:10.0em">
					<div class="progress">
						<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:30%">
					    30%
						</div>
					</div>
					<p style="color:#d62231">По оптимистичным прогнозам запуск сервиса запланирован на ноябрь/декабрь 2018 года.</p> 
				</div>
			</div>

		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 text-center" style="">
			<span>Через <span id="time"></span> сек вы будете перенаправлены на главную страницу...</span>
		</div>
	</div>
</div>

<script type="text/javascript">
var i = 10;//время в сек.
function time(){
	document.getElementById("time").innerHTML = i;
	i--;
	if (i < 0) window.location.href="../../../index.php";
}
time();
setInterval(time, 1000);
</script>

</div>
</body>
</html>
