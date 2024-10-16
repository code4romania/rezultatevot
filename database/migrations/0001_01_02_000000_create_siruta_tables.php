<?php

declare(strict_types=1);

use App\Imports\Siruta\CountiesImport;
use App\Imports\Siruta\LocalitiesImport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counties', function (Blueprint $table) {
            $table->smallInteger('id')->unsigned()->primary();
            $table->string('name');
        });

        Schema::create('localities', function (Blueprint $table) {
            $table->mediumInteger('id')->unsigned()->primary();

            $table->smallInteger('county_id')->unsigned();
            $table->foreign('county_id')
                ->references('id')
                ->on('counties');

            $table->tinyInteger('level')->unsigned();

            $table->tinyInteger('type')->unsigned();

            $table->mediumInteger('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')
                ->references('id')
                ->on('localities');

            $table->string('name');
        });

        // Excel::import(new CountiesImport, database_path('data/siruta_s1_2024.xlsx'));
        // Excel::import(new LocalitiesImport, database_path('data/siruta_s1_2024.xlsx'));

        Schema::withoutForeignKeyConstraints(function () {
            DB::unprepared(
                File::get(database_path('data/siruta.sql'))
            );
        });
    }
};
