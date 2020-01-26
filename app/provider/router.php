<?php

class Router
{
    protected static $middleware = [];
    protected static $prefix = null;
    protected static $namespace = null;

    protected static $additionalMiddleware = [];
    protected static $additionalPrefix = null;
    protected static $additionalNamespace = null;

    public static function init(){
        // Router ayar dosyası okunur.
        foreach(config('router.routers') as $Router){
            // middleware var ise alınır.
            if($Router['middleware'])
                self::$middleware = $Router['middleware'];
            // prefix var ise alınır.
            if($Router['prefix'])
                self::$prefix = '/' . $Router['prefix'];
            // namespace var ise alınır.
            if($Router['namespace'])
                self::$namespace = $Router['namespace'] . '\\';

            require_once $GLOBALS['PATH'] . '/router/' . $Router['name'] . '.php';
        }
        // Herhangi bir rotaya girilmezse 404 sayfası döndürülür.
        abort();
    }

    public static function group($addtionalData = null, $callback){
        // Ek örnek prefix ve Ek örnek middleware boş olarak tanımlandı.
        $instanceAdditionalPrefix = null;
        $instanceAdditionalNamespace = null;
        $instanceAdditionalMiddleware = null;
        // prefix var ise eskisi bir değişkene alınır ve yeni olan static olarak yayınlanır.
        if(isset($addtionalData['prefix'])){
            $instanceAdditionalPrefix = self::$additionalPrefix;
            self::$additionalPrefix = self::$additionalPrefix . '/' . $addtionalData['prefix'];
        }
        // namespace var ise eskisi bir değişkene alınır ve yeni olan static olarak yayınlanır.
        if(isset($addtionalData['namespace'])){
            $instanceAdditionalNamespace = self::$additionalNamespace;
            self::$additionalNamespace = self::$additionalNamespace . $addtionalData['namespace'] . '\\';
        }
        // middleware var ise eskisi bir değişkene alınır ve yeni olan static olarak yayınlanır.
        if(isset($addtionalData['middleware'])){
            $instanceAdditionalMiddleware = self::$additionalMiddleware;
            $addtionalData['middleware'] = is_array($addtionalData['middleware']) ? $addtionalData['middleware'] : array($addtionalData['middleware']);
            self::$additionalMiddleware = array_merge(self::$additionalMiddleware, $addtionalData['middleware']);
        }

        // $callback çağırılabiliyor ise çağırılır. Diğer türlü hata mesajı döndürülür.
        if (is_callable($callback)) {
            call_user_func_array($callback, []);
        }
        else
            abort(500);

        // işlemler bittiğinde alınan örnekler static olarak tekrar yayınlanır.
        if(isset($addtionalData['prefix']))
            self::$additionalPrefix = $instanceAdditionalPrefix;
        if(isset($addtionalData['namespace']))
            self::$additionalNamespace = $instanceAdditionalNamespace;
        if(isset($addtionalData['middleware']))
            self::$additionalMiddleware = $instanceAdditionalMiddleware;
    }

    public static function beforeRun($url, $method){
        // Ön ek var ise url e eklenir.
        if(self::$additionalPrefix)
            $url = self::$additionalPrefix . $url;
        if(self::$prefix)
            $url = self::$prefix . $url;

        // $url in sonunda / var ise silinir.
        $url = empty(rtrim($url, '/')) ? '/' : rtrim($url, '/');

        // gelen metodlar | işaretinden bölünür.
        $method = explode('|', strtoupper($method));

        // hazırlanan veriler bir dizi olarak döndürülür.
        return [
            'url' => $url,
            'method' => $method,
        ];
    }

    public static function afterRun($url, $callback, $parameters){
        // middleware lar çalıştırılır.
        $Middlewares = array_unique(array_merge(self::$middleware, self::$additionalMiddleware));
        if(count($Middlewares)){
            foreach($Middlewares as $Middleware){
                $MiddlewareNamespace = 'App\Middleware\\'. $Middleware;
                $MiddlewareClass = new $MiddlewareNamespace;
                call_user_func_array([$MiddlewareClass, 'check'], []);
            }
        }

        if(count($parameters)){
            // parametre var ise keyleri alınır
            $UrlParemeterKeys = [];
            $ExplodedUrl = explode('/', $url);
            foreach($ExplodedUrl as $eUrl){
                if(preg_match('/{.*}/', $eUrl))
                    $UrlParemeterKeys[] = str_replace(['{', '}'], '', $eUrl);
            }

            // rota üzerinde parametreye verilen isim ile gönderilen değer eşleştirilir ve $_REQUEST e eklenir
            foreach(array_values($parameters) as $parameterKey => $parameter)
                $_REQUEST[$UrlParemeterKeys[$parameterKey]] = $parameter;
        }

        // $callback fonksiyon ise çağırılır
        if (is_callable($callback))
            call_user_func_array($callback, $parameters);
        // $callback bir controller ise burada çağırılır
        else {
            $controller = explode('@', $callback);
            $className = $controller[0];
            $method = $controller[1];

            if(self::$additionalNamespace)
                $className = self::$additionalNamespace . $className;

            if(self::$namespace)
                $className = self::$namespace . $className;

            $className = 'App\Controller\\' . $className;
            $requiredClass = new $className;
            call_user_func_array([$requiredClass, $method], $parameters);
        }
        exit;
    }

    public static function run($url, $callback, $method){
        // Ön hazırlık yapılır.
        $beforeRun = self::beforeRun($url, $method);
        // alınan dizi içindeki veriler kullanıma açılır.
        extract($beforeRun);

        // eğer isteğe ait metod $metod dizisinde var ise
        if (in_array($_SERVER['REQUEST_METHOD'], $method)) {

            // parametreler regex deseni ile yer değiştirir
            $preparedUrl = preg_replace('@{[0-9a-zA-Z]+}@', '([0-9a-zA-Z]+)', $url);

            // Get metoduyla gönderilen veri var ise bölünür
            $REQUEST_URI = explode('?', $_SERVER['REQUEST_URI'])[0] ?? '/';
            // $REQUEST_URI i n sonunda / var ise silinir.
            $REQUEST_URI = empty(rtrim($REQUEST_URI, '/')) ? '/' : rtrim($REQUEST_URI, '/');

            // Url den parametrelere air değerler alınır
            if (preg_match('@^' . $preparedUrl . '$@', $REQUEST_URI, $parameters)) {
                unset($parameters[0]);
                
                // Doğru adrese ait rota çalışıyor ise bu kısımda istenen fonksiyon veya class çalıştırılır
                self::afterRun($url, $callback, $parameters);
            }
        }
    }

    public static function post($url, $callback){
        self::run($url, $callback, 'post');
    }

    public static function get($url, $callback){
        self::run($url, $callback, 'get');
    }

}