@extends('admin.layouts.master')
@section('main-content')

<div class="app-main__outer">
    <div class="app-main__inner">
      <div class="d-flex justify-content-between user-access">
        <div class="user-welcome">
          <h3>Role</h3>
        </div>
      </div>
      <div class="users-datatable mt-3 p-4">
        <div class="row">
          <div class="col-md-6  mb-5">
            <label for="f-name">Name<span>*</span></label>
            <input type="text" name="f-name" id="f-name" class="form-control" placeholder="Enter Name">
          </div>
          <div class="col-md-6 mb-5">
            <label for="l-name">Description</label>
            <input type="text" name="l-name" id="l-name" class="form-control" placeholder="Enter Description">
          </div>
          <div class="col">
                <div class="roles-table">
                  <div class="table-responsive">
                  <table id="rolesTable" class="table table-bordered w-100">
                    <thead>
                      <tr>
                        <th>Modules</th>
                        <th>Full Access</th>
                        <th>View Access</th>
                        <th>Add Access</th>
                        <th>Edit Access</th>
                        <th>Delete Access</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Dashboard</td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                      </tr>
                      <tr>
                        <td>Role Setup</td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                      </tr>
                      <tr>
                        <td>User</td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                      </tr>
                      <tr>
                        <td>Dashboard</td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                      </tr>
                      <tr>
                        <td>Role Setup</td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                      </tr>
                      <tr>
                        <td>User</td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                      </tr>
                      <tr>
                        <td>Dashboard</td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                      </tr>
                      <tr>
                        <td>Role Setup</td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                      </tr>
                      <tr>
                        <td>User</td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                      </tr>
                      <tr>
                        <td>Dashboard</td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                      </tr>
                      <tr>
                        <td>Role Setup</td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                      </tr>
                      <tr>
                        <td>User</td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                        <td><input type="checkbox"></td>
                      </tr>

                    </tbody>
                  </table>
            </div>
          </div>

          </div>
          <!-- Tab End -->

        </div>
      </div>
      <div class="d-flex gap-3 justify-content-end my-4">
        <button class="btn secondary-bg">Cancel</button><button class="btn primary-bg">Update</button>
      </div>
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
                      <tr>
                        <td>Admin</td>
                        <td></td>
                        <td><a class="edit"><i class="fa-solid fa-pen"></i></a><a class="view"><i class="fa-solid fa-eye"></i></a><a class="delete"><i class="fa-solid fa-trash-can"></i></a></td>
                      </tr>
                      <tr>
                        <td>Customer Service</td>
                        <td></td>
                        <td><a class="edit"><i class="fa-solid fa-pen"></i></a><a class="view"><i class="fa-solid fa-eye"></i></a><a class="delete"><i class="fa-solid fa-trash-can"></i></a></td>
                      </tr>
                      <tr>
                        <td>POD Leader</td>
                        <td></td>
                        <td><a class="edit"><i class="fa-solid fa-pen"></i></a><a class="view"><i class="fa-solid fa-eye"></i></a><a class="delete"><i class="fa-solid fa-trash-can"></i></a></td>
                      </tr>
                      <tr>
                        <td>Admin</td>
                        <td></td>
                        <td><a class="edit"><i class="fa-solid fa-pen"></i></a><a class="view"><i class="fa-solid fa-eye"></i></a><a class="delete"><i class="fa-solid fa-trash-can"></i></a></td>
                      </tr>
                      <tr>
                        <td>Customer Service</td>
                        <td></td>
                        <td><a class="edit"><i class="fa-solid fa-pen"></i></a><a class="view"><i class="fa-solid fa-eye"></i></a><a class="delete"><i class="fa-solid fa-trash-can"></i></a></td>
                      </tr>
                      <tr>
                        <td>POD Leader</td>
                        <td></td>
                        <td><a class="edit"><i class="fa-solid fa-pen"></i></a><a class="view"><i class="fa-solid fa-eye"></i></a><a class="delete"><i class="fa-solid fa-trash-can"></i></a></td>
                      </tr>
                      <tr>
                        <td>Admin</td>
                        <td></td>
                        <td><a class="edit"><i class="fa-solid fa-pen"></i></a><a class="view"><i class="fa-solid fa-eye"></i></a><a class="delete"><i class="fa-solid fa-trash-can"></i></a></td>
                      </tr>
                      <tr>
                        <td>Customer Service</td>
                        <td></td>
                        <td><a class="edit"><i class="fa-solid fa-pen"></i></a><a class="view"><i class="fa-solid fa-eye"></i></a><a class="delete"><i class="fa-solid fa-trash-can"></i></a></td>
                      </tr>
                      <tr>
                        <td>POD Leader</td>
                        <td></td>
                        <td><a class="edit"><i class="fa-solid fa-pen"></i></a><a class="view"><i class="fa-solid fa-eye"></i></a><a class="delete"><i class="fa-solid fa-trash-can"></i></a></td>
                      </tr>
                      <tr>
                        <td>Admin</td>
                        <td></td>
                        <td><a class="edit"><i class="fa-solid fa-pen"></i></a><a class="view"><i class="fa-solid fa-eye"></i></a><a class="delete"><i class="fa-solid fa-trash-can"></i></a></td>
                      </tr>
                      <tr>
                        <td>Customer Service</td>
                        <td></td>
                        <td><a class="edit"><i class="fa-solid fa-pen"></i></a><a class="view"><i class="fa-solid fa-eye"></i></a><a class="delete"><i class="fa-solid fa-trash-can"></i></a></td>
                      </tr>
                      <tr>
                        <td>POD Leader</td>
                        <td></td>
                        <td><a class="edit"><i class="fa-solid fa-pen"></i></a><a class="view"><i class="fa-solid fa-eye"></i></a><a class="delete"><i class="fa-solid fa-trash-can"></i></a></td>
                      </tr>
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
