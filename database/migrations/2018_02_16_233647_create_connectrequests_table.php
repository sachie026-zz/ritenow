<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConnectrequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connectrequests', function (Blueprint $table) {
            $table->increments('id');
			$table->string('fbid');
            $table->string('from');
			$table->string('profile_name');
            $table->string('profile_pic')->nullable();
			$table->string('action')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('connectrequests');
    }
}
