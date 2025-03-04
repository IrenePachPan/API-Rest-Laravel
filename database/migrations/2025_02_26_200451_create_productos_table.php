<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->string('nombre')->primary(); 
            $table->decimal('precio', 5, 2); // Precio con formato NNN.NN
            $table->string('descripcion', 200); 
            $table->string('posología', 200); 
            $table->string('efectos_secundarios', 200); 
            $table->string('imagen'); // URL de la imagen
            $table->string('categoria'); 
            $table->string('tratamiento'); 
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
