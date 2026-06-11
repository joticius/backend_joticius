<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rutas extends Model
{
    protected $table = 'rutas';
    protected $fillable = [
        'ciudad_origen',
        'ciudad_destino',
        'distancia',
        'tiempo_estimado',
        'observaciones'
    ];

    public $timestamps = true;
}
