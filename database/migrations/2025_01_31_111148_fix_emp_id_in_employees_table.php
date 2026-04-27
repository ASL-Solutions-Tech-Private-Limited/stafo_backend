<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add column as nullable first
            $table->string('emp_id')->nullable()->after('id');
        });

        // Generate unique emp_ids for existing records
        $employees = DB::table('employees')->get();
        foreach ($employees as $employee) {
            DB::table('employees')
                ->where('id', $employee->id)
                ->update([
                    'emp_id' => 'EMP-' . str_pad($employee->id, 5, '0', STR_PAD_LEFT)
                ]);
        }

        Schema::table('employees', function (Blueprint $table) {
            // Add unique constraint after populating data
            $table->string('emp_id')->nullable(false)->unique()->change();
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(['emp_id']);
            $table->dropColumn('emp_id');
        });
    }
};