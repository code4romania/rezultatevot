<?php

declare(strict_types=1);

use App\Models\City;
use App\Models\County;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siruta');
            $table->string('name');
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(County::class)->constrained();
            $table->tinyInteger('level')->unsigned();
            $table->tinyInteger('type')->unsigned();
            $table->foreignIdFor(City::class, 'parent_id')->nullable()->constrained('cities');
            $table->string('name');
        });

        Schema::withoutForeignKeyConstraints(function () {
            DB::unprepared(
                File::get(database_path('data/siruta.sql'))
            );
        });
    }
};
