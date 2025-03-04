<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    
    protected $table = 'productos'; 

    protected $primaryKey = 'nombre'; // nombre la clave primaria
    protected $keyType = 'string'; //especifica el tipo de la clave primaria
    public $incrementing = false; // quita la creacion de ids que se autoincrementen

    protected $fillable = [
        'nombre',
        'precio',
        'descripcion',
        'posología', //es con acento
        'efectos_secundarios',
        'imagen',
        'categoria',
        'tratamiento',
    ];

    // No usaré timstamps
    public $timestamps = false;

    // Como tengo que usar un formato específico para el precio me creo esta función
    //Luego la usaré en las validaciones
    public function setPrecioAttribute($value)
    {
        $this->attributes['precio'] = number_format($value, 2, '.', '');
    }
}