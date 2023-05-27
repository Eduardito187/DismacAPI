<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoDocumento;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = ['nombre', 'apellido_paterno', 'apellido_materno', 'email', 'num_telefono', 'tipo_documento', 'num_documento', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function TipoDocumento(){
        return $this->hasOne(TipoDocumento::class, 'id', 'tipo_documento');
    }
}