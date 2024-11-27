<?php

declare(strict_types=1);

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
        Schema::table('_temp_turnouts', function (Blueprint $table) {
            $table->id();

            $table->foreign('election_id')
                ->references('id')
                ->on('elections')
                ->cascadeOnDelete();
        });

        Schema::table('_temp_records', function (Blueprint $table) {
            $table->id();

            $table->foreign('election_id')
                ->references('id')
                ->on('elections')
                ->cascadeOnDelete();
        });

        Schema::table('_temp_mandates', function (Blueprint $table) {
            $table->id();

            $table->foreign('election_id')
                ->references('id')
                ->on('elections')
                ->cascadeOnDelete();
        });

        Schema::table('_temp_votes', function (Blueprint $table) {
            $table->id();

            $table->foreign('election_id')
                ->references('id')
                ->on('elections')
                ->cascadeOnDelete();
        });
    }
};
