<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageAndModelPathToStyle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('styles', function (Blueprint $table) {
            $table->renameColumn('path', 'image_path');
            $table->string('model_path')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('styles', function (Blueprint $table) {
            $table->dropColumn('model_path');
            $table->renameColumn('image_path', 'path');
        });
    }
}
