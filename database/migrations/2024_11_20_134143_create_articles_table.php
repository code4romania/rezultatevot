<?php

declare(strict_types=1);

use App\Models\Election;
use App\Models\User;
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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Election::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(User::class, 'author_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('content');

            $table->timestamps();
            $table->timestamp('published_at')->nullable();
        });
    }
};
