<?php

use Drivezy\LaravelAccessManager\Models\Route;
use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDzModelDetailsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_model_details', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();
            $routeTable = ( new Route() )->getTable();

            $table->increments('id');

            $table->string('name');
            $table->string('description')->nullable();

            $table->unsignedInteger('route_id')->nullable();

            $table->string('namespace')->nullable();
            $table->string('table_name')->nullable();

            $table->char('allowed_permissions', 4)->nullable();

            $table->string('model_hash');
            $table->string('route_name')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('route_id')->references('id')->on($routeTable);
            $table->foreign('created_by')->references('id')->on($userTable);
            $table->foreign('updated_by')->references('id')->on($userTable);

            $table->timestamps();
            $table->softDeletes();

            $table->index('model_hash');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::dropIfExists('dz_model_details');
    }
}
