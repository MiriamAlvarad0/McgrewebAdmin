<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaquinariaProveedor extends Model
{
    use HasFactory;

    protected $table = 'maquinaria_proveedor';

    protected $fillable = [
        'id_proveedor',
        'id_maquinaria',
        'link'
    ];

    public function maquinaria()
    {
        return $this->belongsTo(Maquinaria::class, 'id_maquinaria');
    }
}
