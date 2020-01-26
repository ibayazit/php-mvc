<?php

namespace App\Middleware;

class Api
{
    public function check(){
        if(1 == 1){
            return true;
        }
        else
            abort();
    }
}
