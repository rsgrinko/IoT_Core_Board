<?php

/**
 * Класс планировщика задач
 * Данный файл входит в состав системы IoT Core System
 * Разработчик: Роман Сергеевич Гринько
 * E-mail: rsgrinko@gmail.com
 * Сайт: https://it-stories.ru
 * @author rsgrinko@gmail.com
 */
class CCron
{
    /**
     * @var object $DB Объект базы данных
     */
    private static $DB;

    /**
     * @var string $table Имя таблицы с заданиями
     */
    private static $table;

    /**
     * Инициализация класса и задание начальных параметров
     *
     * @param object $DB Объект базы данных
     * @param string $table Имя таблицы с заданиями
     */
    public static function init($DB, $table = 'cron')
    {
        self::$DB = $DB;
        self::$table = $table;
    }

    /**
     * Обновление времени последнего запуска крона
     */
    public static function cronUpdate()
    {
        file_put_contents(DIR . '/cron.run', time());
        return;
    }

    /**
     * Проверка запущен ли крон
     *
     * @return bool Флаг
     */
    public static function cron_is_run()
    {
        if (!file_exists(DIR . '/cron.run')) {
            CEvents::add('Ошибка в работе CRON - время последнего запуска превысило таймаут!', 'warning', 'cron');
            return false;
        }

        $last_run = file_get_contents(DIR . '/cron.run');
        if ($last_run < (time() - 120)) {
            if ($last_run != '0') {
                file_put_contents(DIR . '/cron.run', '0');
                CEvents::add('Ошибка в работе CRON - время последнего запуска превысило таймаут!', 'warning', 'cron');
            }
            return false;
        } else {
            return true;
        }

    }

    /**
     * Считает количество заданий
     *
     * @return int|bool Количество заданий или false
     */
    public static function count_tasks()
    {
        $result = self::$DB->getItems(self::$table, array('id' => '>0'));

        if ($result) {
            return count($result);
        } else {
            return false;
        }
    }

    /**
     * Получение задания по его id
     *
     * @param int $id
     * @return mixed
     */
    public static function getTask($id)
    {
        $res = self::$DB->query('SELECT * FROM `' . self::$table . '` WHERE id=\'' . $id . '\'');
        $res = $res[0];
        return $res;
    }

    /**
     * Добавление нового задания
     * @param array $fields Свойства задания
     * @return bool
     */
    public static function addTask($fields)
    {
        if (isset($fields['command']) and !empty($fields['command'])) {
            $fields['command'] = base64_encode($fields['command']);
        }
        self::$DB->addItem(self::$table, $fields);
        return true;
    }

    /**
     * Изменение задания по id
     *
     * @param int $id
     * @param array $fields
     * @return bool
     */
    public static function updateTask($id, $fields)
    {
        if (isset($fields['command']) and !empty($fields['command'])) {
            $fields['command'] = base64_encode($fields['command']);
        }
        self::$DB->update(self::$table, array('id' => $id), $fields);
        return true;
    }

    /**
     * Получение всех заданий
     *
     * @param int $limit Ограничение по количеству
     * @param string $sort Сортировка по (asc, desc)
     * @return mixed
     */
    public static function getTasks($limit = 10, $sort = 'ASC')
    {
        $res = self::$DB->query('SELECT * FROM `' . self::$table . '` ORDER BY `id` ' . $sort . ' LIMIT ' . $limit);
        return $res;
    }

    /**
     * Запуск задания на выполнение
     *
     * @param int $id
     * @return bool
     */
    public static function runTask($id)
    {
        $res = self::$DB->query('SELECT * FROM `' . self::$table . '` WHERE id=\'' . $id . '\'');
        $res = $res[0];

        try {
            ob_start();
            eval(base64_decode($res['command']));
            $result = ob_get_clean();
            self::$DB->update(self::$table, array('id' => $id), array('last_run' => time()));
            CEvents::add('Выполнено задание с ID: ' . $id . '.' . (!empty($result) ? "\n" . 'Результат выполнения:' . "\n" . '<code>' . $result . '</code>' : ''), 'success', 'cron');
            return true;

        } catch (ParseError $p) {
            ob_clean();
            CEvents::add('Ошибка синтаксиса задания с ID ' . $id . ': <code>' . $p->getMessage() . '</code>', 'warning', 'cron');
            return false;

        } catch (Throwable $e) {
            ob_clean();
            CEvents::add('Фатальная ошибка при выполнении задания с ID ' . $id, 'warning', 'cron');
            return false;
        }
        return true;

    }

    /**
     * Удаление задания по id
     *
     * @param int $id
     * @return bool
     */
    public static function removeTask($id)
    {
        if (!isset($id) or empty($id)) return false;
        $res = self::$DB->remove(self::$table, array('id' => $id));
        CEvents::add('Задание с ID: ' . $id . ' было удалено', 'info', 'cron');
        return true;
    }


    /**
     * Выполнение всех активных заданий
     *
     * @return bool Флаг успеха
     */
    public static function handler()
    {
        $res = self::$DB->query('SELECT * FROM `' . self::$table . '` WHERE active=\'Y\' ORDER BY `id` ASC');
        if (!$res) {
            return false;
        }
        foreach ($res as $task):
            if ($task['last_run'] < (time() - $task['period'])) {
                self::runTask($task['id']);
            }

        endforeach;
        return true;
    }

}