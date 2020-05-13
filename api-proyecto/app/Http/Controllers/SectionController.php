<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Section;
use App\Video;
use App\Recurse;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;

class SectionController extends Controller
{

    /*Devuelve todas las secciones */
    public function index(Request $request){
            //Compruebo que el usuario esta logeado
          $hash = $request->header('Authorization',null);
   
          $jwtAuth = new JwtAuth();
          $checkToken = $jwtAuth->checkToken($hash);
   
          if($checkToken){
           $sections = Section::all();
           return response()->json(array(
               'sections' => $sections,
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
    
        /*Muestra todos las secciones de un curso */
    public function show($id,Request $request){
        //Compruebo que el usuario esta logeado
        $hash = $request->header('Authorization',null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            $sections = Section::where('curse_id','=',$id)->get();
            return response()->json(array(
                'sections' => $sections,
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

    /*Crear una nueva sección*/
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
                 'title' => 'required|min:5'
             ]);
             if($validatedData->fails()){
                 return response()->json($validatedData->erros(),400);
             }
 
             //Guardo el comentario
             $section = new Section();
             $section->curse_id = $params->curse_id;
             $section->title = $params->title;
             $section->description = isset($params->description)? $params->description : null; 
             $section->status = $params->status;
             $section->save();
 
             $data = array(
                 'section' => $section,
                 'message' => 'Sección añadida correctamente',
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

     /*Borra una sección */
    public function destroy($id,Request $request){
        //Compruebo que el usuario esta logeado
        //todo: filtrar tambien que solo pueda borrar una sección si el usuario es profesor del mismo
        $hash = $request->header('Authorization',null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){

            //Comprobar que existe el registro
            $section = Section::find($id);
            
            //Borrar los videos asociados
            $videos = Video::where('section_id','=',$id)->get();
            foreach ($videos as $video){
                //Borrar los comentarios asociados al video
                $comments = Comment::where('video_id','=',$video->id)-get();
                foreach($comments as $comment){
                    $comment->delete();
                }
                $video->delete();
                $file= File::where('id','=',$video->file_id)->first()->delete();
            }

            //Borrar los recursos
            $recurses = Recurse::where('section_id','=',$id)->get();
            foreach ($recurses as $recurse){
                $recurse->delete();
                $file= File::where('id','=',$recurse->file_id)->first()->delete();
            }

            //Borrar el registro
            $section->delete();

            //Devolverlo
            $data = array(
                'section' => $section,
                'message' => "Sección borrada correctamente",
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

    /*Modifica los datos de una sección en función de su id */
    public function update($id,Request $request){
        //Compruebo que el usuario esta logeado
        //todo: filtrar tambien que solo pueda modificar una sección si el usuario es el creador del mismo o profesor del curso
        $hash = $request->header('Authorization',null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            //Recoger parametros POST
            $json = $request->input('json',null);
            $params = json_decode($json);
            $params_array = json_decode($json,true);
           //Validación
           $validatedData = \Validator::make($params_array,[
            'title' => 'required|min:5'
            ]);
            if($validatedData->fails()){
                return response()->json($validatedData->erros(),400);
            }
            
            //Actualizar el registro
            $section = Section::where('id',$id)->update($params_array);

            $data = array(
                'section' => $params,
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
