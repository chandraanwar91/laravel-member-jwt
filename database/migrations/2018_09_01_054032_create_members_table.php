<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('fullname');
            $table->string('password', 128)->nullable()->comment('Using SHA1');
            $table->integer('profile_id')->unsigned();
            $table->enum('status', ['active', 'suspend', 'removed'])->default('active');
            $table->enum('source', ['web','facebook','googleplus'])->default('web');
            $table->enum('authentication', ['pending', 'valid'])->default('pending');
            $table->text('verification_token')->nullable();
            $table->text('reset_token')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('member_profiles')->onDelete('cascade');
            $table->index(['email', 'password', 'status'], 'login_access');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
