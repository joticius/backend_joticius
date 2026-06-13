<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    protected $table = 'rutas';
    protected $fillable = ['origen', 'destino', 'distancia', 'tiempo_estimado', 'observaciones', 'estado'];
    public $timestamps = true;
}