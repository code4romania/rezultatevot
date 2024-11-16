<?php

declare(strict_types=1);

use App\Models\Election;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vote_monitor_stats', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Election::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('key');

            $table->integer('value')->unsigned();
            $table->boolean('enabled')->default(false);
            $table->tinyInteger('order')->unsigned()->default(0);

            $table->timestamps();

            $table->unique(['key', 'election_id']);
        });
    }
};
