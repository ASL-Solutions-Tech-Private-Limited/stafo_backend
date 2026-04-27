@extends('admin.layouts.layout')

@section('title', 'Form Details')

@section('content')
    <div class="card mt-4 p-3 shadow-sm">
        <div class="container">

            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="mb-3 fw-bold">Form Details</h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('expenseformList') }}" class="btn btn-primary float-right">
                        <i class="bi bi-building"></i> Form List
                    </a>
                </div>
            </div>

            <div class="mt-4">
                <div class="container px-0">
                    <div class="row align-items-end">
                    
                    
                    <div class="col-md-6">
                        <div class="col-md-12">
                            <label for="name" class="form-label">Expense Type</label>
                            {{ $expense->name}}
                        </div>
                        <div class="col-md-12">
                            <label for="is_document_req" class="form-label">Accept attachment</label>
                            {{ ($expense->is_document_req==0?'No':'Yes') }}
                        </div>
                    </div> 
                    <!-- Description -->
                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        {{$expense->description }}
                    </div>
                    <!-- Attachment -->
                 </div>
                 
                  @if(count($expense->expenseForms)>0)
                
                <div class="row" id="fieldSection">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                           <b>Field Name</b>
                        </div>
                        <div class="col-md-6 mb-3">
                           <b> Field Description </b>
                        </div>
                        
                    </div>
                    @foreach($expense->expenseForms as $field)
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            {{$field->field_name}}
                        </div>
                        <div class="col-md-6 mb-3">
                            {{$field->description}}
                        </div>
                        
                    </div>
                    @endforeach
                </div>
                @endif
                </div>
            </div>

        </div>
    </div>
@endsection