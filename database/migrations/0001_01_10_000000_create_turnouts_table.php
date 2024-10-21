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

            $table->mediumInteger('locality_id')
                ->unsigned()
                ->nullable();

            $table->foreign('locality_id')
                ->references('id')
                ->on('localities');

            $table->mediumInteger('initial_permanent')->unsigned();
            $table->mediumInteger('initial_complement')->unsigned();
            $table->mediumInteger('permanent')->unsigned();
            $table->mediumInteger('complement')->unsigned();
            $table->mediumInteger('supplement')->unsigned();
            $table->mediumInteger('mobile')->unsigned();

            $table->integer('initial_total')
                ->unsigned()
                ->virtualAs(<<<'SQL'
                    initial_permanent + initial_complement
                SQL);

            $table->integer('total')
                ->unsigned()
                ->virtualAs(<<<'SQL'
                    permanent + complement + supplement + mobile
                SQL);

            $table->float('percent', 2)
                ->unsigned()
                ->virtualAs(<<<'SQL'
                    ROUND(total  / initial_total * 100, 2)
                SQL);

            $table->unique(['election_id', 'locality_id']);
            $table->unique(['election_id', 'country_id']);
        });
    }
};
