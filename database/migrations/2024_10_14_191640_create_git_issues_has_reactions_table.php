<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('git_issues_has_reactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('issue_id')->constrained('git_issues')->onDelete('cascade');
            $table->foreignId('reaction_id')->constrained('git_reactions')->onDelete('cascade');

            $table->integer('count')->default(0)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('git_issues_has_reactions');
    }
};
