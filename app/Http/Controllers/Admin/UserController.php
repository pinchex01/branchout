<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->can('view-users');

        $users = User::filter($request)
            ->latest('users.created_at')
            ->paginate(20);

        $this->flashInput($request->all());

        return view('admin.users.index',[
            'users'=>$users
        ])->with('page_title','Manage Users')
            ->withInput($request->all());
    }

    public function listStaff(Request $request)
    {
        $this->can('view-users');

        $users = User::filter($request)
            ->whereHas('roles', function($query){
                $query->where('type','system');
            })
            ->latest('users.created_at')
            ->paginate(20);

        $roles  = Role::systemRoles()->selectRaw("roles.id as id, roles.display_name as name")
            ->get();

        \Javascript::put([
          'frmRoles' => $roles
        ]);

        $this->flashInput($request->all());

        return view('admin.staffs.index',[
            'users'=>$users,
            'roles' => $roles
        ])->with('page_title','Manage Users')
            ->withInput($request->all());
    }

    public function show(User $user, Request $request)
    {
      return view('admin.users.view', [
        'user' => $user
      ])->with("page_title", "Admin: User Info - Overview");
    }

    public function resetPassword(User $user, Request $request)
    {
        $user->reset_password();

        return redirect()->back()
            ->with('alerts', [
                        ['type' => 'success', 'message' => "Password for {$user} has been reset successfully and notification sent"]
                    ]);
    }

    public function showRoles(User $user, Request $request)
    {
      $roles  = $user->roles()->get();

      return view('admin.users.roles', [
        'user' => $user,
        'roles' => $roles,
      ])->with("page_title", "Admin: User Info - Roles");

    }

    public function showActivities(User $user, Request $request)
    {
      $this->can('view-users');

      $activities = $user->activity()
          ->whereIn('log_name', ['feed','auth'])
          ->latest()
          ->paginate(20);
      return view('admin.users.activities', [
        'user' => $user,
        'activities' => $activities
      ])->with("page_title", "Admin: User Info - Activities");
    }

    public function changeUserRole(User $user, Request $request)
    {
      $this->can('update-staffs');

    }

    public function removeUserRole(User $user, Reqeust $reqeust)
    {
      $this->can('remove-staffs');
    }
}
