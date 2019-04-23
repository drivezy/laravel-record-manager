<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDzColumnDetailsReferenceColumnIdTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::table('dz_column_details', function (Blueprint $table) {
            $table->unsignedInteger('reference_column_id')->nullable();
            $table->foreign('reference_column_id')->references('id')->on('dz_column_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::table('dz_column_details', function (Blueprint $table) {
            $table->dropForeign('dz_column_details_reference_column_id_foreign');
            $table->dropColumn('reference_column_id');
        });
    }
}
