<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('gender', ['M', 'F'])->default('M');;
            $table->date('birth_date')->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->string('secondary_contact', 30)->nullable();
            $table->string('photo')->nullable();
            $table->string('address')->nullable();
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
        Schema::dropIfExists('member_profiles');
    }
}
