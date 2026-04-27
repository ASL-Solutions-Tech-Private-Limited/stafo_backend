<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Adding new columns after 'department_id'
            $table->date('date_of_birth')->nullable()->after('department_id');
            $table->string('gender')->nullable()->after('date_of_birth');
            $table->string('marital_status')->nullable()->after('gender');
            $table->string('blood_group')->nullable()->after('marital_status');
            $table->string('guardian_name')->nullable()->after('blood_group');
            $table->string('country')->nullable()->after('guardian_name');
            $table->string('state')->nullable()->after('country');
            $table->string('city')->nullable()->after('state');
            $table->text('address')->nullable()->after('city');
            $table->string('pin')->nullable()->after('address');
            $table->date('date_of_joining')->nullable()->after('pin');
            $table->date('date_of_leaving')->nullable()->after('date_of_joining');
            $table->unsignedBigInteger('job_title_id')->nullable()->after('date_of_leaving');
            $table->unsignedBigInteger('employee_type_id')->nullable()->after('job_title_id');
            $table->string('official_email_id')->nullable()->after('employee_type_id');
            $table->string('esi_number')->nullable()->after('official_email_id');
            $table->string('pf_number')->nullable()->after('esi_number');
            $table->integer('privileged_leave')->nullable()->after('pf_number');
            $table->integer('sick_leave')->nullable()->after('privileged_leave');
            $table->integer('casual_leave')->nullable()->after('sick_leave');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Dropping the columns when rolling back
            $table->dropColumn([
                'date_of_birth',
                'gender',
                'marital_status',
                'blood_group',
                'guardian_name',
                'country',
                'state',
                'city',
                'address',
                'pin',
                'date_of_joining',
                'date_of_leaving',
                'job_title_id',
                'employee_type_id',
                'official_email_id',
                'esi_number',
                'pf_number',
                'privileged_leave',
                'sick_leave',
                'casual_leave',
            ]);
        });
    }
};