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
        Schema::create('scheduled_jobs', function (Blueprint $table) {
            $table->id();

            $table->string('job');
            $table->string('cron');
            $table->boolean('is_enabled')->default(false);

            $table->string('source_part')->nullable();
            $table->string('source_url')->nullable();
            $table->string('source_username')->nullable();
            $table->string('source_password')->nullable();

            $table->foreignIdFor(Election::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
            $table->timestamp('last_run_at')->nullable();
        });
    }
};
