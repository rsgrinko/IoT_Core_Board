<?php
/**
 * Класс для работы с базой данных
 * Данный файл входит в состав системы IoT Core System
 * Разработчик: Роман Сергеевич Гринько
 * E-mail: rsgrinko@gmail.com
 * Cайт: https://it-stories.ru
 * @author rsgrinko@gmail.com
 */

class CDB
{
    /**
     * @var resource $db Объект базы
     */
    public $db;

    /**
     * @var string $db_server Сервер
     */
    public $db_server;

    /**
     * @var string $db_user Имя пользователя
     */
    public $db_user;

    /**
     * @var string $db_name Имя базы
     */
    public $db_name;

    /**
     * @var string $db_password Пароль
     */
    private $db_password;

    /**
     * @var int $quantity Количество обращений к базе
     */
    public static $quantity = 0;

    /**
     * @var int $workingTime Время выполнения запросов
     */
    public static $workingTime = 0;

    private static $class_version = '1.1.9';
    private static $class_author = 'Roman S Grinko (rsgrinko@gmail.com)';
    private static $class_description = 'Класс для работы с базой данных';

    public static function classinfo()
    {
        $result = [];
        $result['VERSION'] = self::$class_version;
        $result['AUTHOR'] = self::$class_author;
        $result['DESCRIPTION'] = self::$class_description;
        return $result;
    }

    /**
     * Подключение к базе данных
     * @param $db_server
     * @param $db_user
     * @param $db_pass
     * @param $db_name
     */
    public function __construct($db_server, $db_user, $db_pass, $db_name)
    {
        $this->db_server = $db_server;
        $this->db_user = $db_user;
        $this->db_password = $db_pass;
        $this->db_name = $db_name;
        $this->db = new PDO('mysql:host=' . $db_server . ';dbname=' . $db_name, $db_user, $db_pass);
    }

    /**
     * Обработка вызова несуществующего метода
     *
     * @param $method
     * @param $args
     */
    public function __call($method, $args)
    {
        echo json_encode(['status' => 'fail', 'error' => 'Unsupported method', 'method' => $method, 'args' => $args], JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Обработка вызова несуществующего метода
     *
     * @param $method
     * @param $args
     */
    public function __callStatic($method, $args)
    {
        echo json_encode(['status' => 'fail', 'error' => 'Unsupported method', 'method' => $method, 'args' => $args], JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Вспомогательный метод, формирует WHERE из массива
     *
     * @param $where
     * @return false|mixed|string
     */
    private function createWhere($where, $logic = 'AND')
    {
        if (!is_array($where)) {
            return $where;
        }
        $where_string = '';
        foreach ($where as $where_key => $where_item) {
            if (stristr($where_item, '>')) {
                $symbol = '>';
                $where_item = str_replace($symbol, '', $where_item);
            } elseif (stristr($where_item, '<')) {
                $symbol = '<';
                $where_item = str_replace($symbol, '', $where_item);
            } elseif (stristr($where_item, '<=')) {
                $symbol = '<=';
                $where_item = str_replace($symbol, '', $where_item);
            } elseif (stristr($where_item, '>=')) {
                $symbol = '>=';
                $where_item = str_replace($symbol, '', $where_item);
            } else {
                $symbol = '=';
            }
            $where_string = $where_string . ' (' . $where_key . $symbol . '\'' . $where_item . '\') '.$logic;
        }
        $where_string = substr($where_string, 0, -4);

        return $where_string;
    }

    /**
     * Вспомогательный метод, формирует SET из массива
     *
     * @param $set
     * @return false|mixed|string
     */
    private function createSet($set)
    {
        if (!is_array($set)) {
            return $set;
        }
        $set_string = '';
        foreach ($set as $set_key => $set_item) {
            $set_string = $set_string . ' ' . $set_key . '=\'' . $set_item . '\',';
        }
        $set_string = substr($set_string, 0, -1);

        return $set_string;
    }

    /**
     * Вспомогательный метод для создания строки сортировки
     *
     * @param $sort
     * @return string
     */
    private function createSort($sort)
    {
        if (is_array($sort)) {
            foreach ($sort as $k => $v) {
                $sort_string = ' ORDER BY ' . $k . ' ' . $v;
            }
        } else {
            $sort_string = '';
        }

        return $sort_string;
    }


    /**
     * Вспомогательный метод для построения запросов
     *
     * @param $data
     * @param string $param
     * @return false|string
     */
    private function createInsertString($data, $param = 'key')
    {
        $result = '';
        foreach ($data as $k => $v) {
            if ($param == 'key') {
                $result = $result . $k . ', ';
            } elseif ($param == 'value') {
                $result = $result . '\'' . $v . '\', ';
            }
        }
        $result = substr($result, 0, -2);
        return $result;
    }

    /**
     * Метод для простого выполнения заданного SQL запроса.
     * Возвращает результат в виде массива или ложь в случае неудачи
     *
     * @param $sql
     * @param array $params
     * @return array|false
     */
    public function query($sql, $params = [])
    {
        $startTime = microtime(true);
        self::$quantity++;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $endTime = microtime(true);
        self::$workingTime += ($endTime - $startTime);

        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Метод для обновления записи в таблице
     * Принимает 3 аргумента: имя таблицы, массив для WHERE и массив значений для обновления (ключ-значение)
     *
     * @param $table
     * @param $where
     * @param $set
     * @return array|false
     */
    public function update($table, $where, $set)
    {
        self::$quantity++;
        $result = $this->query('UPDATE `' . $table . '` SET ' . $this->createSet($set) . ' WHERE ' . $this->createWhere($where));
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Получить элемент из базы
     *
     * @param $table
     * @param $where
     * @param false $force
     * @return false|mixed
     */
    public function getItem($table, $where)
    {
        self::$quantity++;
        $result = $this->query('SELECT * FROM `' . $table . '` WHERE ' . $this->createWhere($where) . ' LIMIT 1');
        if ($result) {
            return $result[0];
        } else {
            return false;
        }
    }


    /**
     * Добавить элемент в базу
     *
     * @param $table
     * @param $data
     * @return bool
     */
    public function addItem($table, $data)
    {
        self::$quantity++;
        $result = $this->query('INSERT INTO `' . $table . '` (' . $this->createInsertString($data, 'key') . ') VALUES (' . $this->createInsertString($data, 'value') . ')');
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Удалить элемент из базы
     *
     * @param $table
     * @param $where
     * @return bool
     */
    public function remove($table, $where)
    {
        self::$quantity++;
        $result = $this->query('DELETE FROM `' . $table . '` WHERE ' . $this->createWhere($where) . '');
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получить элементЫ из базы
     *
     * @param $table
     * @param $where
     * @param string $sort
     * @param false $force
     * @return array|false|mixed
     */
    public function getItems($table, $where, $sort = '', $force = false)
    {
        self::$quantity++;
        $result = $this->query('SELECT * FROM `' . $table . '` WHERE ' . $this->createWhere($where) . $this->createSort($sort));

        if ($result) {
            return $result;
        } else {
            return false;
        }
    }


    /**
     * Получить все данные из таблицы
     *
     * @param $table
     * @param string $sort
     * @param array $params
     * @param false $force
     * @return array|false|mixed
     */
    public function getAll($table, $sort = '', $params = [], $force = false)
    {
        self::$quantity++;
        $result = $this->query('SELECT * FROM `' . $table . '`' . $this->createSort($sort));

        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Получение строки из таблицы
     *
     * @param $table
     * @param string $sql
     * @param array $params
     * @param false $force
     * @return false|mixed
     */
    public function getRow($table, $sql = '', $params = [], $force = false)
    {
        self::$quantity++;
        $result = $this->query('SELECT * FROM `' . $table . '` ' . $sql . ' LIMIT 1', $params);

        if ($result) {
            return $result[0];
        } else {
            return false;
        }
    }

}