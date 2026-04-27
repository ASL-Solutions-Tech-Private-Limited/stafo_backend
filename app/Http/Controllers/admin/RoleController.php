<?php

/**
 * Role File Doc Comment
 * php version 8.2.4
 *
 * @category Class
 * @package  Class
 * @author   shravan <shravann@chetu.com>
 * @license  Genral License
 * @link     package
 */

namespace App\Http\Controllers\admin;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

/**
 * RoleController Class Doc Comment
 *
 * @category Class
 * @package  RoleController
 * @author   shravan <shravann@chetu.com>
 * @license  GNU General Public License
 * @link     class
 */


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Show Role
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\View\View
     * @throws exception
     */
    public function index(Request $request): View
    {
        $modulename = array();
        try {
            $roles = Role::where('name', '<>', 'superadmin')->orderBy('id', 'DESC')->paginate(10);
            $permission = Permission::get();
            foreach ($permission as $key => $value) {
                $module = explode('-', $value->name)[0];
                $modulename[$module][$key] = $value;
            }
            return view('admin.roles.index', compact('roles', 'modulename', 'permission'))
                ->with('i', ($request->input('page', 1) - 1) * 5);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Create Role
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\View\View
     * @throws exception
     */
    public function create(): View
    {
        try {
            $permission = Permission::get();
            return view('admin.roles.create', compact('permission'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Store Role
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\View\View
     * @throws exception
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $this->validate($request, [
                'name' => 'required|unique:roles,name',
                'permission' => 'required',
            ]);

            $role = Role::create(['name' => $request->input('name'), 'description' => $request->input('description')]);
            $permissions = Permission::whereIn('id', $request->input('permission'))->get();
            $role->syncPermissions($permissions);

            return redirect()->route('roles.index')
                ->with('status', 'Role created successfully');
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
    /**
     * Show Role
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\View\View
     * @throws exception
     */
    public function show($id): View
    {
        try {
            $role = Role::find($id);
            $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
                ->where("role_has_permissions.role_id", $id)
                ->get();

            return view('admin.roles.show', compact('role', 'rolePermissions'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Edit Role
     *
     * @param $id id
     *
     * @return Illuminate\View\View
     * @throws exception
     */
    public function edit($id): View
    {
        try {
            $roles = Role::orderBy('id', 'DESC')->paginate(10);
            $role = Role::find($id);
            $permission = Permission::get();
            $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
                ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                ->all();

            foreach ($permission as $key => $value) {


                $module = explode('-', $value->name)[0];
                $modulename[$module][$key] = $value;
            }

            return view('admin.roles.index', compact('role', 'permission', 'rolePermissions', 'modulename', 'roles'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $this->validate($request, [
                'name' => 'required', Rule::unique('name')->ignore($id),
                'permission' => 'required',
            ]);

            $role = Role::find($id);
            $role->name = $request->input('name');
            $role->description = $request->input('description');
            $role->save();

            $permissions = Permission::whereIn('id', $request->input('permission'))->get();
            $role->syncPermissions($permissions);

            return redirect()->route('roles.index')
                ->with('status', 'Role updated successfully');
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        // DB::table("roles")->where('id',$id)->delete();
        // return redirect()->route('roles.index')
        //                 ->with('status','Role deleted successfully');
        try {
            $role = Role::find($id);

            if ($role) {
                // Retrieve the users assigned to the role
                $users = $role->users;

                // Delete the users
                foreach ($users as $user) {
                    $user->syncRoles([]);
                    $user->delete();
                }

                // Delete the role
                $role->delete();

                return redirect()->route('roles.index')
                    ->with('status', 'Role and associated users have been deleted');
            } else {
                return "Role not found.";
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
