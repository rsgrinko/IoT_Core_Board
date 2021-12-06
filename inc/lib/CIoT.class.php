<?php
/**
*	Класс для работы с контроллером
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*	Сайт: https://it-stories.ru
*/
	
class CIoT {
	public static $DB;
	private static $class_version = '1.0.1';
	private static $class_author = 'Roman S Grinko (rsgrinko@gmail.com)';
	private static $class_description = 'Класс для работы с контроллером';
	
	public static function classinfo(){
		$result = [];
		$result['VERSION'] = self::$class_version;
		$result['AUTHOR'] = self::$class_author;
		$result['DESCRIPTION'] = self::$class_description;
		return $result;
	}

	/**
	 * Сохранение объекта базы данных
	 * 
	 * @param object $DB
	 */
	public static function init($DB):void
	{
		self::$DB = $DB;
	}
	

	/**
	 * Проверка устройства на существование
	 * 
	 * @param string $mac
	 * @return bool
	 */
	public static function isDeviceExists($mac):bool
	{
		$result = self::$DB->query('SELECT id FROM devices WHERE mac="'.$mac.'"');
		if($result) {
			return true;
		} else {
			return false;
		}
	}
	

	/**
	 * Получение идентификатора устройства по мак адресу
     *
     * @param string $mac
	 */
	public static function getDeviceId($mac)
	{
		$result = self::$DB->query('SELECT id FROM devices WHERE mac="'.$mac.'"');
		if($result) {
			return $result[0]['id'];
		} else {
			return false;
		}
	}

    /**
     * Добавление устройства в базу
     *
     * @param $mac
     * @param $chipid
     * @param $hw
     * @param $fw
     */
	public static function addDevice($mac, $chipid, $hw, $fw):void {
		self::$DB->addItem('devices', array('mac' => $mac, 'chipid' => $chipid, 'hw' => $hw, 'fw' => $fw, 'user' => 'SYSTEM', 'time' => time(), 'last_active' => time()));
		$deviceId = self::getDeviceId($mac);
		self::$DB->addItem('relays', array('device' => $deviceId, 'relay1' => '0', 'relay2' => '0', 'relay3' => '0', 'relay4' => '0'));
		self::$DB->addItem('configs', array('device' => $deviceId, 'ds_resulution' => '9', 'sending_interval' => '2000'));
		return;
	}

    /**
     * Обновление информации об устройстве
     *
     * @param $deviceId
     * @param $fw
     */
	public static function updateDeviceInfo($deviceId, $fw):void{
		self::$DB->update('devices', array('id' => $deviceId), array('fw' => $fw, 'last_active' => time()));
		return;
	}


    /**
     * Добавление показаний датчика DS18B20 в базу
     *
     * @param $deviceId
     * @param $sensor
     * @param $value
     */
	public static function addDallasData($deviceId, $sensor, $value):void {
		if($value == '-127.00' or  $value == '85.00' or $value == 'nan') { return; }
		$result = self::$DB->addItem('sensors', array('device' => $deviceId, 'sensor' => $sensor, 'value' => $value, 'date' => date("Y-m-d H:i:00"), 'time' => time()));
		
		$delTime = time() - 3600*24; // удалять старые записи показаний
		self::$DB->query('DELETE FROM sensors WHERE device="'.$deviceId.'" and time<'.$delTime.'');
		return;
	}

    /**
     * Получение состояний релейных выходов устройства
     *
     * @param int $id Идентификатор устройства
     * @return array
     */
	public static function getRelaysState($id){
		$result = self::$DB->query('SELECT * FROM relays WHERE device="'.$id.'"');
		$arResult = [];
		$arResult[0] = $result[0]['relay1'];
		$arResult[1] = $result[0]['relay2'];
		$arResult[2] = $result[0]['relay3'];
		$arResult[3] = $result[0]['relay4'];
		return $arResult;
	}

    /**
     * Установка состояния релейного выхода устройства
     *
     * @param $deviceId
     * @param $relay
     * @param $state
     */
	public static function setRelayState($deviceId, $relay, $state):void{
		self::$DB->update('relays', array('device' => $deviceId), array('relay'.$relay => $state ? '1' : '0'));
		return;
	}

    /**
     * Получаем количество датчиков устройства
     *
     * @param $deviceId
     * @return int
     */
	public static function getCountDallas($deviceId):int{
		$res = self::$DB->query('SELECT DISTINCT sensor FROM sensors WHERE device = "'.$deviceId.'"');
		
		if($res) {
			return count($res);
		} else {
			return 0;
		}
	}

    /**
     * Получение последнюю актуальную температуру датчика DS18B20 из базы
     *
     * @param $deviceId
     * @param $ds
     * @return mixed
     */
	public static function getDallasData($deviceId, $ds){
		$result = self::$DB->query('SELECT * FROM sensors WHERE device="'.$deviceId.'" and sensor="'.$ds.'" ORDER BY id DESC LIMIT 1');
		return $result[0];
	}

    /**
     * Получение последнюю актуальную температуру датчика DTH из базы
     *
     * @param $deviceId
     * @return array|false
     */
	public static function getDTHData($deviceId){
		$result = self::$DB->query('SELECT * FROM sensors WHERE device="'.$deviceId.'" and sensor="dth_t" ORDER BY id DESC LIMIT 1');
		$result2 = self::$DB->query('SELECT * FROM sensors WHERE device="'.$deviceId.'" and sensor="dth_h" ORDER BY id DESC LIMIT 1');
		if($result and $result2){
			return array('dht_t' => $result[0], 'dht_h' => $result2[0]);
		} else {
			return false;
		}
	}

    /**
     * Проверка датчика DS18B20 на существование
     *
     * @param $deviceId
     * @param $ds
     * @return bool
     */
	public static function isDallasFound($deviceId, $ds){
		$result = self::$DB->query('SELECT * FROM sensors WHERE device="'.$deviceId.'" and sensor="ds'.$ds.'" ORDER BY id DESC LIMIT 1');
		if($result){
			return true;
		} else {
			return false;
		}
	}

    /**
     * Получаем показания со всех 10 датчиков DS18B20
     *
     * @param $deviceId
     * @return array
     */
	public static function getDallasArrData($deviceId) {
		$count = self::getCountDallas($deviceId);
		$res = self::$DB->query('SELECT DISTINCT sensor, value, time, id FROM sensors WHERE device="'.$deviceId.'" ORDER BY id DESC LIMIT 10');
		$result = [];
		
		if($res){
			for($i=0; $i<$count; $i++){
				$result[] = $res[$i];
			}
			return array_reverse($result);
		} else {
			return [];
		}
	}

    /**
     * Получаем массив с данными об устройстве
     *
     * @param $deviceId
     * @return array|bool
     */
	public static function getDevice($deviceId) {
		$res = self::$DB->query('SELECT * FROM devices WHERE id="'.$deviceId.'"');
		if($res){
			return $res[0];
		} else {
			return false;
		}
	}

	/**
     * Получаем массив с данными об устройствах
     *
     * @param string $limit
     * @param string $sort
     * @return array|bool
     */
	public static function getDevices($limit = 10, $sort = 'ASC') {
		$res = self::$DB->query('SELECT * FROM devices ORDER BY `id` '.$sort.' LIMIT '.$limit);
		if($res){
			return $res;
		} else {
			return false;
		}
	}

	/**
     * Получаем количество устройств
     *
     * @return int
     */
	public static function getCountDevices():int {
		$res = self::$DB->query('SELECT id FROM devices');
		if($res){
			return count($res);
		} else {
			return 0;
		}
	}

    /**
     * Получаем массив с показаниями датчика для построения графика
     *
     * @param $deviceId
     * @param $sensor
     * @return array
     */
	public static function getPlotDallasValues($deviceId, $sensor) {
		$result = [];
		/*$res = self::$DB->query('SELECT id, device, sensor, value, time FROM sensors WHERE device="'.$deviceId.'" and sensor="ds'.$sensor.'" AND (time % 300) = 0 AND time > '.(time()-86400*24).' ORDER BY id DESC LIMIT 100');
		//SELECT id, device, sensor, value, time FROM sensors WHERE device="'.$deviceId.'" and sensor="ds'.$sensor.'" AND (time % 300) = 0 AND time > '.(time()-86400*24).' ORDER BY id DESC LIMIT 100*/
		
		$res = self::$DB->query('SELECT * FROM sensors WHERE mod(minute(date),5) = 0 AND sensor="'.$sensor.'" AND device="'.$deviceId.'" GROUP BY date ORDER BY id DESC LIMIT 1000');
		if($res) {
			//return $res;
			return array_reverse($res);
		} else {
			return [];
		}
	}


    /**
     * Получение списка прошивок
     *
     * @param false $hw
     * @param int $limit
     * @param string $sort
     * @return array
     */
	public static function getFirmwareList($hw = false, $limit = 10, $sort = 'DESC') {
		$result = [];
		if(!$hw) {
			$res = self::$DB->query('SELECT * FROM firmware ORDER BY `id` '.$sort.' LIMIT '.$limit);
		} else {
			$res = self::$DB->query('SELECT * FROM firmware ORDER BY `id` '.$sort.' WHERE hw="'.$hw.'" LIMIT '.$limit);
		}	
			
		if($res) {
			$result = $res;
		} else {
			$result = [];
		}
		
		
		return $result;
	}

    /**
     * Получаем количество прошивок
     *
     * @param $hw
     * @return int
     */
	public static function countFirmwareList($hw = false) {
		$result = 0;
		if(!$hw) {
			$res = self::$DB->query('SELECT id FROM firmware');
		} else {
			$res = self::$DB->query('SELECT id FROM firmware WHERE hw="'.$hw.'"');
		}
		if($res) {
			$result = count($res);
		} else {
			$result = 0;
		}
		return $result;
	}


    /**
     * Получение конфигурации устройства
     *
     * @param $deviceId
     * @return array
     */
	public static function getBoardConfig($deviceId){
		$res = self::$DB->query('SELECT * FROM configs WHERE device="'.$deviceId.'"');
		
		if($res) {
			$result = $res[0];
		} else {
			$result = [];
		}
		
		return $result;
	}

    /**
     * Формируем строку с датой измерения для графика
     *
     * @param int $time
     * @return false|string
     */
	public static function plotDate($time = 0) {
		if($time == 0) {
			$time = time();
		}
		$months_name = [
			'января', 'февраля', 'марта',
			'апреля', 'мая', 'июня',
			'июля', 'августа', 'сентября',
			'октября', 'ноября', 'декабря'
		];
		$result = date('d '.$months_name[date('n') - 1].' H:i', $time);
		
		return $result;
	}

    /**
     * Инициализация скрипта для отрисовки графиков
     */
	public static function initHighcharts():void{
		echo '<script type="text/javascript">
		    Highcharts.setOptions({
                lang: {
                    loading: "Загрузка...",
                    exportButtonTitle: "Экспорт",
                    printButtonTitle: "Печать",
                    rangeSelectorFrom: "С",
                    rangeSelectorTo: "По",
                    rangeSelectorZoom: "Период",
                    downloadPNG: "Скачать PNG",
                    downloadJPEG: "Скачать JPEG",
                    downloadPDF: "Скачать PDF",
                    downloadSVG: "Скачать SVG",
                    printChart: "Напечатать график"
                }
        });	</script>';
        return;
	}

    /**
     * Отрисовка графика
     *
     * @param $sensor
     * @param $arValues
     */
	public static function drawHighcharts($sensor, $arValues){
		$categories = '';
		$temp = '';
		$temps = '';
		$categories = '';		
		
		foreach($arValues as $k => $value){
			//$dt = date("d.m H:i:s", $value['time']);
			$dt = self::plotDate($value['time']);
			$categories = $categories.'\''.$dt.'\',';
			$temps = $temps.$value['value'].',';
		}
		
		$temps = substr($temps,0,-1);
		$categories = substr($categories,0,-1);
		
		echo '
		<script>
			Highcharts.chart("plotContainer_'.$sensor.'", {
			    chart: {
			        type: "spline",
			        scrollPositionX: 1
			    },
			    title: {
			        text: "Показания температурного датчика (ID:'.$sensor.')"
			    },
			    subtitle: {
			        text: "IoT Core System"
			    },
			    xAxis: {
			        categories: ['.$categories.']
			    },
			    yAxis: {
			        title: {
			            text: "Температура (°C)"
			        }
			    },
			    plotOptions: {
				    
			        line: {
			            dataLabels: {
			                enabled: true
			            },
			            enableMouseTracking: true
			        }
			    },
			        tooltip: {
				    shared: true,
			        crosshairs: true,
			        pointFormat: "Температура: {point.y:.2f} С"
			    },
			    series: [{
				    
			        name: "Показания температуры (ID:<?php echo $sensor;?>)",
			        data: ['.$temps.']
			    }]
			});
		</script>';
		return;
	}
}