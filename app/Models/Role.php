<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['nombre']; // Asegúrate de que coincida con tu estructura de BD

    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    // Si tienes permisos, añade esta relación (ajusta según tu esquema)
    public function permisos() {
        return $this->hasMany(Permiso::class);
    }
}