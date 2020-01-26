<?php

namespace App\Middleware;

class Web
{
    public function check(){
        if(1 == 1){
            return true;
        }
        else
            abort();
    }
}
