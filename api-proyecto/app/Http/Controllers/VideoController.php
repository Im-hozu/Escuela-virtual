<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;
use App\Video;
use App\File;
use App\Comment;

class VideoController extends Controller
{
    /*Devuelve todos los videos*/
    public function index(Request $request){
         //Compruebo que el usuario esta logeado
       $hash = $request->header('Authorization',null);

       $jwtAuth = new JwtAuth();
       $checkToken = $jwtAuth->checkToken($hash);

       if($checkToken){
        $videos = Video::all();
        return response()->json(array(
            'videos' => $videos,
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

    /*Devuelve un video en función de su id y el creador del mismo en un formato agradable */
    public function show($id,Request $request){
     //Compruebo que el usuario esta logeado
     $hash = $request->header('Authorization',null);

     $jwtAuth = new JwtAuth();
     $checkToken = $jwtAuth->checkToken($hash);

     if($checkToken){
        $video = Video::find($id);
        $file = DB::table('files')->where('id','=',$video->file_id)->first();
        $creator = DB::table('users')->where('id','=',$file->user_id)->first();

        return response()->json(array(
            'video' => $video,
            'path' => $file->path,
            'creator' => $creator->name . ' ' .$creator->surname,
            'status' => 'sucess'
        ),200);
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

    /*Almacenar un video */
    public function store(Request $request){
    //Compruebo que el usuario esta logeado
       $hash = $request->header('Authorization',null);

       $jwtAuth = new JwtAuth();
       $checkToken = $jwtAuth->checkToken($hash);

       if($checkToken){
         //Recoger datos por post
         $json = $request->input('json',null);
         $params = json_decode($json);
         $params_array = json_decode($json,true);

        //Validación
        $validatedData = \Validator::make($params_array ,[
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'status' => 'required'
        ]);
        //En caso de error en la validación
        if($validatedData->fails()){
            return response()->json($validatedData->errors(), 400);
        }

        //Recojo los datos del usuario
        $user = $jwtAuth->checkToken($hash,true);

        //Crear el file 
        $file = new File();
        $file->user_id = $user->sub;
        //Subida del video
        $video_file = $request->file('video');
        if($video_file){
            $video_path = time().$video_file->getClientOriginalName();
            \Storage::disk('videos')->put($video_path,\File::get($video_file));
            $file->path = $video_path;
        }
        $file->save();

        //Creo el video
        $video = new Video();
        $video->file_id = $file->id;
        $video->section_id = $params->section_id;
        $video->title = $params->title;
        $video->description = $params->description;
        $video->status = $params->status;
        $video->save();

        $data = array(
            'message' => 'Video creado correctamente',
            'video' => $video,
            'file' => $file,
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

    /*Borrar un video en función de su id */
    public function destroy($id,Request $request){
         //Compruebo que el usuario esta logeado
       $hash = $request->header('Authorization',null);

       $jwtAuth = new JwtAuth();
       $checkToken = $jwtAuth->checkToken($hash);

       if($checkToken){
        //Comprobar que existe el video
        $video = Video::find($id);

        //Borrar los comentarios asociados al video
        $comments = Comment::where('video_id','=',$id)->get();
        foreach($comments as $comment){
            $comment->delete();
        }
        //Borrar el video
        $video->delete();

        //Buscar el file asociado
        $file = File::where('id','=',$video->file_id)->first();
        //Borrar el fichero
        \Storage::disk('videos')->delete($file->path);
        //Borrar el file asociado
        $file->delete();

        $data = array(
            'message' => 'Video borrado correctamente',
            'video' => $video,
            'file' => $file,
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

    /*Actualizar los datos de un video en función del id */
    public function update($id,Request $request){
         //Compruebo que el usuario esta logeado
       $hash = $request->header('Authorization',null);

       $jwtAuth = new JwtAuth();
       $checkToken = $jwtAuth->checkToken($hash);

       if($checkToken){
            //Recoger parametros POST
            $json = $request->input('json',null);
            $params = json_decode($json);
            $params_array = json_decode($json,true);
            //Validación
            $validatedData = \Validator::make($params_array ,[
                'title' => 'required|min:5',
                'description' => 'required|min:10',
                'status' => 'required'
            ]);

            if($validatedData->fails()){
                return response()->json($validatedData->errors(), 400);
            }
            //Actualizar el registro
            $video = Video::where('id',$id)->first();
            //Busco el file
            $file = File::where('id','=',$video->file_id)->first();
            $video = Video::where('id',$id)->update($params_array);
            

            //Subo el archivo nuevo y borro el viejo
           /* $video_file = $request->input('video',false);
            if($video_file){
                $video_path = time().$video_file->getClientOriginalName();
                \Storage::disk('videos')->put($video_path,\File::get($video_file));
                \Storage::disk('videos')->delete($file->path);
                $file->path = $video_path;
            }*/
            $file->update();

            $data = array(
                'video' => $params,
                'status' => 'sucess',
                'code' => 200
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


}
