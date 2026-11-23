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
            $table->string('cargo', 20)->nullable()->after('email'); // admin | porteiro | recepcionista
            $table->string('phone', 20)->nullable()->after('cargo');
            $table->string('foto_path')->nullable()->after('phone');
            $table->string('foto_thumb_path')->nullable()->after('foto_path');
            $table->boolean('active')->default(true)->after('foto_thumb_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
