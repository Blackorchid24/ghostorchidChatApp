<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'google_id')) $table->string('google_id')->nullable()->unique();
            if (!Schema::hasColumn('users', 'avatar')) $table->text('avatar')->nullable();
            if (!Schema::hasColumn('users', 'profile_picture')) $table->string('profile_picture')->nullable();
            if (!Schema::hasColumn('users', 'status')) $table->string('status')->default('active');
            if (!Schema::hasColumn('users', 'last_seen_at')) $table->timestamp('last_seen_at')->nullable()->index();
            if (!Schema::hasColumn('users', 'last_login_at')) $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['google_id', 'avatar', 'profile_picture', 'status', 'last_seen_at', 'last_login_at'] as $column) {
                if (Schema::hasColumn('users', $column)) $table->dropColumn($column);
            }
        });
    }
};
