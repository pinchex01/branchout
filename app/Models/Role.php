<?php

namespace App\Models;

use App\SlugableTrait;
use Illuminate\Database\Eloquent\Model;
use Laratrust\LaratrustRole;

class Role extends LaratrustRole
{
    use SlugableTrait;

    protected $keepRevisionOf = [
        'name', 'display_name','description', 'type', "dashboard"
    ];

    protected $table = 'roles';

    protected $slug_column = 'name';
    protected $slug_source = 'display_name';

    protected $fillable = ['name','display_name','description', 'type', "dashboard"];


    public function __toString()
    {
        return $this->display_name;
    }

    public function perms()
    {
        return $this->permissions();
    }

    public function tag_perms($tag)
    {
        return $this->permissions()
            ->where('permissions.tag',$tag);
    }

    public function system_users(Organiser $organiser)
    {
        return $this->belongsToMany(User::class,'role_user','role_id', 'user_id');
    }

    public function merchant_users(Organiser $organiser)
    {
        return $this->belongsToMany(User::class,'merchant_users','role_id', 'user_id')
            ->where('merchant_users.merchant_id', $organiser->id);
    }

    public function scopeMerchantRoles($query)
    {
        return $query->where('type','merchant');
    }

    public function scopeSystemRoles($query)
    {
        return $query->where('type','system');
    }


    /**
     * Get list of role ids that dont belong to any merchant
     *
     * @return array
     */
    public static function getNonMerchantRoleIds()
    {
        return self::query()->systemRoles()
            ->get()
            ->pluck('id')
            ->toArray();
    }

    public function getPermissionIdsAttribute()
    {
        return $this->permissions->pluck('id')->toArray();
    }

    /**
     * Create a new role
     *
     * @param $role_name
     * @param string $description
     * @param array $permissions
     * @param string $type
     * @param bool $is_root
     * @param bool $is_merchant_admin
     * @return Role|null
     */
    public static function newRole($role_name, $description = "", array $permissions, $type = 'system',
                                   $is_root = false, $is_merchant_admin = false)
    {
        // create the role and provider association within a transaction
        $role = null;
        \DB::transaction(function () use(&$role, $role_name, $description,  $permissions, $type,$is_root ,$is_merchant_admin) {
            $role = new Role([
                'display_name' => $role_name,
                'type' => $type,
                'is_root' => $is_root ? 1 : 0,
                'is_merchant_admin' => $is_merchant_admin ? 1 : 0,
                'description' => $description
            ]);
            $role->save();

            if(!empty($permissions))
            {
                $permissions = array_map(function($action) use($role) {
                    return [
                        'role_id' => $role->id,
                        'permission_id' => $action
                    ];
                }, $permissions);
                $role->perms()->sync($permissions);
            }

        });

        return $role;
    }

    public static function get_merchant_admin()
    {
        return self::where([
            'is_merchant_admin' => 1
        ])->firstOrFail();
    }

    public static function get_system_admin()
    {
        return self::where([
            'is_root' => 1
        ])->firstOrFail();
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($item) {
            if (!$item->name)
                $item->generateSlug();
        });
    }
}
