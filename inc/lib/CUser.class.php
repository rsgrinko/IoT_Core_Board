<?php
/*
	Класс для работы с пользователями панели управления
	Данный файл входит в состав системы IoT Core System
	Разработчик: Роман Сергеевич Гринько
	E-mail: rsgrinko@gmail.com
	Сайт: https://it-stories.ru
*/
	
class CUser {
	public static $id;
	public static $table;
	public static $DB;
	private static $class_version = '1.0.1';
	private static $class_author = 'Roman S Grinko (rsgrinko@gmail.com)';
	private static $class_description = 'Класс для работы с пользователями панели управления';
	
	public static function classinfo(){
		$result = [];
		$result['VERSION'] = self::$class_version;
		$result['AUTHOR'] = self::$class_author;
		$result['DESCRIPTION'] = self::$class_description;
		return $result;
	}
	
	public static function init($DB, $table = 'users') {
		self::$table = $table;
		self::$DB = $DB;
		if(isset($_SESSION['authorize']) and $_SESSION['authorize'] == 'Y'){
			self::$id = $_SESSION['id'];
		}
	}	
	
	// Получение всех полей пользователя
	public static function getFields($id = ''){
		if(empty($id) or $id == ''){
			$id = self::$id;
		}
		
		if(is_array($id)){
			$where = $id;
		} else {
			$where = array('id'=>$id);
		}
		$result = self::$DB->getItem(self::$table, $where);
		if($result) {
			return $result;
		} else {
			return false;
		}
	}
	
	// Получение всех пользователей панели
	public static function getUsers($limit = 10, $sort = 'ASC'){
		$res = self::$DB->query('SELECT * FROM `'.self::$table.'` ORDER BY `id` '.$sort.' LIMIT '.$limit);
		return $res;
	}

	
	// Метод выполняет регистрацию пользователя в системе
	public static function Registration($login, $password, $level = 'user', $name = '', $image = ''){
		self::$DB->addItem(self::$table, array('login' => $login, 'password' => $password, 'access_level' => $level, 'name' => $name,'image' => $image, 'last_active' => time()));
		$result = self::$DB->getItem(self::$table, array('login'=>$login, 'password' => $password));
		
		self::$id = $result['id'];
		$_SESSION['id'] = $result['id'];
		$_SESSION['authorize'] = 'Y';
		$_SESSION['login'] = $result['login'];
		$_SESSION['password'] = $result['password'];	
		$_SESSION['user'] = $result;
		return;
		
	}
	
	// Метод проеряет занятость логина в системе
	public static function user_exists($login){
		$result = self::$DB->getItem(self::$table, array('login'=>$login));
		
		if($result) {
			return true;
		} else {
			return false;
		}
	}
	
	// считает количество пользователей
	public static function count_users(){
		$result = self::$DB->getItems(self::$table, array('id'=>'>0'));
		
		if($result) {
			return count($result);
		} else {
			return false;
		}
	}
		
	// Метод выполняет авторизацию пользователя в системе по ID
	public static function Authorize($id){
		$result = self::$DB->getItem(self::$table, array('id'=>$id), true);
		
		if($result) {
			self::$id = $result['id'];
			$_SESSION['id'] = $result['id'];
			$_SESSION['authorize'] = 'Y';
			$_SESSION['login'] = $result['login'];
			$_SESSION['password'] = $result['password'];	
			$_SESSION['user'] = $result;
			return true;
		} else {
			return false;
		}
	}
	
	// Метод выполняет авторизацию пользователя в системе по логину и паролю
	public static function SecurityAuthorize($login, $password){
		
		$result = self::$DB->getItem(self::$table, array('login'=>$login, 'password' => $password), true);
		if($result) {
			self::$id = $result['id'];
			$_SESSION['id'] = $result['id'];
			$_SESSION['authorize'] = 'Y';
			$_SESSION['login'] = $result['login'];
			$_SESSION['password'] = $result['password'];	
			$_SESSION['user'] = $result;	
			return true;
		} else {
			return false;
		}
	}
	
	//  Проверка на пользователя
	public static function is_user(){
		if(!isset($_SESSION['authorize']) or empty($_SESSION['authorize']) or $_SESSION['authorize']!=='Y') {
			return false;
		}
		$result = self::$DB->getItem(self::$table, array('login'=>$_SESSION['login']));
		if($result) {
				if($result['password']==$_SESSION['password']){
					self::$DB->update(self::$table, array('id'=>$result['id']), array('last_active' => time()));
					self::$id = $result['id'];
					$_SESSION['id'] = $result['id'];
					return true;
				} else {
					return false;
				}		
			
		} else {
			return false;
		}
	}

	// метод вроверяет админ ли перед нами или нет
	public static function is_admin(){
		if(self::is_user()) {
			if(self::getFields(self::$id)['access_level'] == 'admin') {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	// Метод выхода из системы
	public static function Logout(){
		$_SESSION['id'] = '';
		$_SESSION['authorize'] = '';
		$_SESSION['login'] = '';
		$_SESSION['password'] = '';
		$_SESSION['user'] = '';
		//session_unset();
		//session_destroy();
		return true;
		}
		
		

}