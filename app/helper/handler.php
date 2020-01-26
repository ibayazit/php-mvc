<?php

// namespace ile çağırılan class ları içeriğe dahil eder
function classAutoload($className){
    $classPath = $GLOBALS['PATH'] . '/' . strtolower(str_replace('\\', '/', $className)) . '.php';
    if(file_exists($classPath))
        require $classPath;
}
spl_autoload_register('classAutoload');

// Alınan hata bir dosyaya yazılır
function errorLog($errno, $errstr, $errfile, $errline){
    $errtitle = 'ERROR';
    if($errno == 1)
        $errtitle = 'FATAL ERROR';
    elseif($errno == 2)
        $errtitle = 'WARNING';
    elseif($errno == 4)
        $errtitle = 'PARSE (SYNTAX ERROR)';
    elseif($errno == 8)
        $errtitle = 'NOTICE';
    elseif($errno == 8192)
        $errtitle = 'DEPRECATED';

    $errLog = $errtitle . ' : ' . date('Y-m-d H:i:s') . " | [$errno] | $errstr | Error on line $errline in $errfile\n";
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/err.log', $errLog, FILE_APPEND);

    if(config('app.debug')){
        echo '<br>' . $errLog . '<br>';
    }
}

// Ölümcül hatalar hariç tüm hataları işler
function catchError($errno, $errstr, $errfile, $errline) {
    errorLog($errno, $errstr, $errfile, $errline);
}
set_error_handler("catchError");

// Ölümcül hataları işler
function catchFatalError(){
    $err = error_get_last();
    if($err['type'] === 1){
        $errno = $err['type'];
        $errstr = str_replace("\n", "", $err['message']);
        $errfile = $err['file'];
        $errline = $err['line'];

        errorLog($errno, $errstr, $errfile, $errline);

        // Hata kontrolü kapalı ise 500 hata sayfası döndürülür.
        if(!config('app.debug'))
            abort(500);
    }
}
register_shutdown_function('catchFatalError');