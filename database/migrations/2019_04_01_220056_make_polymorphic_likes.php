<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakePolymorphicLikes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->dropForeign(['media_id']);
            $table->renameColumn('media_id', 'likable_id');
            $table->string('likable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->dropColumn('likable_type');
            $table->renameColumn('likable_id', 'media_id');
            $table->foreign('media_id')->references('id')->on('media')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
}
