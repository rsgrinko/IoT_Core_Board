<?php
/*
	Класс для кэширования данных и работы с кэшэм
	Данный файл входит в состав системы IoT Core System
	Разработчик: Роман Сергеевич Гринько
	E-mail: rsgrinko@gmail.com
	Сайт: https://it-stories.ru
*/
class CCache{
	private static $cache_dir;
	public static $quantity = 0;
	public static $quantity_read = 0;
	public static $quantity_write = 0;
	private static $cache_enabled = true;
	private static $class_version = '1.0.9';
	private static $class_author = 'Roman S Grinko (rsgrinko@gmail.com)';
	private static $class_description = 'Класс для кэширования данных и работы с кэшэм';
	
	public static function classinfo(){
		$result = [];
		$result['VERSION'] = self::$class_version;
		$result['AUTHOR'] = self::$class_author;
		$result['DESCRIPTION'] = self::$class_description;
		return $result;
	}
	
	public static function init($dir, $enabled = true){
		self::$cache_dir = $dir;
		self::$cache_enabled = $enabled;
	}
	
	
	public static function checkCache($name){ // Проверка наличия элемента в кэше
		if(!self::$cache_enabled) { return false; }
		if(file_exists(self::$cache_dir.md5($name).'.tmp')){
			return true;
		} else {
			return false;
		}
	}
	
	public static function getCache($name){	// Получить элемент из кэша
		self::$quantity++;
		self::$quantity_read++;
		if(self::checkCache($name)){
			return unserialize(base64_decode(file_get_contents(self::$cache_dir.md5($name).'.tmp')));
		} else {
			return false;
		}
	}
		
	public static function writeCache($name, $arValue){ // Записать элемент в кэш
		if(!self::$cache_enabled) { return false; }
		self::$quantity++;
		self::$quantity_write++;
		if(file_put_contents(self::$cache_dir.md5($name).'.tmp', base64_encode(serialize($arValue)))){
			return true;
		} else {
			return false;
		}
	}
	
	public static function clearCache(){ // Очистить кэш
		foreach(scandir(self::$cache_dir) as $file){
			if($file == '.' or $file == '..') continue;
			self::$quantity++;
			self::$quantity_write++;
			if(!unlink(self::$cache_dir.$file)){
				return false;
			}
		}
		return true;
	}
	
	public static function delFromCache($name){ // Удалить элемент из кэша
		if(self::checkCache($name)){
			if(!unlink(self::$cache_dir.md5($name).'.tmp')){
				self::$quantity++;
				self::$quantity_write++;
				return false;	
			}
		}
		return true;
	}	
	
	public static function getSize($name){ // Получить размер элемента в кэше
		if(self::checkCache($name)){
			return filesize(self::$cache_dir.md5($name).'.tmp');
			
		}
		return true;
	}
	
	public static function getCacheSize(){ // Получить размер кэша
		$return_size = 0;
		foreach(scandir(self::$cache_dir) as $file){
			if($file == '.' or $file == '..') continue;
			$return_size = $return_size + filesize(self::$cache_dir.$file);
		}
		return $return_size;
	}
	
	public static function ageOfCache($name) { // Получить возраст элемента кэша
		if(self::checkCache($name)){
			return  (time() - filectime(self::$cache_dir.md5($name).'.tmp'));
		} else {
			return false;
		}
	}
}
?>