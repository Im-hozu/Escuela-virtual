<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;
use App\File;
use App\Recurse;

class FileController extends Controller
{
    /*Devuelve todos los recursos*/
    public function index(Request $request){
        //Compruebo que el usuario esta logeado
      $hash = $request->header('Authorization',null);

      $jwtAuth = new JwtAuth();
      $checkToken = $jwtAuth->checkToken($hash);

      if($checkToken){
       $files = File::all();
       return response()->json(array(
           'files' => $files,
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

   /*Devuelve un file en función de su id y el creador del mismo en un formato agradable */
   public function show($id,Request $request){
    //Compruebo que el usuario esta logeado
    $hash = $request->header('Authorization',null);

    $jwtAuth = new JwtAuth();
    $checkToken = $jwtAuth->checkToken($hash);

    if($checkToken){
       $file = File::find($id);
       $creator = DB::table('users')->where('id','=',$file->user_id)->first();

       return response()->json(array(
           'file' => $file,
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

    //Recojo los datos del usuario
    $user = $jwtAuth->checkToken($hash,true);

    //Crear el file 
    $file = new File();
    $file->user_id = $user->sub;
    //Subida del recurso
    $file_input = $request->file('file');
    if($file_input){
        $file_path = time().$file_input->getClientOriginalName();
        \Storage::disk('recurses')->put($file_path,\File::get($file_input));
        $file->path = $file_path;
    }
    $file->save();


    $data = array(
        'message' => 'File subido correctamente',
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
       $file = File::find($id);
       //Borrar el fichero
       \Storage::disk('recurses')->delete($file->path);
       //Borrar el file asociado
       $file->delete();

       $data = array(
           'message' => 'Recurso borrado correctamente',
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
