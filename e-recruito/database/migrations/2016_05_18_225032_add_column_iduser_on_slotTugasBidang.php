<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIduserOnSlotTugasBidang extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('slot-tugas-bidang', function(Blueprint $table) {
			$table->integer('iduser');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('slot-tugas-bidang', function(Blueprint $table) {
			$table->dropColumn('iduser');
		});
	}

}
