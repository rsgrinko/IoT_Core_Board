<?php
/**
 * Страница управления потчовыми рассылками
 */
require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/header.php';

if(isset($_REQUEST['save']) and $_REQUEST['save'] == 'Y'):
    //TODO: делаем рассылку
endif;


?>
<div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Управление почтовыми рассылками</h4>
                    <div class="card-actions">
                        <code>CMail</code>
                    </div>
                </div>
                <div class="card-block">
                    <form class="form-horizontal m-t-sm" action="" method="post">
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