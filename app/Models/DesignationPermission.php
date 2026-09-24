<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignationPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation_id',
        'permission_key',
    ];

    /**
     * Get the designation that owns this permission.
     */
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }
}
