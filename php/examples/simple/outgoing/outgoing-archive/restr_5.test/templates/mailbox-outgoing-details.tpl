'<div id="detailsBlock-'+row.id()+'" data-rowID="'+row.id()+'" class="detailsBlock shadow mx-3 my-2 rounded-lg">'+

	'<ul class="nav nav-tabs mb-3" id="tab-tabs" role="tablist">'+
		'<li class="nav-item" role="presentation">'+
			'<a class="nav-link text-dark active" id="'+row.id()+'-navitem-1" data-toggle="tab" data-target="#'+row.id()+'-tab-1" type="" role="tab" aria-controls="'+row.id()+'-tab-1" aria-selected="true">Общее</a>'+
		'</li>'+
		'<li class="nav-item" role="presentation">'+
			'<a class="nav-link text-dark" id="'+row.id()+'-navitem-2" data-toggle="tab" data-target="#'+row.id()+'-tab-2" type="" role="tab" aria-controls="'+row.id()+'-tab-2" aria-selected="false">Контрагент</a>'+
		'</li>'+
		'<li class="nav-item" role="presentation">'+
			'<a class="nav-link text-dark" id="'+row.id()+'-navitem-3" data-toggle="tab" data-target="#'+row.id()+'-tab-3" type="" role="tab" aria-controls="'+row.id()+'-tab-3" aria-selected="false">Контроль исполнения</a>'+
		'</li>'+
		'<li class="nav-item" role="presentation">'+
			'<a class="nav-link text-dark" id="'+row.id()+'-navitem-4" data-toggle="tab" data-target="#'+row.id()+'-tab-4" type="" role="tab" aria-controls="'+row.id()+'-tab-4" aria-selected="false">Прикрепленные файлы</a>'+
		'</li>'+
	'</ul>'+
	'<div class="tab-content" id="tabs-tabContent-'+row.id()+'">'+
					
		'<div class="media position-relative mx-3 mb-3 p-2 align-items-center profile-header">'+
			'<img src="http://qrcoder.ru/code/?http%3A%2F%2F192.168.1.89%2Fmailnew%2Findex.php%3Ftype%3Dout%26mode%3Dprofile%26uid%3D'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail+'&4&0" class="mr-3" width="100px" alt="...">'+
			'<div class="media-body text-left">'+
				'<h3 class="title mt-0 mb-0">Письмо №&nbsp;1-1/'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docID+'</h3>'+
				'<h4 class="subtitle mt-0 mb-1">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docAbout+'</h4>'+
				'<a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/mailnew/index.php?type=out&mode=profile&uid='+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail+'" alt="Профиль по документу UID '+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail+'" class="stretched-link text-dark">Перейти в Профиль документа</a>'+
			'</div>'+
		'</div>'+

		'<div class="tab-pane text-secondary fade show active" id="'+row.id()+'-tab-1" role="tabpanel" aria-labelledby="'+row.id()+'-tab-1">'+
			'<div class="section d-flex flex-row w-100">'+
				'<div class="block d-flex flex-column w-50">'+
					'<div class="title text-left text-dark mb-3">Название</div>'+
					'<dl class="row mx-2 text-left">'+
						'<dt class="col-sm-8">Внутренний KOD документа</dt>'+
						'<dd class="col-sm-4">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.koddocmail+'</dd>'+
						'<dt class="col-sm-8">Внутренний ID записи в БД</dt>'+
						'<dd class="col-sm-4">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.ID+'</dd>'+
					'</dl>'+
				'</div>'+
				'<div class="block d-flex flex-column w-50">'+
					'<div class="title text-left text-dark mb-3">Получатель (адресант) письма</div>'+
					'<dl class="row mx-2 text-left">'+
						'<dt class="col-sm-8">Внутренний KOD отправителя</dt>'+
						'<dd class="col-sm-4">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSender_kodzayvtel+'</dd>'+
						'<dt class="col-sm-8">Отправитель документа</dt>'+
						'<dd class="col-sm-4">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSenderSTR+'</dd>'+
					'</dl>'+
				'</div>'+
			'</div>'+
			'<div class="section d-flex flex-column w-100">'+
				'<div class="block d-flex flex-column">'+
					'<div class="title text-left text-dark mb-3">Действия с документом</div>'+

					'<div class="table-responsive-sd mx-3 px-2">'+
						'<table class="table table-borderless table-striped">'+
							'<thead class="thead-dark">'+
								'<tr>'+
									'<th class="text-left" scope="col">Действие</th>'+
									'<th class="text-left" scope="col">Пользователь</th>'+
									'<th class="text-left" scope="col">Временная метка</th>'+
								'</tr>'+
							'</thead>'+
							'<tbody>'+
								'<tr>'+
									'<td class="text-left">Добавлено в БД</td>'+
									'<td class="text-left">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docCreatedBySTR+'</td>'+
									'<td class="text-left">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docDate+'</td>'+
								'</tr>'+
								'<tr>'+
									'<td class="text-left">Обновлено в БД</td>'+
									'<td class="text-left">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docUpdatedBySTR+'</td>'+
									'<td class="text-left">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docUpdatedWhen+'</td>'+
								'</tr>'+
								'<tr>'+
									'<td class="text-left">Последнее уведомление на email</td>'+
									'<td class="text-left">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_emailSentBySTR+'</td>'+
									'<td class="text-left">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_emailSentWhen+'</td>'+
								'</tr>'+
							'</tbody>'+
						'</table>'+
					'</div>'+

				'</div>'+
			'</div>'+
		'</div>'+
		'<div class="tab-pane text-secondary fade" id="'+row.id()+'-tab-2" role="tabpanel" aria-labelledby="'+row.id()+'-tab-2">'+
			'<div class="section d-flex flex-column w-100">'+
				'<div class="block">'+
					'<div class="title text-left text-dark mb-3">Входящая информация</div>'+
					'<dl class="row mx-2 text-left">'+
						'<dt class="col-sm-8">Организация-получатель</dt>'+
						'<dd class="col-sm-4">'+d.recipientName+'</dd>'+
						'<dt class="col-sm-8">Внутренний KOD организации-получателя</dt>'+
						'<dd class="col-sm-4">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docRecipient_kodzakaz+'</dd>'+
						'<dt class="col-sm-8">Входящий номер</dt>'+
						'<dd class="col-sm-4">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSourceID+'</dd>'+
						'<dt class="col-sm-8">Входящая дата</dt>'+
						'<dd class="col-sm-4">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docSourceDate+'</dd>'+
						'<dt class="col-sm-8">Доп. информация</dt>'+
						'<dd class="col-sm-4">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docComment+'</dd>'+
					'</dl>'+
				'</div>'+
			'</div>'+
		'</div>'+
		'<div class="tab-pane text-secondary fade" id="'+row.id()+'-tab-3" role="tabpanel" aria-labelledby="'+row.id()+'-tab-3">'+
			'<div class="section d-flex flex-column w-100">'+
				'<div class="block">'+
					'<div class="title text-left text-dark mb-3">Исполнитель(ли)</div>'+
					'<dl class="row mx-2 text-left">'+
						'<dt class="col-sm-8">Внутренний KOD исполнителя</dt>'+
						'<dd class="col-sm-4">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docContractor_kodzayvispol+'</dd>'+
						'<dt class="col-sm-8">Ответственный(ые) по документу (email)</dt>'+
						'<dd class="col-sm-4">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docContractorSTR+' (<a href="mailto:'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docContractorEMAIL+'">'+d.<?php echo __MAIL_OUTGOING_TABLENAME; ?>.outbox_docContractorEMAIL+'</a>)</dd>'+
					'</dl>'+
				'</div>'+
			'</div>'+
			'<div class="section d-flex flex-column w-100">'+
				'<div class="block">'+
					'<div class="title text-left text-dark mb-3">Параметры контроля исполнения</div>'+
					'<div class="table-responsive-sd mx-3 px-2">'+
						'<table class="table table-borderless table-striped">'+
							'<thead class="thead-dark">'+
								'<tr>'+
									'<th class="text-left" scope="col">Параметр</th>'+
									'<th class="text-left" scope="col">Значение</th>'+
									'<th class="text-left" scope="col">Последнее изменение параметра</th>'+
									'<th class="text-left" scope="col">Последнее email-уведомление</th>'+
								'</tr>'+
							'</thead>'+
							'<tbody>'+
								'<tr>'+
									'<td class="text-left">Дедлайн по документу</td>'+
									'<td class="text-left">'+d.deadlineDate+'</td>'+
									'<td class="text-left"><span id="lastUpate-docDateDeadline"></span></td>'+
									'<td class="text-left"><span id="lastNotify-docDateDeadline"></span></td>'+
								'</tr>'+
								'<tr>'+
									'<td class="text-left">Режим контроля исполнения</td>'+
									'<td class="text-left">'+d.ispolActive+'</td>'+
									'<td class="text-left"><span id="lastUpate-ispolActive"></span></td>'+
									'<td class="text-left"><span id="lastNotify-ispolActive"></span></td>'+
								'</tr>'+
								'<tr>'+
									'<td class="text-left">Напоминание 1 (до дедлайна менее 3 суток)</td>'+
									'<td class="text-left">'+d.reminder1+'</td>'+
									'<td class="text-left"><span id="lastUpate-reminder1"></span></td>'+
									'<td class="text-left"><span id="lastNotify-reminder1"></span></td>'+
								'</tr>'+
								'<tr>'+
									'<td class="text-left">Напоминание 2 (до дедлайна менее суток)</td>'+
									'<td class="text-left">'+d.reminder2+'</td>'+
									'<td class="text-left"><span id="lastUpate-reminder2"></span></td>'+
									'<td class="text-left"><span id="lastNotify-reminder2"></span></td>'+
								'</tr>'+
								'<tr>'+
									'<td class="text-left">Отметка о полном исполнении документа</td>'+
									'<td class="text-left">'+d.ispolCheckout+'</td>'+
									'<td class="text-left">'+d.ispolCheckoutWhen+'</td>'+
									'<td class="text-left"><span id="lastNotify-ispolCheckout"></span></td>'+
								'</tr>'+
								'<tr>'+
									'<td class="text-left">Статус исполнения документа</td>'+
									'<td class="text-left" colspan="3">'+d.ispolStatus+'</td>'+
								'</tr>'+
							'</tbody>'+
						'</table>'+
					'</div>'+
				'</div>'+
			'</div>'+
		'</div>'+
		'<div class="tab-pane text-secondary fade" id="'+row.id()+'-tab-4" role="tabpanel" aria-labelledby="'+row.id()+'-tab-4">'+
			'<div class="section d-flex flex-column w-100">'+
				'<div class="block">'+
					'<div class="title text-left text-dark mb-3">Прикрепленные файлы к документу</div>'+
					'<dl class="row mx-2 text-left">'+
						'<dt class="col-sm-8">Основной прикрепленный файл</dt>'+
						'<dd class="col-sm-4">'+d.mainfile+'</dd>'+
					'</dl>'+
				'</div>'+
			'</div>'+
		'</div>'+
	'</div>'+

'</div>'
