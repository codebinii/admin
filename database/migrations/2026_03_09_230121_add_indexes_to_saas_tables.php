<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Composite index: auth queries filter by key_prefix + activo simultaneously
        Schema::table('empresa_api_keys', function (Blueprint $table): void {
            $table->index(['key_prefix', 'activo'], 'empresa_api_keys_prefix_activo_index');
        });

        // Foreign key indexes: module queries filter by empresa_id or modulo_id alone
        Schema::table('empresa_modulos', function (Blueprint $table): void {
            $table->index('empresa_id', 'empresa_modulos_empresa_id_index');
            $table->index('modulo_id',  'empresa_modulos_modulo_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('empresa_api_keys', function (Blueprint $table): void {
            $table->dropIndex('empresa_api_keys_prefix_activo_index');
        });

        Schema::table('empresa_modulos', function (Blueprint $table): void {
            $table->dropIndex('empresa_modulos_empresa_id_index');
            $table->dropIndex('empresa_modulos_modulo_id_index');
        });
    }
};
