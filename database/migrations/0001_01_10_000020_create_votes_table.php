<?php

declare(strict_types=1);

use App\Models\Country;
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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Election::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Country::class)
                ->nullable()
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

            $table->string('section');

            $table->tinyInteger('part')->unsigned();

            $table->morphs('votable');

            $table->mediumInteger('votes')->unsigned()->default(0);

            $table->unique(['election_id', 'county_id', 'section', 'votable_type', 'votable_id'], 'votes_unique_county_index');
            $table->unique(['election_id', 'country_id', 'section', 'votable_type', 'votable_id'], 'votes_unique_country_index');
        });

        Schema::create('_temp_votes', function (Blueprint $table) {
            $table->bigInteger('election_id')
                ->unsigned()
                ->nullable();

            $table->string('country_id', 2)
                ->nullable();

            $table->smallInteger('county_id')
                ->unsigned()
                ->nullable();

            $table->mediumInteger('locality_id')
                ->unsigned()
                ->nullable();

            $table->string('section');

            $table->tinyInteger('part')->unsigned();

            $table->string('votable_type');
            $table->bigInteger('votable_id')
                ->unsigned();

            $table->mediumInteger('votes')->unsigned();
        });
    }
};
