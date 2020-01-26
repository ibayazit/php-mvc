<?php
// oturum başlatıldı.
session_start();
ob_start();

// Uygulamaya bir dosya dahil ederken izleyeceğimiz yol.
$GLOBALS['PATH'] = __DIR__;

// Yardımcı dosyalar dahil edilir
foreach(glob($GLOBALS['PATH'] . '/app/helper/*.php') as $HelperFile)
    require_once $HelperFile;

// Hata kontrolü
ini_set('display_errors', config('app.debug'));
ini_set('display_startup_errors', config('app.debug'));
error_reporting(E_ALL ^ E_ERROR);

// Router sınıfı dahil edildi
require_once $GLOBALS['PATH'] . '/app/provider/router.php';

// Route ayarları yapılır
Router::init();