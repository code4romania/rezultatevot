<?php

declare(strict_types=1);

use App\Models\Election;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('acronym');
            $table->string('color');

            $table->foreignIdFor(Election::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['name', 'election_id']);
            $table->unique(['color', 'election_id']);
            $table->unique(['acronym', 'election_id']);
        });
    }
};
