<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_tag', function (Blueprint $table) {
            $table->bigInteger('media_id')->unsigned();
            $table->bigInteger('tag_id')->unsigned();

            $table->unique(['media_id', 'tag_id']);
            $table->foreign('media_id')->references('id')->on('media')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_tag');
    }
}
