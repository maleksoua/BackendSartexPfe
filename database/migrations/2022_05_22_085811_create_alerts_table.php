<?php

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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment('1:alerts about guards, 2:alerts about chef');
            $table->integer('percentage')->nullable();

            $table->unsignedBigInteger('chef_id')->nullable();
            $table->timestamp('alert_date');
            $table->foreign('chef_id')
                ->references('id')
                ->on('users');

            $table->unsignedBigInteger('guard_id')->nullable();
            $table->foreign('guard_id')
                ->references('id')
                ->on('guards');

            $table->unsignedBigInteger('zone_id')->nullable();
            $table->foreign('zone_id')
                ->references('id')
                ->on('zones');
            $table->timestamps();

            $table->unsignedBigInteger('alert_id')->nullable();
            $table->foreign('alert_id')
                ->references('id')
                ->on('alerts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alerts');
    }
};
