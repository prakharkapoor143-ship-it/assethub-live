<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accessory_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('accessory_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->unsignedInteger('quantity');
            $table->string('counterparty')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('transacted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accessory_transactions');
    }
};
