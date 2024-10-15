<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('git_orgs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->dateTime('last_update')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('git_orgs');
    }
};
