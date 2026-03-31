<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('license_records', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('license_key')->nullable();
            $table->unsignedInteger('seats_total')->default(0);
            $table->unsignedInteger('seats_used')->default(0);
            $table->date('expires_at')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_records');
    }
};
