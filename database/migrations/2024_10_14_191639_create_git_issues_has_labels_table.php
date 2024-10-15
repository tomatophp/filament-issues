<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('git_issues_has_labels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('issue_id')->constrained('git_issues')->onDelete('cascade');
            $table->foreignId('label_id')->constrained('git_labels')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('git_issues_has_labels');
    }
};
