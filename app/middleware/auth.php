<?php

namespace App\Middleware;

class Auth
{
    public function check(){
        if(1 == 1){
            return true;
        }
        else
            abort();
    }
}
