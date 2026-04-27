@extends('admin.layouts.layout')
@section('title', 'Roles')
@section('content')
<div class="app-main__outer">
    <div class="app-main__inner">
        @if(\Session::has('status'))
        <script type="text/javascript">
      console.log('{{\Session::get('status')}}')
        function massge() {
        Swal.fire({
          title: 'Success',
        text: '{{\Session::get('status')}}',
        timer: 2000,
        icon:'success',
        showCancelButton: false,
        showConfirmButton: false
        }).then(
        function () {},
        // handling the promise rejection
        function (dismiss) {
          if (dismiss === 'timer') {
            //console.log('I was closed by the timer')
          }
        }
      );
        }

        window.onload = massge;
       </script>
      @endif
      <div class="d-flex justify-content-between user-access">
        <div class="user-welcome">
          <h3>Role</h3>
        </div>
      </div>
      @if (isset($role))
      <form action="{{route('roles.update', $role->id)}}" method="POST">
        @csrf
        @method('put')
      <div class="users-datatable mt-3 p-4">
        <div class="row">
          <div class="col-md-6  mb-5">
            <label for="f-name">Name<span>*</span></label>
            <input type="text" name="name"  class="form-control" placeholder="Enter Name" value={{$role->name}}>
            
          </div>
          <div class="col-md-6 mb-5">
            <label for="l-name">Description</label>
            <input type="text" name="description"  class="form-control" value="{{$role->description}}" placeholder="Enter Description">
          </div>
          <div class="col">
                <div class="roles-table">
                  <div class="table-responsive">
                  <table id="rolesTable" class="table table-bordered w-100">
                    <thead>

                      <tr>
                        <th>Module</th>
                        <th>Full Access</th>
                        <th>View Access</th>
                        <th>Add Access</th>
                        <th>Edit Access</th>
                        <th>Delete Access</th>
                      </tr>

                    </thead>
                    <tbody>
                        @foreach($modulename as $key=>$module)

                      <tr>
                        <td>{{ucfirst($key)}}</td>

                        @foreach ( $module as $permession)
                        <td><input type="checkbox" class="permission {{$loop->first ? ' firstcheckbox' : 'actionPermession' }}"  value={{$permession->id}} name="permission[]" {{in_array($permession->id, $rolePermissions) ? 'checked' : ''}}></td>
                        @endforeach
                      </tr>
                      @endforeach

                    </tbody>
                  </table>
            </div>
          </div>

          </div>
          <!-- Tab End -->

        </div>
      </div>
      <div class="d-flex gap-3 justify-content-end my-4">
        <a href="{{route('roles.index')}}"><button type="button" class="btn btn-secondary">Cancel</button></a><button class="btn btn-primary" type="submit">Update</button>
      </div>
    </form>

      @else
      <form action="{{route('roles.store')}}" method="POST">
        @csrf
      <div class="users-datatable mt-3 p-4">
        <div class="row">
          <div class="col-md-6  mb-5">
            <label for="f-name">Name<span>*</span></label>
            <input type="text" name="name"  class="form-control" placeholder="Enter Name">
            
          </div>
          <div class="col-md-6 mb-5">
            <label for="l-name">Description</label>
            <input type="text" name="description"  class="form-control" placeholder="Enter Description" >
          </div>
          <div class="col">
                <div class="roles-table">
                  <div class="table-responsive">
                  <table id="rolesTable" class="table table-bordered w-100">
                    <thead>

                      <tr class="table-row">
                        <th>Module</th>
                        <th>Full Access</th>
                        <th>View Access</th>
                        <th>Add Access</th>
                        <th>Edit Access</th>
                        <th>Delete Access</th>
                      </tr>

                    </thead>
                    <tbody>
                        @foreach($modulename as $key=>$module)

                      <tr>
                        <td>{{ucfirst($key)}}s</td>
                        @foreach ( $module as $permession)
                        <td><input type="checkbox"  class="permission {{$loop->first ? ' firstcheckbox' : 'actionPermession' }}"  value={{$permession->id}} name="permission[]"></td>
                        @endforeach
                      </tr>
                      @endforeach

                    </tbody>
                  </table>
            </div>
          </div>

          </div>
          <!-- Tab End -->

        </div>
      </div>
      <div class="d-flex gap-3 justify-content-end my-4">
        <a href="{{route('roles.index')}}"><button  type="button" class="btn btn-secondary">Cancel</button></a><button class="btn btn-primary">Save</button>
      </div>
    </form>
    @endif
      <div class="users-datatable mt-3 p-4">
        <div class="row">
          <div class="col">
                <div class="access-table">
                  <div class="table-responsive">
                  <table id="accessTable" class="table table-bordered w-100">
                    <thead>
                      <tr>
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $key => $role)
                      <tr>
                        <td>{{$role->name}}</td>
                        <td>{{$role->description}}</td>

                        <td>
                          @can('role-edit')
                            <a class="edit" href="{{ route('roles.edit',$role->id) }}"><i class="fa fa-solid fa-pen"></i></a>
                          @endcan
                            @can('role-delete')
                            <form method="POST" style="all:unset;"action="{{ route('roles.destroy', $role->id) }}">
                                @csrf

                                <input name="_method" type="hidden" value="DELETE">
                                <button class="delete" style="all:unset;"><i class="fa fa-solid fa-trash-can"></i></button>
                            </form>

                            @endcan</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
            </div>
          </div>

          </div>
          <!-- Tab End -->

        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script>
   $(document).ready(function() {
    $('.delete').click(function(event) {
          var form =  $(this).closest("form");
          var name = $(this).data("name");
          event.preventDefault();
          Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {

                form.submit();
            }
        });
      });

  $('.firstcheckbox').on("change", function() {

var isChecked= $(this).prop("checked");
         $(this).closest("tr").find(".permission").prop("checked",isChecked)
  });

  $('.actionPermession').on("change", function() {
     let checkLength=$(this).closest("tr").find('.actionPermession').filter(':checked').length
     let ViewCheck=$(this).closest("tr").find("td:eq(2)").find('input:checkbox').val()
     let currentCheck=$(this).val()
    if(checkLength>3){
        $(this).closest("tr").find(".permission").prop("checked",true)
    }
            if(currentCheck != ViewCheck){
                $(this).closest("tr").find("td:eq(2)").find('input:checkbox').prop("checked",true)
            }else{

                var isChecked= $(this).prop("checked");
                if(isChecked != true){
                    $(this).closest("tr").find(".permission").prop("checked",false)
                }

            }

  });

});
    </script>
@endsection
