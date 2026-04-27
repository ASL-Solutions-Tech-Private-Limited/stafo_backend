<?php

namespace App\Http\Controllers\User;

use App\Models\Expense;
use App\Models\Expenseform;
use App\Models\Expensetype;
use App\Models\ExpenseDetail;
use App\Models\ExpenseAttachment;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{

    public function expenseList(Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $name = $request->input('name');
        $status = $request->input('status');
        $companyId = Auth::id();

            $expensees_query = Expense::with(['employee:id,name','expense_type:id,name,description','expense_details','attachments'])->where('company_id', $companyId);
            $expensees_query->when($request->has('employee_id'), function ($query) use ($request) {
                return $query->where('employee_id', $request->employee_id);
            });
        $expensees = $expensees_query->paginate(10);
        

        return view('user.expense.expenselist', compact('expensees'));
    }

    public function expenseCreate()
    {
        $expenseTypes = Expensetype::where('company_id', Auth::id())->get();
        $employees = Employee::where('company_id', Auth::id())->get();
        return view('user.expense.expensecreate', compact('expenseTypes', 'employees'));
    }

    public function expenseStore (Request $request)
    {
            $request->validate([
                'employee_id' => 'required',
            ]);
            $companyId = Auth::id();
            $expense = new Expense();
            $expense->company_id = $companyId;    
            $expense->employee_id = $request->employee_id;
            $expense->amount = $request->amount;
            $expense->expensetype_id = $request->expense_type;
            $expense->save();
    
            if ($request->has('expense_details')) {
                foreach ($request->expense_details as $detail) {
                    $expenseDetail = new ExpenseDetail();
                    $expenseDetail->expense_id = $expense->id;
                    $expenseDetail->expenseform_id = $detail['expenseform_id'];
                    $expenseDetail->expense_value = $detail['expense_value'];
                    $expenseDetail->save();
                }
            }

            
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $key=>$file) {
                    $fileName = 'attachment_' .$key. time() . '.' . $file->getClientOriginalExtension();
                    $folder   = public_path('uploads/expense_attachments');
                    if (!file_exists($folder)) {
                        mkdir($folder, 0777, true);
                    }
                    $file->move($folder, $fileName);
                    $expensefiles = New ExpenseAttachment;
                    $expensefiles->expense_id=$expense->id;
                    $expensefiles->filename =  $fileName;
                    $expensefiles->save();
                }
            }


        return redirect()->route('expenseList')->with('success', 'Record added successfully!');
    }

    public function expenseEdit($id)
    {
        // Get company_id from authenticated user and ensure reimbursement belongs to that company
        $expense = Expense::with(['expense_type:id,name,description','expense_details:id,expense_id,expenseform_id,expense_value','expense_details.expenseFormDetails:id,field_name,description','attachments'])->where('id',$id)->first();
        $expenseTypes = Expensetype::where('company_id', Auth::id())->get();
        $employees = Employee::where('company_id', Auth::id())->get();
        $attachments = ExpenseAttachment::where('expense_id', $id)->get();
        return view('user.expense.expenseedit', compact('expense','employees','expenseTypes','attachments'));
    }

    public function expenseUpdate(Request $request, $id)
    {
        // Get company_id from authenticated user and ensure reimbursement belongs to that company
        $companyId = Auth::id();
        $expense = Expense::where('id', $id)->first();
            $expense->employee_id = $request->employee_id;
            $expense->amount = $request->amount;
            $expense->save();
            
            if ($request->has('expense_details')) {
                //ExpenseDetail::where('expense_id', $id)->delete(); // Clear existing details
                foreach ($request->expense_details as $detail) {
                   
                    $expenseDetail = ExpenseDetail::find($detail['id']);
                    $expenseDetail->expense_value = $detail['expense_value'];
                    $expenseDetail->save();
                }
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $key=>$file) {
                    $fileName = 'attachment_' .$key. time() . '.' . $file->getClientOriginalExtension();
                    $folder   = public_path('uploads/expense_attachments');
                    if (!file_exists($folder)) {
                        mkdir($folder, 0777, true);
                    }
                    $file->move($folder, $fileName);
                    $expensefiles = New ExpenseAttachment;
                    $expensefiles->expense_id=$expense->id;
                    $expensefiles->filename =  $fileName;
                    $expensefiles->save();
                }
            }

        // Redirect back with success message
        return redirect()->route('expenseList')->with('success', 'Record updated successfully!');
    }

    public function expenseFormDisplay(Request $request)
    {
        $type_id = $request->input('expense_type_id');
        $expenseForms = Expenseform::where('type_id', $type_id)->get();
        
        $html = '';
        if(!empty($expenseForms)){
            foreach($expenseForms as $k=> $field){

                $html .= '<div class="form-group">';
                $html .= '<label for="'.$field->field_name.'">'.$field->field_name.'</label>';
                $html .= '<input type="text" name=expense_details['.$k.'][expense_value]" class="form-control" placeholder="'.$field->description.'">';
                $html .= '<input type="hidden" name=expense_details['.$k.'][expenseform_id]" value="'.$field->id.'"> ';
                //$html .= '<small class="form-text text-muted">'.$field->description.'</small>';
                $html .= '</div>';
            }
        } 
        return response()->json(['html' => $html]);
    }

    public function expenseDetails($id)
    {
        $companyId = Auth::id();
        $expense = Expense::with(['employee:id,name','expense_type:id,name,description','expense_details','expense_details.expenseFormDetails:id,field_name,description','attachments'])->where('company_id', $companyId)->where('id', $id)->first();

        // $expensees_query = Expense::with(['expense_type:id,name,description','expense_details:id,expense_id,expenseform_id,expense_value','expense_details.expenseFormDetails:id,field_name,description','attachments']);
            
        //     $expensees_query->where('id', $id);
        //     $expensees = $expensees_query->first();
        //dd($expensees);
        if (!$expense) {
            throw new ModelNotFoundException("Record not found.");
        }
        return view('user.expense.expensedetails', compact('expense'));
    }

    public function expenseDelete($id)
    {

        $companyId = Auth::id();
        $data = Expense::where('company_id',$companyId)->where('id',$id)->first();
        $attachments = ExpenseAttachment::where('expense_id', $id)->get();

        if($attachments->count()>0){
            foreach($attachments as $attachment){
                if(isset($data->filename)){
                    $fileName = $data->filename;
                    $file = public_path('uploads/expense_attachments/').$fileName;            
                    @unlink($file);
                }
                $attachment->delete();
            }
        }
        
        if (!$data) {
            throw new ModelNotFoundException("Record not found.");
        }
        $data->delete();
        return redirect()->route('expenseList',$id )->with('success', 'Record deleted successfully!');
    }

    public function expenseAttachmentDelete($id)
    {

        $companyId = Auth::id();
        $data = ExpenseAttachment::find($id);
        $expense_id = $data->expense_id;
        if(isset($data->filename)){
            $fileName = $data->filename;
            $file = public_path('uploads/expense_attachments/').$fileName;            
            @unlink($file);
        }
        if (!$data) {
            throw new ModelNotFoundException("Record not found.");
        }
        $data->delete();
        return redirect()->route('expenseEdit',$expense_id )->with('success', 'Record deleted successfully!');
    }
    
    public function expenseformList(Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $name = $request->input('name');
        $status = $request->input('status');
        $companyId = Auth::id();
        $expenseforms = Expensetype::with(['expenseForms'])->where('company_id', $companyId)->paginate(10);

        return view('user.expense.formlist', compact('expenseforms'));
    }


    public function expenseformCreate()
    {
        
        return view('user.expense.formcreate');
    }

  
    public function expenseformStore(Request $request)
    {
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Get company_id from authenticated user
        $companyId = Auth::id();

        $Expensetype = new Expensetype();
        $Expensetype->company_id = $companyId;    
        $Expensetype->name = $request->name;
        $Expensetype->description = $request->description;
        $Expensetype->is_document_req = $request->is_document_req;
        $Expensetype->save();
    
        if ($request->has('fieldName')) {
        
            foreach ($request->fieldName as $k=> $field) {
                $Expenseform = new Expenseform();
                $Expenseform->type_id = $Expensetype->id;
                $Expenseform->company_id = $companyId;
                $Expenseform->field_name = $field;
                $Expenseform->description = $request->fielddescription[$k];
                $Expenseform->save();
            }
        }

        

        // Redirect back with success message
        return redirect()->route('expenseformList')->with('success', 'Record added successfully!');
    }

    // Show the details of a reimbursement
    public function expenseformDetails($id)
    {
        $companyId = Auth::id();
        $expense = Expensetype::with(['expenseForms'])->where('company_id', $companyId)->where('id', $id)->first();
        return view('user.expense.formdetails', compact('expense'));
    }

    // Show the edit form for a reimbursement
    public function expenseformEdit($id)
    {
        // Get company_id from authenticated user and ensure reimbursement belongs to that company
        $expense = Expensetype::with(['expenseForms'])->where('id', $id)->first();
        return view('user.expense.formedit', compact('expense'));
    }

    // Update an existing 
    public function expenseformUpdate(Request $request, $id)
    {
        // Get company_id from authenticated user and ensure reimbursement belongs to that company
        $companyId = Auth::id();
      
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $companyId = Auth::id();

        $Expensetype = Expensetype::where('id', $id)->first();
        $Expensetype->company_id = $companyId;    
        $Expensetype->name = $request->name;
        $Expensetype->description = $request->description;
        $Expensetype->is_document_req = $request->is_document_req;
        $Expensetype->save();
    
        if ($request->has('fieldName')) {
        Expenseform::where('type_id', $Expensetype->id)->delete(); // Clear existing fields
            foreach ($request->fieldName as $k=> $field) {
                $Expenseform = new Expenseform();
                $Expenseform->type_id = $Expensetype->id;
                $Expenseform->company_id = $companyId;
                $Expenseform->field_name = $field;
                $Expenseform->description = $request->fielddescription[$k];
                $Expenseform->save();
            }
        }

        // Redirect back with success message
        return redirect()->route('expenseformList')->with('success', 'Record updated successfully!');
    }

    // Delete a reimbursement
    public function expenseformDelete($id)
    {
        $companyId = Auth::id();
        $Expensetype = Expensetype::findOrFail($id);
        $Expensetype->delete();
         Expenseform::where('type_id', $id)->delete(); 
        return redirect()->route('expenseformList')->with('success', 'Record deleted successfully!');
    }

    // Toggle the status of a reimbursement
    public function expensestatuschange(Request $request)
    {
        $companyId = Auth::id();
        $id = $request->input('id');
        $status = $request->input('status');
        $reimbursement = Expense::where('id', $id)->where('company_id', $companyId)->firstOrFail();
        $reimbursement->status = $status;
        $reimbursement->save();

        return redirect()->route('expenseList')->with('success', 'Expense '.$status.' successfully!');
    }
}