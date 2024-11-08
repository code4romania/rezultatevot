<?php

declare(strict_types=1);

use App\Models\Election;
use App\Models\Party;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();

            $table->foreignIdFor(Election::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Party::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['name', 'election_id']);
        });
    }
};
