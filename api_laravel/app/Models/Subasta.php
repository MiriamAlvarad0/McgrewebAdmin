<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subasta extends Model
{
    use HasFactory;

    protected $table = 'subastas';

    protected $fillable = [
        'id_maquinaria',
        'fecha_inicio',
        'fecha_fin',
        'precio_inicial',
    ];

    public function maquinaria()
    {
        return $this->belongsTo(Maquinaria::class, 'id_maquinaria');
    }
}
