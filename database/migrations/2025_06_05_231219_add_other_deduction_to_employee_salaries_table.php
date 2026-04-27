<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
          
           $table->float('other_deduction')->default(0)->after('gross_salary');
           $table->float('absent_days')->default(0)->after('other_deduction');
           $table->float('reimbursement')->default(0)->after('absent_days');
           $table->integer('working_days')->default(0)->after('reimbursement');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
 
            $table->dropColumn('other_deduction'); 
            $table->dropColumn('absent_days');
            $table->dropColumn('reimbursement'); 
            $table->dropColumn('working_days');           
        });
    }
};
