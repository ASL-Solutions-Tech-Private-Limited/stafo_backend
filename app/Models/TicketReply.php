<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'admin_id',
        'company_id',
        'employee_id',
        'message',
        'reply',
        'message_by'
    ];

    // Each reply belongs to a single ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Relationships for employee and company
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }
}