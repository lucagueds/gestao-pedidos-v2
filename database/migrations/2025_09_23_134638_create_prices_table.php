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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            // Chave estrangeira que se conecta à tabela 'products'
            // onDelete('cascade') significa que se um produto for deletado, seus preços também serão.
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('list_name'); // Ex: "Preço a Vista", "Preço a Prazo"
            $table->decimal('value', 10, 2); // Preço com 10 dígitos no total e 2 casas decimais
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
