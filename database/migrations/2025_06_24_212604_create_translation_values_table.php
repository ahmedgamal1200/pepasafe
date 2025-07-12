<?php

use App\Models\TranslationKey;
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
        Schema::create('translation_values', function (Blueprint $table) {
            $table->id();
            $table->string('locale'); // ar , en , fr
            $table->string('value');

            $table->foreignIdFor(TranslationKey::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_values');
    }
};
