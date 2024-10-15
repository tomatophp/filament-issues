<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('git_issue_owners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('profilePictureUrl')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('git_issue_owners');
    }
};
