<?php

use App\Models\Property;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table): void {
            $table->id();

            $table->foreignIdFor(Property::class, 'property_id')
                ->constrained('properties')
                ->cascadeOnDelete();

            $table->string('name', 50);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price');
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->tinyInteger('floor')->nullable();
            $table->json('facilities')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
