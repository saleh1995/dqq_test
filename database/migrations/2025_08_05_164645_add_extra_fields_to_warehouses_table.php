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
        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('mobile')->nullable()->after('name');
            $table->string('city')->nullable()->after('mobile');
            $table->text('address')->nullable()->after('city');
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn([
                'mobile',
                'city',
                'address',
                'lat',
                'lng'
            ]);
        });
    }
};
