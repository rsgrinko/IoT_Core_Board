<?php

/**
 * Класс для кэширования данных и работы с кэшэм
 * Данный файл входит в состав системы IoT Core System
 * Разработчик: Роман Сергеевич Гринько
 * E-mail: rsgrinko@gmail.com
 * Сайт: https://it-stories.ru
 * @author rsgrinko@gmail.com
 */

class Cache
{
    /**
     * @var string $cache_dir Директория хранения файлов кэша
     */
    private static $cache_dir;

    /**
     * @var int $cache_ttl Время жизни кэша в секуднах
     */
    private static $cache_ttl;

    /**
     * @var string Хост memcache сервера
     */
    private static $memcacheHost = 'localhost';

    /**
     * @var int Порт memcache сервера
     */
    private static $memcachePort = 11211;

    /**
     * @var bool Флаг использования memcache
     */
    private static $useMemcache = false;

    /**
     * @var object Объект Memcache
     */
    private static $memcacheObject;

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

    /**
     * @var string $lastError Последняя ошибка
     */
    private static $lastError = '';

    /**
     * Инициализация кэша
     *
     * @param string $dir Дирректория хранения файлов кэша
     * @param bool $enabled Флаг включения кэширования
     */
    public static function init($dir, $cache_ttl, $enabled = true): void
    {
        if (!isset($dir) or empty($dir)) {
            self::$lastError = 'Incorrect cache path!';
        } else {
            self::$cache_dir = $dir;
        }

        if (!isset($cache_ttl) or empty($cache_ttl)) {
            self::$lastError = 'Incorrect cache TTL!';
        } else {
            self::$cache_ttl = $cache_ttl;
        }

        self::$cache_enabled = $enabled;
    }

    /**
     * Обработка вызова несуществующего метода
     *
     * @param $method
     * @param $args
     */
    public function __callStatic($method, $args)
    {
        self::$lastError = 'Unsupported method. Method: ' . $method . ', arguments: ' . implode(',', $args);
        echo json_encode(['status' => 'fail', 'error' => 'Unsupported method', 'method' => $method, 'args' => $args], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Проверка наличия элемента в кэше с проверкой срока годности
     *
     * @param string $name Имя элемента кэша
     * @return bool Флаг наличия или отсутствия кэша
     */
    public static function check($name): bool
    {
        // если кэш отключен
        if (!self::$cache_enabled) {
            return false;
        }

        // если используется memcache
        if (self::$useMemcache) {
            if (self::getMemcache($name)) {
                return true;
            } else {
                return false;
            }
        }

        // если время жизни элемента истекло
        if (self::getAge($name) > self::$cache_ttl) {
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
        if (self::$useMemcache) {
            return self::getMemcache($name);
        } else {
            return unserialize(base64_decode(file_get_contents(self::$cache_dir . md5($name) . '.tmp')));
        }
    }

    /**
     * Запись значения в кэш
     *
     * @param string $name Имя элемента кэша
     * @param mixed $arValue Значение элемента кэша
     * @return bool Флаг успешной или неудачной записи данных
     */
    public static function write($name, $arValue): bool
    { // Записать элемент в кэш
        if (!self::$cache_enabled) {
            return false;
        }
        self::$quantity++;
        self::$quantity_write++;

        if (self::$useMemcache) {
            self::writeMemcache($name, $arValue);
            return true;
        } else {
            if (file_put_contents(self::$cache_dir . md5($name) . '.tmp', base64_encode(serialize($arValue)))) {
                return true;
            } else {
                self::$lastError = 'Error writing cache "' . $name . '" to file: ' . self::$cache_dir . md5($name) . '.tmp';
                return false;
            }
        }
    }

    /**
     * Полная очистка кэша
     *
     * @return bool Флаг успеха
     */
    public static function flush(): bool
    { // Очистить кэш
        if (self::$useMemcache) {
            self::flushMemcache();
        } else {
            foreach (scandir(self::$cache_dir) as $file) {
                if ($file == '.' or $file == '..') continue;
                self::$quantity++;
                self::$quantity_write++;
                if (!unlink(self::$cache_dir . $file)) {
                    self::$lastError = 'Error remove file: ' . self::$cache_dir . $file;
                    return false;
                }
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
    public static function del($name): bool
    { // Удалить элемент из кэша
        if (self::check($name)) {
            if (!unlink(self::$cache_dir . md5($name) . '.tmp')) {
                self::$quantity++;
                self::$quantity_write++;
                self::$lastError = 'Error remove file: ' . self::$cache_dir . md5($name) . '.tmp';
                return false;
            }
        }
        return true;
    }

    /**
     * Получение размера элемента кэша в байтах
     *
     * @param string $name Имя элемента кэша
     * @return int Размер элемента в байтах или false
     */
    public static function getSize($name): int
    { // Получить размер элемента в кэше
        if (self::check($name)) {
            return filesize(self::$cache_dir . md5($name) . '.tmp');
        }
        return 0;
    }

    /**
     * Получение общего размера кэша в байтах
     *
     * @return int Размер кэша в байтах или false
     */
    public static function getCacheSize(): int
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
        if (self::$useMemcache) {
            return 0;
        } else {
            return (time() - @filectime(self::$cache_dir . md5($name) . '.tmp'));
        }
    }

    /**
     * Включение использования memcache вместо файлов
     *
     * @param string $host
     * @param int $port
     * @return bool
     */
    public static function useMemcache(string $host = 'localhost', int $port = 11211): bool
    {
        self::$memcacheHost = $host;
        self::$memcachePort = $port;
        self::$memcacheObject = new Memcache;
        $status = @self::$memcacheObject->connect(self::$memcacheHost, self::$memcachePort);
        if ($status) {
            self::$useMemcache = true;
            return true;
        } else {
            self::$useMemcache = false;
            self::$lastError = 'Fail connect to Memcache! Host: ' . self::$memcacheHost . ', port: ' . self::$memcachePort;
            return false;
        }
    }

    /**
     * Метод для записи в мемкэш
     */
    private static function writeMemcache($name, $value): void
    {
        self::$memcacheObject->set($name, $value, MEMCACHE_COMPRESSED, self::$cache_ttl);
        return;
    }

    /**
     * Метод для чтения из мемкэша
     */
    private static function getMemcache($name)
    {
        return self::$memcacheObject->get($name);
    }

    /**
     * Тестовый метод для очистки мемкэша
     */
    private static function flushMemcache(): void
    {
        self::$memcacheObject->flush();
    }

    /**
     * Получение последней ошибки
     *
     * @return string
     */
    public static function getLastError(): string
    {
        return self::$lastError;
    }
}

?>