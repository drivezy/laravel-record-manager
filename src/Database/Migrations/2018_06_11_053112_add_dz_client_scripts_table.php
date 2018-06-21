<?php

use Drivezy\LaravelRecordManager\Database\Seeds\ActivityTypeSeeder;
use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDzClientScriptsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_client_scripts', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->increments('id');

            $table->string('name');
            $table->string('description')->nullable();

            $table->string('source_type')->nullable();
            $table->unsignedInteger('source_id')->nullable();

            $table->unsignedInteger('activity_type_id')->nullable();

            $table->unsignedInteger('script_id')->nullable();
            $table->boolean('active')->default(true);

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('activity_type_id')->references('id')->on('dz_lookup_values');
            $table->foreign('script_id')->references('id')->on('dz_system_scripts');

            $table->foreign('created_by')->references('id')->on($userTable);
            $table->foreign('updated_by')->references('id')->on($userTable);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['source_type', 'source_id']);
        });

        //add the necessary lookup for activity type
        ( new ActivityTypeSeeder() )->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::dropIfExists('dz_client_scripts');

        //remove records created as part of the migration
        ( new ActivityTypeSeeder() )->drop();
    }
}
