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
            $table->string('work')->nullable()->after('dateOfBirth');
            $table->string('photo', 500)->nullable()->after('work');
            $table->string('role', 5)->nullable()->after('photo');
            $table->string('bio')->nullable()->after('role');
            $table->string('telephone')->nullable()->after('bio');
            $table->string('activation_token')->nullable()->unique();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['work', 'photo']);
        });
    }
};
