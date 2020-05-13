<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;
use App\Curse;
use App\Enrollment;


class CurseController extends Controller
{
    /*Devuelve todos los cursos */
    public function index(Request $request){
        $curses = Curse::all();
        return response()->json(array(
            'curses' => $curses,
            'status' => 'success'
        ), 200);
    }

    /*Devuelve un curso en función de su id y el creador del mismo en un formato agradable */
    public function show($id){
        $curse = Curse::find($id);
        $enrollment = DB::table('enrollments')->where('curse_id','=',$id)->where('role','=','creator')->first();
        $creator = DB::table('users')->where('id','=',$enrollment->user_id)->first();

        return response()->json(array(
            'curse' => $curse,
            'creator' => $creator->name . ' ' .$creator->surname,
            'status' => 'sucess'
        ),200);
    }

    /*Crea un curso nuevo*/
    public function store(Request $request){
        //Compruebo si el usuario esta logeado
        $hash = $request->header('Authorization',null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            //Recoger datos por post
            $json = $request->input('json',null);
            $params = json_decode($json);
            $params_array = json_decode($json,true);

            //Conseguir el usuario
            $user = $jwtAuth->checkToken($hash,true);
            
            
            //Validación
            $validatedData = \Validator::make($params_array ,[
                'theme' => 'required|min:3',
                'title' => 'required|min:6',
                'description' => 'required|min:10'
            ]);
            //En caso de error en la validación
            if($validatedData->fails()){
                return response()->json($validatedData->errors(), 400);
            }
        

            //Guardar curso
            $curse = new Curse();
            $curse->status = $params->status;
            $curse->theme = $params->theme;
            $curse->title = $params->title;
            $curse->description = $params->description;
            $curse->save();

            //Creo un enrollment para ese curso con el usuario que lo ha creado
            $enrollment = new Enrollment();
            $enrollment->user_id = $user->sub;
            $enrollment->curse_id = $curse->id;
            $enrollment->role = "creator";
            $enrollment->save();

            $data = array(
                'curse' => $curse,
                'enrollment' => $enrollment,
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

    /*Modifica los datos de un curso en función de su id */
    public function update($id,Request $request){
        //Compruebo que el usuario esta logeado
        //todo: filtrar tambien que solo pueda modificar un curso si el usuario es el creador del mismo
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
            'theme' => 'required|min:3',
            'title' => 'required|min:6',
            'description' => 'required|min:10'
            ]);

            if($validatedData->fails()){
                return response()->json($validatedData->errors(), 400);
            }
            
            //Actualizar el registro
            $curse = Curse::where('id',$id)->update($params_array);

            $data = array(
                'curse' => $params,
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

    /*Borra un curso */
    public function destroy($id,Request $request){
        //Compruebo que el usuario esta logeado
        //todo: filtrar tambien que solo pueda borrar un curso si el usuario es el creador del mismo
        $hash = $request->header('Authorization',null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){

            //Comprobar que existe el registro
            $curse = Curse::find($id);
            
            //Borrar los enrollments asociados
            $enrollments = Enrollment::where('curse_id','=',$id)->get();
            foreach ($enrollments as $enrollment){
                $enrollment->delete();
            }

            //todo Habría que hacer lo mismo con todo el contenido asociado al curso

            //Borrar el registro
            $curse->delete();

            //Devolverlo
            $data = array(
                'curse' => $curse,
                'message' => "Objeto borrado correctamente",
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
}
