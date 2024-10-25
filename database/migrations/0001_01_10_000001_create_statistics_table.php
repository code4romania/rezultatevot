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
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();

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

            $table->string('area', 1)->nullable();

            $table->unique(['election_id', 'county_id', 'section']);
            $table->unique(['election_id', 'country_id', 'section']);

            $table->mediumInteger('men_18-24')->unsigned();
            $table->mediumInteger('men_25-34')->unsigned();
            $table->mediumInteger('men_35-44')->unsigned();
            $table->mediumInteger('men_45-64')->unsigned();
            $table->mediumInteger('men_65+')->unsigned();
            $table->mediumInteger('women_18-24')->unsigned();
            $table->mediumInteger('women_25-34')->unsigned();
            $table->mediumInteger('women_35-44')->unsigned();
            $table->mediumInteger('women_45-64')->unsigned();
            $table->mediumInteger('women_65+')->unsigned();

            // $table->mediumInteger('men_18')->unsigned();
            // $table->mediumInteger('men_19')->unsigned();
            // $table->mediumInteger('men_20')->unsigned();
            // $table->mediumInteger('men_21')->unsigned();
            // $table->mediumInteger('men_22')->unsigned();
            // $table->mediumInteger('men_23')->unsigned();
            // $table->mediumInteger('men_24')->unsigned();
            // $table->mediumInteger('men_25')->unsigned();
            // $table->mediumInteger('men_26')->unsigned();
            // $table->mediumInteger('men_27')->unsigned();
            // $table->mediumInteger('men_28')->unsigned();
            // $table->mediumInteger('men_29')->unsigned();
            // $table->mediumInteger('men_30')->unsigned();
            // $table->mediumInteger('men_31')->unsigned();
            // $table->mediumInteger('men_32')->unsigned();
            // $table->mediumInteger('men_33')->unsigned();
            // $table->mediumInteger('men_34')->unsigned();
            // $table->mediumInteger('men_35')->unsigned();
            // $table->mediumInteger('men_36')->unsigned();
            // $table->mediumInteger('men_37')->unsigned();
            // $table->mediumInteger('men_38')->unsigned();
            // $table->mediumInteger('men_39')->unsigned();
            // $table->mediumInteger('men_40')->unsigned();
            // $table->mediumInteger('men_41')->unsigned();
            // $table->mediumInteger('men_42')->unsigned();
            // $table->mediumInteger('men_43')->unsigned();
            // $table->mediumInteger('men_44')->unsigned();
            // $table->mediumInteger('men_45')->unsigned();
            // $table->mediumInteger('men_46')->unsigned();
            // $table->mediumInteger('men_47')->unsigned();
            // $table->mediumInteger('men_48')->unsigned();
            // $table->mediumInteger('men_49')->unsigned();
            // $table->mediumInteger('men_50')->unsigned();
            // $table->mediumInteger('men_51')->unsigned();
            // $table->mediumInteger('men_52')->unsigned();
            // $table->mediumInteger('men_53')->unsigned();
            // $table->mediumInteger('men_54')->unsigned();
            // $table->mediumInteger('men_55')->unsigned();
            // $table->mediumInteger('men_56')->unsigned();
            // $table->mediumInteger('men_57')->unsigned();
            // $table->mediumInteger('men_58')->unsigned();
            // $table->mediumInteger('men_59')->unsigned();
            // $table->mediumInteger('men_60')->unsigned();
            // $table->mediumInteger('men_61')->unsigned();
            // $table->mediumInteger('men_62')->unsigned();
            // $table->mediumInteger('men_63')->unsigned();
            // $table->mediumInteger('men_64')->unsigned();
            // $table->mediumInteger('men_65')->unsigned();
            // $table->mediumInteger('men_66')->unsigned();
            // $table->mediumInteger('men_67')->unsigned();
            // $table->mediumInteger('men_68')->unsigned();
            // $table->mediumInteger('men_69')->unsigned();
            // $table->mediumInteger('men_70')->unsigned();
            // $table->mediumInteger('men_71')->unsigned();
            // $table->mediumInteger('men_72')->unsigned();
            // $table->mediumInteger('men_73')->unsigned();
            // $table->mediumInteger('men_74')->unsigned();
            // $table->mediumInteger('men_75')->unsigned();
            // $table->mediumInteger('men_76')->unsigned();
            // $table->mediumInteger('men_77')->unsigned();
            // $table->mediumInteger('men_78')->unsigned();
            // $table->mediumInteger('men_79')->unsigned();
            // $table->mediumInteger('men_80')->unsigned();
            // $table->mediumInteger('men_81')->unsigned();
            // $table->mediumInteger('men_82')->unsigned();
            // $table->mediumInteger('men_83')->unsigned();
            // $table->mediumInteger('men_84')->unsigned();
            // $table->mediumInteger('men_85')->unsigned();
            // $table->mediumInteger('men_86')->unsigned();
            // $table->mediumInteger('men_87')->unsigned();
            // $table->mediumInteger('men_88')->unsigned();
            // $table->mediumInteger('men_89')->unsigned();
            // $table->mediumInteger('men_90')->unsigned();
            // $table->mediumInteger('men_91')->unsigned();
            // $table->mediumInteger('men_92')->unsigned();
            // $table->mediumInteger('men_93')->unsigned();
            // $table->mediumInteger('men_94')->unsigned();
            // $table->mediumInteger('men_95')->unsigned();
            // $table->mediumInteger('men_96')->unsigned();
            // $table->mediumInteger('men_97')->unsigned();
            // $table->mediumInteger('men_98')->unsigned();
            // $table->mediumInteger('men_99')->unsigned();
            // $table->mediumInteger('men_100')->unsigned();
            // $table->mediumInteger('men_101')->unsigned();
            // $table->mediumInteger('men_102')->unsigned();
            // $table->mediumInteger('men_103')->unsigned();
            // $table->mediumInteger('men_104')->unsigned();
            // $table->mediumInteger('men_105')->unsigned();
            // $table->mediumInteger('men_106')->unsigned();
            // $table->mediumInteger('men_107')->unsigned();
            // $table->mediumInteger('men_108')->unsigned();
            // $table->mediumInteger('men_109')->unsigned();
            // $table->mediumInteger('men_110')->unsigned();
            // $table->mediumInteger('men_111')->unsigned();
            // $table->mediumInteger('men_112')->unsigned();
            // $table->mediumInteger('men_113')->unsigned();
            // $table->mediumInteger('men_114')->unsigned();
            // $table->mediumInteger('men_115')->unsigned();
            // $table->mediumInteger('men_116')->unsigned();
            // $table->mediumInteger('men_117')->unsigned();
            // $table->mediumInteger('men_118')->unsigned();
            // $table->mediumInteger('men_119')->unsigned();
            // $table->mediumInteger('men_120')->unsigned();
            // $table->mediumInteger('women_18')->unsigned();
            // $table->mediumInteger('women_19')->unsigned();
            // $table->mediumInteger('women_20')->unsigned();
            // $table->mediumInteger('women_21')->unsigned();
            // $table->mediumInteger('women_22')->unsigned();
            // $table->mediumInteger('women_23')->unsigned();
            // $table->mediumInteger('women_24')->unsigned();
            // $table->mediumInteger('women_25')->unsigned();
            // $table->mediumInteger('women_26')->unsigned();
            // $table->mediumInteger('women_27')->unsigned();
            // $table->mediumInteger('women_28')->unsigned();
            // $table->mediumInteger('women_29')->unsigned();
            // $table->mediumInteger('women_30')->unsigned();
            // $table->mediumInteger('women_31')->unsigned();
            // $table->mediumInteger('women_32')->unsigned();
            // $table->mediumInteger('women_33')->unsigned();
            // $table->mediumInteger('women_34')->unsigned();
            // $table->mediumInteger('women_35')->unsigned();
            // $table->mediumInteger('women_36')->unsigned();
            // $table->mediumInteger('women_37')->unsigned();
            // $table->mediumInteger('women_38')->unsigned();
            // $table->mediumInteger('women_39')->unsigned();
            // $table->mediumInteger('women_40')->unsigned();
            // $table->mediumInteger('women_41')->unsigned();
            // $table->mediumInteger('women_42')->unsigned();
            // $table->mediumInteger('women_43')->unsigned();
            // $table->mediumInteger('women_44')->unsigned();
            // $table->mediumInteger('women_45')->unsigned();
            // $table->mediumInteger('women_46')->unsigned();
            // $table->mediumInteger('women_47')->unsigned();
            // $table->mediumInteger('women_48')->unsigned();
            // $table->mediumInteger('women_49')->unsigned();
            // $table->mediumInteger('women_50')->unsigned();
            // $table->mediumInteger('women_51')->unsigned();
            // $table->mediumInteger('women_52')->unsigned();
            // $table->mediumInteger('women_53')->unsigned();
            // $table->mediumInteger('women_54')->unsigned();
            // $table->mediumInteger('women_55')->unsigned();
            // $table->mediumInteger('women_56')->unsigned();
            // $table->mediumInteger('women_57')->unsigned();
            // $table->mediumInteger('women_58')->unsigned();
            // $table->mediumInteger('women_59')->unsigned();
            // $table->mediumInteger('women_60')->unsigned();
            // $table->mediumInteger('women_61')->unsigned();
            // $table->mediumInteger('women_62')->unsigned();
            // $table->mediumInteger('women_63')->unsigned();
            // $table->mediumInteger('women_64')->unsigned();
            // $table->mediumInteger('women_65')->unsigned();
            // $table->mediumInteger('women_66')->unsigned();
            // $table->mediumInteger('women_67')->unsigned();
            // $table->mediumInteger('women_68')->unsigned();
            // $table->mediumInteger('women_69')->unsigned();
            // $table->mediumInteger('women_70')->unsigned();
            // $table->mediumInteger('women_71')->unsigned();
            // $table->mediumInteger('women_72')->unsigned();
            // $table->mediumInteger('women_73')->unsigned();
            // $table->mediumInteger('women_74')->unsigned();
            // $table->mediumInteger('women_75')->unsigned();
            // $table->mediumInteger('women_76')->unsigned();
            // $table->mediumInteger('women_77')->unsigned();
            // $table->mediumInteger('women_78')->unsigned();
            // $table->mediumInteger('women_79')->unsigned();
            // $table->mediumInteger('women_80')->unsigned();
            // $table->mediumInteger('women_81')->unsigned();
            // $table->mediumInteger('women_82')->unsigned();
            // $table->mediumInteger('women_83')->unsigned();
            // $table->mediumInteger('women_84')->unsigned();
            // $table->mediumInteger('women_85')->unsigned();
            // $table->mediumInteger('women_86')->unsigned();
            // $table->mediumInteger('women_87')->unsigned();
            // $table->mediumInteger('women_88')->unsigned();
            // $table->mediumInteger('women_89')->unsigned();
            // $table->mediumInteger('women_90')->unsigned();
            // $table->mediumInteger('women_91')->unsigned();
            // $table->mediumInteger('women_92')->unsigned();
            // $table->mediumInteger('women_93')->unsigned();
            // $table->mediumInteger('women_94')->unsigned();
            // $table->mediumInteger('women_95')->unsigned();
            // $table->mediumInteger('women_96')->unsigned();
            // $table->mediumInteger('women_97')->unsigned();
            // $table->mediumInteger('women_98')->unsigned();
            // $table->mediumInteger('women_99')->unsigned();
            // $table->mediumInteger('women_100')->unsigned();
            // $table->mediumInteger('women_101')->unsigned();
            // $table->mediumInteger('women_102')->unsigned();
            // $table->mediumInteger('women_103')->unsigned();
            // $table->mediumInteger('women_104')->unsigned();
            // $table->mediumInteger('women_105')->unsigned();
            // $table->mediumInteger('women_106')->unsigned();
            // $table->mediumInteger('women_107')->unsigned();
            // $table->mediumInteger('women_108')->unsigned();
            // $table->mediumInteger('women_109')->unsigned();
            // $table->mediumInteger('women_110')->unsigned();
            // $table->mediumInteger('women_111')->unsigned();
            // $table->mediumInteger('women_112')->unsigned();
            // $table->mediumInteger('women_113')->unsigned();
            // $table->mediumInteger('women_114')->unsigned();
            // $table->mediumInteger('women_115')->unsigned();
            // $table->mediumInteger('women_116')->unsigned();
            // $table->mediumInteger('women_117')->unsigned();
            // $table->mediumInteger('women_118')->unsigned();
            // $table->mediumInteger('women_119')->unsigned();
            // $table->mediumInteger('women_120')->unsigned();
        });
    }
};
