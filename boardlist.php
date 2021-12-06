<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/inc/bootstrap.php';
	if(!CUSer::is_admin()) {
		die('403 - Access denied');
	}
	require_once $_SERVER['DOCUMENT_ROOT'].'/inc/header.php';
	
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
                                                    if(CCache::checkCache($cacheId)) {
                                                        $countDevices = CCache::getCache($cacheId);
                                                    } else {
                                                        $countDevices = CIoT::getCountDevices();
                                                        CCache::writeCache($cacheId, $countDevices);
                                                    }

													CPagination::execute($_REQUEST['page'], $countDevices, PAGINATION_LIMIT);
													$limit = CPagination::getLimit();
												?>
	                                            <?php
                                                    $cacheId = md5('CIoT::getDevices_'.$limit.'_ASC');
                                                    if(CCache::checkCache($cacheId)) {
                                                        $arrDevices = CCache::getCache($cacheId);
                                                    } else {
                                                        $arrDevices = CIoT::getDevices($limit, 'ASC');
                                                        CCache::writeCache($cacheId, $arrDevices);
                                                    }
                                                    
                                                    foreach($arrDevices as $device):
                                                    $arrUserFields = CUser::getFields($device['user']);
                                                ?>
                                                <tr>
                                                    <td class="text-center"><a href="boardinfo.php?id=<?php echo $device['id']; ?>"><?php echo $device['id']; ?></a></td>
                                                    <td><a href="boardinfo.php?id=<?php echo $device['id']; ?>"><?php echo $device['mac']; ?></a></td>
                                                    <td><a href="boardinfo.php?id=<?php echo $device['id']; ?>"><?php echo $device['chipid']; ?></a></td>
                                                    <td><?php echo $device['hw']; ?></td>
                                                    <td><?php echo $device['fw']; ?></td>
                                                    <td><?php echo $arrUserFields['name']; ?> (<?php echo $arrUserFields['login'];?>, ID: <?php echo $device['user'];?>)</td>
                                                    <td><?php echo date("d.m.Y H:i:s", $device['time']); ?></td>
                                                    <td><?php echo date("d.m.Y H:i:s", $device['last_active']); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
	                                    </div>
                                        <?php CPagination::show('page'); ?>
                                    </div>
                                    <!-- .card-block -->
                                </div>
                                <!-- .card -->
                                <!-- End Header BG Table -->
                            </div>
</div>

<?php	
	require_once $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';
?>