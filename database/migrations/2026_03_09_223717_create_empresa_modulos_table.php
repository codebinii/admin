<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa_modulos', function (Blueprint $table): void {
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('modulo_id');
            $table->foreign('empresa_id')->references('id')->on('01empresas')->onDelete('cascade');
            $table->foreign('modulo_id')->references('id')->on('04modulos')->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->primary(['empresa_id', 'modulo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_modulos');
    }
};
