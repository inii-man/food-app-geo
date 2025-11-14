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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('merchant_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 8)->nullable(); // Koordinat latitude customer
            $table->decimal('longitude', 11, 8)->nullable(); // Koordinat longitude customer
            $table->string('address')->nullable(); // Alamat customer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['merchant_id']);
            $table->dropColumn(['merchant_id', 'latitude', 'longitude', 'address']);
        });
    }
};
