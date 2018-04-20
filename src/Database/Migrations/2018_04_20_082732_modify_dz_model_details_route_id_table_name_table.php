<?php

use Drivezy\LaravelAccessManager\Models\Route;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDzModelDetailsRouteIdTableNameTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::table('dz_model_details', function (Blueprint $table) {
            $routeTable = ( new Route() )->getTable();

            $table->unsignedInteger('route_id')->nullable();
            $table->string('table_name')->nullable();

            $table->foreign('route_id')->references('id')->on($routeTable);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::table('dz_model_details', function (Blueprint $table) {
            $table->dropForeign('dz_model_details_route_id_foreign');
            $table->dropColumn('route_id');
        });
    }
}
