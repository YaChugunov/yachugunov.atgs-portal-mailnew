<?php
	$__msg = "Недостаточно прав";
?>
<style>
#section-nopermission > div.row { font-family:'Oswald', sans-serif }
#section-nopermission > div > div > div.media > div.media-body > h1 {
	color:#111;
}
#section-nopermission > div > div > div.media > div.media-body > h3 {
/* 	font-family: 'Jura', sans-serif; */
/* 	font-size: 1.0em; */
/* 	font-weight: 200; */
	color:#950100;
	line-height: 1.2em;
	font-family:'Oswald', sans-serif;
	margin-bottom: 0.5em;
	font-size: 1.8em;
    font-weight: 300;
    letter-spacing: -0.05em;
}
#section-nopermission > div > div > div.media > div.media-body > div > h4 {
	color:#666;
    font-weight: 300;
	margin-bottom: 0.75em;
}
#section-nopermission > div > div > div.media > div.media-body > div > p {
	color:#111;
	font-family:'Oswald', sans-serif;
    font-weight: 500;
}
#section-nopermission > div > div > div.media > div.media-body > div > p > span > a {
	text-decoration:underline;
}
#section-nopermission > div > div > div.media > div.media-body > div > p > span > a:hover {
	text-decoration:none;
}
#section-nopermission > div.media > div.media-body > ul {
	font-family: 'Oswald', sans-serif;
    font-size: 1.2em;
    font-weight: 300;
    letter-spacing: normal;
	margin-top: 5px;
	margin-left: 10px;
}
.vh-content {
    min-height: calc(100vh - 185px);
}
</style>

<?php
$query1 = mysqlQuery( "SELECT COUNT(id) as cnt FROM service_access WHERE service_name!='allservices'" );
$query2 = mysqlQuery( "SELECT service_name, service_title, access, service_path FROM service_access" );
$row1 = mysqli_fetch_array($query1, MYSQLI_ASSOC);
$_cnt = $row1['cnt'];
?>

<div id="section-nopermission" class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
			<div class="space50"></div>
			<div class="media">
				<div class="media-body text-center">
					<h1 class="text-uppercase space10">Что-то пошло не так</h1>
					<h3 class="text-uppercase">У вас недостаточно прав для доступа к данному сервису</h3>
					<div style="background-color:#fafafa; padding:10px">
						<h4 class="text-uppercase">Вам доступны следующие сервисы</h4>
						<p>
							<?php
							if ($_cnt > 0) {
								if (($row2['service_name']!="allservices") && checkServiceAccess('allservices')==1) {
									while($row2 = mysqli_fetch_array($query2)) {
										if ($row2['service_name']!="allservices") {
											if ($row2['access']==1 && checkUserRestrictions($_SESSION['id'],$row2['service_name'],2,5)==1) {
												echo "<span style='color:#111; margin:0 10px'><a href='".$row2['service_path']."'>".$row2['service_title']."</a></span>";
											}
											else {
												echo "<span style='color:#e1e1e1; margin:0 10px'><s>".$row2['service_title']."</s></span>";
											}
										}
									}
								}
								else {
									echo "<span class='text-uppercase'>Все сервисы недоступны</span>";
								}
							}
							else {
								echo "<span class='text-uppercase'>Все сервисы недоступны</span>";
							}
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="space50"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	msg = '<?php echo $__msg; ?>';
	document.getElementById("subtitle").innerHTML = msg;
</script>
