<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    //1 permiso puede estar en muchos roles estas son las relaciones entre tablas 
    public function roles(){
        return $this->belongsToMany(Role::class)->withTimestamps(); //1 a muchos 
    }
}
