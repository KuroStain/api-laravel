<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Car;

use Validator;

class CarControler extends Controller
{
    public function index()
    {
        $car = Car::all()->load('user');
        return response()->json(array(
            'cars'      => $car,
            'status'    => 'success',
            'code'      => 200,
        ));
    }

    public function show($id)
    {
        $car = Car::find($id)->load('user');
        return response()->json(array(
            'card'      => $car,
            'status'    => 'success',
            'code'      => 200,
        ));
    }

    public function store(Request $request)
    {
        $hash = $request->header('Authorization', null);

        $jwtAuth    = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            // Recoger datos por POST
            $json           = $request->input('json', null);
            $params         = json_decode($json);
            $params_array   = json_decode($json, true);

            // Conseguir el user identificado
            $user = $jwtAuth->checkToken($hash, true);

            // Validar datos
            $validate = Validator::make($params_array,[
                    'title'			=> 'required|string|max:255|min:1',
                    'description'	=> 'required|max:2048|min:1',
                    'price' 		=> 'required',
                    'status'		=> 'required']);
            
            if($validate->fails()){
                return response()->json($validate->errors(), 400);
            }
                
                
            // Guardar auto
            
            $car = new Car();
            $car->user_id       = $user->sub;
            $car->title         = $params->title;
            $car->description   = $params->description;
            $car->price         = $params->price;
            $car->status        = $params->status;

            $car->save();

            $data = array(
                    'car'   => $car,
                    'message' => 'Registro OK',
                    'status'=> 'success',
                    'code'  => 200,
            );
            
        }else{
            // Enviar error
            $data = array(
                'message'   => 'Login incorrecto',
                'status'    => 'error',
                'code'      => 300,
        );
        }

        return $data;
    }

    public function update($id, Request $request)
    {
        $hash = $request->header('Authorization', null);

        $jwtAuth    = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){

            $json           = $request->input('json', null);
            $params         = json_decode($json);
            $params_array   = json_decode($json, true);

            $validate = Validator::make($params_array,[
                'title'			=> 'required|string|max:255|min:1',
                'description'	=> 'required|max:2048|min:1',
                'price' 		=> 'required',
                'status'		=> 'required']);
        
            if($validate->fails()){
                return response()->json($validate->errors(), 400);
            }

            $car = Car::where('id', $id)->update($params_array);

            $data = array(
                'car'   => $params,
                'status'=> 'success',
                'code'  => 200,
        );

            
        }else{
            $data = array(
                'message'   => 'Login incorrecto',
                'status'    => 'error',
                'code'      => 300,
            );
        }

        return $data;
    }

    public function destroy($id, Request $request)
    {
        $hash = $request->header('Authorization', null);

        $jwtAuth    = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){

            $car = Car::Find($id);
            $car->delete();
            return response()->json(array(
                'card'      => $car,
                'status'    => 'success',
                'code'      => 200,
            ));

            
        }else{
            $data = array(
                'message'   => 'Login incorrecto',
                'status'    => 'error',
                'code'      => 300,
            );
        }

        return $data;
    }    
}
