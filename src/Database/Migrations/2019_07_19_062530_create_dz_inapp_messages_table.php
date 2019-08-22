<?php

use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDzInappMessagesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_inapp_messages', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->increments('id');
            $table->uuid('uuid');

            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('platform_id')->nullable();

            $table->string('content', 1024)->nullable();
            $table->text('description')->nullable();

            $table->string('deep_link_url', 1024)->nullable();

            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();

            $table->date('viewed_at')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('user_id')->references('id')->on($userTable);
            $table->foreign('platform_id')->references('id')->on('dz_lookup_values');

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
        Schema::dropIfExists('dz_inapp_messages');
    }
}
