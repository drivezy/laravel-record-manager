<?php

use App\User;
use Drivezy\LaravelRecordManager\Database\Seeds\ModelRelationshipTypeSeeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDzRelationshipDefinitionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('dz_relationship_definitions', function (Blueprint $table) {
            $userTable = ( new User() )->getTable();

            $table->increments('id');

            $table->string('name');
            $table->string('description')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on($userTable);
            $table->foreign('updated_by')->references('id')->on($userTable);

            $table->timestamps();
            $table->softDeletes();
        });

        //load the model column lookup type
        ( new ModelRelationshipTypeSeeder() )->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::dropIfExists('dz_relationship_definitions');
    }
}
