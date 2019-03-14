<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;

class CarControler extends Controller
{
    public function index(Request $request)
    {
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            echo "Index CarController | Autorizado";
        }else{
            echo "Index CarController | No autorizado";
        }
    }
}
