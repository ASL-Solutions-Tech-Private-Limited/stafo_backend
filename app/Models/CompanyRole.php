<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'status',
    ];

    /**
     * Get the company that owns this role.
     */
    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }

    /**
     * Get the permissions assigned to this role.
     */
    public function permissions()
    {
        return $this->hasMany(CompanyRolePermission::class, 'company_role_id');
    }

    /**
     * Get employees assigned to this role.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'company_role_id');
    }

    /**
     * Check if role has a specific permission key.
     */
    public function hasPermission($permissionKey)
    {
        return $this->permissions->contains('permission_key', $permissionKey);
    }

    /**
     * Get array of permission keys for this role.
     */
    public function getPermissionKeysAttribute()
    {
        return $this->permissions->pluck('permission_key')->toArray();
    }
}
