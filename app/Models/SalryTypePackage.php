<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalryTypePackage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'salry_type_packages';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'package_name',
        'package_description',
        'package_type',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'company_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Default values for attributes.
     */
    protected $attributes = [
        'status' => 'Active',
    ];

    // ==================== Relationships ====================

    /**
     * Get the company that owns the package.
     */
    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }

    /**
     * Get the salary types for this package.
     */
    public function salaryTypes()
    {
        return $this->hasMany(Salarytype::class, 'package_id', 'id');
    }

    /**
     * Get the employees assigned to this package.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'package_id', 'id');
    }

    // ==================== Accessors ====================

    /**
     * Get status with badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        if ($this->status == 'Active') {
            return '<span class="badge bg-success rounded-pill"><i class="fas fa-check-circle"></i> Active</span>';
        }
        return '<span class="badge bg-danger rounded-pill"><i class="fas fa-ban"></i> Inactive</span>';
    }

    /**
     * Get package type with icon.
     */
    public function getPackageTypeIconAttribute()
    {
        $icons = [
            'Basic' => 'fas fa-star',
            'Standard' => 'fas fa-gem',
            'Premium' => 'fas fa-crown',
            'Custom' => 'fas fa-cogs',
        ];
        
        return $icons[$this->package_type] ?? 'fas fa-box';
    }

    /**
     * Check if package is active.
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'Active';
    }

    // ==================== Scopes ====================

    /**
     * Scope a query to only active packages.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope a query to only inactive packages.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }

    /**
     * Scope a query by package type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('package_type', $type);
    }

    // ==================== Helper Methods ====================

    /**
     * Activate the package.
     */
    public function activate()
    {
        $this->status = 'Active';
        return $this->save();
    }

    /**
     * Deactivate the package.
     */
    public function deactivate()
    {
        $this->status = 'Inactive';
        return $this->save();
    }

    /**
     * Toggle package status.
     */
    public function toggleStatus()
    {
        $this->status = $this->status === 'Active' ? 'Inactive' : 'Active';
        return $this->save();
    }

   

   
}