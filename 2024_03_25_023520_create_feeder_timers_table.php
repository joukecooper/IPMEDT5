<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feeder_timers', function (Blueprint $table) {
            $table->bigIncrements('timer_id')->notnull();
            $table->string('days_of_week');
            $table->time('time')->notnull();
            $table->boolean('feed_time_is_active')->notnull();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feeder_timers');
    }
};
