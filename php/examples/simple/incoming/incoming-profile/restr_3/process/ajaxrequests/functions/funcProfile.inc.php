<?php
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
# +++++ Функция
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 

# ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
# ##### ##### ##### ##### ##### ##### ##### ##### ##### ##### 
#
#	Сумма всех счетов-фактур по договору субподряда на дату/без даты
# >>> $koddocsub		- Идентификатор договора субподряда
# >>> $ondate				- Дата отсечки сплитов по дате (для справки о задолженности на дату). Если дата отсутствует (null), то суммируются ВСЕ сплиты
#
function F_PROFILE_MAIN_SUM_STAGE_CHF($kodstage, $ondate) {
  $result = "";
  if (!empty($kodstage) && empty($ondate)) {
    // Считаем сумму счетов-фактур без учета даты ( $ondate = NULL )
    $_QRY = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(chetfsumma) as sum FROM dognet_kalplanchf WHERE koddel<>'99' AND kodkalplan='{$kodstage}'"));
    $result = $_QRY['sum'];
  } elseif (!empty($koddocsub) && !empty($ondate) && ((DateTime::createFromFormat('Y-m-d', $ondate) !== false))) {
    // Считаем сумму сплитов по авансу с учетом даты ( $ondate )
    $_QRY = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(chetfsumma) as sum FROM dognet_kalplanchf WHERE koddel<>'99' AND kodkalplan='{$kodstage}' AND chetfdate<='{$ondate}'"));
    $result = $_QRY['sum'];
  } else {
    $result = 0;
  }
  return $result;
}




#
# ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- 
