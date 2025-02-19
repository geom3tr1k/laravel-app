<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

function makeResponse($task, $user){
    $arrayUsers = $task->users()->select("users.id",'users.email')->get()->makeHidden(['pivot']);

    $authorId = $task->creator;  

    $usersWithType = $arrayUsers->map(function($user) use ($authorId) {
    $user->type = ($user->id == $authorId) ? 'author' : 'co-author';
    return $user;
    
});

    $usersWithTypeArray = $usersWithType->toArray();
    return $usersWithTypeArray;
}


class TaskController extends Controller



{
    public function add(Request $request) {
        $validData = $request -> validate(
            [
                'title' => 'required',
                'description' => 'required' 
            ]);

            try {
                $user = $request->user();
                $task = $user->createdTasks()->create($validData);
                $task->users()->attach($user->id);
        
                return response()->json([
                    "success" => true,
                    "message" => "Success",
                    "name" => $validData["title"],
                ]);
            } catch(\Exception $e) {
                return response()->json([
                    "success" => false,
                    "message" => "Task not loaded",
                    "name" => $validData["title"], 
                ]);
            };
    }

    public function updateTitle(Request $request, string $id){
        $validData_two = $request -> validate(
            ['title' => 'required|unique:tasks,title,id,creator']
        );

        $task = Task::find($id);
        $userId = Auth::id(); 


        if ($task->creator !== $userId ) {
            return response()->json([
                "success" => false,
                "message" => "You are not the creator of this task"
            ], 403); }

        $task -> update(['title' => $validData_two['title']]);

        return response() -> json([
                "success" =>  true,
                "message" => "Renamed"
        ], 200);


    }

    public function deleteTask(Request $request, string $id){
        $task = Task::find($id);
        $userId = Auth::id(); 

        if ($task->creator !== $userId ) {
            return response()->json([
                "success" => false,
                "message" => "You are not the creator of this task"
            ], 403); };
        $task->users()->detach();
        $task -> delete();
        return response() -> json([
            "success"=> true,
            "message"=> "File already deleted"
          
        ],200);
    }

    public function addUser(Request $request, string $id){
        $validData_three = $request->validate([
            "email"=> "required|email"
        ]);

        $user = User::where("email", $validData_three["email"])->first();

        if (!$id) {
            return response() -> json([
                'message' => 'No task'
            ], 404);};


        $userId = Auth::id(); 
        $task = Task::find($id);


        if ($request->user()-> id !== $userId ) {
            return response()->json([
                    "success" => false,
                    "message" => "You are not the creator of this task"
            ], 403); };

            if (!$task->users->contains($user->id)) {
                 $task->users()->attach($user->id);
                 return response()->json(['message' => makeResponse($task, $user)]);
            } else {
                 return response()->json(['message' => 'This user is already added']);
            };
            } 
    
    public function deleteUser(Request $request,  $id){
        $validData = $request->validate([
            "email"=> "string|required|email"
        ]);
        $task = Task::find($id);
        $userId = Auth::id(); 


        if($request->user()->email === $validData["email"]){
            return response()->json(["message"=>"You try delete yourself"]);
        }
        
        $user = User::where("email", $validData["email"])->first();
        
        if ($request->user()->id === $userId) {
            $task->users()->detach($user->id);
            return response()->json(['message' => makeResponse($task, $user)]);
        };      
        
    }

    public function disk(Request $request){
        return response()->json(["message"=>$request->user()->createdTasks()->select("title", "tasks.id", "description")->get()->toArray()]);
    }

    public function shared(Request $request){
        return response()->json(["message"=>$request->user()->accessibleTasks()->select("title", "tasks.id", "description")->get()->toArray()]);
    }
}

    




