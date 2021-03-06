<?php
/**
 * Выполнение авторизации под каким либо пользователем
 * Административный функционал
 * Данный файл входит в состав системы IoT Core System
 * Разработчик: Роман Сергеевич Гринько
 * E-mail: rsgrinko@gmail.com
 * Сайт: https://it-stories.ru
 */
require_once __DIR__ . '/inc/bootstrap.php';
if (!User::isAdmin()) {
    die('403 - Access denied');
}

if (!isset($_REQUEST['id']) or trim($_REQUEST['id']) == '') {
    die('400 - Bad request');
}
if (!isGod($USER['id'])) {
    die('403 - Access denied');
}

$id = prepareString($_REQUEST['id']);

Log::add('Пользователь '.$USER['login'].', ID: '.$USER['id'].' авторизовался под пользователем '.User::getFields($id)['login'].', ID: '.$id, 'info', 'core');
sendPush('Пользователь '.$USER['login'].', ID: '.$USER['id'].' авторизовался под пользователем '.User::getFields($id)['login'].', ID: '.$id, 'Система безопасности');
User::logout();
User::authorize($id);

header("Location: index.php");
die();