<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;
use App\Comment;

class CommentController extends Controller
{   
    /*Muestra todos los comentarios de un video */
    public function show($id,Request $request){
        //Compruebo que el usuario esta logeado
       $hash = $request->header('Authorization',null);

       $jwtAuth = new JwtAuth();
       $checkToken = $jwtAuth->checkToken($hash);

       if($checkToken){
        $comments = Comment::where('video_id','=',$id)->get();
        return response()->json(array(
            'comments' => $comments,
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
        return response()->json($data,200);
    }

     //Crear un nuevo comentario
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
            //Recojo los datos del usuario
            $user = $jwtAuth->checkToken($hash,true);
            //Validación
            $validatedData = \Validator::make($params_array,[
                'body' => 'required|min:10|max:255'
            ]);
            if($validatedData->fails()){
                return response()->json($validatedData->errors(),400);
            }

            //Guardo el comentario
            $comment = new Comment();
            $comment->video_id = $params->video_id;
            $comment->user_id = $user->sub;
            $comment->body = $params->body;
            $comment->save();

            $data = array(
                'comment' => $comment,
                'message' => 'Comentario añadido correctamente',
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
        return response()->json($data,200);

    }

    //Borrar un comentario en función de su id
    public function destroy($id,Request $request){
        //Compruebo que el usuario esta logeado
        $hash = $request->header('Authorization',null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            //Comprobar que existe el registro
            $comment = Comment::find($id);

            //Borrar el registro
            $comment->delete();

            $data = array(
                'comment' => $comment,
                'message' => 'Comentario eliminado correctamente',
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
        return response()->json($data,200);

    }

    //posible mejora:citar
}
