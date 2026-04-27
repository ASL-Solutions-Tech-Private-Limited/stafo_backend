<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'tickets';


    protected $fillable = [
        'admin_id',
        'company_id',
        'employee_id',
        'title',
        'message',
        'reply',
        'message_by',
        'is_read_admin',
        'is_read_company'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }
}