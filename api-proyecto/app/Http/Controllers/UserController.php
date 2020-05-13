<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Helpers\JwtAuth;


class UserController extends Controller
{
    public function register(Request $request){
        //Recoger las variables POST
        $json = $request->input('json',null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $role = 'ROLE_USER';
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

        if(!is_null($email) && !is_null($password) && !is_null($name)){
            
            //Creamos el usuario
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->surname = $surname;
            $user->role = $role;

            //Contraseña
            $pwd = hash('sha256',$password);
            $user->password = $pwd;

            //Comprobar si el usuario está duplicado
            $isset_user = User::where('email','=',$email)->first();
            if(is_null($isset_user)){
                //Guardar usuario
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Usuario registrado correctamente'
                );
            }else{
                //No guardarlo
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El usuario ya existe'
                );
            }

        }
        else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Registro fallido'
            );
        }
        
        return response()->json($data,200);
    }

    public function login(Request $request){
        $jwtAuth = new JwtAuth();

        //Recibir datos por post
        $json = $request->input('json',null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $getToken = (!is_null($json) && isset($params->getToken)) ? $params->getToken : null;

        //Cifrar la password
        $pwd = hash('sha256',$password);

        if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')){
            $signup = $jwtAuth->signup($email,$pwd);

        }elseif($getToken != null){
            $signup = $jwtAuth->signup($email,$pwd,$getToken);

        }else{

            $signup = array(
                'status' => 'error',
                'message' => 'Envia tus datos por post'
            );
        }

        return response()->json($signup,200);
    }
}
