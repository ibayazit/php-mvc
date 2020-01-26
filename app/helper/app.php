<?php

if(!function_exists('_Search')){
    /*
        Kaynak dosyasından veri almak için kullanılır
        $File : Arama yapılacak yol
        $Path : Aranacak veri
    */
    function _Search($File, $Path){
        // Alınan veri kullanılmak için parçalandı
        $Path = explode('.', $Path);
        // İstenen dosya okundu
        $data = require $GLOBALS['PATH'] . '/' . $File . '/' . $Path[0] . '.php';
        
        // istenen veriye ulaşmak için döngü kuruldu
        for($i = 1; $i < count($Path); $i++){
            // Dosya içinde istenen veri arandı
            if(isset($data[$Path[$i]]))
                $data = $data[$Path[$i]];
            else
                $data = null;
        }

        return $data;
    }
}

if(!function_exists('config')){
    /*
        Config dosyalarından veri alma
        Kullanımı dosyaAdı.veri.veri ...
    */
    function config($configPath){
        return _Search('config', $configPath);
    }
}

if(!function_exists('printJSON')){
    /*
        Gönderilen veriyi json formatında ekrana basar.
    */
    function printJSON($data){
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}

if(!function_exists('dataFilter')){
    /*
        Veri içeriğini temizler
    */
    function dataFilter($data){
        if(!is_array($data))
            return trim(htmlspecialchars($data));
        else{
            foreach($data as $key => $d){
                if(is_array($d))
                    $data[$key] = dataFilter($d);
                else
                    $data[$key] = trim(htmlspecialchars($d));
            }
            return $data;
        }
    }
}

if(!function_exists('request')){
    /*
        $select : isteğin içinden istenen veriyi (name) döndürür. Boş bırakılırsa istek içerisindeki tüm veriyi döndürür.
        $method : isteğin tipine göre filtreleme yapılabilir. Default : request
    */
    function request($select = null, $method = 'request'){
        $DATAS = $_REQUEST;
        if($method != 'request')
            $DATAS = ($method == 'get') ? $_GET : $_POST;

        if($select)
            return dataFilter($DATAS[$select]);
        return dataFilter($DATAS);
    }
}

if(!function_exists('view')){
    /*
        $name : İstenen view dosyasının yolu girilir. Örnek 'welcome.index' (welcome dizininin içindeki index)
        $data : View dosyasına gönderilecek veri.
    */
    function view($name, $data = []){
        extract($data);
        require $GLOBALS['PATH'] . '/resources/view/' . str_replace('.', '/', $name) . '.php';
    }
}

if(!function_exists('abort')){
    /*
        Hata sayfası döndürür. Default : 404
    */
    function abort($errCode = 404){
        require $GLOBALS['PATH'] . '/resources/error/' . $errCode . '.php';
        exit;
    }
}

if(!function_exists('lang')){
    /*
        Dil dosyalarından veri alma
        $langPath : Dil dosyasının yolu
        $Key      : Dil dosyasındaki veri içinde değiştirilecek alana için veri
    */
    function lang($langPath, $Key = null){
        $data = _Search('resources/lang/' . config('app.language'), $langPath);
        if($Key){
            $searchForCustom = _Search('resources/lang/' . config('app.language'), 'validation.custom_keys.' . $Key) ?? null;
            $data = str_replace(':attribute', $searchForCustom ?? $Key, $data);
        }

        return $data;
    }
}

if(!function_exists('prepareLink')){
    function prepareLink($link){
        return substr( $link, 0, 4 ) === "http" ? $link : 'http://' . $link;
    }
}

if(!function_exists('asset')){
    /*
        Ana dizin içinden dosya çağırmak için kullanılır. (Örnek : css, js ...)
    */
    function asset($path){
        return prepareLink(config('app.url')) . '/' . $path;
    }
}