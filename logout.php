<?php
/**
*	Выход из системы
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*	Сайт: https://it-stories.ru
*/
require_once __DIR__ . '/inc/bootstrap.php';
CUser::logout();
header("Location: index.php");