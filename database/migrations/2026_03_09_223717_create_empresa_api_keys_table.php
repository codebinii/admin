<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa_api_keys', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('01empresas')->onDelete('cascade');
            $table->string('key_prefix', 10)->index();
            $table->string('api_key_hash', 64);
            $table->string('nombre')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_api_keys');
    }
};
