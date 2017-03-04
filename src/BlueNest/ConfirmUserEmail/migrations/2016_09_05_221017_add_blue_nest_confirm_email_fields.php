<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBlueNestConfirmEmailFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->boolean('is_confirmed')->default(false);
        });

        Schema::create('confirmation_codes', function (Blueprint $table) {
            $table->integer('user_id');
            $table->string('confirmation_code');
            $table->boolean("expired");
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
        Schema::table('users', function ($table) {
            $table->dropColumn('is_confirmed');
        });

        Schema::drop('confirmation_codes');
    }
}
