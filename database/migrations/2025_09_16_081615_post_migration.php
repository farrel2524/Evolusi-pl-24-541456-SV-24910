<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('plantd')) {
            return; // already created by 2025_09_16_081615_create_plantd_table
        }

    Schema::create('plantd', function (Blueprint $table) {
        $table->increments("id");
        $table->string("Pemilik_lahan");
        $table->integer("Luas_lahan");
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
        {
            Schema::dropIfExists('plantd');
        }
};
