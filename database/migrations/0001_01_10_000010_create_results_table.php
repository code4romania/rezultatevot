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
        Schema::create('results', function (Blueprint $table) {
            $table->id();

            $table->boolean('has_issues')->default(false);

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

            $table->integer('eligible_voters_total')
                ->unsigned()
                ->storedAs(<<<'SQL'
                    eligible_voters_permanent + eligible_voters_special
                SQL);
            $table->mediumInteger('eligible_voters_permanent')->unsigned()->default(0);
            $table->mediumInteger('eligible_voters_special')->unsigned()->default(0);

            $table->integer('present_voters_total')
                ->unsigned()
                ->storedAs(<<<'SQL'
                    present_voters_permanent + present_voters_special + present_voters_supliment
                SQL);

            $table->mediumInteger('present_voters_permanent')->unsigned()->default(0);
            $table->mediumInteger('present_voters_special')->unsigned()->default(0);
            $table->mediumInteger('present_voters_supliment')->unsigned()->default(0);

            $table->mediumInteger('papers_received')->unsigned()->default(0);
            $table->mediumInteger('papers_unused')->unsigned()->default(0);

            $table->mediumInteger('votes_valid')->unsigned()->default(0);
            $table->mediumInteger('votes_null')->unsigned()->default(0);

            $table->unique(['election_id', 'county_id', 'section']);
            $table->unique(['election_id', 'country_id', 'section']);
        });

        Schema::create('_temp_results', function (Blueprint $table) {
            $table->boolean('has_issues')->default(false);

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

            $table->mediumInteger('eligible_voters_permanent')->unsigned();
            $table->mediumInteger('eligible_voters_special')->unsigned();
            $table->mediumInteger('present_voters_permanent')->unsigned();
            $table->mediumInteger('present_voters_special')->unsigned();
            $table->mediumInteger('present_voters_supliment')->unsigned();
            $table->mediumInteger('papers_received')->unsigned();
            $table->mediumInteger('papers_unused')->unsigned();
            $table->mediumInteger('votes_valid')->unsigned();
            $table->mediumInteger('votes_null')->unsigned();
        });
    }
};
