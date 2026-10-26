<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_project', function (Blueprint $table) {
            $table->unique(
                ['employee_id', 'project_id'],
                'employee_project_employee_id_project_id_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('employee_project', function (Blueprint $table) {
            $table->dropUnique(
                'employee_project_employee_id_project_id_unique'
            );
        });
    }
};