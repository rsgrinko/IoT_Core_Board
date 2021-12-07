<?php
/**
 * Файл для тестирования и отладки нового функционала
 */
require_once __DIR__.'/inc/bootstrap.php';
if(!CUser::is_admin()) {
    die('403 - Access denied');
}
//userSendMail(1, 'Test userMail', 'Hello!!!',false);
adminSendMailWithTemplate('default', 'Тестовое уведомление', 'Запушен тестовый скрипт для проверки функционала почтовых уведомлений.<br>Если Вы видите данное сообщение - значит система работает.');
echo 'ok';