<?php
/*
	Класс для работы с базой данных
	Данный файл входит в состав системы IoT Core System
	Разработчик: Роман Сергеевич Гринько
	E-mail: rsgrinko@gmail.com
	Сайт: https://it-stories.ru
*/
	
class CDB {
	public  $db;
	public  $db_server;
	public  $db_user;
	public  $db_name;
	private $db_password;
	public static $quantity = 0;
	public static $workingTime = 0;
	private static $class_version = '1.1.9';
	private static $class_author = 'Roman S Grinko (rsgrinko@gmail.com)';
	private static $class_description = 'Класс для работы с базой данных';
	
	public static function classinfo(){
		$result = [];
		$result['VERSION'] = self::$class_version;
		$result['AUTHOR'] = self::$class_author;
		$result['DESCRIPTION'] = self::$class_description;
		return $result;
	}
	
	public function __construct($db_server, $db_user, $db_pass, $db_name) {
			$this->db_server = $db_server;
			$this->db_user = $db_user;
			$this->db_password = $db_pass;
			$this->db_name = $db_name;
			$this->db = new PDO('mysql:host='.$db_server.';dbname='.$db_name, $db_user, $db_pass);
	}
	
	// Вспомогательный метод, формирует WHERE из массива
	private function createWhere($where) {
		if(!is_array($where)) { return $where; }
		$where_string = '';
		foreach($where as $where_key => $where_item){
			if(stristr($where_item, '>')) {
				$symbol = '>';
				$where_item = str_replace($symbol, '', $where_item);
			} elseif(stristr($where_item, '<')) {
				$symbol = '<';
				$where_item = str_replace($symbol, '', $where_item);
			} elseif(stristr($where_item, '<=')) {
				$symbol = '<=';
				$where_item = str_replace($symbol, '', $where_item);
			} elseif(stristr($where_item, '>=')) {
				$symbol = '>=';
				$where_item = str_replace($symbol, '', $where_item);
			} else {
				$symbol = '=';
			}
			$where_string = $where_string.' ('.$where_key.$symbol.'\''.$where_item.'\') AND';
		}
		$where_string = substr($where_string, 0, -4);
		
		return $where_string;
	}
	
	// Вспомогательный метод, формирует SET из массива
	private function createSet($set) {
		if(!is_array($set)) { return $set; }
		$set_string = '';
		foreach($set as $set_key => $set_item){
			$set_string = $set_string.' '.$set_key.'=\''.$set_item.'\',';
		}
		$set_string = substr($set_string, 0, -1);
		
		return $set_string;
	}
	
	// Вспомогательный метод для создания строки сортировки
	private function createSort($sort) {
		if(is_array($sort)){
			foreach($sort as $k=>$v){
				$sort_string = ' ORDER BY '.$k.' '.$v;	
			}			
		} else {
			$sort_string = '';
		}
		
		return $sort_string;
	}
	
	// Вспомогательный метод для построения запросов
	private function createInsertString($data, $param = 'key'){
		$result = '';
		foreach($data as $k=>$v){
			if($param=='key') {
				$result = $result.$k.', ';
			}elseif($param=='value') {
				$result = $result.'\''.$v.'\', ';
			}
		}
		$result = substr($result, 0, -2);
		return $result;
	}

	// Метод для простого выполнения заданного SQL запроса.
	// Возвраает результат в виде массива или ложь в случае неудачи
	public function query($sql, $params = []) {
		$startTime = microtime(true);
		self::$quantity++;
		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$endTime = microtime(true);
		self::$workingTime += ($endTime - $startTime);
		if($result) {
			return $result;
		} else {
			return false;
		}
	}

	// Метод для обновления записи в таблице
	// Принимает 3 аргумента: имя таблицы, массив для WHERE и массив значений для обновления (ключ-значение
	public function update($table, $where, $set) {
		self::$quantity++;
		$result = $this->query('UPDATE `'.$table.'` SET '.$this->createSet($set).' WHERE '.$this->createWhere($where));
		if($result) {
			return $result;
		} else {
			return false;
		}
	}
	
	// Получить элемент из базы
	public function getItem($table, $where, $force = false) {
		$cache_id = 'getItem_'.md5($table.print_r($where, true));
		if(!$force and CCache::checkCache($cache_id) and CCache::ageOfCache($cache_id) < 300 and CCache::getSize($cache_id)>0) {
			$result = CCache::getCache($cache_id);
		} else {
			self::$quantity++;
			$result = $this->query('SELECT * FROM `'.$table.'` WHERE '.$this->createWhere($where).' LIMIT 1');
			if(!$force){
				CCache::writeCache($cache_id, $result);
			}
		}
		if($result) {
			return $result[0];
		} else {
			return false;
		}
	}	

	
	// Добавить элемент в базу
	public function addItem($table, $data) {
		self::$quantity++;
		$result = $this->query('INSERT INTO `'.$table.'` ('.$this->createInsertString($data, 'key').') VALUES ('.$this->createInsertString($data, 'value').')');
		if($result) {
			return true;
		} else {
			return false;
		}
	}	
	
	// удалить элемент из базы
	public function remove($table, $where) {
		self::$quantity++;
		$result = $this->query('DELETE FROM `'.$table.'` WHERE '.$this->createWhere($where).'');
		if($result) {
			return true;
		} else {
			return false;
		}
	}	
	
	// Получить элементЫ из базы
	public function getItems($table, $where, $sort = '', $force = false) {
		$cache_id = 'getItems_'.md5($table.print_r($where, true).print_r($sort, true));
		if(!$force and CCache::checkCache($cache_id) and CCache::ageOfCache($cache_id) < 300 and CCache::getSize($cache_id)>0) {
			$result = CCache::getCache($cache_id);
		} else {
			self::$quantity++;
			$result = $this->query('SELECT * FROM `'.$table.'` WHERE '.$this->createWhere($where).$this->createSort($sort));
			CCache::writeCache($cache_id, $result);
		}
		if($result) {
			return $result;
		} else {
			return false;
		}
	}	
	
	
	
	
	
/******************/	
	
	// Получить все из таблицы
	public function getAll($table, $sort = '', $params = [], $force = false) {
		$cache_id = 'getAll_'.md5($table.print_r($sort, true).print_r($params, true));
		if(!$force and CCache::checkCache($cache_id) and CCache::ageOfCache($cache_id) < 300 and CCache::getSize($cache_id)>0) {
			$result = CCache::getCache($cache_id);
		} else {
			self::$quantity++;
			$result = $this->query('SELECT * FROM `'.$table.'`'.$this->createSort($sort));
			CCache::writeCache($cache_id, $result);
		}
		if($result){
			return $result;
		} else {
			return false;
		}		 
	}


	public function getRow($table, $sql = '', $params = [], $force = false) {
		$cache_id = 'getRow_'.md5($table.$sql.print_r($params, true));
		if(!$force and CCache::checkCache($cache_id) and CCache::ageOfCache($cache_id) < 300 and CCache::getSize($cache_id)>0) {
			$result = CCache::getCache($cache_id);
		} else {
			self::$quantity++;
			$result = $this->query('SELECT * FROM `'.$table.'` '.$sql.' LIMIT 1', $params);
		}
		if($result) {
			return $result[0]; 
		} else {
			return false;
		}
	}
	
}