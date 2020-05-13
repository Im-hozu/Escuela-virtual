<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\Recurse;
use App\Tasksfile;   
use App\File;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;
use Illuminate\Support\Arr;

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
    public function show($id,Request $request){
        //Compruebo que el usuario esta logeado
        //todo: filtrar tambien que solo pueda borrar un enrollment si el usuario es teacher del curso
        $hash = $request->header('Authorization',null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            //Todos los taskfiles del task
            $taskfiles = DB::table('tasksfiles')->where('task_id','=',$id)->get();
            $data = array (
                'status' => "success",
                'code' => 200
            );
            $i = 0;
            foreach ($taskfiles as $taskfile){
                $task = DB::table('tasks')->where('id','=',$taskfile->task_id)->first();
                $recurse = DB::table('recurses')->where('file_id','=',$taskfile->file_id)->first();
                $var = array (
                    'id' => $taskfile->id ,
                    'task_id' => $taskfile->task_id,
                    'task_name' => $task->title,
                    'file_id' => $taskfile->file_id,
                    'file_name' => $recurse->title,
                );
                $data = Arr::add($data,$i,$var);
                $i++;
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
    
            //Guardar taskfile
            $taskfile = new Tasksfile();
            $taskfile->file_id = $params->file_id;
            $taskfile->task_id = $params->task_id;
            $taskfile->save();
    
    
            $data = array(
                'taskfile' => $taskfile,
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
            $taskfile = Tasksfile::find($id);
            $taskfile->delete();
        
            //Devolverlo
            $data = array(
                'taskfile' => $taskfile,
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
