<?php
	require_once __DIR__.'/inc/bootstrap.php';
	require_once __DIR__.'/inc/header.php';
	
	if(isset($_REQUEST['save']) and $_REQUEST['save'] == 'Y'):
		$DB->update('configs', ['device' => $USER['deviceId']], ['ds_resolution' => $_REQUEST['ds_resolution'], 'sending_interval' => $_REQUEST['sending_interval']]);
		Log::add('Произведено изменение конфигурации контроллера ID: '.$USER['deviceId'], 'notice', 'core');
		
		echo '<script>alert("Настройки успешно сохранены");</script>';
	
	endif;
	
	$boardConfig = IoT::getBoardConfig($USER['deviceId']);
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

                    <div class="form-group">
                        <div class="col-sm-9">
                            <div class="form-material">
                                <label for="sending_interval-select">Интервал обмена данными </label>
                                <select class="form-control" id="sending_interval-select" name="sending_interval">
                                    <option value="1000" <?php if($boardConfig['sending_interval'] == '1000') { echo 'selected'; } ?>>1 секунда</option>
                                    <option value="2000" <?php if($boardConfig['sending_interval'] == '2000') { echo 'selected'; } ?>>2 секунды</option>
                                    <option value="3000" <?php if($boardConfig['sending_interval'] == '3000') { echo 'selected'; } ?>>3 секунды</option>
                                    <option value="4000" <?php if($boardConfig['sending_interval'] == '4000') { echo 'selected'; } ?>>4 секунды</option>
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
	require_once __DIR__.'/inc/footer.php';
?>