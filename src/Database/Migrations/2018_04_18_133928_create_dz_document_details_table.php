<?php

use Drivezy\LaravelRecordManager\Database\Seeds\DocumentTypeSeeder;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\Models\LookupValue;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDzDocumentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create('dz_document_details', function (Blueprint $table) {
            $userTable = LaravelUtility::getUserTable();
            $lookupTable = ( new LookupValue() )->getTable();

            $table->increments('id');

            $table->string('source_type')->nullable();
            $table->unsignedInteger('source_id')->nullable();

            $table->unsignedInteger('document_type_id')->nullable();
            $table->string('document_url');

            $table->boolean('restricted_access')->default(false);
            $table->date('expiry_date')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('document_type_id')->references('id')->on($lookupTable);

            $table->foreign('created_by')->references('id')->on($userTable);
            $table->foreign('updated_by')->references('id')->on($userTable);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::dropIfExists('dz_document_details');
    }
}
