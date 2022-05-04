<?php
/**
*	Обработчик запросов от контроллера
*    Данный файл входит в состав системы IoT Core System
*    Разработчик: Роман Сергеевич Гринько
*    E-mail: rsgrinko@gmail.com
*    Сайт: https://it-stories.ru
*/
require_once __DIR__ . '/../../inc/bootstrap.php';

if(isset($_REQUEST['act']) && $_REQUEST['act'] === 'getupdate') {
    Log::add('Запрос на скачивание обновления контроллером '.$_REQUEST['mac'], 'notice', 'core');
    echo 'http://new-dev.it-stories.ru/controller/update/firmware/binary/IoT_PRODUCTION_1_v105.bin';
} else {
    Log::add('Проверка обновлений контроллером '.$_REQUEST['mac'], 'notice', 'core');
    echo 115;
}