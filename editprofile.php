<?php
/**
 * Страница редактирования профиля пользователя
 */
require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/header.php';

$userId = $USER['id'];


if (isset($_REQUEST['id']) and !empty($_REQUEST['id']) and User::isAdmin()) {
    $userId = $_REQUEST['id'];
}

$cacheId = md5('CUser::getFields_' . $userId);

if(isset($_REQUEST['save']) and $_REQUEST['save'] == 'Y'):
    $DB->update('users', ['id' => $userId], ['name' => $_REQUEST['name'], 'email' => $_REQUEST['email'], 'image' => $_REQUEST['image']]);
    Cache::del($cacheId);
    Events::add('Изменен профиль пользователя с ID: '.$userId.', инициатор ID: '.User::$id.' ('.$USER['login'].')', 'notice', 'user');
    echo '<script>alert("Профиль успешно отредактирован");</script>';
endif;


if (Cache::check($cacheId)) {
    $arrUser = Cache::get($cacheId);
} else {
    $arrUser = User::getFields($userId);
    Cache::write($cacheId, $arrUser);
}
//pre($arrUser);
?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Редактирование профиля</h4>
                    <div class="card-actions">
                        <code>CUser</code>
                    </div>
                </div>
                <div class="card-block">
                    <form class="form-horizontal m-t-sm" action="" method="post">

                        <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                        <input type="hidden" name="save" value="Y">

                        <div class="form-group">
                            <label class="col-xs-12">Логин:</label>
                            <div class="col-sm-9">
                                <div class="form-control-static"><?php echo $arrUser['login']; ?></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12">Права:</label>
                            <div class="col-sm-9">
                                <div class="form-control-static"><?php echo $arrUser['access_level']; ?></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12">Последняя активность:</label>
                            <div class="col-sm-9">
                                <div class="form-control-static"><?php echo date("d.m.Y H:i:s", $arrUser['last_active']); ?></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12" for="login-input">Имя:</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" id="login-input" name="name" placeholder="" value="<?php echo $arrUser['name']; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12" for="email-input">E-Mail:</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="email" id="email-input" name="email" placeholder="" value="<?php echo $arrUser['email']; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12" for="avatar-input">Путь к изображению профиля:</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" id="avatar-input" name="image" placeholder="" value="<?php echo $arrUser['image']; ?>">
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
require_once __DIR__ . '/inc/footer.php';
?>