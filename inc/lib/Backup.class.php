<?php

class Backup {
    private string $backupFolder;// куда будут сохранятся файлы
    private string $backupName;// имя архива
    private string $dir;// что бэкапим
    private int $delayDelete;    // время жизни архива (в секундах)

    private string $dbHost;
    private string $dbHser;
    private string $dbPassword;
    private string $dbName;

        /**
         * @param string $backupFolder
         */
        public function setBackupFolder(string $backupFolder): void
        {
            $this->backupFolder = $backupFolder;
        }

        /**
         * @param string $backupName
         */
        public function setBackupName(string $backupName): void
        {
            $this->backupName = $backupName;
        }

        /**
         * @param string $dir
         */
        public function setDir(string $dir): void
        {
            $this->dir = $dir;
        }

        /**
         * @param int $delayDelete
         */
        public function setDelayDelete(int $delayDelete): void
        {
            $this->delayDelete = $delayDelete;
        }

        /**
         * @param string $dbHost
         */
        public function setDbHost(string $dbHost): void
        {
            $this->dbHost = $dbHost;
        }

        /**
         * @param string $dbHser
         */
        public function setDbHser(string $dbHser): void
        {
            $this->dbHser = $dbHser;
        }

        /**
         * @param string $dbPassword
         */
        public function setDbPassword(string $dbPassword): void
        {
            $this->dbPassword = $dbPassword;
        }

        /**
         * @param string $dbName
         */
        public function setDbName(string $dbName): void
        {
            $this->dbName = $dbName;
        }


public function __construct() {

}


public function backupFiles($backup_folder, $backup_name, $dir) {
    $fullFileName = $backup_folder . '/' . $backup_name . '.tar.gz';
    shell_exec("tar -cvf " . $fullFileName . " " . $dir . "/* ");
    return $fullFileName;
}

public function backupDB($backup_folder, $backup_name) {
    $fullFileName = $backup_folder . '/' . $backup_name . '.sql';
    $command = 'mysqldump -h' . $db_host . ' -u' . $db_user . ' -p' . $db_password . ' ' . $db_name . ' > ' . $fullFileName;
    shell_exec($command);
    return $fullFileName;
}

public function deleteOldArchives($backup_folder, $delay_delete) {
    $this_time = time();
    $files = glob($backup_folder . "/*.tar.gz*");
    $deleted = array();
    foreach ($files as $file) {
        if ($this_time - filemtime($file) > $delay_delete) {
            array_push($deleted, $file);
            unlink($file);
        }
    }
    return $deleted;
}


public function createBackup() {

}

}
$mail_to = 'my_email@example.com';
$mail_subject = 'Site backup';
$mail_message = '';
$mail_headers = 'MIME-Version: 1.0' . "\r\n";
$mail_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$mail_headers .= 'To: me <my_email@example.com>' . "\r\n";
$mail_headers .= 'From: my_site <info@example.com>' . "\r\n";










$start = microtime(true);    // запускаем таймер

$deleteOld = deleteOldArchives($backup_folder, $delay_delete);    // удаляем старые архивы
$doBackupFiles = backupFiles($backup_folder, $backup_name, $dir);    // делаем бэкап файлов
$doBackupDB = backupDB($backup_folder, $backup_name);    // и базы данных

// добавляем в письмо отчеты
if ($doBackupFiles) {
    $mail_message .= 'site backuped successfully<br/>';
    $mail_message .= 'Files: ' . $doBackupFiles . '<br/>';
}

if ($doBackupDB) {
    $mail_message .= 'DB: ' . $doBackupDB . '<br/>';
}

if ($deleteOld) {
    foreach ($deleteOld as $val) {
        $mail_message .= 'File deleted: ' . $val . '<br/>';
    }
}

$time = microtime(true) - $start;     // считаем время, потраченое на выполнение скрипта
$mail_message .= 'script time: ' . $time . '<br/>';

mail($mail_to, $mail_subject, $mail_message, $mail_headers);    // и отправляем письмо