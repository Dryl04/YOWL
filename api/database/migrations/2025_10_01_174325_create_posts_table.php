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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->integer('likes')->default(0);
            $table->json('likes_users')->default(json_encode([]));
            $table->json('shares_users')->default(json_encode([]));
            $table->integer('shares')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")->on("users")->references("id");
            $table->string("status");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
