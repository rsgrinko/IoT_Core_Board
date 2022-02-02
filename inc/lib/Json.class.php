<?php
/**
 * Класс для JSON представления
 */

 class Json {
     public static function create($assocArray) {
         return json_encode($assocArray, JSON_UNESCAPED_UNICODE);
     }
 }