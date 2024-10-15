<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('git_repos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')->constrained('git_orgs')->onDelete('cascade');

            $table->string('name');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('git_repos');
    }
};
