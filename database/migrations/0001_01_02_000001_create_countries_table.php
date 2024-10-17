<?php

declare(strict_types=1);

use App\Imports\CountriesImport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->string('id', 2)->primary();
            $table->string('name');
            $table->json('aliases');
        });

        Excel::import(new CountriesImport, database_path('data/countries.csv'));
    }
};
