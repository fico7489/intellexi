<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->date('dob')->nullable();
            $table->string('role')->nullable();
            $table->unsignedBigInteger('user_type_id')->nullable();

            $table->foreign('user_type_id')->references('id')->on('user_types');

            $table->timestamps();
        });
    }

    public function down(): void{}
};
