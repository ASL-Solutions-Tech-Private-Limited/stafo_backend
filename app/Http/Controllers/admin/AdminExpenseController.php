<?php

namespace App\Http\Controllers\admin;

use App\Models\Expense;
use App\Models\Expenseform;
use App\Models\Expensetype;
use App\Models\ExpenseDetail;
use App\Models\ExpenseAttachment;
use App\Models\CompanyDetail;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminExpenseController extends Controller
{

    public function expenseList(Request $request)
    {
        
        $name = $request->input('name');
        $status = $request->input('status');
        $companyId = $request->input('company_id');

            $expensees_query = Expense::with(['employee:id,name','expense_type:id,name,description','expense_details','attachments']);
            $expensees_query->when($request->has('company_id'), function ($query) use ($request) {
                return $query->where('company_id', $request->company_id);
            });
            $expensees_query->when($request->has('employee_id'), function ($query) use ($request) {
                return $query->where('employee_id', $request->employee_id);
            });
        $expensees = $expensees_query->paginate(10);
        $companies = CompanyDetail::select('id','company_name')->get();

        return view('admin.expense.expenselist', compact('expensees','companies','companyId'));
    }

    public function expenseCreate(Request $request)
    {
        $companyId = $request->input('company_id');
        $expenseTypes = Expensetype::where('company_id', $companyId)->get();
        $employees = Employee::where('company_id', $companyId)->get();
        $companies = CompanyDetail::select('id','company_name')->get();
        return view('admin.expense.expensecreate', compact('expenseTypes', 'employees','companies','companyId'));
    }

    public function expenseStore (Request $request)
    {
        
            $request->validate([
                'employee_id' => 'required',
            ]);
            $companyId = $request->input('company_id');
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


        return redirect()->route('admin.expenseList')->with('success', 'Record added successfully!');
    }

    public function expenseEdit(Request $request,$id)
    {
        
        $expense = Expense::with(['expense_type:id,name,description','expense_details:id,expense_id,expenseform_id,expense_value','expense_details.expenseFormDetails:id,field_name,description','attachments'])->where('id',$id)->first();
        $companyId = $expense->company_id;
        $expenseTypes = Expensetype::where('company_id', $companyId)->get();
        $employees = Employee::where('company_id', $companyId)->get();
        $attachments = ExpenseAttachment::where('expense_id', $id)->get();
        $companies = CompanyDetail::select('id','company_name')->get();
        return view('admin.expense.expenseedit', compact('expense','employees','expenseTypes','attachments','companies','companyId'));
    }

    public function expenseUpdate(Request $request, $id)
    {
        $companyId = $request->input('company_id');
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
        return redirect()->route('admin.expenseList')->with('success', 'Record updated successfully!');
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

    public function expenseDetails(Request $request,$id)
    {
        $companyId = $request->input('company_id');
        $expense = Expense::with(['employee:id,name','expense_type:id,name,description','expense_details','expense_details.expenseFormDetails:id,field_name,description','attachments'])->where('id', $id)->first();

        // $expensees_query = Expense::with(['expense_type:id,name,description','expense_details:id,expense_id,expenseform_id,expense_value','expense_details.expenseFormDetails:id,field_name,description','attachments']);
            
        //     $expensees_query->where('id', $id);
        //     $expensees = $expensees_query->first();
        //dd($expensees);
        if (!$expense) {
            throw new ModelNotFoundException("Record not found.");
        }
        return view('admin.expense.expensedetails', compact('expense'));
    }

    public function expenseDelete(Request $request,$id)
    {

        $companyId = $request->input('company_id');
        $data = Expense::where('id',$id)->first();
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
        return redirect()->route('admin.expenseList',$id )->with('success', 'Record deleted successfully!');
    }

    public function expenseAttachmentDelete($id)
    {

        $companyId = $request->input('company_id');
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
        return redirect()->route('admin.expenseEdit',$expense_id )->with('success', 'Record deleted successfully!');
    }
    
    public function expenseformList(Request $request)
    {
        
        $name = $request->input('name');
        $status = $request->input('status');
        $companyId = $request->input('company_id');
        $expenseforms = Expensetype::with(['expenseForms'])->paginate(10);

        return view('admin.expense.formlist', compact('expenseforms'));
    }


    public function expenseformCreate()
    {
        $companies = CompanyDetail::select('id','company_name')->get();
        return view('admin.expense.formcreate', compact('companies'));
    }

  
    public function expenseformStore(Request $request)
    {
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        
        $companyId = $request->input('company_id');

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
        return redirect()->route('admin.expenseformList')->with('success', 'Record added successfully!');
    }

    // Show the details of a reimbursement
    public function expenseformDetails(Request $request, $id)
    {
        $companyId = $request->input('company_id');
        $expense = Expensetype::with(['expenseForms'])->where('id', $id)->first();
        return view('admin.expense.formdetails', compact('expense'));
    }

    // Show the edit form for a reimbursement
    public function expenseformEdit($id)
    {
        $expense = Expensetype::with(['expenseForms'])->where('id', $id)->first();
        $companies = CompanyDetail::select('id','company_name')->get();
        return view('admin.expense.formedit', compact('expense', 'companies'));
    }

    // Update an existing 
    public function expenseformUpdate(Request $request, $id)
    {
       
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $companyId = $request->input('company_id');

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
        return redirect()->route('admin.expenseformList')->with('success', 'Record updated successfully!');
    }

    // Delete a reimbursement
    public function expenseformDelete($id)
    {
        //$companyId = $request->input('company_id');
        $Expensetype = Expensetype::findOrFail($id);
        $Expensetype->delete();
         Expenseform::where('type_id', $id)->delete(); 
        return redirect()->route('admin.expenseformList')->with('success', 'Record deleted successfully!');
    }

    // Toggle the status of a reimbursement
    public function expensestatuschange(Request $request)
    {
        $companyId = $request->input('company_id');
        $id = $request->input('id');
        $status = $request->input('status');
        $reimbursement = Expense::where('id', $id)->where('company_id', $companyId)->firstOrFail();
        $reimbursement->status = $status;
        $reimbursement->save();

        return redirect()->route('admin.expenseList')->with('success', 'Expense '.$status.' successfully!');
    }
}