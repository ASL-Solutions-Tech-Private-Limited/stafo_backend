<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BbpsCommission extends Model
{
    use HasFactory;

    protected $table = 'd2_package_commission_tbl';

    public $timestamps = false;

    protected $fillable = [
        'pacid',
        'company_id',
        'optid',
        'billtypeid',
        'gift_id',
        'comper',
        'gstper',
        'issurcharge',
        'isflat',
        'status',
        'insert_date',
        'insert_user',
        'update_date',
        'update_user',
    ];

    /**
     * Relationship to Company
     */
    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }

    /**
     * Relationship to BBPS Service Category
     */
    public function serviceCategory()
    {
        return $this->belongsTo(BbpsServiceCategory::class, 'billtypeid', 'id');
    }
}
