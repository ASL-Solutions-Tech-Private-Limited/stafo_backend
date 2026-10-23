<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'user_type',
        'txnid',
        'amount',
        'bill_amount',
        'commission',
        'product_info',
        'status',
        'payu_payment_id',
        'hash_received',
        'hash_calculated',
        'payu_response'
    ];

    public function transaction_insert($data){
       return Transaction::create($data)->id;
    }

    public function update_transaction($data, $id){
       $res =  self::where('id', $id)->update($data);
       return $id;
    }

    /**
     * Relationship to Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    /**
     * Relationship to Company
     */
    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'user_id');
    }
}
