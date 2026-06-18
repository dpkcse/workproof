<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('report_exports')) {
            Schema::create('report_exports', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('report_key');
                $table->json('filters')->nullable();
                $table->string('format');
                $table->string('status')->default('pending');
                $table->string('file_path')->nullable();
                $table->string('file_disk')->default('local');
                $table->text('error_message')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['workspace_id', 'report_key']);
                $table->index(['workspace_id', 'status']);
                $table->index('user_id');
            });
        }

        if (! Schema::hasTable('performance_scores')) {
            Schema::create('performance_scores', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->date('score_date');
                $table->string('period_type')->default('daily');
                $table->integer('assigned_tasks_count')->default(0);
                $table->integer('completed_tasks_count')->default(0);
                $table->integer('overdue_tasks_count')->default(0);
                $table->integer('reopened_tasks_count')->default(0);
                $table->boolean('daily_report_submitted')->default(false);
                $table->boolean('daily_report_late')->default(false);
                $table->integer('blocker_count')->default(0);
                $table->integer('proof_rejection_count')->default(0);
                $table->integer('approval_rejection_count')->default(0);
                $table->decimal('score', 5, 2)->default(0);
                $table->json('metadata')->nullable();
                $table->timestamp('calculated_at')->nullable();
                $table->timestamps();

                $table->unique(['workspace_id', 'user_id', 'score_date', 'period_type'], 'perf_scores_unique_period');
                $table->index(['workspace_id', 'score_date']);
                $table->index(['workspace_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('login_histories')) {
            Schema::create('login_histories', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('logged_in_at');
                $table->boolean('successful')->default(true);
                $table->string('failure_reason')->nullable();
                $table->timestamps();

                $table->index(['workspace_id', 'logged_in_at']);
                $table->index(['user_id', 'logged_in_at']);
            });
        }

        $this->addIndexIfColumnsExist(
            'task_activity_logs',
            ['workspace_id', 'task_id', 'created_at'],
            'task_activity_logs_archive_idx'
        );

        $this->addIndexIfColumnsExist(
            'audit_logs',
            ['workspace_id', 'subject_type', 'subject_id', 'created_at'],
            'audit_logs_archive_idx'
        );
    }

    public function down(): void
    {
        // Phase migrations are intentionally non-destructive to preserve tenant data.
    }

    private function addIndexIfColumnsExist(string $table, array $columns, string $indexName): void
    {
        if (! Schema::hasTable($table) || $this->hasIndex($table, $indexName)) {
            return;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return;
            }
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName): void {
            $blueprint->index($columns, $indexName);
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        if (method_exists(Schema::getFacadeRoot(), 'hasIndex')) {
            return Schema::hasIndex($table, $indexName);
        }

        if (method_exists(Schema::getFacadeRoot(), 'getIndexes')) {
            foreach (Schema::getIndexes($table) as $index) {
                if (($index['name'] ?? null) === $indexName) {
                    return true;
                }
            }
        }

        return false;
    }
};
