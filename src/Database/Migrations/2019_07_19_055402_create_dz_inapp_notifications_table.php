<?php

use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDzInappNotificationsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_inapp_notifications', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->increments('id');
            $table->string('name')->nullable();

            $table->unsignedInteger('notification_id')->nullable();
            $table->unsignedInteger('platform_id')->nullable();

            $table->string('content', 1024)->nullable();
            $table->text('description')->nullable();

            $table->string('deep_link_url', 1024)->nullable();
            $table->integer('offset_end_time')->nullable();

            $table->unsignedInteger('run_condition_id')->nullable();

            $table->boolean('default_users')->default(true);
            $table->boolean('active')->default(true);

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('notification_id')->references('id')->on('dz_notification_details');
            $table->foreign('platform_id')->references('id')->on('dz_lookup_values');
            $table->foreign('run_condition_id')->references('id')->on('dz_system_scripts');

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
        Schema::dropIfExists('dz_inapp_notifications');
    }
}
