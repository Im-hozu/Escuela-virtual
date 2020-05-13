<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enrollment;
use App\Curse;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;
use Illuminate\Support\Arr;

class EnrollmentController extends Controller
{
    /*Devuelve todos los enrollments */
    public function index(Request $request){
        //Compruebo que el usuario esta logeado
        $hash = $request->header('Authorization',null);
   
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
   
        if($checkToken){
        $enrollments = Enrollment::all();
        return response()->json(array(
            'enrollments' => $enrollments,
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
    /*Devuelve todos los enrollment de un curso función de su id y el usuario del mismo en un formato agradable */
    public function show($id){
        //Todos los enrollments del curso
        $enrollments = DB::table('enrollments')->where('curse_id','=',$id)->get();
        $data = array (
            'status' => "success",
            'code' => 200
        );
        $i = 0;
        foreach ($enrollments as $enrollment){
            $user = DB::table('users')->where('id','=',$enrollment->user_id)->first();
            $curse = DB::table('curses')->where('id','=',$enrollment->curse_id)->first();
            $var = array (
                'id' => $enrollment->id ,
                'user' => $user->name.' '.$user->surname,
                'curse' => $curse->title
            );
            $data = Arr::add($data,$i,$var);
            $i++;
        }

        
        return response()->json($data,$data['code']);
    }
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

        //Guardar enrollment
        $enrollment = new Enrollment();
        $enrollment->user_id = $params->user_id;
        $enrollment->curse_id = $params->curse_id;
        $enrollment->role = $params->role;
        $enrollment->save();


        $data = array(
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

    public function destroy($id,Request $request){
        //Compruebo que el usuario esta logeado
        //todo: filtrar tambien que solo pueda borrar un enrollment si el usuario es teacher del curso
        $hash = $request->header('Authorization',null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            //Borrar los enrollments asociados
            $enrollment = Enrollment::find($id);
            $enrollment->delete();
        
            //Devolverlo
            $data = array(
                'enrollment' => $enrollment,
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
