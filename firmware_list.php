<?php
	require_once __DIR__.'/inc/bootstrap.php';
	require_once __DIR__.'/inc/header.php';
	$arFirmwareList = IoT::getFirmwareList();
?>

<div class="row maxheight">
	<div class="col-lg-12">
                                <!-- Header BG Table -->
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Версии прошивок</h4>
                                        <div class="card-actions">
                                            <code>FIRMWARE</code>
                                        </div>
                                    </div>
                                    <div class="card-block">
	                                    <div class="table-responsive">
                                        <table class="table table-striped table-borderless table-header-bg">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 50px;">ID</th>
                                                    <th style="width: 138px;">Дата выпуска</th>
                                                    <th>Название</th>
                                                    <th style="display:none;">Описание</th>
                                                    <th>Изменено</th>
                                                    <th>Ревизия</th>
                                                    <th>Версия</th>
                                                    <th>Скачать</th>
                                                </tr>
                                            </thead>
                                            <tbody>
	                                            <?php
													Pagination::execute($_REQUEST['page'], IoT::countFirmwareList(), PAGINATION_LIMIT);
													$limit = Pagination::getLimit();
												?>
	                                            <?php foreach(IoT::getFirmwareList($hw = false, $limit, 'DESC') as $arFirmware): ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $arFirmware['id']; ?></td>
                                                    <td><?php echo $arFirmware['created']; ?></td>
                                                    <td><?php echo $arFirmware['name']; ?></td>
                                                    <td style="display:none;"><?php echo $arFirmware['description']; ?></td>
                                                    <td><?php echo nl2br($arFirmware['changelog']); ?></td>
                                                    <td><code><?php echo $arFirmware['hw']; ?></code></td>
                                                    <td><code><?php $fw = str_split($arFirmware['version']); echo implode('.', $fw);?></code></td>
                                                    <td>
	                                                    <a target="_blank" class="btn btn-app-cyan" href="controller/update/firmware/binary/<?php echo $arFirmware['path']; ?>">BIN</a>
	                                                    <br><br>
	                                                    <a target="_blank" class="btn btn-app-cyan" href="controller/update/firmware/sources/<?php echo $arFirmware['spath']; ?>">SRC</a></td>
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