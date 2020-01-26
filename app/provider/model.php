<?php

namespace App\Provider;

use App\Provider\Database;

class Model extends Database
{
    // Controller içerisinde çağırılan modele ait tablo adı
    protected static $table = NULL;

    // Sıralı metodlar için özellik
    protected static $sql;

    public function __construct(){
        static::$sql = 'SELECT * FROM ' . static::$table;
    }

    /*
        $sql    : işlenecek sql
        $params : var ise parametreler
        $fetch  : dönüt fetch mi yoksa fetchAll olarak mı olacak onu belirler
    */
    public static function raw($sql, $params = [], $fetch = null){
        $DB = parent::connection();
        $data = $DB->prepare($sql);
        $data->execute($params);
        if($data->errorInfo()[0] == 00000){
            $fetch = strtolower($fetch);
            switch ($fetch) {
                case 'fetch':
                    $data = $data->fetch(\PDO::FETCH_ASSOC);
                    break;
                case 'fetchall':
                    $data = $data->fetchAll(\PDO::FETCH_ASSOC);
                    break;
                case 'lastid':
                    $data = $DB->lastInsertId();
                    break;
                
                default:
                    $data = $data->fetch(\PDO::FETCH_ASSOC);
                    break;
            }
                
            return $data;
        }
        else
            return $data->errorInfo();
    }

    // PDO prepare için kolonlar ayarlanır.
    public static function prepareDataAndColumns($data, $separator, $withColumn = true, $prefix = null){
        $sql = null;

        // Dizi değilde sadece numerik bir değer gelirse bunu id keyi ile bir diziye çevirir
        if($data != null && !is_array($data) && is_numeric($data))
            $data = ['id' => $data];

        // Veri bir diziyse
        if(is_array($data)){
            foreach($data as $key => $d){
                // cümle hazırlanır
                $sql .= ($withColumn ? $key . '=' : null) . ':' . $prefix . $key . $separator;

                // Veriler prefix e göre yeniden düzenlenir.
                if($prefix){
                    $data[$prefix . $key] = $d;
                    unset($data[$key]);
                }
            }
            $sql = rtrim($sql, $separator);
        }

        return [$sql, $data];
    }

    /*
        $columns : Eklenecek Alanlar ve Değerleri dizi olarak gönderilmelidir.
    */
    public static function create($columns){
        $columnNames = implode(',', array_keys($columns));

        $preparedColumns = self::prepareDataAndColumns($columns, ',', false, 'create_');

        $sql = 'INSERT INTO ' . static::$table . '(' . $columnNames . ') VALUES(' . $preparedColumns[0] . ')';
        return self::raw($sql, $preparedColumns[1], 'lastId');
    }

    /*
        $selector : Silinecek satırların seçimi. id harici bir selector kullanılacak ise dizi olarak gönderilmelidir.
    */
    public static function delete($selector = null){
        $where = null;

        $preparedSelector = self::prepareDataAndColumns($selector, ' AND ', true, 'delete_');

        if($selector)
            $where = ' WHERE ' . $preparedSelector[0];

        $sql = 'DELETE FROM '. static::$table . $where;
        return self::raw($sql, $preparedSelector[1]);
    }

    /*
        $selector : Güncellenecek satırların seçimi. id harici bir selector kullanılacak ise dizi olarak gönderilmelidir.
        $columns  : Güncellenecek Alanlar ve Değerleri dizi olarak gönderilmelidir.
    */
    public static function update($selector = null, $columns){
        $where = null;

        $preparedSelector = self::prepareDataAndColumns($selector, ' AND ', true, 'update_selector_');
        $preparedColumns = self::prepareDataAndColumns($columns, ',', true, 'update_columns_');

        if($selector){
            $where = ' WHERE ' . $preparedSelector[0];
        }

        $sql = 'UPDATE '. static::$table . ' SET ' . $preparedColumns[0] . $where;

        $params = array_merge($preparedColumns[1], $preparedSelector[1]);
        return self::raw($sql, $params);
    }

    // STATİC METHODLARDA SIRALI KULLANIM ÖĞRENİLECEK
    // public function select($select = null){
    //     if($select) static::$sql = str_replace('*', $select, static::$sql);
    //     return $this;
    // }

    // public function get(){
    //     return static::$sql;
    //     return self::raw(static::$sql);
    // }
}