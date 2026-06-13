<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    protected $table = 'viajes';
    protected $fillable = ['conductor_id', 'vehiculo_id', 'ruta_id', 'fecha_salida', 'hora_salida', 'fecha_estimada_llegada', 'observaciones', 'estado'];
    public $timestamps = true;
}