@extends('user.layouts.app')

@section('title', 'Add Form')

@section('content')
    <div class="card mt-4 p-3">

        <div class="container">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="fw-bold">Add Form</h2>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('expenseformList') }}" class="btn btn-primary" style="float: right;">Form
                        List</a>
                </div>
            </div>

            <form action="{{ route('expenseformStore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-end">
                    
                    <!-- Reimbursement Name -->
                    <div class="col-md-6">
                        <div class="col-md-12">
                            <label for="name" class="form-label">Expense Type</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="is_document_req" class="form-label">Accept attachment</label>
                            <select name="is_document_req" id="is_document_req" class="form-control">
                                <option value="0" >No</option>
                                <option value="1" >Yes</option>
                            </select>

                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div> 
                    <!-- Description -->
                    <div class="col-md-6 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Attachment -->
                 </div>
                 <div class="row">
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-primary" id="add_more_field">Add More Field</button>
                    </div>
                </div>   
                 <div class="row" id="fieldSection">
                    <!-- Save Button -->
                    <div class="col-md-5 mb-3">
                        <label for="name" class="form-label">Enter field Name</label>
                        <input type="text" name="fieldName[]" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="description" class="form-label">Enter field Description</label>                       
                        <input type="text" name="fielddescription[]" class="form-control">
                    </div>
                    <div class="col-md-1 mb-3">                        
                        <button type="button" class="btn btn-danger mb-3">X</button>
                    </div>
                </div>
                <div class="row">
                    <!-- Save Button -->
                    <div class="col-md-6 mb-3 text-end">
                        <button type="submit" class="btn btn-success ">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const addMoreButton = document.getElementById('add_more_field');
            const fieldSection = document.getElementById('fieldSection');
            
            addMoreButton.addEventListener('click', function() {
                
                const newField = `
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label for="name" class="form-label">Enter field Name</label>
                        <input type="text" name="fieldName[]" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Enter field Description</label>
                        <input type="text" name="fielddescription[]" class="form-control">
                    </div>
                    <div class="col-md-1 mb-3">                        
                        <button type="button" class="btn btn-danger mb-3 remove_field">X</button>
                    </div></div>`;
                fieldSection.insertAdjacentHTML('beforeend', newField);
                
            });
        });

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove_field')) {
                event.target.closest('.row').remove();
            }
        });

        // $(document).ready(function() {
        //     // Initialize any additional JavaScript functionality if needed
        //     $('.remove_field').on('click', function() {
        //         alert('Hiii');
        //         $(this).closest('.row').remove();
        //     });
        // });
    </script>
@endsection