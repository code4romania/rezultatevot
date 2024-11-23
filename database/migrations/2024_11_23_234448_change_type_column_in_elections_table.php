<?php

declare(strict_types=1);

use App\Enums\ElectionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->tinyInteger('new_type')->after('type');
        });

        $this->migrateData();

        Schema::table('elections', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->renameColumn('new_type', 'type');
        });
    }

    protected function migrateData(): void
    {
        collect([
            'presidential' => ElectionType::PRESIDENTIAL,
            'referendum' => ElectionType::REFERENDUM,
            'parliamentary' => ElectionType::PARLIAMENTARY,
            'local' => ElectionType::LOCAL,
            'euro' => ElectionType::EURO,
        ])->each(
            fn (ElectionType $newType, string $oldType) => DB::table('elections')
                ->where('type', $oldType)
                ->update(['new_type' => $newType])
        );
    }
};
