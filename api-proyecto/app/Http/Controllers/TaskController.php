<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\Section;
use App\Video;
use App\Recurse;
use App\Tasksfile;   
use App\File;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;

class TaskController extends Controller
{
    /*Devuelve todas las tasks */
    public function index(Request $request){
        //Compruebo que el usuario esta logeado
      $hash = $request->header('Authorization',null);

      $jwtAuth = new JwtAuth();
      $checkToken = $jwtAuth->checkToken($hash);

      if($checkToken){
       $tasks = Task::all();
       return response()->json(array(
           'tasks' => $tasks,
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

    /*Muestra todos las tareas de una seccion */
    public function show($id,Request $request){
        //Compruebo que el usuario esta logeado
        $hash = $request->header('Authorization',null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            $tasks = Task::where('section_id','=',$id)->get();
            if(is_object($tasks)){
                $data = array(
                    'task' => $task,
                    'status' => 'success',
                    'code' => 200
                );
            }else{
                $data = array(
                    'message' => "Registro no encontrado",
                    'status' => 'error',
                    'code' => 300,
                );
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

    /*Crear una nueva tarea*/
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
                 'title' => 'required|min:5',
                 'description' => 'required|min:10'
                 //'finish_at' => 'required'
             ]);
             if($validatedData->fails()){
                 return response()->json($validatedData->erros(),400);
             }
              //Recojo los datos del usuario
             $user = $jwtAuth->checkToken($hash,true);
 
             //Guardo la tarea
             $task = new Task();
             $task->section_id = $params->section_id;
             $task->title = $params->title;
             $task->description =$params->description; 
             $task->status = $params->status;
             //$task->finish_at = $params->finish_at;
             $task->user_id = $user->sub;
             $task->save();
 
             $data = array(
                 'task' => $task,
                 'message' => 'Tarea añadida correctamente',
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

    /*Modifica los datos de una tarea en función de su id */
    public function update($id,Request $request){
        //Compruebo que el usuario esta logeado
        //todo: filtrar tambien que solo pueda modificar una tarea si el usuario es el creador de la mismo o profesor del curso
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
                'title' => 'required|min:5',
                'description' => 'required|min:10'
                //'finish_at' => 'required'
            ]);
            if($validatedData->fails()){
                return response()->json($validatedData->erros(),400);
            }
            
            //Actualizar el registro
            $task = Task::where('id',$id)->update($params_array);

            $data = array(
                'task' => $params,
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

    /*Borra una tarea */
    public function destroy($id,Request $request){
        //Compruebo que el usuario esta logeado
        //todo: filtrar tambien que solo pueda borrar una sección si el usuario es profesor del mismo
        $hash = $request->header('Authorization',null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){

            //Comprobar que existe el registro
            $task = Task::find($id);
            
            //Borrar los TaskFiles asociados
            $taskfiles = Tasksfile::where('task_id','=',$id)->get();
            foreach ($taskfiles as $taskfile){
                //Borrar los recursos asociados al task
                $recurses = Recurse::where('file_id','=',$taskfile->file_id)-get();
                foreach($recurses as $recurse){
                    $recurse->delete();
                }
                $file = File::where('id','=',$taskfile->file_id)->first();
                //Borrar el fichero
                \Storage::disk('recurses')->delete($file->path);
                //Borro el file
                $file->delete();
                $taskfile->delete();
            }
            //Faltaria por borrar las delivers


            //Borrar el registro
            $task->delete();

            //Devolverlo
            $data = array(
                'task' => $task,
                'message' => "Tarea borrada correctamente",
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
