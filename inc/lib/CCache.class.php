<?php

/**
 * Класс для кэширования данных и работы с кэшэм
 * Данный файл входит в состав системы IoT Core System
 * Разработчик: Роман Сергеевич Гринько
 * E-mail: rsgrinko@gmail.com
 * Сайт: https://it-stories.ru
 * @author rsgrinko@gmail.com
 */

class CCache
{
    /**
     * @var string $cache_dir Директория хранения файлов кэша
     */
    private static $cache_dir;

    /**
     * @var int $quantity Количество обращений к кэшу
     */
    public static $quantity = 0;

    /**
     * @var int $quantity_read Количествоо обращений к кэшу на чтение
     */
    public static $quantity_read = 0;

    /**
     * @var int $quantity_write Количество обращений к кэшу на запись
     */
    public static $quantity_write = 0;

    /**
     * @var bool $cache_enabled Включает и выключает работу кэша
     */
    private static $cache_enabled = true;

    private static $class_version = '1.0.9';
    private static $class_author = 'Roman S Grinko (rsgrinko@gmail.com)';
    private static $class_description = 'Класс для кэширования данных и работы с кэшэм';

    public static function classinfo():array
    {
        $result = [];
        $result['VERSION'] = self::$class_version;
        $result['AUTHOR'] = self::$class_author;
        $result['DESCRIPTION'] = self::$class_description;
        return $result;
    }

    /**
     * Инициализация кэша
     *
     * @param string $dir Дирректория хранения файлов кэша
     * @param bool $enabled Флаг включения кэширования
     */
    public static function init($dir, $enabled = true):void
    {
        self::$cache_dir = $dir;
        self::$cache_enabled = $enabled;
    }

    /**
     * Проверка наличия элемента в кэше
     *
     * @param string $name Имя элемента кэша
     * @return bool Флаг наличия или отсутствия кэша
     */
    public static function check($name):bool
    { // Проверка наличия элемента в кэше
        if (!self::$cache_enabled) {
            return false;
        }
        if(self::getAge($name) > CACHE_TTL) {
            return false;
        }
        if (file_exists(self::$cache_dir . md5($name) . '.tmp')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получение кэшированых данных из кэша
     *
     * @param string $name Имя элемента кэша
     * @return mixed Кэшированные данные
     */
    public static function get(string $name)
    {
        self::$quantity++;
        self::$quantity_read++;
        return unserialize(base64_decode(file_get_contents(self::$cache_dir . md5($name) . '.tmp')));
    }

    /**
     * Запись значения в кэш
     *
     * @param string $name Имя элемента кэша
     * @param mixed $arValue Значение элемента кэша
     * @return bool Флаг успешной или неудачной записи данных
     */
    public static function write($name, $arValue):bool
    { // Записать элемент в кэш
        if (!self::$cache_enabled) {
            return false;
        }
        self::$quantity++;
        self::$quantity_write++;
        if (file_put_contents(self::$cache_dir . md5($name) . '.tmp', base64_encode(serialize($arValue)))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Полная очистка кэша
     *
     * @return bool Флаг успеха
     */
    public static function flush():bool
    { // Очистить кэш
        foreach (scandir(self::$cache_dir) as $file) {
            if ($file == '.' or $file == '..') continue;
            self::$quantity++;
            self::$quantity_write++;
            if (!unlink(self::$cache_dir . $file)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Удаление элемента из кэша
     *
     * @param string $name Имя элемента кэша
     * @return bool Флаг успеха
     */
    public static function del($name):bool
    { // Удалить элемент из кэша
        if (self::check($name)) {
            if (!unlink(self::$cache_dir . md5($name) . '.tmp')) {
                self::$quantity++;
                self::$quantity_write++;
                return false;
            }
        }
        return true;
    }

    /**
     * Получение размера элемента кэша в байтах
     *
     * @param string $name Имя элемента кэша
     * @return bool|int Размер элемента в байтах или false
     */
    public static function getSize($name)
    { // Получить размер элемента в кэше
        if (self::check($name)) {
            return filesize(self::$cache_dir . md5($name) . '.tmp');

        }
        return true;
    }

    /**
     * Получение общего размера кэша в байтах
     * 
     * @return int Размер кэша в байтах или false
     */
    public static function getCacheSize():int
    { // Получить размер кэша
        $return_size = 0;
        foreach (scandir(self::$cache_dir) as $file) {
            if ($file == '.' or $file == '..') continue;
            $return_size = $return_size + filesize(self::$cache_dir . $file);
        }
        return $return_size;
    }

    /**
     * Получение времени существованя кэша в секундах
     * 
     * @param string $name Имя элемента кэша
     * @return int Время в секундах или false
     */
    public static function getAge(string $name)
    {
        return (time() - @filectime(self::$cache_dir . md5($name) . '.tmp'));
    }


    /**
     * Тестовый метод для записи в мемкэш
     * TODO: переделать
     */
    public static function memcacheWrite($name, $value):bool {
        $memcache = memcache_connect('localhost', 11211);

        if ($memcache) {
            $memcache->set($name, serialize($value));
            return true;
        } else {
            return false;
        }
    }

    /**
     * Тестовый метод для чтения из мемкэша
     * TODO: переделать
     */
    public static function memcacheGet($name) {
        $memcache = memcache_connect('localhost', 11211);

        if ($memcache) {
           return unserialize($memcache->get($name));
        } else {
           return false;
        }
    }


    /**
     * Тестовый метод для очистки мемкэша
     * TODO: переделать
     */
    public static function memcacheFlush() {
        $memcache = memcache_connect('localhost', 11211);
        $memcache->flush();
    }
    
}

?>