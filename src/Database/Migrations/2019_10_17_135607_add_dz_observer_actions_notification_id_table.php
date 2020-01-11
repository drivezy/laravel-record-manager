<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDzObserverActionsNotificationIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table('dz_observer_actions', function (Blueprint $table) {
            $table->unsignedInteger('notification_id')->nullable();
            $table->foreign('notification_id')->references('id')->on('dz_notification_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::table('dz_observer_actions', function (Blueprint $table) {
            $table->dropForeign('dz_observer_actions_notification_id_foreign');
            $table->dropColumn('notification_id');
        });
    }
}
