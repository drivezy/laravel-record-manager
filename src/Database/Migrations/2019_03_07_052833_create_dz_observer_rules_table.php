<?php

use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDzObserverRulesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_observer_rules', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->increments('id');

            $table->string('name');
            $table->string('description');

            $table->unsignedInteger('model_id')->nullable();
            $table->unsignedInteger('trigger_type_id')->nullable();

            $table->unsignedInteger('execution_order')->default(1);
            $table->boolean('active')->default(true);

            $table->text('filter_condition')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('model_id')->references('id')->on('dz_model_details');
            $table->foreign('trigger_type_id')->references('id')->on('dz_lookup_values');

            $table->foreign('created_by')->references('id')->on($userTable);
            $table->foreign('updated_by')->references('id')->on($userTable);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::dropIfExists('dz_observer_rules');
    }
}
