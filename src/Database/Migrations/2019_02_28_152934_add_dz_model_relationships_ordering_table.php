<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDzModelRelationshipsOrderingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table('dz_model_relationships', function (Blueprint $table) {
            $table->string('default_ordering')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::table('dz_model_relationships', function (Blueprint $table) {
            $table->dropColumn('default_ordering');
        });
    }
}
