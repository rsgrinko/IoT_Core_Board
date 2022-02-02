<?php
/**
 * Страница управления потчовыми рассылками
 */
require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/header.php';

if (isset($_REQUEST['save']) and $_REQUEST['save'] == 'Y'):

    $userEmails = [];

    if (isset($_REQUEST['admin']) and $_REQUEST['admin'] == 'on') {
        $arUsers = $DB->query('SELECT id, email FROM users WHERE access_level="admin"');
        foreach ($arUsers as $mailingUser) {
            $userEmails[] = $mailingUser['email'];
        }
    }

    if (isset($_REQUEST['user']) and $_REQUEST['user'] == 'on') {
        $arUsers = $DB->query('SELECT id, email FROM users WHERE access_level="user"');
        foreach ($arUsers as $mailingUser) {
            $userEmails[] = $mailingUser['email'];
        }
    }

    if (isset($_REQUEST['demo']) and $_REQUEST['demo'] == 'on') {
        $arUsers = $DB->query('SELECT id, email FROM users WHERE access_level="demo"');
        foreach ($arUsers as $mailingUser) {
            $userEmails[] = $mailingUser['email'];
        }
    }

    if (isset($_REQUEST['use_html_message']) and $_REQUEST['use_html_message'] == 'on') {
        $message = $_REQUEST['message'];
    } else {
        $message = nl2br($_REQUEST['message']);
    }

    $mail = new Mail;
    $mail->dump = true;
    $mail->dumpPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/emails';
    $mail->from('iot@' . $_SERVER['SERVER_NAME'], 'IoT Core Board v.' . VERSION);
    $mail->to(implode(',', $userEmails));
    $mail->subject = $_REQUEST['subject'];
    $mail->assignTemplateVars(
        [
            'HEADER' => $_REQUEST['subject'],
            'MESSAGE' => $message,
            'TITLE' => $_REQUEST['title'],
            'LINK' => 'https://it-stories.ru/',
            'LINKNAME' => 'Перейти в панель',
            'HOME' => 'https://it-stories.ru'
        ]
    );

    $mail->template = $_REQUEST['template'] ?? 'default';
    $mail->templateDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/mail_templates';

    /*
    // прикрепляем лог, если он есть
    if(isset($file) and !empty($file) and file_exists($file)){
        $mail->addFile($file);
    }*/
    $mail->send();
    ?>
    <script>
        $(document).ready(function () {
            alert("Почтовая рассылка отправлена");
        });
    </script>
<?php
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
                            <label class="col-xs-12" for="template-select">Выберите шаблон письма:</label>
                                <div class="col-sm-9">
                                <select class="form-control" id="template-select" name="template" size="1">
                                    <?php foreach(Mail::getMailTemplates() as $mailTemplate): ?>
									    <option value="<?php echo $mailTemplate; ?>"><?php echo $mailTemplate; ?></option>
                                    <?php endforeach; ?>
								</select>
                                </div>
                        </div>


                        <div class="form-group">
                            <label class="col-xs-12" for="subject-text-input">Тема письма:</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" id="subject-text-input" name="subject" placeholder="Введите тему письма..." value="Почтовая рассылка IoT Core Board">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12" for="header-text-input">Заголовок письма:</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" id="header-text-input" name="title" placeholder="Введите заголовок письма...">
                            </div>
                        </div>


                        <div class="form-group">


                            <label class="col-xs-12" for="body-textarea-input">Содержимое письма:</label>

                            <div class="col-xs-12">
                                <p>
                                    <label class="css-input switch switch-sm switch-primary">
                                        <input type="checkbox" name="use_html_message"><span></span> HTML
                                    </label>
                                </p>
                                <textarea class="form-control" id="body-textarea-input" name="message" rows="6" placeholder="Введите содержимое письма..."></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12" for="example-textarea-input">Выберите группы для рассылки:</label>
                            <div class="col-xs-12">
                                <p>
                                    <label class="css-input switch switch-sm switch-primary">
                                        <input type="checkbox" name="admin"><span></span> Администрация
                                    </label>
                                </p>

                                <p>
                                    <label class="css-input switch switch-sm switch-primary">
                                        <input type="checkbox" name="user"><span></span> Пользователи
                                    </label>
                                </p>

                                <p>
                                    <label class="css-input switch switch-sm switch-primary">
                                        <input type="checkbox" name="demo"><span></span> Демо-пользователи
                                    </label>
                                </p>
                            </div>
                        </div>




                        <div class="form-group m-b-0">
                            <div class="col-sm-9">
                                <button class="btn btn-app" type="submit">Запустить рассылку</button>
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