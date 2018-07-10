<?php

use Drivezy\LaravelRecordManager\Database\Seeds\FormTypeSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDzCustomFormsFormTypeIdTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::table('dz_custom_forms', function (Blueprint $table) {
            $table->unsignedInteger('form_type_id')->nullable();
            $table->foreign('form_type_id')->references('id')->on('dz_lookup_values');
        });

        ( new FormTypeSeeder() )->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::table('dz_custom_forms', function (Blueprint $table) {
            $table->dropForeign('dz_custom_forms_form_type_id_foreign');
            $table->dropColumn('form_type_id');
        });

        ( new FormTypeSeeder() )->drop();
    }
}
