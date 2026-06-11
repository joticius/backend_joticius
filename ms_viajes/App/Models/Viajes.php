<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viajes extends Model
{
    protected $table = 'seguimientos_viajes';
    protected $fillable = [
        'programacion_viaje_id',
        'fecha',
        'hora',
        'estado',
        'novedad'
    ];

    public $timestamps = true;
}
