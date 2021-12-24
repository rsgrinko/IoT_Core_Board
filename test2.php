<?php
/**
 * Файл для тестирования и отладки нового функционала
 */
require_once __DIR__.'/inc/bootstrap.php';
/*if(!CUser::isAdmin()) {
    die('403 - Access denied');
}*/

header('Set-Cookie: KEY=VerySecretUniqueKey');

echo 'ok';