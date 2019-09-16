<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDzObserverActionsObserverRuleIdTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::table('dz_observer_actions', function (Blueprint $table) {
            $table->unsignedBigInteger('observer_rule_id')->nullable();
            $table->foreign('observer_rule_id')->references('id')->on('dz_observer_rules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::table('dz_observer_actions', function (Blueprint $table) {
            $table->dropForeign('dz_observer_actions_observer_rule_id_foreign');
            $table->dropColumn('observer_rule_id');
        });
    }
}
