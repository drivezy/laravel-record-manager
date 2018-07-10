<?php

use Drivezy\LaravelRecordManager\Database\Seeds\UIActionSeeder;
use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDzUiActionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_ui_actions', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->increments('id');

            $table->string('name');
            $table->string('identifier')->nullable();

            $table->string('description')->nullable();

            $table->string('source_type')->nullable();
            $table->unsignedInteger('source_id')->nullable();

            $table->integer('display_order')->default(0);
            $table->string('image')->nullable();

            $table->boolean('as_header')->default(false);
            $table->boolean('as_footer')->default(false);
            $table->boolean('as_dropdown')->default(false);
            $table->boolean('as_context')->default(false);
            $table->boolean('as_record')->default(false);

            $table->boolean('active')->default(true);

            $table->unsignedInteger('form_id')->nullable();
            $table->unsignedInteger('filter_condition_id')->nullable();
            $table->unsignedInteger('execution_script_id')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('form_id')->references('id')->on('dz_custom_forms');
            $table->foreign('filter_condition_id')->references('id')->on('dz_system_scripts');
            $table->foreign('execution_script_id')->references('id')->on('dz_system_scripts');

            $table->foreign('created_by')->references('id')->on($userTable);
            $table->foreign('updated_by')->references('id')->on($userTable);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['source_type', 'source_id']);
        });

        ( new UIActionSeeder() )->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        ( new UIActionSeeder() )->drop();

        Schema::dropIfExists('dz_ui_actions');
    }
}
