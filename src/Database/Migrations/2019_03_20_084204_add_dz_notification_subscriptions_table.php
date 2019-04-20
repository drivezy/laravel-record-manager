<?php

use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDzNotificationSubscriptionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_notification_subscriptions', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->increments('id');

            $table->unsignedInteger('notification_id')->nullable();

            $table->string('source_type')->nullable();
            $table->unsignedInteger('source_id')->nullable();

            $table->boolean('email', false);
            $table->boolean('sms', false);
            $table->boolean('push', false);


            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('notification_id')->references('id')->on('dz_notification_details');

            $table->foreign('created_by')->references('id')->on($userTable);
            $table->foreign('updated_by')->references('id')->on($userTable);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::dropIfExists('dz_notification_subscriptions');
    }
}
