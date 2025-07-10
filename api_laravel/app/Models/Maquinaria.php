<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maquinaria extends Model
{

    use HasFactory;

    protected $table = 'maquinaria';

    protected $fillable = [
        'nombre',
        'id_categoria',
        'id_subcategoria',
        'marca',
        'modelo',
        'ano',
        'precio',
        'descripcion',
        'disponible',
        'imagen'
    ];

    protected $casts = [
        'disponible' => 'boolean',
        'precio' => 'float',
    ];

    public function subasta()
    {
        return $this->hasOne(Subasta::class, 'id_maquinaria');
    }

    public function proveedores()
    {
        return $this->hasMany(MaquinariaProveedor::class, 'id_maquinaria');
    }

    

    


}