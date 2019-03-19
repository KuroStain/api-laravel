<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Helpers\JwtAuth;

class UserControler extends Controller
{
    public function register(Request $request)
    {
        // Recoger variables que llegan por POST
        $json = $request->input('json', null);
        $params = json_decode($json);

        $role = 'USER_ROLE';
        // Si el $json no es null y existe el parametro indicado, entonces asociara el 
        // dato si no, dejara null
        $name       = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname    = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $email      = (!is_null($json) && isset($params->email)) ? $params->email : null;      
        $password   = (!is_null($json) && isset($params->password)) ? $params->password : null;

        // Verificar que los campos existan
        if (!is_null($name) && !is_null($email) && !is_null($password)) {
              
            // Se crea el objeto usuario y se asignan sus valores correspondientes
            $user = new User();
            $user->name = $name;
            $user->surname = $surname;
            $user->email = $email;
            
            // Se codifica la contraseña con un cifrado para seguridad
            $psw = hash('sha256', $password);
            $user->password = $psw;

            // Comprovar usuario duplicado, consultando si el correo ingresado ya existe
            // si encuentra al menos uno, lo contara. De esta forma solo guardara cuando
            // no existe el correo en la bd
            $isset_user = User::where('email', '=', $email)->count();
            if ($isset_user == 0) {
                // Guardar usuario
                $user->save();
                $data = array(
                    'status'    => 'succes',
                    'code'      => 200,
                    'message'   => 'Usuario registrado correctamente'
                );
            }else {
                // Duplicado
                $data = array(
                    'status'    => 'error',
                    'code'      => 400,
                    'message'   => 'Email ya registrado'
                );
            }

        // Error en caso de no crear el usuario
        }else {
            $data = array(
                'status'    => 'error',
                'code'      => 400,
                'message'   => 'Usuario no creado'
            );
        }

        return response()->json($data, 200);
    }

    public function login(Request $request)
    {
        $jwtAuth = new JwtAuth();

        // Recibir via POST
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email      = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password   = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $getToken   = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;
        
        //var_dump($jwtAuth);
        //die();
        $pwd = hash('SHA256', $password);  // Cifrar pass

        if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')){
            $signup = $jwtAuth->signup($email, $pwd);
        }elseif ($getToken != null) {
            $signup = $jwtAuth->signup($email, $pwd, $getToken);
        }else {
            $signup = array(
                'status' => 'error',
                'message' => 'Enviar datos por POST'
            );
        }
        return response()->json($signup, 200);

    }
}
