<?php
// app/Models/Disciplina.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disciplina extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}