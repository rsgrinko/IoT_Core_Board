<?php
/**
 * Файл для тестирования и отладки нового функционала
 */
require_once __DIR__.'/inc/bootstrap.php';
if(!CUser::isAdmin()) {
    die('403 - Access denied');
}

try {
    echo 'hi<br>';
    throw new Exception("Configuration file not found.");
    echo '123 boom!<br>';
} catch (Exception $e) {
    echo 'ERROR: '.$e->getMessage();
}

echo '<br>work also...';