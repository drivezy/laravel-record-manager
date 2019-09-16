<?php

use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDzEmailNotificationsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_email_notifications', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->bigIncrements('id');

            $table->string('name');

            $table->unsignedBigInteger('notification_id')->nullable();

            $table->string('template_name')->nullable();
            $table->string('subject', '2048')->nullable();

            $table->unsignedBigInteger('run_condition_id')->nullable();
            $table->foreign('run_condition_id')->references('id')->on('dz_system_scripts');

            $table->unsignedBigInteger('body_id')->nullable();
            $table->foreign('body_id')->references('id')->on('dz_system_scripts');

            $table->boolean('default_users', true);
            $table->boolean('active', false);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('notification_id')->references('id')->on('dz_notification_details');
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
        Schema::dropIfExists('dz_email_notifications');
    }
}
