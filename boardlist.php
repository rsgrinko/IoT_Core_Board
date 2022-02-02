<?php
	require_once __DIR__.'/inc/bootstrap.php';
	if(!User::isAdmin()) {
		die('403 - Access denied');
	}
	require_once __DIR__.'/inc/header.php';
	
?>

<div class="row maxheight">
	<div class="col-lg-12">
                                <!-- Header BG Table -->
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Список устройств</h4>
                                        <div class="card-actions">
                                            <code>CIoT</code>
                                        </div>
                                    </div>
                                    <div class="card-block">
	                                    <div class="table-responsive">
                                        <table class="table table-striped table-borderless table-header-bg">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 50px;">ID</th>
                                                    <th>Статус</th>
                                                    <th>MAC</th>
                                                    <th>ID чипа</th>
                                                    <th>HW</th>
                                                    <th>FW</th>
                                                    <th>Пользователь</th>
                                                    <th>Дата регистрации</th>
                                                    <th>Последняя активность</th>
                                                </tr>
                                            </thead>
                                            <tbody>
	                                            <?php
                                                    $cacheId = md5('CIoT::getCountDevices');
                                                    if(Cache::check($cacheId)) {
                                                        $countDevices = Cache::get($cacheId);
                                                    } else {
                                                        $countDevices = IoT::getCountDevices();
                                                        Cache::write($cacheId, $countDevices);
                                                    }

													Pagination::execute($_REQUEST['page'], $countDevices, PAGINATION_LIMIT);
													$limit = Pagination::getLimit();
												?>
	                                            <?php
                                                    $cacheId = md5('CIoT::getDevices_'.$limit.'_ASC');
                                                    if(Cache::check($cacheId)) {
                                                        $arrDevices = Cache::get($cacheId);
                                                    } else {
                                                        $arrDevices = IoT::getDevices($limit, 'ASC');
                                                        Cache::write($cacheId, $arrDevices);
                                                    }
                                                    
                                                    foreach($arrDevices as $device):
                                                    $arrUserFields = User::getFields($device['user']);

                                                    $cacheId = md5('CIoT::isOnline_'.$device['id']);
                                                    if(Cache::check($cacheId) and Cache::getAge($cacheId) < DEVICE_ONLINE_TIME) {
                                                        $isDeviceOnline = Cache::get($cacheId);
                                                    } else {
                                                        $isDeviceOnline = IoT::isOnline($device['id']);
                                                        Cache::write($cacheId, $isDeviceOnline);
                                                    }
                                                ?>
                                                <tr>
                                                    <td class="text-center"><a href="boardinfo.php?id=<?php echo $device['id']; ?>"><?php echo $device['id']; ?></a></td>
                                                    <td><?php if($isDeviceOnline) { ?><span class="bg-green label">Online</span><?php } else {?><span class="bg-red label">Offline</span><?php } ?></td>
                                                    <td><a href="boardinfo.php?id=<?php echo $device['id']; ?>"><?php echo $device['mac']; ?></a></td>
                                                    <td><a href="boardinfo.php?id=<?php echo $device['id']; ?>"><?php echo $device['chipid']; ?></a></td>
                                                    <td><?php echo $device['hw']; ?></td>
                                                    <td><?php echo IoT::parseFW($device['fw']); ?></td>
                                                    <td><a href="editprofile.php?id=<?php echo $device['user'];?>"><?php echo $arrUserFields['name']; ?> (<?php echo $arrUserFields['login'];?>, ID: <?php echo $device['user'];?>)</td></td>
                                                    <td><?php echo date("d.m.Y H:i:s", $device['time']); ?></td>
                                                    <td><?php echo date("d.m.Y H:i:s", $device['last_active']); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
	                                    </div>
                                        <?php Pagination::show('page'); ?>
                                    </div>
                                    <!-- .card-block -->
                                </div>
                                <!-- .card -->
                                <!-- End Header BG Table -->
                            </div>
</div>

<?php	
	require_once __DIR__.'/inc/footer.php';
?>