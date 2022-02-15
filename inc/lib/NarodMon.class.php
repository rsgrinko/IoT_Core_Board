<?php

/**
 * Класс для работы с проектом народного мониторинга
 */
class NarodMon
{
    private ?string $mac = null;
    private array $arData = [];

    public function __construct($mac)
    {
        $this->mac = $mac;
    }

    public function set(string $sensor, string $value)
    {
        $this->arData[$sensor] = $value;
    }

    public function send():bool
    {
        $fp = @fsockopen('tcp://narodmon.ru', 8283, $errno, $errstr);
        if(!$fp) {
            return false;
        } else {
            $package = '#'.$this->mac."\n";
            foreach($this->arData as $sensor => $value){
                $package .= '#'.$sensor.'#'.$value."\n";
            }

            fwrite($fp, $package);
            fclose($fp);
            return true;
        }

    }
}