<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;
use App\File;
use App\Recurse;



class RecurseController extends Controller
{
    /*Devuelve todos los recursos*/
    public function index(Request $request){
        //Compruebo que el usuario esta logeado
      $hash = $request->header('Authorization',null);

      $jwtAuth = new JwtAuth();
      $checkToken = $jwtAuth->checkToken($hash);

      if($checkToken){
       $recurses = Recurse::all();
       return response()->json(array(
           'recurses' => $recurses,
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

   /*Devuelve un recurso en función de su id y el creador del mismo en un formato agradable */
   public function show($id,Request $request){
    //Compruebo que el usuario esta logeado
    $hash = $request->header('Authorization',null);

    $jwtAuth = new JwtAuth();
    $checkToken = $jwtAuth->checkToken($hash);

    if($checkToken){
       $recurso = Recurse::find($id);
       $file = DB::table('files')->where('id','=',$recurso->file_id)->first();
       $creator = DB::table('users')->where('id','=',$file->user_id)->first();

       return response()->json(array(
           'recurse' => $recurso,
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

   /*Almacenar un recurso */
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
    //Subida del recurso
    $recurso_file = $request->file('recurso');
    if($recurso_file){
        $recurso_path = time().$recurso_file->getClientOriginalName();
        \Storage::disk('recurses')->put($recurso_path,\File::get($recurso_file));
        $file->path = $recurso_path;
    }
    $file->save();

    //Creo el recurso
    $recurse = new Recurse();
    $recurse->file_id = $file->id;
    $recurse->section_id = $params->section_id;
    $recurse->title = $params->title;
    $recurse->description = $params->description;
    $recurse->status = $params->status;
    $recurse->save();

    $data = array(
        'message' => 'Recurso subido correctamente',
        'recurso' => $recurse,
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
    return response()->json($data,$data['code']);
    }

    /*Borrar un recurso en función de su id */
    public function destroy($id,Request $request){
        //Compruebo que el usuario esta logeado
      $hash = $request->header('Authorization',null);

      $jwtAuth = new JwtAuth();
      $checkToken = $jwtAuth->checkToken($hash);

      if($checkToken){
       //Comprobar que existe el video
       $recurso = Recurse::find($id);

      
       //Borrar el recurso
       $recurso->delete();

       //Buscar el file asociado
       $file = File::where('id','=',$recurso->file_id)->first();
       //Borrar el fichero
       \Storage::disk('recurses')->delete($file->path);
       //Borrar el file asociado
       $file->delete();

       $data = array(
           'message' => 'Recurso borrado correctamente',
           'recurse' => $recurso,
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
       return response()->json($data,$data['code']);
   }
}
