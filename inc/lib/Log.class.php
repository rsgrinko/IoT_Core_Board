<?php
/*
	Класс, отвечающий за события системы
	Данный файл входит в состав системы IoT Core System
	Разработчик: Роман Сергеевич Гринько
	E-mail: rsgrinko@gmail.com
	Сайт: https://it-stories.ru
*/

class Log {
	private static $DB;
	private static $table;

	public static function init($DB, $table = 'logs'){
		self::$DB = $DB;
		self::$table = $table;
	}
	
	public static function add($message, $type = 'info', $module = 'core'){
		$result = self::$DB->addItem(self::$table, array('message' => $message, 'type' => $type, 'module' => $module, 'time' => time()));
		
		return $result;
	}
	
	public static function getCount($types = array('info', 'notice', 'warning', 'success')){
		if(!empty($types)) {
			foreach($types as $type){
				$where .= ' type=\''.$type.'\' OR';
			}
			$where = 'WHERE '.substr($where, 0, -2);
		} else {
			$where = '';
		}
		$res = self::$DB->query('SELECT id FROM `'.self::$table.'` '.$where);
		if($res){
			return count($res);
		} else {
			return 0;
		}
		
	}
	
	public static function getEvents($types = array('error'), $limit = 10, $sort = 'DESC'){
		$where = '';
		
		if(!empty($types)) {
			foreach($types as $type){
				$where .= ' type=\''.$type.'\' OR';
			}
			$where = 'WHERE '.substr($where, 0, -2);
		} else {
			$where = '';
		}
		$res = self::$DB->query('SELECT * FROM `'.self::$table.'` '.$where.' ORDER BY `id` '.$sort.' LIMIT '.$limit, [], true);
		if($res){
			return $res;
		} else {
			return [];
		}
		
	}
	
	public static function typeToClassName($type){
		$result = 'info';
		
		switch($type) {			
			case 'warning':
				$result = 'danger';
			break;
			
			case 'info':
				$result = 'info';
			break;
			
			case 'notice':
				$result = 'warning';
			break;
			
			case 'success':
				$result = 'success';
			break;
		}
		return $result;
	}
	
	public static function typeToRus($type){
		$result = 'Уведомление';
		
		switch($type) {			
			case 'warning':
				$result = 'Ошибка';
			break;
			
			case 'info':
				$result = 'Уведомление';
			break;
			
			case 'notice':
				$result = 'Предупреждение';
			break;
			
			case 'success':
				$result = 'Выполнено';
			break;
		}
		return $result;
	}
	
	
	
}