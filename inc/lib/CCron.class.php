<?php
/*
	Класс планировщика задач
	Данный файл входит в состав системы IoT Core System
	Разработчик: Роман Сергеевич Гринько
	E-mail: rsgrinko@gmail.com
	Сайт: https://it-stories.ru
*/
class CCron {
	private static $DB;
	private static $table;
	private static $class_version = '1.0.5';
	private static $class_author = 'Roman S Grinko (rsgrinko@gmail.com)';
	private static $class_description = 'Класс планировщика задач';
	
	public static function classinfo(){
		$result = [];
		$result['VERSION'] = self::$class_version;
		$result['AUTHOR'] = self::$class_author;
		$result['DESCRIPTION'] = self::$class_description;
		return $result;
	}
	
	public static function init($DB, $table = 'cron'){
		self::$DB = $DB;
		self::$table = $table;
	}
	
	// обновляем время крона
	public static function cronUpdate(){
		global $CONFIG;
		file_put_contents($CONFIG['DIR'].'/cron.run', time());
		return;
	}
	
	// запущен ли крон
	public static function cron_is_run(){
		global $CONFIG;
		
		if(!file_exists($CONFIG['DIR'].'/cron.run')) {
			CEvents::add('Ошибка в работе CRON - время последнего запуска превысило таймаут!', 'warning', 'cron');
			return false;
		}
		
		$last_run = file_get_contents($CONFIG['DIR'].'/cron.run');
		if($last_run<(time() - 120)) {
			if($last_run != '0'){
				file_put_contents($CONFIG['DIR'].'/cron.run', '0');
				CEvents::add('Ошибка в работе CRON - время последнего запуска превысило таймаут!', 'warning', 'cron');
			}
			return false;
		} else {
			return true;
		}
		
	}
	
	// считает количество заданий
	public static function count_tasks(){
		$result = self::$DB->getItems(self::$table, array('id'=>'>0'));
		
		if($result) {
			return count($result);
		} else {
			return false;
		}
	}
	
	// Получение задания
	public static function getTask($id){
		$res = self::$DB->query('SELECT * FROM `'.self::$table.'` WHERE id=\''.$id.'\'');
		$res = $res[0];
		return $res;
	}
	
	// Добавление задания
	public static function addTask($fields){
		if(isset($fields['command']) and !empty($fields['command'])){
			$fields['command'] = base64_encode($fields['command']);
		}
		self::$DB-> addItem(self::$table, $fields);
		return true;
	}
	
	// Изменение задания
	public static function updateTask($id, $fields){
		if(isset($fields['command']) and !empty($fields['command'])){
			$fields['command'] = base64_encode($fields['command']);
		}
		self::$DB->update(self::$table, array('id' => $id), $fields);
		return true;
	}
	
	// Получение всех заданий
	public static function getTasks($limit = 10, $sort = 'ASC'){
		$res = self::$DB->query('SELECT * FROM `'.self::$table.'` ORDER BY `id` '.$sort.' LIMIT '.$limit);
		return $res;
	}
	
	// Получение активных заданий
	/*public static function getActiveTasks($limit = 10, $sort = 'ASC'){
		$res = self::$DB->query('SELECT * FROM `'.self::$table.'` ORDER BY `id` '.$sort.' WHERE active=\'Y\' LIMIT '.$limit);
		return $res;
	}*/
	
	// Получение активных заданий
	public static function runTask($id){
		$res = self::$DB->query('SELECT * FROM `'.self::$table.'` WHERE id=\''.$id.'\'');
		$res = $res[0];
		ob_start();
		eval(base64_decode($res['command']));
		$result = ob_get_clean();
		self::$DB->update(self::$table, array('id' => $id), array('last_run' => time()));
		CEvents::add('Выполнено задание с ID: '.$id.'.'.(!empty($result) ? "\n".'Результат выполнения:'."\n".'<code>'.$result.'</code>' : ''), 'success', 'cron');
		return true;
	}
	
	// Удаление задания
	public static function removeTask($id){
		if(!isset($id) or empty($id)) return false;
		$res = self::$DB->remove(self::$table, array('id' => $id));
		CEvents::add('Задание с ID: '.$id.'. было удалено', 'info', 'cron');
		return true;
	}
	
	
	public static function handler(){
		$res = self::$DB->query('SELECT * FROM `'.self::$table.'` WHERE active=\'Y\' ORDER BY `id` ASC');
		if(!$res) {
			return false;
		}
		foreach($res as $task):
			if($task['last_run']<(time()-$task['period'])) {
				self::runTask($task['id']);
			}
			
		endforeach;
		return true;
	}
	
}