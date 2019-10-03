<?php

use Drivezy\LaravelRecordManager\Database\Seeds\ModelEventTypeSeeder;
use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDzObserverActionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_observer_actions', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->bigIncrements('id');

            $table->string('name');

            $table->unsignedBigInteger('observer_rule_id')->nullable();
            $table->unsignedBigInteger('script_id')->nullable();

            $table->unsignedBigInteger('execution_order')->default(1);
            $table->boolean('active')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('observer_rule_id')->references('id')->on('dz_system_scripts');
            $table->foreign('script_id')->references('id')->on('dz_system_scripts');

            $table->foreign('created_by')->references('id')->on($userTable);
            $table->foreign('updated_by')->references('id')->on($userTable);

            $table->timestamps();
            $table->softDeletes();
        });

        ( new ModelEventTypeSeeder() )->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::dropIfExists('dz_observer_actions');
        ( new ModelEventTypeSeeder() )->drop();
    }
}
