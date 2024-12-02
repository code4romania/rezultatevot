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

            $table->mediumInteger('locality_id')
                ->unsigned()
                ->nullable();

            $table->foreign('locality_id')
                ->references('id')
                ->on('localities');

            $table->morphs('votable');

            $table->tinyInteger('mandates')->unsigned()->default(0);

            $table->unique(['election_id', 'county_id', 'locality_id', 'votable_type', 'votable_id'], 'votes_unique_county_locality_index');
        });
    }
};
