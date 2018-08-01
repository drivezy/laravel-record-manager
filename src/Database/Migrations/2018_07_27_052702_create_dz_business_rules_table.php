<?php

use Drivezy\LaravelRecordManager\Database\Seeds\ExecutionTypeSeeder;
use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDzBusinessRulesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_business_rules', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->increments('id');

            $table->string('name');
            $table->string('description')->nullable();

            $table->unsignedInteger('model_id')->nullable();
            $table->unsignedInteger('execution_type_id')->nullable();
            $table->unsignedInteger('script_id')->nullable();
            $table->unsignedInteger('filter_condition_id')->nullable();

            $table->string('model_hash')->nullable();

            $table->boolean('on_query')->default(false);
            $table->boolean('on_insert')->default(false);
            $table->boolean('on_update')->default(false);
            $table->boolean('on_delete')->default(false);

            $table->boolean('active')->default(true);
            $table->integer('order')->default(100);

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('model_id')->references('id')->on('dz_model_details');
            $table->foreign('script_id')->references('id')->on('dz_system_scripts');
            $table->foreign('execution_type_id')->references('id')->on('dz_lookup_values');
            $table->foreign('filter_condition_id')->references('id')->on('dz_system_scripts');

            $table->foreign('created_by')->references('id')->on($userTable);
            $table->foreign('updated_by')->references('id')->on($userTable);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['model_hash', 'active']);
        });

        ( new ExecutionTypeSeeder() )->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        ( new ExecutionTypeSeeder() )->drop();
        Schema::dropIfExists('dz_business_rules');
    }
}
