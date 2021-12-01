<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/inc/bootstrap.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/inc/header.php';
	
	if(isset($_REQUEST['save']) and $_REQUEST['save'] == 'Y'):
		$DB->update('configs', array('device' => $USER['deviceId']), array('ds_resolution' => $_REQUEST['ds_resolution']));
		CEvents::add('Произведено изменение конфигурации контроллера ID: '.$USER['deviceId'], 'notice', 'core');
		
		echo '<script>alert("Настройки успешно сохранены");</script>';
	
	endif;
	
	$boardConfig = CIoT::getBoardConfig($USER['deviceId']);
?>

<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h4>Конфигурация контроллера</h4>
				<div class="card-actions">
					<code>CBoard</code>
				</div>
			</div>
			<div class="card-block">
				<form class="form-horizontal m-t-sm" action="" method="post">
				<input type="hidden" name="save" value="Y">
					<div class="form-group">
						<div class="col-sm-9">
							<div class="form-material">
								<label for="resolution-select">Разрешение измерения температуры DS18B20 </label>
								<select class="form-control" id="resolution-select" name="ds_resolution">
									<option value="9" <?php if($boardConfig['ds_resolution'] == '9') { echo 'selected'; } ?>>9 бит</option>
									<option value="10" <?php if($boardConfig['ds_resolution'] == '10') { echo 'selected'; } ?>>10 бит</option>
									<option value="11" <?php if($boardConfig['ds_resolution'] == '11') { echo 'selected'; } ?>>11 бит</option>
									<option value="12" <?php if($boardConfig['ds_resolution'] == '12') { echo 'selected'; } ?>>12 бит</option>
								</select>
								
							</div>
						</div>
					</div>
					<div class="form-group m-b-0">
					<div class="col-sm-9">
					<button class="btn btn-app" type="submit">Сохранить</button>
					</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php	
	require_once $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';
?>