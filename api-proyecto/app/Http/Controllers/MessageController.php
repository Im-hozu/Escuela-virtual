<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\DB;
use App\Message;

class MessageController extends Controller
{
    /*Devuelve todos los mensajes en los que se ha participado*/
    public function index(Request $request){
        //Compruebo que el usuario esta logeado
      $hash = $request->header('Authorization',null);

      $jwtAuth = new JwtAuth();
      $checkToken = $jwtAuth->checkToken($hash);

      if($checkToken){
        $user = $jwtAuth->checkToken($hash,true);
       $enviados = DB::table('messages')->where('sender','=',$user->sub)->get();
       $recibidos = DB::table('messages')->where('addressee','=',$user->sub)->get();
       return response()->json(array(
           'enviados' => $enviados,
           'recibidos' => $recibidos,
           'status' => 'success'
       ), 200);
       }else{
           //Devolver error
           $data = array(
               'message' => "Login incorrecto",
               'status' => 'error',
               'code' => 300,
           );
       }
       return response()->json($data,$data['code']);
   }

   /*Muestra una conversación con un usuario */
   public function show($id,Request $request){
        //Compruebo que el usuario esta logeado
        $hash = $request->header('Authorization',null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            $messages = DB::table('messages')->where('sender','=',$id)->orWhere('addressee','=',$id)->orderBy('created_at')->get();
            if(is_object($messages)){
                $data = array(
                    'messages' => $messages,
                    'status' => 'success',
                    'code' => 200
                );
            }else{
                $data = array(
                    'message' => "Registro no encontrado",
                    'status' => 'error',
                    'code' => 300,
                );
            }
        }else{
            //Devolver error
            $data = array(
                'message' => "Login incorrecto",
                'status' => 'error',
                'code' => 300,
            );
        }
        return response()->json($data,$data['code']);
    }
    /*Crear una nueva tarea*/
    public function store(Request $request){
        //Compruebo que el usuario esta logeado
        $hash = $request->header('Authorization',null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            //Recojo parametros por POST
            $json = $request->input('json',null);
            $params = json_decode($json);
            $params_array = json_decode($json,true);
            
    
            //Validación
            $validatedData = \Validator::make($params_array,[
                'body' => 'required'
            ]);
            if($validatedData->fails()){
                return response()->json($validatedData->erros(),400);
            }
            //Recojo los datos del usuario
            $user = $jwtAuth->checkToken($hash,true);

            //Guardo la tarea
            $message = new Message();
            $message->sender = $user->sub;
            $message->addressee = $params->addressee;
            $message->body =$params->body; 
            $message->save();

            $data = array(
                'message' => $message,
                'system_message' => 'Mensaje enviado correctamente',
                'status' => 'success',
                'code' => 200,
            );
        }else{
            //Devolver error
            $data = array(
                'message' => "Login incorrecto",
                'status' => 'error',
                'code' => 300,
            );
        }
        return response()->json($data,$data['code']);

    }
}
