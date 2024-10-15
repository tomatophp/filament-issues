<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('git_issues', function (Blueprint $table) {
            $table->id();
            $table->string('issue_id')->unique();

            $table->foreignId('repo_id')->constrained('git_repos')->onDelete('cascade');
            $table->foreignId('createdBy')->constrained('git_issue_owners')->onDelete('cascade');

            $table->integer('number')->default(0)->nullable();
            $table->string('repoName');
            $table->string('repoUrl');
            $table->string('title');
            $table->string('url');
            $table->longText('body')->nullable();
            $table->integer('commentCount')->default(0)->nullable();
            $table->dateTime('createdAt');
            $table->boolean('isPullRequest')->default(0)->nullable();

            $table->boolean('is_public')->default(0)->nullable();
            $table->boolean('is_trend')->default(0)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('git_issues');
    }
};
