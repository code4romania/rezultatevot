<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\Election;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnouts', function (Blueprint $table) {
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

            $table->mediumInteger('initial_permanent')->unsigned();
            $table->mediumInteger('initial_complement')->unsigned();
            $table->mediumInteger('permanent')->unsigned();
            $table->mediumInteger('complement')->unsigned();
            $table->mediumInteger('supplement')->unsigned();
            $table->mediumInteger('mobile')->unsigned();

            $table->integer('initial_total')
                ->unsigned()
                ->storedAs(<<<'SQL'
                    initial_permanent + initial_complement
                SQL);

            $table->integer('total')
                ->unsigned()
                ->storedAs(<<<'SQL'
                    permanent + complement + supplement + mobile
                SQL);

            $table->float('percent', 2)
                ->unsigned()
                ->storedAs(<<<'SQL'
                    IF(initial_total > 0, ROUND(total  / initial_total * 100, 2), 0)
                SQL);

            $table->unique(['election_id', 'county_id', 'section']);
            $table->unique(['election_id', 'country_id', 'section']);

            $table->string('area', 1)->nullable();

            $table->mediumInteger('men_18-24')->unsigned()->nullable();
            $table->mediumInteger('men_25-34')->unsigned()->nullable();
            $table->mediumInteger('men_35-44')->unsigned()->nullable();
            $table->mediumInteger('men_45-64')->unsigned()->nullable();
            $table->mediumInteger('men_65')->unsigned()->nullable();
            $table->mediumInteger('women_18-24')->unsigned()->nullable();
            $table->mediumInteger('women_25-34')->unsigned()->nullable();
            $table->mediumInteger('women_35-44')->unsigned()->nullable();
            $table->mediumInteger('women_45-64')->unsigned()->nullable();
            $table->mediumInteger('women_65')->unsigned()->nullable();
        });

        Schema::create('_temp_turnouts', function (Blueprint $table) {
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

            $table->mediumInteger('initial_permanent')->unsigned();
            $table->mediumInteger('initial_complement')->unsigned();
            $table->mediumInteger('permanent')->unsigned();
            $table->mediumInteger('complement')->unsigned();
            $table->mediumInteger('supplement')->unsigned();
            $table->mediumInteger('mobile')->unsigned();

            $table->string('area', 1)->nullable();

            $table->mediumInteger('men_18-24')->unsigned()->nullable();
            $table->mediumInteger('men_25-34')->unsigned()->nullable();
            $table->mediumInteger('men_35-44')->unsigned()->nullable();
            $table->mediumInteger('men_45-64')->unsigned()->nullable();
            $table->mediumInteger('men_65')->unsigned()->nullable();
            $table->mediumInteger('women_18-24')->unsigned()->nullable();
            $table->mediumInteger('women_25-34')->unsigned()->nullable();
            $table->mediumInteger('women_35-44')->unsigned()->nullable();
            $table->mediumInteger('women_45-64')->unsigned()->nullable();
            $table->mediumInteger('women_65')->unsigned()->nullable();
        });
    }
};
