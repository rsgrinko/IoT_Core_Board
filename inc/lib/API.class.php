<?php
/**
 * Класс для предоставления API функционала
 *
 * @package IOT_CORE
 */
class API {

    public static function test() {
        return ['status' => 'ok', 'message' => 'test was completed :)'];
    }

    public static function getUserInfo() {
        global $USER;
        unset($USER['password']);
        $result = $USER;
        $result['devices'] = getUserDevices($USER['id']);
        foreach($result['devices'] as $key => $value) {
            $result['devices'][$key]['config'] = IoT::getBoardConfig($value['id']);
        }
        $result['status'] = 'ok';
        return $result;
    }

    public static function getSensorValue() {
        global $USER;
        $sensor = $_REQUEST['sensor'];
        $deviceId = $_REQUEST['deviceId'];

        $arUserDevices = getUserDevices($USER['id']);

        $canAccessToDevice = false;
        foreach($arUserDevices as $device) {
            if($device['id'] == $deviceId) {
                $canAccessToDevice = true;
                break;
            }
        }
        if (!$canAccessToDevice and $USER['access_level'] !== 'admin') {
            return ['status' => 'fail', 'message' => 'Недостаточно прав для доступа к показаниям данного устройства'];
        }

        $sensorValue = IoT::getSensorData($deviceId, $sensor)['value'];
        return ['status' => 'ok', 'deviceId' => $deviceId, 'sensor' => $sensor, 'value' => $sensorValue];
       
    }

    public static function getSensorData()
    {
        $sensor   = $_REQUEST['sensor'];
        $deviceId = $_REQUEST['deviceId'];

        $cacheId = md5('CIoT::getPlotDallasValues_' . $deviceId . '_' . $sensor);
        if (Cache::check($cacheId) and Cache::getAge($cacheId) < 300) {
            $arValues = Cache::get($cacheId);
        } else {
            $arValues = IoT::getPlotDallasValues($deviceId, $sensor);
            Cache::write($cacheId, $arValues);
        }

        return ['status' => 'ok', 'deviceId' => $deviceId, 'sensor' => $sensor, 'data' => $arValues];
    }
}