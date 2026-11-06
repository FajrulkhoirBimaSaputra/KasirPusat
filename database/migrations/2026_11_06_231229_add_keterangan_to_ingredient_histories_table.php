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
        Schema::table('ingredient_histories', function (Blueprint $table) {
            $table->string('keterangan')->nullable()->after('difference');
        });
    }

    public function down()
    {
        Schema::table('ingredient_histories', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
