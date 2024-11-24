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
        Schema::table('records', function (Blueprint $table) {
            $table->mediumInteger('present_voters_mail')->unsigned()->default(0);

            $table->integer('present_voters_total')
                ->unsigned()
                ->storedAs(<<<'SQL'
                present_voters_permanent + present_voters_special + present_voters_supliment + present_voters_mail
            SQL)
                ->change();
        });

        Schema::table('_temp_records', function (Blueprint $table) {
            $table->mediumInteger('present_voters_mail')->unsigned();
        });
    }
};
