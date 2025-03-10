<?php

use App\Enums\EmployerExpenseType;
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
        Schema::create('additionals', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [EmployerExpenseType::reward->value, EmployerExpenseType::deduction->value]);
            $table->integer('amount');
            $table->foreignId('total_additional_id')->constrained('total_additionals')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additionals');
    }
};
