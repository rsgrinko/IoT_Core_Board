<?php
	require_once __DIR__.'/inc/bootstrap.php';
	if(!CUSer::isAdmin()) {
		die('403 - Access denied');
	}
	require_once __DIR__.'/inc/header.php';
	
?>

<div class="row maxheight">
	<div class="col-lg-12">
                                <!-- Header BG Table -->
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Пользователи</h4>
                                        <div class="card-actions">
                                            <code>CUsers</code>
                                        </div>
                                    </div>
                                    <div class="card-block">
	                                    <div class="table-responsive">
                                        <table class="table table-striped table-borderless table-header-bg">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 50px;">ID</th>
                                                    <th>Логин</th>
                                                    <th>Статус</th>
                                                    <th>Имя</th>
                                                    <th>E-Mail</th>
                                                    <th>Доступ</th>
                                                    <th>Изображение</th>
                                                    <th>Последняя активность</th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
	                                            <?php
													CPagination::execute($_REQUEST['page'], CUser::countUsers(), PAGINATION_LIMIT);
													$limit = CPagination::getLimit();	
												?>
	                                            <?php foreach(CUser::getUsers($limit, 'ASC') as $user): ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $user['id']; ?></td>
                                                    <td><?php echo $user['login']; ?></td>
                                                    <th><?php if(CUser::isOnline($user['id'])) { ?><span class="bg-green label">Online</span><?php } else {?><span class="bg-red label">Offline</span><?php } ?></th>
                                                    <td><?php echo $user['name']; ?></td>
                                                    <td><a href="mailto:<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a></td>
                                                    <td><?php echo $user['access_level']; ?></td>
                                                    <td style="text-align:center;"><a href="<?php echo $user['image']; ?>" target="_blank"><img style="max-width: 40px;" src="<?php echo $user['image']; ?>" alt="avatar"></a></td>
                                                    <td><?php echo date("d.m.Y H:i:s", $user['last_active']); ?></td>
                                                    <td><a href="editprofile.php?id=<?php echo $user['id']; ?>" class="btn btn-app-orange btn-block">Изменить</a></td>
                                                    <td><a href="login_as.php?id=<?php echo $user['id']; ?>" class="btn btn-app-blue btn-block">Войти</a></td>
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
	require_once __DIR__.'/inc/footer.php';
?>