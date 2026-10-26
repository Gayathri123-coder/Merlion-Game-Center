<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiTokenToCreateAccountModelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::table('create_account_models', function (Blueprint $table) {
			$table->string('api_token', 80)->unique()->nullable()->after('password');
		});
	}

	public function down()
	{
		Schema::table('create_account_models', function (Blueprint $table) {
			$table->dropColumn('api_token');
		});
	}
}
