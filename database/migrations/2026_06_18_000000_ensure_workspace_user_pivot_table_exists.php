<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('workspace_user')) {
            Schema::create('workspace_user', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('role_key')->nullable();
                $table->string('status')->default('active');
                $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('joined_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'workspace_id'], 'workspace_user_user_id_workspace_id_unique');
                $table->index(['workspace_id', 'status']);
            });

            return;
        }

        Schema::table('workspace_user', function (Blueprint $table): void {
            if (! Schema::hasColumn('workspace_user', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            }

            if (! Schema::hasColumn('workspace_user', 'workspace_id')) {
                $table->foreignId('workspace_id')->after('user_id')->constrained()->cascadeOnDelete();
            }

            if (! Schema::hasColumn('workspace_user', 'role_key')) {
                $table->string('role_key')->nullable()->after('workspace_id');
            }

            if (! Schema::hasColumn('workspace_user', 'status')) {
                $table->string('status')->default('active')->after('role_key');
            }

            if (! Schema::hasColumn('workspace_user', 'invited_by')) {
                $table->foreignId('invited_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('workspace_user', 'joined_at')) {
                $table->timestamp('joined_at')->nullable()->after('invited_by');
            }

            if (! Schema::hasColumn('workspace_user', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (! Schema::hasColumn('workspace_user', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        if ($this->canAddMembershipUniqueIndex()) {
            Schema::table('workspace_user', function (Blueprint $table): void {
                $table->unique(['user_id', 'workspace_id'], 'workspace_user_user_id_workspace_id_unique');
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty: this migration is additive and must not remove tenant membership data.
    }

    private function canAddMembershipUniqueIndex(): bool
    {
        if (! Schema::hasColumn('workspace_user', 'user_id') || ! Schema::hasColumn('workspace_user', 'workspace_id')) {
            return false;
        }

        if ($this->hasDuplicateMemberships() || $this->hasMembershipUniqueIndex()) {
            return false;
        }

        return true;
    }

    private function hasDuplicateMemberships(): bool
    {
        return DB::table('workspace_user')
            ->select('user_id', 'workspace_id')
            ->groupBy('user_id', 'workspace_id')
            ->havingRaw('COUNT(*) > 1')
            ->limit(1)
            ->exists();
    }

    private function hasMembershipUniqueIndex(): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            return collect(DB::select("\n                SELECT INDEX_NAME\n                FROM INFORMATION_SCHEMA.STATISTICS\n                WHERE TABLE_SCHEMA = DATABASE()\n                    AND TABLE_NAME = 'workspace_user'\n                    AND NON_UNIQUE = 0\n                    AND COLUMN_NAME IN ('user_id', 'workspace_id')\n                GROUP BY INDEX_NAME\n                HAVING COUNT(DISTINCT COLUMN_NAME) = 2\n            "))->isNotEmpty();
        }

        if ($driver === 'sqlite') {
            foreach (DB::select("PRAGMA index_list('workspace_user')") as $index) {
                if (! ($index->unique ?? false)) {
                    continue;
                }

                $columns = collect(DB::select("PRAGMA index_info('{$index->name}')"))->pluck('name')->all();

                if (empty(array_diff(['user_id', 'workspace_id'], $columns))) {
                    return true;
                }
            }
        }

        return false;
    }
};
