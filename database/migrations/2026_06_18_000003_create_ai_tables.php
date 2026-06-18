<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_usage_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('feature');
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->decimal('estimated_cost', 12, 6)->nullable();
            $table->string('status')->default('success');
            $table->text('error_message')->nullable();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index(['workspace_id', 'feature', 'created_at']);
            $table->index(['workspace_id', 'status']);
            $table->index('user_id');
        });

        Schema::create('ai_summaries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('summary_type');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('source_ids')->nullable();
            $table->string('title')->nullable();
            $table->longText('content');
            $table->string('status')->default('generated');
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['workspace_id', 'summary_type']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['workspace_id', 'generated_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('ai_summaries');
        Schema::dropIfExists('ai_usage_logs');
    }
};
