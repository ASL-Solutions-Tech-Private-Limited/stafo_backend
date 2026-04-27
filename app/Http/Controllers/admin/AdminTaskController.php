<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAssign;
use App\Models\TaskImages;
use App\Models\Employee;
use App\Models\TaskComment;
use App\Models\CompanyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTaskController extends Controller
{
    public function taskList(Request $request)
    {
        $companyId = $request->company_id ? $request->company_id : ''; // Get the company ID from the request or authenticated admin
        $tasks = Task::query()
            ->when($request->company_id, fn($q) => $q->where('company_id', $request->company_id))
            ->when($request->search, function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $companies = CompanyDetail::select('id','company_name')->get();
        return view('admin.task.index', compact('tasks','companies','companyId')); // Return the view with all salarytypes
    }

    public function taskCreate()
    {
        $companies = CompanyDetail::select('id','company_name')->get();
        $employees = Employee::where('company_id', Auth::id())->get(); // Fetch all employees for the dropdown
        return view('admin.task.create', compact('employees','companies')); // Return the view to create a new task
    }


    public function taskStore(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'title' => 'required',
        ]);
       
        $task = Task::create([
            'company_id' => $request->company_id, // Use authenticated admin's company_id if not provided
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
        return redirect()->route('admin.taskList')->with('success', 'Record created successfully.');
    }


    public function taskEdit($id)
    {
        $companies = CompanyDetail::select('id','company_name')->get();
        $task = Task::with(['assignedEmployees','taskFiles'])->findOrFail($id); // Fetch the salarytype to edit
        $assignedEmployeeIds = $task->assignedEmployees->pluck('id')->toArray();
        
        $employees = Employee::where('company_id', Auth::id())->get();
        return view('admin.task.edit', compact('task','employees','companies', 'assignedEmployeeIds')); // Return the edit view
    }

    public function taskUpdate(Request $request, $id)
    {
        // Validate the incoming request
        // $request->validate([
        //     'employee_id' => 'required',
        //     'name' => 'required',
        // ]);

        
        $task = Task::findOrFail($id);
        

        $task->update([
            'company_id' => $request->company_id,
            'title' => $request->title,
            'description' => $request->description,                
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'priority' => $request->priority,
        ]);
        // If task_assign is provided, create task assignments
        if ($request->has('task_assign')) {
            TaskAssign::where('task_id', $task->id)->delete();
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
                $fileName = 'task_' .$key.time() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('uploads/task'), $fileName);
                $taskimages = New TaskImages;
                $taskimages->task_id=$task->id;
                $taskimages->filename =  $fileName;
                $taskimages->save();
            }
        }

        // Redirect back with success message
        return redirect()->route('admin.taskList')->with('success', 'Record updated successfully.');
    }


    public function destroy($id)
    {
        $task = Task::findOrFail($id); // Find the salarytype to delete
        $task->delete(); // Delete the salarytype

        return redirect()->route('taskList')->with('success', 'Record deleted successfully.');
    }

    public function commentList($id)
    {
        
        $task = Task::findOrFail($id); // Fetch the salarytype to edit
        $comments = TaskComment::where('task_id', $id)->get();
        return view('admin.task.comment', compact('task','comments')); // Return the edit view
    }

    public function commentCreate($task_id)
    {
               
        return view('admin.task.comment-create', compact('task_id')); // Return the view to create a new task
    }


    public function commentStore($task_id,Request $request)
    {
        // Validate the incoming request
        $task = Task::findOrFail($task_id); 
        $adminId = Auth::id();
        TaskComment::create([
            'company_id' => $task->company_id,
            'task_id' => $task_id,
            'comments' => $request->comments
        ]);
        return redirect()->route('admin.commentList',$task_id)->with('success', 'Record created successfully.');
    }

    public function commentEdit($id)
    {
        
        $comment = TaskComment::findOrFail($id); // Fetch the salarytype to edit
        return view('admin.task.comment-edit', compact('comment')); // Return the edit view
    }

    public function commentUpdate(Request $request, $id)
    {
        
        $adminId = Auth::id();
        $Comment = TaskComment::findOrFail($id);
        $Comment->update([
            'comments' => $request->comments
        ]);

        // Redirect back with success message
        return redirect()->route('admin.commentList',$Comment->task_id)->with('success', 'Record updated successfully.');
    }

    public function commentDelete($id)
    {
        $comment = TaskComment::findOrFail($id); // Find the salarytype to delete
        $comment->delete(); // Delete the salarytype

        return redirect()->route('admin.commentList',$comment->task_id)->with('success', 'Record deleted successfully.');
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

    
}