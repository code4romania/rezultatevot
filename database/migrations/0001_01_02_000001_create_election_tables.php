<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('slug')->unique();
            $table->date('date');
            $table->year('year')->storedAs('(YEAR(date))');
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_live')->default(false);
            $table->boolean('has_lists')->default(false);
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->tinyInteger('old_id')->unsigned()->nullable()->unique();
        });
    }
};
