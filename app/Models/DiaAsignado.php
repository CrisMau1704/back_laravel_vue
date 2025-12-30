<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaAsignado extends Model
{
    use HasFactory;

    protected $fillable = ['inscripcion_id', 'dia'];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }
}
