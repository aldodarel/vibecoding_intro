<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table): void {
            $table->id();

            $table->foreignIdFor(User::class, 'owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('property_type_id')
                ->nullable()
                ->constrained('property_types')
                ->nullOnDelete();

            $table->string('name', 150);
            $table->string('slug')->unique();
            $table->text('address');
            $table->string('city', 100);
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
