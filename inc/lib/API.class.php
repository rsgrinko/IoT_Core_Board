<?php
/**
 * Класс для предоставления API функционала
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
}