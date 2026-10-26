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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            // İlişki: Bu ürün kime ait? (Boş olabilir, yani depoda duruyor olabilir)
            // constrained(): Laravel otomatik olarak 'employees' tablosuna bağlar.
            // nullOnDelete(): Çalışan silinirse ürün silinmesin sahipsiz kalsın.
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name'); // Ürün Adı (MacBook Pro)
            $table->string('type'); // Tipi (Laptop)
            $table->string('serial_number')->unique(); // Seri No
            $table->string('image')->nullable(); // Ürün Resmi
            $table->boolean('is_available')->default(true); // Kullanıma uygun mu?
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
