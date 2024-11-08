<?php

declare(strict_types=1);

use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('failed_import_rows', function (Blueprint $table) {
            $table->id();
            $table->json('data');

            $table->foreignIdFor(Import::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->text('validation_error')->nullable();
            $table->timestamps();
        });
    }
};
