<?php
/**
*	Файл, запускаемый по расписанию и запускающий периодические события
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*	Сайт: https://it-stories.ru
*/

set_time_limit(60);
require_once __DIR__ . '/bootstrap.php';

CCron::cronUpdate();	 // обновляем время выполнения крона
CCron::handler();        // работаем с периодическими заданиями