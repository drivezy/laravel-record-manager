<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDzColumnDetailsModelIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table('dz_column_details', function (Blueprint $table) {
            $table->unsignedInteger('reference_model_id')->nullable();
            $table->foreign('reference_model_id')->references('id')->on('dz_model_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::table('dz_column_details', function (Blueprint $table) {
            $table->dropForeign('dz_column_details_reference_model_id_foreign');
            $table->dropColumn('reference_model_id');
        });
    }
}
