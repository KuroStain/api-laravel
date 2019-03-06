<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\User;

class UserControler extends Controller
{
    public function register(Request $request)
    {
        // Recoger variables que llegan por POST
        $json = $request->input('json', null);
        $params = json_decode($json);

        $role = 'USER_ROLE';
        // Si el $json no es null y existe el parametro indicado, entonces asociara el dato si no, dejara null
        $name       = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname    = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $email      = (!is_null($json) && isset($params->email)) ? $params->email : null;      
        $password   = (!is_null($json) && isset($params->password)) ? $params->password : null;

        if (!is_null($name) && !is_null($email) && !is_null($password)) {
              
            $user = new User();
                $user->name = $name;
                $user->surname = $surname;
                $user->email = $email;
                
                $psw = hash('sha256', $password);
                $user->password = $psw;

                // Comprovar usuario duplicado
                $isset_user = User::where('email', '=', $email)->count();
                if ($isset_user == 0) {
                    // Guardar usuario
                    $user->save();
                    $data = array(
                        'status'    => 'succes',
                        'code'      => 200,
                        'mesage'    => 'Usuario registrado correctamente'
                    );
                }else {
                    // Duplicado
                    $data = array(
                        'status'    => 'error',
                        'code'      => 400,
                        'mesage'    => 'Email ya registrado'
                    );
                }

        }else {
            $data = array(
                'status'    => 'error',
                'code'      => 400,
                'mesage'    => 'Usuario no creado'
            );
        }

        return response()->json($data, 200);
    }

    public function login(Request $request)
    {
        echo "Accion Login";
        die();
    }
}
