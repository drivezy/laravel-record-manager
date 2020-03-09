<?php

use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDzServerDeploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create('dz_server_deployments', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();

            $table->increments('id');

            $table->string('name')->nullable();

            $table->string('key_name')->nullable();
            $table->string('public_url')->nullable();
            $table->string('instance_identifier')->nullable();

            $table->string('hostname')->nullable();
            $table->string('private_ip')->nullable();
            $table->string('public_ip')->nullable();

            $table->string('repository_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('commit')->nullable();

            $table->boolean('active')->default(true);
            $table->dateTime('last_ping_time')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

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
    public function down ()
    {
        Schema::dropIfExists('dz_server_deployments');
    }
}
