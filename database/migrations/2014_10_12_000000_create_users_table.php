<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('register_number')->unique();
            $table->string('phone')->unique();
            $table->tinyInteger('role')->comment('1:admin, 2:chef, 3:super chef');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('profile_image');
            $table->unsignedBigInteger('super_chef_id')->nullable();
            $table->foreign('super_chef_id')
                ->references('id')
                ->on('users');
            $table->rememberToken();
            $table->timestamps();
        });

        $admin = new User();
        $admin->profile_image = 'no_image';
        $admin->first_name = 'admin';
        $admin->last_name = 'admin';
        $admin->register_number = 'key';
        $admin->email = 'admin@admin.com';
        $admin->phone = '+33711111111';
        $admin->role = User::ROLE_ADMIN;
        $admin->password = bcrypt('azerty');
        $admin->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
