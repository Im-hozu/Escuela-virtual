<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;
use App\Deliver;

class DeliverController extends Controller
{
    /*Devuelve todos los delivers*/
    public function index(Request $request){
        //Compruebo que el usuario esta logeado
      $hash = $request->header('Authorization',null);

      $jwtAuth = new JwtAuth();
      $checkToken = $jwtAuth->checkToken($hash);

      if($checkToken){
       $deliver = Deliver::all();
       return response()->json(array(
           'deliver' => $deliver,
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

   /*Devuelve un deliver en función de su id y el creador del mismo en un formato agradable */
   public function show($id,Request $request){
    //Compruebo que el usuario esta logeado
    $hash = $request->header('Authorization',null);

    $jwtAuth = new JwtAuth();
    $checkToken = $jwtAuth->checkToken($hash);

    if($checkToken){
       $deliver = Deliver::find($id);
       $creator = DB::table('users')->where('id','=',$deliver->user_id)->first();

       return response()->json(array(
           'deliver' => $deliver,
           'path' => $deliver->path,
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

   /*Almacenar un deliver */
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

    //Crear el deliver 
    $deliver = new Deliver();
    $deliver->user_id = $user->sub;
    $deliver->task_id = $params->task_id;
    //Subida del deliver
    $deliver_file = $request->file('deliver');
    if($deliver_file){
        $deliver_path = time().$deliver_file->getClientOriginalName();
        \Storage::disk('delivers')->put($deliver_path,\File::get($deliver_file));
        $deliver->path = $deliver_path;
    }
    $deliver->save();

    $data = array(
        'message' => 'Deliver subido correctamente',
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
       $deliver = Deliver::find($id);

      
       //Borrar el recurso
       $deliver->delete();

       //Borrar el fichero
       \Storage::disk('delivers')->delete($deliver->path);
       //Borrar el file asociado
       $file->delete();

       $data = array(
           'message' => 'deliver borrado correctamente',
           'deliver' => $deliver,
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
