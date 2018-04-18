<?php

use App\User;
use Drivezy\LaravelRecordManager\Database\Seeds\DataModelSeeder;
use Drivezy\LaravelRecordManager\Database\Seeds\ModelColumnTypeSeeder;
use Drivezy\LaravelRecordManager\Database\Seeds\ModelRelationshipTypeSeeder;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\ModelColumn;
use Drivezy\LaravelUtility\Models\LookupValue;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDzModelRelationshipsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_model_relationships', function (Blueprint $table) {
            $userTable = ( new User() )->getTable();

            $modelTable = ( new DataModel() )->getTable();
            $modelColumn = ( new ModelColumn() )->getTable();
            $lookupTable = ( new LookupValue() )->getTable();

            $table->increments('id');
            $table->unsignedInteger('model_id')->nullable();

            $table->string('name');
            $table->string('display_name');
            $table->string('description')->nullable();

            $table->unsignedInteger('reference_type_id')->nullable();
            $table->unsignedInteger('reference_model_id')->nullable();
            $table->unsignedInteger('column_id')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('model_id')->references('id')->on($modelTable);
            $table->foreign('reference_type_id')->references('id')->on($lookupTable);
            $table->foreign('reference_model_id')->references('id')->on($modelTable);
            $table->foreign('column_id')->references('id')->on($modelColumn);

            $table->foreign('created_by')->references('id')->on($userTable);
            $table->foreign('updated_by')->references('id')->on($userTable);

            $table->timestamps();
            $table->softDeletes();
        });

        //load the model column lookup type
        ( new ModelColumnTypeSeeder() )->run();

        //load model relationship seeder
        ( new ModelRelationshipTypeSeeder() )->run();

        //populate the data model table
        ( new DataModelSeeder() )->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::dropIfExists('dz_model_relationships');
    }
}
