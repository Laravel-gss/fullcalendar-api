<?php

use App\Enums\Api\FullCalendarEventStatus;
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
        Schema::create('full_calendar_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50);
            $table->text('description')->nullable();
            $table->date('date');
            $table->enum('status', array_column(FullCalendarEventStatus::cases(), 'value'))->default(FullCalendarEventStatus::PENDING);
            $table->foreignUuid('user_id')->constrained();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('full_calendar_events');
    }
};
