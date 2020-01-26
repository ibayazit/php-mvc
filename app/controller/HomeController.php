<?php

namespace App\Controller;

use App\Provider\Controller;
use App\Model\User;

class HomeController extends Controller
{
    public function index(){
        view('welcome.index');
    }

    public function post_test(){
        // printJSON(request());
        // exit;
        $result = $this->validate(request(), [
            'ad' => 'array',
            'soyad' => 'number',
            'data' => 'array',
            'data.*' => 'number|array',
            'tt.test.*' => 'required|number',
            'test' => 'required|array',
            'test2' => 'number'
        ]);
        printJSON($result);
    }

    public function user(){

        // $Data = self::model('user');
        
        // SIRALI KULLANIM AYARLANACAK
        // $Data = $Data->select()->get();

        // printJSON($Data);

        // $Data = User::raw('SELECT * FROM users', null, 'fetchAll');
        // $Data = User::select('id')->get();
        // printJSON($Data);        
    }
}
