<?php
# Считываем все данные по записи в Почте
if (isset($_GET['uid'])) {
  $_reqDB_docmail = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM " . __MAIL_INCOMING_TABLENAME . " WHERE koddocmail='{$_GET['uid']}'"));
}
if (isset($_GET['reqfrom']) && $_GET['reqfrom'] == "mailinc" && isset($_reqDB_docmail)) {
  $noerrors = true;
} else {
  $noerrors = false;
}
?>
<script type="text/javascript" language="javascript" class="">
  //
  var userID_session = '<?php echo $_SESSION['id']; ?>';
  var userLogin_session = '<?php echo $_SESSION['login']; ?>';


  function toggleTextOnClick(objElement) {
    $(objElement).toggle(function() {
      $(objElement).html('(кликните, чтобы скрыть текст)');
    }, function() {
      $(objElement).html('(кликните, чтобы посмотреть текст)');
    });
  }

  function showHideElement(objElement) {
    $(objElement).toggle(function() {
      $(objElement).css({
        display: "block"
      });
    }, function() {
      $(objElement).css({
        display: "none"
      });
    });
  }

  function close_window() {
    close();
  }
  // --- --- --- --- --- --- --- --- --- --- 
  // ФУНКЦИИЯ ВЫВОДА ДАННЫХ ПО ТЕКУЩЕМУ ДОКУМЕНТУ
  //
  var reqField_profilingDocMain = {
    profilingDocMain: function(response) {
      console.log('profilingDocMain output', response);
      if (response !== "" && typeof response !== "undefined" && response !== null) {
        $('#tab1-docMain-output-mainFile').html('');
        $('#tab1-docMain-output-addFiles').html('');
        x = response.split('///-///');
        mainfiles = x[0];
        addfiles = x[1];
        x0 = mainfiles.split('|||');
        x1 = addfiles.split('|||');
        //
        if (x0 !== "" && typeof x0 !== "undefined" && x0 !== null) {
          var res_attached = "";
          for (i = 0; i < x0.length; i++) {
            res_attached += x0[i];
            console.log('profilingDocMain response ' + i + ':', x0[i]);
          }
          if (res_attached !== "" && typeof res_attached !== "undefined" && res_attached !== null) {
            $('#tab1-docMain-output-mainFile').html(res_attached);
          }
        } else {
          $('#tab1-docMain-output-mainFile').html(
            '<span style="color:red">Нет основного файла</span>');
        }
        //
        if (x1 !== "" && typeof x1 !== "undefined" && x1 !== null) {
          var res_attached = "";
          for (i = 0; i < x1.length; i++) {
            res_attached += x1[i];
            console.log('profilingDocMain response ' + i + ':', x1[i]);
          }
          if (res_attached !== "" && typeof res_attached !== "undefined" && res_attached !== null) {
            $('#tab1-docMain-output-addFiles').html(res_attached);
          }
        } else {
          $('#tab1-docMain-output-addFiles').html(
            '<span style="color:red">Нет дополнительных прикрепленных файлов</span>');
        }
      }
    }
  };

  function ajaxRequest_profilingDocMain(koddocmail, responseHandler) {
    request_profilingDocMain = $.ajax({
      type: "post",
      url: '<?php echo __ROOT . __SERVICENAME_MAILNEW . __MAIL_INCOMING_PROFILE_WORKPATH . __MAIL_RESTR5; ?>/process/ajaxrequests/ajaxReq-profilingDoc-docMain-files.php',
      cache: false,
      data: {
        koddocmail: koddocmail
      },
      success: reqField_profilingDocMain[responseHandler]
    });
    // Callback handler that will be called on success
    request_profilingDocMain.done(function(response, textStatus, jqXHR) {
      res = response.replace(new RegExp("\\r?\\n", "g"), "");
      // console.log('request_profilingDocMain:', 'success', response)
    });
  }
  //
  //

  // --- --- --- --- --- --- --- --- --- --- 
</script>
<?php

?>
<style>
  #profile-title,
  #profile-top,
  #profile-main {
    font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif;
  }

  #dinner-block1,
  #dinner-block2 {
    opacity: 0;
    animation: dinner-block 5.0s forwards
  }

  @keyframes dinner-block {
    0% {
      opacity: 0
    }

    100% {
      opacity: 1
    }
  }

  #profile-main h4.YC_h4-title {
    font-family: 'Stolzl Book', sans-serif;
    color: #999;
    text-transform: none;
    font-size: 1.3rem;
    text-decoration: underline
  }

  #profile-main input {
    color: #AF0B0B
  }

  #profile-main div.card-body,
  #profile-main div.card-body p {
    font-size: 14px
  }

  #profile-main a.card-link {
    color: #0d6efd
  }

  #profile-main a.card-link:hover {
    color: #0a58ca
  }

  #profile-main label {
    font-family: "Stolzl", Arial, Helvetica Neue, Helvetica, sans-serif;
    font-weight: 400;
    color: #333;
    padding-top: 1px
  }

  #profile-main h3.card-title {
    font-size: 24px;
    font-family: "Stolzl Bold", Arial, Helvetica Neue, Helvetica, sans-serif;
    margin-bottom: 2.0rem
  }

  #profile-main h4.card-title {
    font-size: 20px;
    font-family: "Stolzl Book", Arial, Helvetica Neue, Helvetica, sans-serif
  }

  #profile-main h5.card-title {
    font-size: 15px;
    font-family: "Stolzl Regular", Arial, Helvetica Neue, Helvetica, sans-serif
  }

  #profile-main .nav .nav-link {
    color: #999999
  }

  #profile-main .nav .nav-link.active,
  #profile-main .nav .nav-link:hover {
    color: #000000
  }


  #tab1 div.docMain.filesList a {
    color: #0a58ca;
    font-size: 1.2em;
  }
</style>
<?php

?>

<div id="profile-title" class="jumbotron jumbotron-fluid mt-5 pt-2 text-start">
  <div class="container">
    <h1 class="display-3 mb-3 text-center text-body">Профиль документа</h1>
    <h3 class="display-6 mb-1 text-center text-dark">
      <?php echo $noerrors ? "Входящая почта № 1-2/" . $_reqDB_docmail['inbox_docIDSTR'] : ""; ?></h3>
    <p class="lead text-center text-muted">Все в БД Портала, что как-либо связано с этим документом</p>
  </div>
</div>

<?php
// if ( ($_GET['userid']==$_SESSION['id']) || (checkIsItSuperadmin_defaultDB($_SESSION['id'])==1) ) {
if ($noerrors) {
?>

  <div id="profile-main" class="container mt-5">
    <div id="profile-whatabout" class="shadow p-4 mb-4 bg-white text-dark">

      <p class="lead mb-1 text-danger text-decoration-none"><a href="#profile-whatisit" data-bs-toggle="collapse" class="text-info"><b>Что
            это за профиль такой?</b></a></p>
      <!-- <p class="click-and-read"><small>(кликните, чтобы посмотреть текст)</small></p> -->
      <div id="profile-whatisit" class="mt-3 collapse">
        <p style="text-align:justify">
          Замысел прост - сконцентрировать все исходящие связи по принципу "звезды" (установленные как
          автоматически так и вручную
          пользователем) от исходного документа к записям, документам и файлам в других
          разделах и сервисах, разбитые по соответствующим вкладкам.
        </p>
        <p style="text-align:justify">
          По текущему документу, например, это краткое общее саммари плюс линки на прикрепленные файлы. Во вкладке
          "Связи в почте" это будут краткие саммари по каждой из связанных записей (во входящих и исходящих) с
          линком
          на аналогичный профиль каждой из записей, а также все линки на прикрепленные файлы всех связанных
          записей.<br>
          Создавая связь с договорами, мы прокидываем мостик в данные по конкретному связанному договору - к общей
          информации по нему, к финансовой (обсуждаемо, надо или нет и в зависимости от прав доступа), к
          справочным данным, к прикрепленным файлам и так далее. И все это
          сразу в одном месте. Также можно будет перейти в аналогичный
          профиль договора и посмотреть все связи уже из него.
        </p>
        <p style="text-align:justify">
          Таким образом создавая такие цепочки мы из одного профиля видим сразу все возможные основные данные и
          прикрепленные файлы всех связанных записей, что позволяет не скакать из записи в запись и из сервиса в
          сервис по ходу теряя
          цепочку зависимостей и время. Это первый шаг в реализации подхода взаимной интеграции всех
          сервисов Портала между собой.
        </p>
        <p style="text-align:justify">
          В настоящий момент Профиль реализуется только в сервисе Почта, но в
          перспективе подобный профиль можно будет создавать и смотреть в сервисе Договор.
        </p>
        <p class="text-danger text-small" style="text-align:justify">
          *Профиль находится в стадии разработки, осмысления общей концепции и подходов, поэтому работает пока в
          тестовом режиме.
          Предложения и идеи примаются.
        </p>
      </div>
    </div>

    <ul class="nav nav-tabs mb-5" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="true">Текущий документ</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="tab2" aria-selected="false">Связи в почте</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab" aria-controls="tab3" aria-selected="false">Связи в договорах</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab4-tab" data-bs-toggle="tab" data-bs-target="#tab4" type="button" role="tab" aria-controls="tab4" aria-selected="false">Связи в справочниках</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab5-tab" data-bs-toggle="tab" data-bs-target="#tab5" type="button" role="tab" сщььщтШтащ aria-controls="tab5" aria-selected="false">Прочие связи</button>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent" style="padding-top: 0.1rem">
      <div class="tab-pane mt-4 fade show active" id="tab1" role="tabpanel" aria-labelledby="profile-tab">
        <div class="row">
          <div class="col-md-12">

          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="d-flex flex-column mb-5">
              <div class="docMain commonInfo" style="flex-basis:100%">
                <h3 class="mb-3">Общая информация</h3>
                <div id="tab1-docMain-output-commonInfo"></div>
              </div>
            </div>
            <div class="d-flex flex-row mb-5">
              <div class="docMain filesList" style="flex-basis:50%">
                <h3 class="mb-3">Основной файл</h3>
                <div id="tab1-docMain-output-mainFile"></div>
              </div>
              <div class="docMain filesList" style="flex-basis:50%">
                <h3 class="mb-3">Дополнительные файлы</h3>
                <div id="tab1-docMain-output-addFiles"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane mt-4 fade" id="tab2" role="tabpanel" aria-labelledby="access-tab">
        <div class="row">
          <div class="col-md-12">
            <div class="shadow-lg p-3 mb-5 bg-white text-center rounded">
              Если были установлены связи с другими документами во входящей или исходящей почте, то тут будет
              выведена информация по этим документам, включая ссылки на прикрепленные файлы к этим
              документам.<br>В работе...
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane mt-4 fade" id="tab3" role="tabpanel" aria-labelledby="notifications-tab">
        <div class="row">
          <div class="col-md-12">
            <div class="shadow-lg p-3 mb-5 bg-white text-center rounded">
              Если были установлены связи с договорами, то тут будет выведена информация по этиим договорам,
              ссылка на карточку этого договора, а также ссылки на прикрепленные файлы к этим договорам.<br>В
              работе...
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane mt-4 fade" id="tab4" role="tabpanel" aria-labelledby="notifications-tab">
        <div class="row">
          <div class="col-md-12">
            <div class="shadow-lg p-3 mb-5 bg-white text-center rounded">
              Вся доступная иноформация из справочников Портала о контрагентах текущего документа, а также
              контрагентах связанных документов и договоров.<br>В работе...
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane mt-4 fade" id="tab5" role="tabpanel" aria-labelledby="notifications-tab">
        <div class="row">
          <div class="col-md-12">
            <div class="shadow-lg p-3 mb-5 bg-white text-center rounded">
              Что-то еще, что не попадает в предыдущие вкладки...
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="my-2">&nbsp;</div>

  </div>
<?php
} else {
?>
  <div class="container mt-5">
    <div class="row">
      <div class="col-sm-12">
        <p class="text-danger text-center">Произошла какая-то ошибка. Доступ к данным невозможен.
        </p>
      </div>
    </div>
  </div>
<?php
}
?>
<div class="container mt-5">
  <div class="col-sm-12">
    <div class="text-center profiling-link"><a href="javascript:close_window();">Закрыть вкладку</a></div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom:none">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ок</button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript" language="javascript" class="init">
  $(window).load(function() {

    var koddocmail = <?php echo $_GET['uid']; ?>;
    ajaxRequest_profilingDocMain(koddocmail, 'profilingDocMain');
    console.log('koddocmail', koddocmail);


  });
</script>