<?php

use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateDzSmsNotificationsTable
 */
class CreateDzSmsNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create('dz_sms_notifications', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->increments('id');

            $table->string('name');

            $table->unsignedInteger('notification_id')->nullable();
            $table->unsignedInteger('sms_template_id')->nullable();
            $table->unsignedInteger('run_condition_id')->nullable();

            $table->boolean('default_users', true);
            $table->boolean('active', false);

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('notification_id')->references('id')->on('dz_notification_details');
            $table->foreign('sms_template_id')->references('id')->on('dz_sms_templates');
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
    public function down ()
    {
        Schema::dropIfExists('dz_sms_notifications');
    }
}
