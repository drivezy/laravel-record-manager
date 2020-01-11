<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDzAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create('dz_audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->char('model_hash', 32)->nullable();
            $table->unsignedInteger('model_id')->nullable();

            $table->string('parameter')->nullable();

            $table->string('old_value', 1024)->nullable();
            $table->string('new_value', 1024)->nullable();

            $table->unsignedInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::dropIfExists('dz_audit_logs');
    }
}
