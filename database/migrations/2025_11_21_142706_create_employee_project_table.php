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
        Schema::create('employee_project', function (Blueprint $table) {
            $table->id();

            // Çalışan ID'si
            // cascadeOnDelete: Çalışan silinirse, proje kaydı da bu tablodan silinsin (Veri kirliliği olmasın)
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            // Proje ID'si
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_project');
    }
};
