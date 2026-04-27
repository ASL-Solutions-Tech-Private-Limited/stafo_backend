<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Task;
use App\Models\TaskAssign;
use App\Models\TaskComment;
use App\Models\TaskImages;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }
    public function list(Request $request)
    {

        try {
            $companyId = $request->company_id;
           
            $tasks = Task::with(['company','assignedEmployees','taskFiles'])->when($request->company_id, fn($q) => $q->where('company_id', $companyId))->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Record fetched successfully.',
                'data' => $tasks,
                'file_path' => asset('uploads/task'),
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the branches.',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    // Create a new branch
    public function store(Request $request)
    {
        try {

            // Create the branch           

            $task = Task::create([
                'company_id' => $request->company_id ? $request->company_id : Auth::user()->id, // Use authenticated user's company_id if not provided
                'title' => $request->title,
                'description' => $request->description,                
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'priority' => $request->priority,
            ]);
            // If task_assign is provided, create task assignments
            if ($request->has('task_assign')) {
                foreach ($request->task_assign as $assign) {
                    TaskAssign::create([
                        'task_id' => $task->id,
                        'employee_id' => $assign,
                    ]);
                }
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key=>$file) {
                    //$image = $request->file('image');
                    $fileName = 'task_' .$key. time() . '.' . $file->getClientOriginalExtension();

                    $file->move(public_path('uploads/task'), $fileName);
                    $taskimages = New TaskImages;
                    $taskimages->task_id=$task->id;
                    $taskimages->filename =  $fileName;
                    $taskimages->save();
                }
            }


            return response()->json([
                'success' => true,
                'message' => 'Record added successfully.',
                'data' => $task
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the task.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update an existing branch
    public function update(Request $request, $id)
    {
        try {
            
            $data = Task::find($id);

            if (!$data) {
                throw new ModelNotFoundException("Record not found.");
            }
            $data->update([                
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'priority' => $request->priority,

            ]);
            // If task_assign is provided, update task assignments
            if ($request->has('task_assign')) {
                // First, delete existing task assignments
                TaskAssign::where('task_id', $id)->delete();
                
                // Then, create new task assignments
                foreach ($request->task_assign as $assign) {
                    TaskAssign::create([
                        'task_id' => $data->id,
                        'employee_id' => $assign,
                    ]);
                }
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key=>$file) {
                    //$image = $request->file('image');
                    $fileName = 'task_' .$key.time() . '.' . $file->getClientOriginalExtension();

                    $file->move(public_path('uploads/task'), $fileName);
                    $taskimages = New TaskImages;
                    $taskimages->task_id=$data->id;
                    $taskimages->filename =  $fileName;
                    $taskimages->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully.',
                'data' => $data
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a branch
    public function destroy($id)
    {
        try {
            $task = Task::find($id);

            if (!$task) {
                throw new ModelNotFoundException("Record not found.");
            }
            $task->delete();

            return response()->json([
                'message' => 'Record deleted successfully.',
                'status' => true // Indicating successful operation
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the record.',
                'error' => $e->getMessage(),
                'status' => false // Indicating failure due to general error
            ], 500);
        }
    }

    public function commentStore(Request $request)
    {
        try {

            // Create the branch
            $comment = TaskComment::create($request->all());
            return response()->json([
                'message' => 'Record added successfully.',
                'data' => $comment
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => true,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the branch.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function commentList(Request $request)
    {

        try {
            $taskId = $request->task_id;
            $comments = TaskComment::with(['employee'])->where('task_id', $taskId)->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Record fetched successfully.',
                'logged_id' => Auth::id(),
                'data' => $comments,
                
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the branches.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function commentDelete($id)
    {
        try {
            $comment = TaskComment::find($id);

            if (!$comment) {
                throw new ModelNotFoundException("Record not found.");
            }
            $comment->delete();

            return response()->json([
                'message' => 'Record deleted successfully.',
                'status' => true // Indicating successful operation
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the record.',
                'error' => $e->getMessage(),
                'status' => false // Indicating failure due to general error
            ], 500);
        }
    }

    public function fileDelete($id)
    {
        try {
            $data = TaskImages::find($id);
            if(isset($data->filename)){
                $fileName = $data->filename;
                $file = public_path('uploads/task/').$fileName;            
                @unlink($file);
            }
            if (!$data) {
                throw new ModelNotFoundException("Record not found.");
            }
            $data->delete();
            

            return response()->json([
                'message' => 'Record deleted successfully.',
                'status' => true // Indicating successful operation
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the record.',
                'error' => $e->getMessage(),
                'status' => false // Indicating failure due to general error
            ], 500);
        }
    }

    public function fileUploads(Request $request)
    {
        try {
            
            $task_id =   $request->task_id;
            $taskimages =[];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key=>$file) {
                    //$image = $request->file('image');
                    $fileName = 'task_' .$key.time() . '.' . $file->getClientOriginalExtension();

                    $file->move(public_path('uploads/task'), $fileName);
                    $taskimages = New TaskImages;
                    $taskimages->task_id= $task_id;
                    $taskimages->filename =  $fileName;
                    $taskimages->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully.',
                //'data' => $taskimages
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function statusChange(Request $request, $id)
    {
        try {
            
            $data = Task::find($id);

            if (!$data) {
                throw new ModelNotFoundException("Record not found.");
            }
            $data->update([  
                'status' => $request->status,
            ]);   

            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully.',
                'data' => $data
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}