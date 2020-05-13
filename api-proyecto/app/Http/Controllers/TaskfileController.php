<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskfileController extends Controller
{
    /*Devuelve todos los taskfile */
    public function index(Request $request){
        //Compruebo que el usuario esta logeado
        $hash = $request->header('Authorization',null);
   
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
   
        if($checkToken){
        $taskfile = Tasksfile::all();
        return response()->json(array(
            'tasksfile' => $taskfile,
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

    /*Devuelve todos los taskfile de un task función de su id y el usuario del mismo en un formato agradable */
    public function show($id){
        //Todos los enrollments del curso
        $taskfile = DB::table('enrollments')->where('curse_id','=',$id)->get();
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
}
