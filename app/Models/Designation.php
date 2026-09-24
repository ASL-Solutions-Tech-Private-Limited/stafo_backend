<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'status',
    ];

    /**
     * Get the company that owns this designation.
     */
    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }

    /**
     * Get employees who hold this designation.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'designation_id');
    }

    /**
     * Get the permissions assigned to this designation.
     */
    public function permissions()
    {
        return $this->hasMany(DesignationPermission::class, 'designation_id');
    }

    /**
     * Check if designation has a specific permission key.
     */
    public function hasPermission($permissionKey)
    {
        return $this->permissions->contains('permission_key', $permissionKey);
    }

    /**
     * Get array of permission keys for this designation.
     */
    public function getPermissionKeysAttribute()
    {
        return $this->permissions->pluck('permission_key')->toArray();
    }
}
