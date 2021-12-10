<?php

namespace ScApp\Helpers;

/**
 * Класс обработки ошибок
 */
class ScException extends \Exception
{

    /**
     * Ошибка входных данных
     */
    const ERROR_INPUT_DATA = 10;

    /**
     * Обибки работы с логированием webApi
     */
    public const ERROR_BRUTEFORCE = 12;

    /**
     * Ошибка авторизации
     */
    const ERROR_AUTH = 20;

    /**
     * Ошибка подключения контроллера
     */
    const ERROR_GET_CONTROLLER = 30;


    /**
     * Ошибка ядра битрикса
     */
    const ERROR_BITRIX = 60;

    /**
     * Объект уже существует
     */
    const ERROR_ALREADY_EXISTS = 70;
    /**
     * Объект уже существует
     * Например - сервис уже отключен
     */
    const ERROR_ALREADY_NO_EXISTS = 80;

    /**
     * Ошибка в имени дополнительного пользователя
     */
    const ERROR_DOP_USER_LOGIN = 80;

    /**
     * Ошибка доступ запрещен
     */
    const ERROR_ACCESS_IS_DENIED = 90;

    /**
     * Ошибка изменения тарификации Gb
     */
    const ERROR_CHANGE_GB_TARIF = 100;

    /**
     * Ошибка не существует объект
     */
    const ERROR_NO_EXISTS = 110;
    /**
     * ################### Ошибки санитизации ##################
     */

    const ERROR_WSRQ_QUERY = 120;

    /**
     *  Ошибка проверки логина
     */
    const ERROR_SANITIZE_LOGIN_IS_EMPTY = 1000;

    /**
     *  Ошибка проверки логина
     */
    const ERROR_SANITIZE_LOGIN_A_Z = 1010;

    /**
     *  Ошибка проверки логина логин содержит sc111
     */
    const ERROR_SANITIZE_LOGIN_SC11111 = 1011;

    /**
     *  Ошибка проверки логина логин существует
     */
    const ERROR_SANITIZE_LOGIN_EXIST = 1012;

    /**
     *  Ошибка проверки логина - он более 20 символво
     */
    const ERROR_SANITIZE_LOGIN_TOO_LONG = 1020;

    /**
     *  Ошибка проверки логина - он менее 3 символов
     */
    const ERROR_SANITIZE_LOGIN_TOO_MINI = 1021;

    /**
     *  Ошибка проверки логина - он менее 8 символов
     */
    const ERROR_SANITIZE_LOGIN_LESS_8_CHARACTERS = 1021;

    /**
     *  Логин поьзователя не может начинаться с sc
     */
    const ERROR_SANITIZE_LOGIN_STARTS_SC = 1030;

    /**
     *  В пароле используются специальные символы
     */
    const ERROR_SANITIZE_PASS_HAS_SPECIAL_CHARS = 1040;

    /**
     *  В пароле нет маленьких англиских букв
     */
    const ERROR_SANITIZE_PASS_HASNT_SMALL_LETTER = 1050;

    /**
     *  В пароле нет заглавных англиских букв
     */
    const ERROR_SANITIZE_PASS_HASNT_CAPITAL_LETTER = 1060;

    /**
     *  В пароле нет цифр
     */
    const ERROR_SANITIZE_PASS_HASNT_NUMBERS = 1070;

    /**
     *  ID пользователя не может быть пустым
     */
    const ERROR_SANITIZE_USER_ID = 1080;

    /**
     *  Телефон пользователя содержит недопустимые символы
     */
    const ERROR_SANITIZE_USER_PHONE = 1090;

    /**
     *  Email не соответствует стандарту
     */
    const ERROR_SANITIZE_EMAIL = 1100;

    /**
     *  Число месяцев оказалось не целым числом
     */
    const ERROR_SANITIZE_PAY_PERIOD = 1110;

    /**
     *  Ошибка проверки суммы платежа
     */
    const ERROR_SANITIZE_PAY_SUM = 1120;

    /**
     *  Ошибка проверки наименования платежной системы
     */
    const ERROR_SANITIZE_PAY_SYSTEM_NAME = 1130;

    /**
     *  Ошибка проверки идентификатора счета
     */
    const ERROR_SANITIZE_ORDER_ID = 1140;

    /**
     *  Ошибка проверки идентификатора акта
     */
    const ERROR_SANITIZE_ACT_ID = 1141;

    /**
     *  Ошибка проверки идентификатора счета-фактуры
     */
    const ERROR_SANITIZE_BILL_OF_PARCEL_ID = 1142;

    /**
     *  Ошибка проверки названия метода объекта
     */
    const ERROR_SANITIZE_METHOD_NAME = 1150;

    /**
     *  Ошибка проверки идентификатора тарифа
     */
    const ERROR_SANITIZE_TARIF_ID = 1160;

    /**
     *  Ошибка проверки СТРОКА
     */
    const ERROR_SANITIZE_UNIVERSAL_STRING = 1170;

    /**
     *  Ошибка проверки СТРОКА
     */
    const ERROR_SANITIZE_UNIVERSAL_NUMBER = 1180;

    /**
     *  Ошибка проверки ДАТА
     */
    const ERROR_SANITIZE_UNIVERSAL_DATE = 1190;
    /**
     *  Ошибка проверки ДАТА
     */
    const ERROR_SANITIZE_DATE = 1195;

    /**
     *  Ошибка проверки общая
     */
    const ERROR_SANITIZE_COMMON = 1196;

    /**
     *  Ошибка проверки по допустимому списку значений
     */
    const ERROR_SANITIZE_BY_LIST = 1197;

    /**
     *  Ошибка проверки на дубликат
     */
    const ERROR_SANITIZE_DUPLICATE = 1198;

    /**
     * Ошибка проверки на GUID
     */
    public const ERROR_SANITIZE_GUID = 1199;

    /**
     * Ошибка: Дополнительный пользователь не существует
     */
    const ERROR_DOP_USER_EXISTS = 1200;

    /**
     * Ошибка: Запись не существует
     */
    const ERROR_RECORD_NOT_EXISTS = 1200;

    /**
     * Ошибка: Нельзя делать продление в оплаченный период
     */
    const ERROR_RENEWAL_PAY_PERIOD = 1300;

    /**
     * Ошибка: Не переданы данные
     */
    const ERROR_RENEWAL_EMPTY_PERIOD = 1400;

    /**
     * Ошибка: Не переданы данные
     */
    const ERROR_FREE_PERIOD_DATE = 1400;

    /**
     * Ошибка: Не верный аргумент функции
     */
    const ERROR_ARGUMENTS_FUNCTION = 1500;

    /**
     * Ошибка: Не верный аргумент функции
     */
    const ERROR_MEMCACHE_CONNECT = 1600;

    /**
     * Ошибка: Почновый шаблон не существует
     */
    const ERROR_MAIL_TEMPLATE_DONT_EXISTS = 1700;

    /**
     * Ошибка: ID не найден
     */
    const ERROR_US_ID_NOTFOUND = 1800;

    /**
     * Ошибка работы с файлами пользователя
     */
    const ERROR_USER_FILE = 1900;

    /**
     * Ошибка работы с email пользователя
     */
    const ERROR_EMAIL_DATA = 2000;

    /**
     * Ошибка авторизации на сервере почты
     */
    public const ERROR_EMAIL_AUTH = 2001;

    /**
     * Ошибка WSRQ - "Пользователь уже существует"
     */
    const ERROR_WSRQ_USER_EXIST = 3000;

    /**
     * Ошибка WSRQ - "Пользователь не существует"
     */
    const ERROR_WSRQ_USER_NO_EXIST = 3001;
    /**
     * Ошибка WSRQ - "Дополнительный пользователь не существует"
     */
    const ERROR_WSRQ_DOP_USER_NO_EXIST = 3002;

    /**
     * Ошибка WSRQ - Нет файла сертификата
     */
    public const ERROR_WSRQ_NO_CERTIFICATE = 3003;
    /**
     * Ошибка WSRQ - Ошибка подключения к WSIS
     */
    public const ERROR_WSRQ_WSIS_CONNECTION = 3004;
    /**
     * Ошибка WSRQ - Версия DBRM не корректна
     */
    public const ERROR_WSRQ_DBRM_VERSION = 3005;

    /**
     * Ошибка работы с базами данных
     */
    public const ERROR_DATA_BASE = 4000;

    /**
     * Ошибка работы с безопасными папками
     */
    public const ERROR_SECURE_FOLDER = 5000;


    /**
     * Ошибка ID быстрой опции
     */
    public const ERROR_QUICK_OPTION_ID = 2200;

    /**
     * Ошибка: Несуществующее ID роли
     */
    public const ERROR_ROLE_ID = 2300;

    /**
     * Общие ошибки работы с внешнием источниками
     */
    public const ERROR_EXCHANGE = 2500;

    /**
     * Общие ошибки работы с sms
     */
    public const ERROR_SMS = 3000;

    /**
     * Общие ошибки работы с мультиаккаунтом
     */
    public const ERROR_MULTIACC = 6000;

    /**
     * Ошибки работы с логированием webApi
     */
    public const ERROR_WEB_API = 7000;

    /**
     * Ошибка работы webApi, попытка изменения опций по клиенту из Казахстана
     */
    const ERROR_WEB_API_KZ_CHANGE_OPTION = 7100;
    /**
     * Ошибка работы webApi, попытка изменения параметров SQL по клиенту из Казахстана
     */
    const ERROR_WEB_API_KZ_CHANGE_SQL = 7101;
    /**
     * Ошибка работы webApi, попытка создания пользователя по клиенту из Казахстана
     */
    const ERROR_WEB_API_KZ_CREATE_DOP_USER = 7102;
    /**
     * Ошибка работы webApi, попытка удаления пользователя по клиенту из Казахстана
     */
    const ERROR_WEB_API_KZ_DELETE_DOP_USER = 7103;
    /**
     * Ошибка работы webApi, попытка смены тарифа по клиенту из Казахстана
     */
    const ERROR_WEB_API_KZ_CHANGE_TARIFF = 7104;
    /**
     * Ошибка работы webApi, попытка смены тарифа за ГБ по клиенту из Казахстана
     */
    const ERROR_WEB_API_KZ_CHANGE_TARIFF_GB = 7105;
    /**
     * Ошибка работы webApi, попытка изменения ГБ по клиенту из Казахстана
     */
    const ERROR_WEB_API_KZ_CHANGE_GB = 7106;
    /**
     * Ошибка работы webApi, попытка подключения SQL по клиенту из Казахстана
     */
    const ERROR_WEB_API_KZ_CONNECT_SQL = 7107;
    /**
     * Ошибка работы webApi, попытка создания счета по клиенту из Казахстана
     */
    const ERROR_WEB_API_KZ_CREATE_ORDER = 7110;

    /**
     * Неопределенная ошибка работы с BPM
     */
    public const ERROR_BPM = 8000;

    /**
     * Ошибка работы с BPM - нет данных сокращенного каталога
     */
    public const ERROR_BPM_BRIEF_SERVICE_LIST = 8001;

    /**
     * Ошибка работы с BPM - некорректная тема тикета
     */
    public const ERROR_BPM_SUMMARY = 8002;

    /**
     * Ошибка работы с BPM - некорректное описание тикета
     */
    public const ERROR_BPM_DESCRIPTION = 8003;

    /**
     * Ошибка работы с BPM - некорректное значение идентификатора конфигурации
     */
    public const ERROR_BPM_CONFIGURATION = 8004;

    /**
     * Ошибка работы с BPM - некорректно задана дата создания
     */
    public const ERROR_BPM_TICKET_DATE = 8005;

    /**
     * Ошибка работы с BPM - ошибка создания тикета
     */
    public const ERROR_BPM_TICKET_CREATE = 8006;

    /**
     * Ошибка работы с BPM - ошибка состояния тикета
     */
    public const ERROR_BPM_TICKET_STATE = 8007;

    /**
     * Ошибка работы с BPM - ошибка идентификатора тикета
     */
    public const ERROR_BPM_TICKET_IDENTIFIER = 8008;

    /**
     * Ошибка работы с BPM - ошибка оценки тикета
     */
    public const ERROR_BPM_TICKET_EVALUATE = 8009;

    /**
     * Ошибка работы с BPM - ошибка ответа пользователя по тикету
     */
    public const ERROR_BPM_TICKET_REPLY = 8010;

    /**
     * Ошибка работы с BPM - ошибка статуса тикета
     */
    public const ERROR_BPM_TICKET_STATUS = 8011;

    /**
     * Ошибка работы с BPM - ошибка получения файлов
     */
    public const ERROR_BPM_TICKET_ATTACHMENT = 8012;

    /**
     * Ошибка работы с BPM - ошибка подтверждения тикета
     */
    public const ERROR_BPM_TICKET_CONFIRMATION = 8013;

    /**
     * Ошибка работы с BPM - ошибка работы с нотификацией
     */
    public const ERROR_BPM_TICKET_NOTIFICATION = 8014;

    /**
     * Ошибка работы с BPM - ошибка работы с источниками тикетов
     */
    public const ERROR_BPM_TICKET_CHANNEL = 8015;

    /**
     * Ошибка работы с BPM - ошибка отмены тикета
     */
    public const ERROR_BPM_TICKET_CANCEL = 8016;

    /**
     * Ошибка работы с BPM - ошибка идентификации причины отмены тикета
     */
    public const ERROR_BPM_CAUSE_IDENTIFIER = 8017;

    /**
     * Ошибка имени группы настраиваемой пользовательской настройки
     */
    public const ERROR_CUSTOMIZABLE_USER_SETTING_GROUP = 8100;

    /**
     * Ошибка работы с BPM - некорректное значение типа создания тикета
     */
    public const ERROR_BPM_TYPE_REQUEST_CREATE_TICKET = 8016;

    /**
     * Ошибка работы с BPM - некорректное значение типа тикета для типизированных тикетов
     */
    public const ERROR_BPM_SERVICE_PAIR_KEY = 8017;

    /**
     * Ошибка работы с BPM - ошибка получения списка тикетов
     */
    public const ERROR_BPM_GET_TICKET_LIST = 8018;

    /**
     * Ошибка работы с BPM - ошибка доступа к методам ОКК
     */
    public const ERROR_BPM_ERROR_PERMISSION_OKK = 8019;

    /**
     * Ошибка работы с BPM - ошибка работы с видом операции тикета
     */
    public const ERROR_BPM_TICKET_OPERATION_TYPE = 8020;

    /**
     * Ошибка работы с BPM - ошибка работы с типом аккаунта тикета
     */
    public const ERROR_BPM_TICKET_ACCOUNT_TYPE = 8021;

    /**
     * Ошибка работы с BPM - ошибка при получении актуальных нотификаций
     */
    public const ERROR_BPM_ACTUAL_NOTIFICATIONS = 8022;



    /**
     * Ошибка обращения к сервису moneta.ru
     */
    const ERROR_MONETA_REQUERS = 9000;

    /**
     * Ошибка создания недопустимого счета
     */
    const ERROR_CREATING_AN_INVALID_ORDER = 9100;

    /**
     * Ошибки работы с SOAP сервисами
     */
    const ERROR_SOAP = 10000;


    /**
     * Ошибки работы с комплектными тарифами
     */
    const ERROR_KIT = 11000;

    /**
     * Ошибки работы с комплектными тарифами, когда не удалось найти текущий комплект
     */
    const ERROR_KIT_NOT_SEARCH_CURRENT = 11020;

    /**
     * Ошибки работы с комплектными тарифами - тариф пользователя не комплектный
     */
    const ERROR_KIT_NOT_HAS_KIT = 11010;
    /**
     * Ошибки работы с комплектными тарифами - несколько комплектов имеют признак базового для ЛК
     */
    const ERROR_KIT_SEVERAL_BASIC_KIT = 11020;
    /**
     * Ошибки работы с комплектными тарифами - не указан базовый для ЛК комплект
     */
    const ERROR_KIT_NOT_BASIC_KIT = 11030;
    /**
     * Ошибки работы со счетами - создание счета запрещено
     */
    const ERROR_ORDER_CREATION_PROHIBITED = 12000;

    /**
     * Возвращает обработанный callTrace текущего исключения
     *
     * @return array
     */
    function generateCallTrace()
    {
        $trace = explode("\n", $this->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace = array_reverse($trace);
        array_shift($trace); // remove {main}
        // array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = [];
        for ($i = 0; $i < $length; $i++) {
            $result[] = ($i + 1) . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }
        return $result;
    }


    /**
     * Единый метод выброса исключения об устаревании метода
     *
     * @param string|null $method Наименование метода
     *
     * @throws ScException
     */
    public static function throwExceptionOldMethod(?string $method = null)
    {
        if (strlen(trim($method)) > 0) {
            $text = 'Метод ' . $method . ' устарел. Обновите приложение или обратитесь по другому каналу';
        } else {
            $text = 'Метод устарел. Обновите приложение или обратитесь по другому каналу';
        }
        throw new ScException(
            $text,
            ScException::ERROR_INPUT_DATA
        );
    }
}
