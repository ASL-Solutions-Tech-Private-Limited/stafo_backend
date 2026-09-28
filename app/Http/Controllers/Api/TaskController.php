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
    /**
     * @OA\Get(
     *     path="/api/task/list",
     *     summary="Get task list",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         required=false,
     *         description="Company ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Record fetched successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="file_path", type="string", example="https://stafo.in/uploads/task")
     *         )
     *     )
     * )
     */
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





    /**
     * @OA\Post(
     *     path="/api/task/create",
     *     summary="Create a new task",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title"},
     *                 @OA\Property(property="company_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Build API documentation"),
     *                 @OA\Property(property="description", type="string", example="Add Swagger annotations"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2026-09-26"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2026-09-30"),
     *                 @OA\Property(property="status", type="string", example="Pending"),
     *                 @OA\Property(property="priority", type="string", example="High"),
     *                 @OA\Property(property="task_assign[]", type="array", @OA\Items(type="integer", example=5)),
     *                 @OA\Property(property="files[]", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Record added successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/task/update/{id}",
     *     summary="Update a task",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string", example="Updated Task Title"),
     *                 @OA\Property(property="description", type="string", example="Updated task description"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2026-09-26"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2026-10-05"),
     *                 @OA\Property(property="status", type="string", example="In Progress"),
     *                 @OA\Property(property="priority", type="string", example="Medium"),
     *                 @OA\Property(property="task_assign[]", type="array", @OA\Items(type="integer", example=5)),
     *                 @OA\Property(property="files[]", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Record updated successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/task/delete/{id}",
     *     summary="Delete a task",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Record deleted successfully."),
     *             @OA\Property(property="status", type="boolean", example=true)
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/task/comment-create",
     *     summary="Add a comment to task",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"task_id","comment"},
     *             @OA\Property(property="task_id", type="integer", example=1),
     *             @OA\Property(property="employee_id", type="integer", example=5),
     *             @OA\Property(property="comment", type="string", example="Task is progressing well.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Record added successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/task/comment-list",
     *     summary="Get comments for a task",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="task_id",
     *         in="query",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Record fetched successfully."),
     *             @OA\Property(property="logged_id", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/task/comment-delete/{id}",
     *     summary="Delete a task comment",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Comment ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Record deleted successfully."),
     *             @OA\Property(property="status", type="boolean", example=true)
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/task/file-delete/{id}",
     *     summary="Delete a task attachment file",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task Image/File ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Record deleted successfully."),
     *             @OA\Property(property="status", type="boolean", example=true)
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/task/file-uploads",
     *     summary="Upload files to a task",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"task_id","files"},
     *                 @OA\Property(property="task_id", type="integer", example=1),
     *                 @OA\Property(property="files[]", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Record updated successfully.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/task/status-change/{id}",
     *     summary="Change task status",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="Completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Record updated successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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