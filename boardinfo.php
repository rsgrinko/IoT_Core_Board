<?php
	require_once __DIR__ . '/inc/bootstrap.php';
	
	if(!isset($_REQUEST['id']) or $_REQUEST['id'] == '') {
		header("Location: index.php?"); die();
	}
	$deviceId = prepareString($_REQUEST['id']);
	if(!isHaveAccessToDevice($deviceId, $USER['id']) and !CUser::is_admin()) {
		die('403 - Access denied');
	}
	
	require_once __DIR__ . '/inc/header.php';

    $cacheId = md5('CIoT::getDevice_'.$deviceId);
    if(CCache::check($cacheId)) {
        $arDevice = CCache::get($cacheId);
    } else {
        $arDevice = CIoT::getDevice($deviceId);
        CCache::write($cacheId, $arDevice);
    }

    $cacheId = md5('CUser::getFields_'.$arDevice['user']);
    if(CCache::check($cacheId)) {
        $arDeviceUser = CCache::get($cacheId);
    } else {
        $arDeviceUser = CUser::getFields($arDevice['user']);
        CCache::write($cacheId, $arDeviceUser);
    }

?>	
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<h4>Информация об устройстве</h4>
				
			</div>
			<div class="card-block">
				<form class="form-horizontal" onsubmit="return false;">
					
					<div class="form-group">
						<label class="col-xs-12 custom_bold">ID устройства</label>
						<div class="col-sm-9">
							<div class="form-control-static"><?php echo $arDevice['id'];?></div>
						</div>
					</div>

                    <div class="form-group">
                        <label class="col-xs-12 custom_bold">Статус</label>
                        <div class="col-sm-9">
                            <div class="form-control-static">
                                <?php if(CIoT::isOnline($arDevice['id'])) { ?>
                                    <span class="bg-green label">Online</span>
                                <?php } else {?>
                                    <span class="bg-red label">Offline</span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
					
					<div class="form-group">
						<label class="col-xs-12 custom_bold">Владелец</label>
						<div class="col-sm-9">
							<div class="form-control-static"><?php echo $arDeviceUser['name'];?> (<?php echo $arDeviceUser['login'];?>, ID: <?php echo $arDeviceUser['id'];?>)</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 custom_bold">MAC адрес</label>
						<div class="col-sm-9">
							<div class="form-control-static"><code><?php echo $arDevice['mac'];?></code></div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 custom_bold">ID чипа устройства</label>
						<div class="col-sm-9">
							<div class="form-control-static"><code><?php echo $arDevice['chipid'];?></code></div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 custom_bold">Аппаратная версия</label>
						<div class="col-sm-9">
							<div class="form-control-static"><code><?php echo $arDevice['hw'];?></code></div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 custom_bold">Версия прошивки</label>
						<div class="col-sm-9">
							<div class="form-control-static"><code><?php $fw = str_split($arDevice['fw']); echo implode('.', $fw);?></code></div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 custom_bold">Дата регистрации в системе</label>
						<div class="col-sm-9">
							<div class="form-control-static"><code><?php echo date("d.m.Y H:i:s", $arDevice['time']);?></code></div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 custom_bold">Последняя активность</label>
						<div class="col-sm-9">
							<div class="form-control-static"><code><?php echo date("d.m.Y H:i:s", $arDevice['last_active']);?></code></div>
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