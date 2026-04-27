@extends('admin.layouts.layout')
@section('title', 'User List')
@section('content')
    <div class="app-main__outer">

        <div class="app-main__inner">
            <div class="d-flex justify-content-between user-access">
              <div class="user-welcome">
                <h3>Members List</h3>
              </div>@can('user-create')
              <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fa fa-solid fa-plus"></i> Add New User</a> @endcan
            </div>
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
            <div class="users-datatable mt-3 p-3">
              <div class="row">
                <div class="col">
                      <div class="users-table">
                        <div class="table-responsive">
                        <table id="usersTable" class="table w-100">
                          <thead>
                            <tr>
                              {{-- <th><input type="checkbox" name="" id=""></th> --}}
                              <th>User Name</th>
                              <th>Email Address</th>
                              
                              <th>Role</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($data as $key=>$user)

                            <tr>
                                {{-- <td><input type="checkbox" name="" id=""></td> --}}
                                <td>{{$user->f_name}}</td>
                                <td>{{$user->email}}</td>
                                
                                <td>
                                    @if(!empty($user->getRoleNames()))
                                      @foreach($user->getRoleNames() as $v)
                                      <button class="super">{{$v}}</button>
                                      @endforeach
                                    @endif
                                  </td>
                                  <td>
                                    @can('user-edit')
                                      <a class="edit" href="{{ route('users.edit',$user->id) }}"><i class="fa fa-solid fa-pen"></i></a>
                                    @endcan
                                    @can('user-delete')
                                    <form method="POST" style="all:unset;"action="{{ route('users.destroy', $user->id) }}">
                                        @csrf

                                        <input name="_method" type="hidden" value="DELETE">
                                        <button class="delete" style="all:unset;"><i class="fa fa-solid fa-trash"></i></button>
                                    </form>
                                    @endcan</td>
                            </tr>
                            @endforeach
                            
                          </tbody>
                        </table>
                        <div class=" d-flex justify-content-center"><ul class="pagination">{{$data->links()}}</ul></div>
                  </div>
                </div>
                </div>
                <!-- Tab End -->

              </div>
            </div>
          </div>


    </div>




    <script>
$(document).ready(function(){

    new DataTable('#usersTable');
        $('#usersTable_paginate').hide()
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
})


    </script>
@endsection

