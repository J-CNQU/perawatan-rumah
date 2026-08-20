<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Contoh: AC Split 1PK, Honda Vario
            $table->enum('condition', ['New', 'Used'])->default('New'); // Kelayakan: Baru / Bekas
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->date('purchase_date');
            $table->date('warranty_expiration')->nullable();
            $table->integer('maintenance_interval_months')->default(3); // Interval perawatan (misal 3 bulan sekali)
            $table->text('description')->nullable();
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
