<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->can('create-roles');

        $roles  = Role::with(['perms'])
            ->systemRoles()
            ->paginate(20);

        $permissions = Permission::whereIn('type',['all','system'])->get();

        return view('admin.roles.index',[
            'roles'=>$roles,
            'permissions'=>$permissions
        ])->with('page_title',"Manage Groups | Control Panel | ");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->can('create-roles');

        $permissions = Permission::whereIn('type',['all','system'])->get();

        return view('backend.admin.roles.forms.form_role_quick_create',[
            'permissions'=>$permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->can('create-roles');

        $this->validate($request,[
            'display_name'=>'required|title',
            'permissions'=>'required|array',
            'description'=>'required',
            "dashboard" => "required"
        ]);

        $role = Role::newRole($request->input('display_name'), $request->input('description'),
            $request->input('permissions'),'system',false);

        $role->dashboard = $request->input('dashboard');
        $role->save();

        $user  = user();
        activity('feed')->log("$user created admin role: #{$role->id} - {$role->display_name}")
            ->causedBy($user);

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Role {$role} created successfully"]
            ]);;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $this->can('update-roles');

        $permissions = Permission::whereIn('type',['all','system'])->get();

        return view('admin.roles.view',[
            'role'=>$role,
            'permissions'=>$permissions
        ])->with('page_title',"Edit Group | {$role} | Control Panel");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Role $role)
    {
        $this->can('update-roles');

        $this->validate($request,[
            'display_name'=>'required',
            'permissions'=>'required|array',
            'description' => "required",
            'dashboard' => "required"
        ]);

        $role->fill($request->only(['display_name', "description"]));
        $role->perms()->sync($request->get('permissions'));
        $role->dashboard = $request->input('dashboard');
        $role->save();

        $user  = user();
        activity('feed')->log("$user updated admin role: #{$role->id} - {$role->display_name}")
            ->causedBy($user);


        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Role {$role} updated successfully"]
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
