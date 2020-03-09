<?php

use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDzNotificationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create('dz_notification_details', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->increments('id');

            $table->string('name');
            $table->string('description', 2048)->nullable();

            $table->unsignedInteger('data_model_id')->nullable();
            $table->string('includes', 2048)->nullable();

            $table->unsignedInteger('custom_data_id')->nullable();
            $table->unsignedInteger('run_condition_id')->nullable();

            $table->boolean('active', false);

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('data_model_id')->references('id')->on('dz_model_details');
            $table->foreign('run_condition_id')->references('id')->on('dz_system_scripts');
            $table->foreign('custom_data_id')->references('id')->on('dz_system_scripts');

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
    public function down ()
    {
        Schema::dropIfExists('dz_notification_details');
    }
}
