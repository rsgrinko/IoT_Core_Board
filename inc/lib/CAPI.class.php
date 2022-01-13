<?php
/**
 * Класс для предоставления API функционала
 */
class CAPI {

    public static function test() {
        return ['status' => 'ok', 'message' => 'test was completed :)'];
    }

    public static function getUserInfo() {
        global $USER;
        unset($USER['password']);
        $result = $USER;
        $result['devices'] = getUserDevices($USER['id']);
        foreach($result['devices'] as $key => $value) {
            $result['devices'][$key]['config'] = CIoT::getBoardConfig($value['id']);
        }
        $result['status'] = 'ok';
        return $result;
    }

    public static function getSensorValue() {
        global $USER;
        $sensor = $_REQUEST['sensor'];
        $deviceId = $_REQUEST['deviceId'];

        $arUserDevices = CIoT::getUserDevices($USER['id']);
        if (!in_array($deviceId, $arUserDevices)) {
            return ['status' => 'fail', 'message' => 'Выбранное устройство не принадлежит текущему пользователю'];
        }

        $sensorValue = CIoT::getSensorData($deviceId, $sensor);
        return ['status' => 'ok', 'deviceId' => $deviceId, 'sensor' => $sensor, 'value' => $sensorValue];
       
    }
}