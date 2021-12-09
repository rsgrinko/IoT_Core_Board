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
                            <label class="col-xs-12" for="template-select">Select</label>
                                <div class="col-sm-9">
                                <select class="form-control" id="template-select" name="template" size="1">
                                    <?php foreach(CMail2::getMailTemplates() as $mailTemplate): ?>
									    <option value="<?php echo $mailTemplate; ?>"><?php echo $mailTemplate; ?></option>
                                    <?php endforeach; ?>
								</select>
                                </div>
                        </div>
                        

                        <div class="form-group m-b-0">
                            <div class="col-sm-9">
                                <button class="btn btn-app" type="submit">Запустить</button>
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