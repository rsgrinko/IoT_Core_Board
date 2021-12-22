<?php
/**
*	Класс для работы с пользователями панели управления
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*	Сайт: https://it-stories.ru
*/
	
class CUser {

	/**
	 * ID текущего пользователя
	 * 
	 * @var int
	 */
	public static $id;

	/**
	 * Таблица с пользователями
	 * 
	 * @var string
	 */
	public static $table;

	/**
	 * Объект базы данных
	 * 
	 * @var object
	 */
	public static $DB;

	/**
	 * Инициализация класса
	 * 
	 * @param object $DB
	 * @param string $table
	 */
	public static function init($DB, $table = 'users'):void {
		self::$table = $table;
		self::$DB = $DB;
		if(isset($_SESSION['authorize']) and $_SESSION['authorize'] == 'Y'){
			self::$id = $_SESSION['id'];
		}
	}	
	
	/**
	 * Получение всех полей пользователя
	 * 
	 * @param int $id
	 * @return array|bool
	 */
	public static function getFields($id = false){
		if(empty($id) or $id === false){
			$id = self::$id;
		}
		
		if(is_array($id)){
			$where = $id;
		} else {
			$where = array('id' => $id);
		}
		$result = self::$DB->getItem(self::$table, $where);
		if($result) {
			return $result;
		} else {
			return false;
		}
	}
	
	/**
	 * Получение всех пользователей панели
	 * 
	 * @param int $limit
	 * @param string $sort
	 * 
	 * @return array
	 */
	public static function getUsers($limit = 10, $sort = 'ASC'){
		$res = self::$DB->query('SELECT * FROM `'.self::$table.'` ORDER BY `id` '.$sort.' LIMIT '.$limit);
		return $res;
	}

    /**
     * Проверка пользователя на онлайн
     *
     * @param $int $id
     */
    public static function isOnline($id = false) {
        if(!$id) {
            return false;
        }
        $res = self::$DB->query('SELECT last_active FROM `'.self::$table.'` WHERE id='.$id);
        $last_active = $res[0]['last_active'];
        $timeNow = time();
        if($last_active > ($timeNow - USER_ONLINE_TIME)) {
            return true;
        } else {
            return false;
        }
    }

	
	/**
	 * Выполняет регистрацию пользователя в системе
	 * 
	 * @param string $login
	 * @param string $password
	 * @param string $level
	 * @param string $name
	 * @param string $image
	 */
	public static function registration($login, $password, $email, $level = 'user', $name = '', $image = ''):void {
		self::$DB->addItem(self::$table, ['login' => $login, 'password' => $password, 'access_level' => $level, 'name' => $name,'image' => $image, 'token' => '', 'email' => $email, 'last_active' => time()]);
		$result = self::$DB->getItem(self::$table, array('login'=>$login, 'password' => $password));
		
		self::$id = $result['id'];
		$_SESSION['id'] = $result['id'];
		$_SESSION['authorize'] = 'Y';
		$_SESSION['login'] = $result['login'];
		$_SESSION['password'] = $result['password'];	
		$_SESSION['token'] = $result['token'];
		$_SESSION['user'] = $result;
		return;
		
	}
	
	/**
	 * Проверяет логин на существование
	 * 
	 * @param string $login
	 * 
	 * @return bool
	 */
	public static function isUserExists($login):bool{
		$result = self::$DB->getItem(self::$table, array('login'=>$login));
		
		if($result) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Проверка существования токена
	 * 
	 * @param string $token
	 * @return bool
	 */
	public static function isTokenExists($token):bool{
		$result = self::$DB->getItem(self::$table, array('token'=>$token));
		
		if($result) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Считает количество пользователей
	 * 
	 * @return int
	 */
	public static function countUsers():int{
		$result = self::$DB->getItems(self::$table, array('id'=>'>0'));
		
		if($result) {
			return count($result);
		} else {
			return 0;
		}
	}
		
	/**
	 * Выполняет авторизацию пользователя в системе по ID
	 * 
	 * @param int $id
	 * 
	 * @return bool
	 */
	public static function authorize($id):bool{
		$result = self::$DB->getItem(self::$table, array('id'=>$id), true);
		
		if($result) {
			self::$id = $result['id'];
			$_SESSION['id'] = $result['id'];
			$_SESSION['authorize'] = 'Y';
			$_SESSION['login'] = $result['login'];
			$_SESSION['password'] = $result['password'];	
			$_SESSION['token'] = $result['token'];
			$_SESSION['user'] = $result;
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Выполняет авторизацию пользователя в системе по логину и паролю
	 * 
	 * @param string $login
	 * @param string $password
	 * 
	 * @return bool
	 */
	public static function securityAuthorize($login, $password):bool{
		
		$result = self::$DB->getItem(self::$table, array('login'=>$login, 'password' => $password), true);
		if($result) {
			self::$id = $result['id'];
			$_SESSION['id'] = $result['id'];
			$_SESSION['authorize'] = 'Y';
			$_SESSION['login'] = $result['login'];
			$_SESSION['password'] = $result['password'];
			$_SESSION['token'] = $result['token'];
			$_SESSION['user'] = $result;	
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Проверка на пользователя
	 * 
	 * @return bool
	 */
	public static function isUser():bool {
		if(!isset($_SESSION['authorize']) or empty($_SESSION['authorize']) or $_SESSION['authorize'] !== 'Y') {
			return false;
		}
		$result = self::$DB->getItem(self::$table, array('login' => $_SESSION['login']));
		if($result) {
				if($result['password'] == $_SESSION['password']){
					self::$DB->update(self::$table, array('id' => $result['id']), array('last_active' => time()));
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

	/**
	 * Проверка на админа
	 * 
	 * @return bool
	 */
	public static function isAdmin():bool {
		if(self::isUser()) {
			if(self::getFields(self::$id)['access_level'] == 'admin') {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Метод выхода из системы
	 */
	public static function logout():void {
		$_SESSION['id'] = '';
		$_SESSION['authorize'] = '';
		$_SESSION['login'] = '';
		$_SESSION['password'] = '';
		$_SESSION['token'] = '';
		$_SESSION['user'] = '';
		return;
    }

		

}