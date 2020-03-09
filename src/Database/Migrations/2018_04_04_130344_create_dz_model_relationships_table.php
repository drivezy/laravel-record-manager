<?php

use Drivezy\LaravelRecordManager\Database\Seeds\DataModelSeeder;
use Drivezy\LaravelRecordManager\Database\Seeds\ModelRelationshipTypeSeeder;
use Drivezy\LaravelRecordManager\Models\Column;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\Models\LookupValue;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDzModelRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create('dz_model_relationships', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $modelTable = ( new DataModel() )->getTable();
            $modelColumn = ( new Column() )->getTable();
            $relationshipTable = ( new LookupValue() )->getTable();

            $table->increments('id');
            $table->unsignedInteger('model_id')->nullable();

            $table->string('name');
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->string('join_definition')->nullable();

            $table->unsignedInteger('reference_type_id')->nullable();
            $table->unsignedInteger('reference_model_id')->nullable();
            $table->unsignedInteger('source_column_id')->nullable();
            $table->unsignedInteger('alias_column_id')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('model_id')->references('id')->on($modelTable);

            $table->foreign('reference_type_id')->references('id')->on($relationshipTable);
            $table->foreign('reference_model_id')->references('id')->on($modelTable);

            $table->foreign('source_column_id')->references('id')->on($modelColumn);
            $table->foreign('alias_column_id')->references('id')->on($modelColumn);

            $table->foreign('created_by')->references('id')->on($userTable);
            $table->foreign('updated_by')->references('id')->on($userTable);

            $table->timestamps();
            $table->softDeletes();
        });

        //populate the data model table
        ( new ModelRelationshipTypeSeeder() )->run();
        ( new DataModelSeeder() )->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        ( new ModelRelationshipTypeSeeder() )->drop();
        Schema::dropIfExists('dz_model_relationships');
    }
}
