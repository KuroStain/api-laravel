<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Iluminate\Support\Facades\DB;
use App\User;

class JwTAuth{

    public $key;

    public function __construct(){
        $this->key = 'R3M3SL4M3J0RW41FUD3LUN1V3RS0';
    }

    public function signup($email, $password, $getToken=null)
    {
        // Busqueda en la base de datos con el ORM Eloquent
        // Se busca la primera coincidencia.
        $user = User::where(
            array(
                'email'     => $email,
                'password'  => $password
        ))->first();

        // Verificar que el objeto sea correcto
        if(is_object($user)){
            // Generar token
            $token = array(
                'sub'       => $user->id,   // Segun lo explicado, en los token al id se le llama 'sub' por norma
                'email'     => $user->email,
                'name'      => $user->name,
                'surname'   => $user->surname,
                'iat'       => time(),      // tiempo de creacion del token
                'exp'       => time() + (7 * 24 * 60 * 60)
            );

            // En este punto el token es crifrado. Se le entrega el token con la informacion, una key
            // con la que se encriptara todo y un metodo de cifrado
            $jwt = JWT::encode($token, $this->key, 'HS256');

            $decoded = JWT::decode($jwt, $this->key, array('HS256'));

            if(is_null($getToken)){
                return $jwt;
            }else {
                return $decoded;
            }
        }else{
            // Devolver error 
            return array(
                'status'    => 'error',
                'code'      => 400,
                'message'   => 'Error en el login'
            );
        }
    }

    // Lo que hace este metodo es recibir un token. Decodificarlo y verificar que sea un objeto
    // Entregando los datos decodificados en caso de ser necesario
    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;

        try{
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
        } catch(\UnexpectedValueException $e){
            $auth = false;
        } catch(\DomainException $e){
            $auth = false;
        } catch (\SignatureInvalidException $e) {
            $auth = false;
        }

        if(isset($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }

        if($getIdentity){
            return $decoded;
        }

        return $auth;
    }

}