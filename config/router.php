<?php
return [
    'routers' => [
        /*
            name       : router dosyasının adı zorunludur.
            prefix     : tanımlanan rotaların ön eki var ise yazılır. zorunlu değil.
                         Örnek : api/user (https://example.com/api/user için kullanılabilir)
            namespace  : tanımlanan controller dosyalarının bulunduğu dizini gösterir. zorunlu değil.
                         Örnek : Admin\User (namespace App\Controller\Admin\User; Bu namespacene sahip sınıfı çağırmamızı sağlar)
            middleware : gerekli arakatmanlar sırasıyla girilir. zorunlu değil.

            örnek router tanımı.
        */
        /*
        [
            'name' => 'api',
            'prefix' => 'api',
            'namespace' => 'Api',
            'middleware' => [
                'Api',
            ]
        ],
        */
        [
            'name' => 'web',
            'prefix' => null,
            'namespace' => null,
            'middleware' => [
                'Web',
            ]
        ],
        [
            'name' => 'api',
            'prefix' => 'api',
            'namespace' => null,
            'middleware' => [
                'Api',
            ]
        ],
    ],
];