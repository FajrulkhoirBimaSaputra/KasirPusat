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
   Schema::table('ingredients', function (Blueprint $table) {
        // Menambahkan kolom foreign key yang merujuk ke tabel users
        $table->foreignId('last_updated_by')
              ->nullable() 
              ->constrained('users')
              ->onDelete('set null'); 
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropForeign(['last_updated_by']);
            $table->dropColumn('last_updated_by');
        });
    }
};