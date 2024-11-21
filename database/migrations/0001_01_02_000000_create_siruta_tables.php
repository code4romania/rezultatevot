<?php

declare(strict_types=1);

use App\Imports\Siruta\CountiesImport;
use App\Imports\Siruta\LocalitiesImport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counties', function (Blueprint $table) {
            $table->smallInteger('id')->unsigned()->primary();
            $table->string('code', 2)->unique();
            $table->string('name');

            $table->smallInteger('old_id')->unsigned()->nullable()->unique();
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

            $table->json('old_ids')->nullable();
        });

        // Excel::import(new CountiesImport, '240708-siruta.xlsx', 'seed-data');
        // Excel::import(new LocalitiesImport, '240708-siruta.xlsx', 'seed-data');

        Schema::withoutForeignKeyConstraints(function () {
            DB::unprepared(
                Storage::disk('seed-data')->get('siruta.sql')
            );
        });
    }
};
