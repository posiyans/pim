<?php
$start = microtime(true);

print_r($argv);



function mysqlDump()
{
    $start = microtime(true);

    $dd=date("Y-m-d_H:i:s");

    $lines = file('./.env');

// Осуществим проход массива и выведем содержимое в виде HTML-кода вместе с номерами строк.
    $conf = [];
    foreach ($lines as $line_num => $line) {
        $array = explode('=', $line);
        if (isset($array[1])) {
            $conf[$array['0']] = str_replace(array("\r\n", "\r", "\n"), '', strip_tags($array[1]));
        }
    }

    unlink('./database/dump.sql');


    $db = $conf['DB_DATABASE'];

//system('mysqldump --user='.$conf['DB_USERNAME'].' --password='.$conf['DB_PASSWORD'].' --host='.$conf['DB_HOST'].' '.$conf['DB_DATABASE'].' > ./dump/sql/sql-'.$dd.'.sql');
//$base=system('mysql -u root -p'.$conf['DB_PASSWORD'].' -e \'show databases;\' | grep '.$db);
//print($base);
//for ($base as $db){
//    echo $db;
//}

    //system('mysqldump --user=' . $conf['DB_USERNAME'] . ' --password=' . $conf['DB_PASSWORD'] . ' --host=' . $conf['DB_HOST'] . ' --databases $(mysql -u root -p' . $conf['DB_PASSWORD'] . ' -e \'show databases;\' | grep ' . $db . ') > ./../wp_dump/sql/sql-' . $dd . '.sql');
    system('mysqldump --user=' . $conf['DB_USERNAME'] . ' --password=' . $conf['DB_PASSWORD'] . ' --host=' . $conf['DB_HOST'] . ' --databases '.  $db. ' > ./database/dump.sql');





}



function dump($argv)
{
    $start = microtime(true);

    $dd=date("Y-m-d_H:i:s");

    $lines = file('./.env');

// Осуществим проход массива и выведем содержимое в виде HTML-кода вместе с номерами строк.
    $conf = [];
    foreach ($lines as $line_num => $line) {
        $array = explode('=', $line);
        if (isset($array[1])) {
            $conf[$array['0']] = str_replace(array("\r\n", "\r", "\n"), '', strip_tags($array[1]));
        }
    }
    foreach (glob("./../wp_dump/sql/sql*.sql") as $filename) {
        unlink($filename);
    }

    $db = substr($conf['DB_DATABASE'], 0, 2);

//system('mysqldump --user='.$conf['DB_USERNAME'].' --password='.$conf['DB_PASSWORD'].' --host='.$conf['DB_HOST'].' '.$conf['DB_DATABASE'].' > ./dump/sql/sql-'.$dd.'.sql');
//$base=system('mysql -u root -p'.$conf['DB_PASSWORD'].' -e \'show databases;\' | grep '.$db);
//print($base);
//for ($base as $db){
//    echo $db;
//}

    system('mysqldump --user=' . $conf['DB_USERNAME'] . ' --password=' . $conf['DB_PASSWORD'] . ' --host=' . $conf['DB_HOST'] . ' --databases $(mysql -u root -p' . $conf['DB_PASSWORD'] . ' -e \'show databases;\' | grep ' . $db . ') > ./../wp_dump/sql/sql-' . $dd . '.sql');


    $rootPath = realpath('./');

    $zip = new ZipArchive();

    $zip->open('../wp_dump/' . $dd . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Skip directories (they would be added automatically)
        if (!$file->isDir()) {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }

// Zip archive will be created only after closing object
    $zip->close();





print_r('');
$time = microtime(true) - $start;
printf('Скрипт выполнялся %.4F сек.', $time);


//system('rsync -aruvv --delete-during ./ root@10.101.11.100:/var/www/all_www/ssl-oz.phaetonhc.com/');
    if ($argv[2] == 'sync') {
        system('rsync -aruvv --delete-during ../wp_dump root@10.101.11.100:/var/www/all_www/');
        $time = microtime(true) - $start;
        printf('Сихронизация выполнялась %.4F сек.', $time);
    }
}


function getCopy(){
    exit();
    $loc_user='dump';
    $loc_password='123';
    $loc_host='';

    $db=['oz','oz_forum'];
    $lines = file('./.env');

// Осуществим проход массива и выведем содержимое в виде HTML-кода вместе с номерами строк.
    $conf = [];
    foreach ($lines as $line_num => $line) {
        $array = explode('=', $line);
        if (isset($array[1])) {
            $conf[$array['0']] = str_replace(array("\r\n", "\r", "\n"), '', strip_tags($array[1]));
        }
    }
    foreach ($db as $val){
	echo $val;
        system('mysqldump --user=' . $conf['DB_USERNAME'] . ' --password=' . $conf['DB_PASSWORD'] . ' --host=' . $conf['DB_HOST'] . ' --databases '.$val.' | mysql -u '.$loc_user.' -p'.$loc_password.' --host='.$loc_host.'  '.$val);
        //printf('mysqldump --user=' . $conf['DB_USERNAME'] . ' --password=' . $conf['DB_PASSWORD'] . ' --host=' . $conf['DB_HOST'] . ' --databases '.$val.' | mysql -u '.$loc_user.' -p'.$loc_password.' --host='.$loc_host.'  '.$val);
        //system('mysqldump --user=' . $conf['DB_USERNAME'] . ' --password=' . $conf['DB_PASSWORD'] . ' --host=' . $conf['DB_HOST'] . ' --databases '.$val);


    }

//    mysqldump -u USER -pPASSWORD DATABASE | mysql -u USER -pPASSWORD DATABASE


}

if ($argv[1]=='dump'){
    dump($argv);
}

if ($argv[1]=='copy'){
    getCopy();
}

if ($argv[1]=='mysql'){
    mysqlDump();
}

//mysqldump --user='root' -p --databases oz > ./dump/sql/sql-oz.sql
//mysqldump --user='root' -p --databases oz_forum > ./dump/sql/sql-forum.sql
