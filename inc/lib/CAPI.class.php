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
        return $result;
    }
}