<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['phone_verified', 'whatsapp_verified']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->timestamp('whatsapp_verified_at')->nullable()->after('whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['phone_verified_at', 'whatsapp_verified_at']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('phone_verified')->default(false)->after('phone');
            $table->boolean('whatsapp_verified')->default(false)->after('whatsapp');
        });
    }
};
