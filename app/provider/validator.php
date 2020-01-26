<?php

namespace App\Provider;

trait Validator
{
    /*
        $Datas : Kontrol edilecek veriler.
        $Rules : Verilere ait kurallar ve anahtarlar.
    */
    protected function validate($Datas, $Rules){
        // Hatalar
        $errors = [];
        // Gönderilen kurallar döngüye alınır.
        foreach($Rules as $RuleKey => $Rule){
            $err = [];
            // Kurallar pipe işaretinden bölünür.
            $preparedRule = explode('|', $Rule);
            // Kural Anahtarları nokta dan bölünür.
            $preparedRuleKey = explode('.', $RuleKey);
            // $preparedRuleKey in içindeki ilk değer $Datas ın içinde aranır yoksa null atanır.
            $Data = $Datas[$preparedRuleKey[0]] ?? null;

            // İşlenecek verinin dizi olup olmadığı kontrol edilir
            $validateArray = false;
            
            // çok boyutlu bir dizi var ise istenen değer aranır
            for($i = 1; $i < count($preparedRuleKey); $i ++){
                if($preparedRuleKey[$i] != '*')
                    $Data = $Data[$preparedRuleKey[$i]];
                else
                    $validateArray = true;
            }

            // Kontrol edilecek veri diziyse döngüye alınır diğer türlü direk kontrole gönderilir.
            if(!$validateArray){
                $err = $this->validationCheck($Data, $preparedRule, $RuleKey);
                $errors = array_merge($errors, $err);
            }
            else{
                foreach($Data as $DKey => $D){
                    $e = $this->validationCheck($D, $preparedRule, str_replace('*', $DKey, $RuleKey));
                    if(count($e)){
                        $err = array_merge((array) $err, $e);
                    }
                }
                $errors = array_merge($errors, [$RuleKey => $err]);
            }
        }

        return $errors;
    }

    /*
        $Data  : Kontrol edilecek veri.
        $Rules : Kontrol edilecek kurallar.
        $Key   : Kontrol edilecek veriye ait anahtar.
    */
    private function validationCheck($Data, $Rules, $Key){
        $errors = [];

        // İstenen kontrol $Rules ın içinde yer alıyor ise ve diğer şart karşılanıyor ise çalışır.
        if(array_search('required', $Rules) > -1 && empty($Data))
            $errors[$Key][] = lang('validation.required', $Key);

        if(array_search('array', $Rules) > -1 && !is_array($Data))
            $errors[$Key][] = lang('validation.array', $Key);

        if(array_search('string', $Rules) > -1 && !is_string($Data))
            $errors[$Key][] = lang('validation.string', $Key);

        if(array_search('number', $Rules) > -1 && !is_numeric($Data))
            $errors[$Key][] = lang('validation.number', $Key);

        return $errors;
    }
}
