<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';
    protected $fillable = ['nombre', 'telefono', 'correo', 'links'];

    public function maquinarias()
    {
        return $this->belongsToMany(Maquinaria::class, 'maquinaria_proveedor', 'id_proveedor', 'id_maquinaria');
    }
}