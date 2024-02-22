<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('roles')->delete();

        //create system administrator role
        $permissions = Permission::whereIn('type',['all','system'])->get()->pluck('id')->toArray();
        $admin_role = Role::newRole("Super Admin", "Has full access to all areas of the system", $permissions,'system',true);

        /* Create merchant admin role */
        $permissions = Permission::whereIn('type',['all','merchant'])->get()->pluck('id')->toArray();
        $role = Role::newRole("Director", "Has full access to all areas of the merchant account", $permissions,'merchant',false,true);
    }
}
