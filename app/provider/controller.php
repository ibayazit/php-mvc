<?php

namespace App\Provider;
use App\Provider\Validator;

class Controller
{
    use Validator;
    
    public function model($name)
    {
        $name = 'App\Model\\' . $name;
        return new $name();
    }
}