<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $dates = ['date', 'in_time', 'out_time'];

    protected $fillable = [
        'company_id',
        'branch_id',
        'employee_id',
        'department_id',
        'attendance',
        'halfday',
        'date',
        'in_time',
        'out_time',
        'punchin_image',
        'punchout_image',
    ];






    /**
     * Relationship with CompanyDetail Model
     */
    // public function company()
    // {
    //     return $this->belongsTo(CompanyDetail::class, 'company_id');
    // }

    /**
     * Relationship with Branch Model
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Relationship with Employee Model
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // company login relation
    // app\Models\Attendance.php

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }
}