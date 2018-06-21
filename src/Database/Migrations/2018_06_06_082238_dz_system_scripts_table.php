<?php

use Drivezy\LaravelRecordManager\Database\Seeds\ScriptTypeSeeder;
use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DzSystemScriptsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_system_scripts', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->increments('id');

            $table->string('name');
            $table->string('description')->nullable();

            $table->unsignedInteger('script_type_id')->nullable();

            $table->string('source_type')->nullable();
            $table->unsignedInteger('source_id')->nullable();
            $table->string('source_column')->nullable();

            $table->text('script')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('script_type_id')->references('id')->on('dz_lookup_values');

            $table->foreign('created_by')->references('id')->on($userTable);
            $table->foreign('updated_by')->references('id')->on($userTable);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['source_type', 'source_id']);
        });

        ( new ScriptTypeSeeder() )->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::dropIfExists('dz_system_scripts');

        ( new ScriptTypeSeeder() )->drop();
    }
}
