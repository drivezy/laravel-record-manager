<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDzColumnDetailsAdditionalQueryParamsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::table('dz_column_details', function (Blueprint $table) {
            $table->string('additional_query_params', 1024)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::table('dz_column_details', function (Blueprint $table) {
            $table->dropColumn('additional_query_params');
        });
    }
}
