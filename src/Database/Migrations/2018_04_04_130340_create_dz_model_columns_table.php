<?php

use App\User;
use Drivezy\LaravelRecordManager\Models\ColumnDefinition;
use Drivezy\LaravelRecordManager\Models\DataModel;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDzModelColumnsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_model_columns', function (Blueprint $table) {
            $userTable = ( new User() )->getTable();
            $modelTable = ( new DataModel() )->getTable();
            $columnTable = ( new ColumnDefinition() )->getTable();

            $table->increments('id');
            $table->unsignedInteger('model_id')->nullable();

            $table->string('name');
            $table->string('display_name');
            $table->string('description')->nullable();

            $table->boolean('visibility')->default(true);

            $table->unsignedInteger('column_type_id')->nullable();
            $table->unsignedInteger('reference_model_id')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('model_id')->references('id')->on($modelTable);
            $table->foreign('column_type_id')->references('id')->on($columnTable);
            $table->foreign('reference_model_id')->references('id')->on($modelTable);

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
        Schema::dropIfExists('dz_model_columns');
    }
}
