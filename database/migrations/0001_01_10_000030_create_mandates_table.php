<?php

declare(strict_types=1);

use App\Models\Election;
use App\Models\Party;
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
        Schema::create('mandates', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Election::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->smallInteger('county_id')
                ->unsigned()
                ->nullable();

            $table->foreign('county_id')
                ->references('id')
                ->on('counties');

            $table->foreignIdFor(Party::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->mediumInteger('mandates')->unsigned()->default(0);

            $table->unique(['election_id', 'county_id', 'party_id']);
        });

        Schema::create('_temp_mandates', function (Blueprint $table) {
            $table->bigInteger('election_id')
                ->unsigned()
                ->nullable();

            $table->smallInteger('county_id')
                ->unsigned()
                ->nullable();

            $table->bigInteger('party_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->mediumInteger('mandates')->unsigned();
        });
    }
};
