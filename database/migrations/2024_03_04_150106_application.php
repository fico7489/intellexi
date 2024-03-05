<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('club', 255)->nullable();
            $table->uuid('race_id');
            $table->uuid('user_id');

            $table->foreign('race_id')->references('id')->on('races');
            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        //
    }
};
