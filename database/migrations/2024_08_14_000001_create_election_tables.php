<?php

declare(strict_types=1);

use App\Models\ElectionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('election_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ElectionType::class, 'type_id')
                ->constrained('election_types')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('slug')->unique();
            $table->year('year');
            $table->boolean('is_live');
            $table->timestamps();
        });
    }
};
