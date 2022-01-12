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
        return $USER;
    }
}