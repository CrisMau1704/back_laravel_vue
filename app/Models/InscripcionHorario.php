<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Carbon\Carbon;

class InscripcionHorario extends Pivot
{
    // ¡IMPORTANTE! Especifica el nombre de la tabla
    protected $table = 'inscripcion_horarios';
    
    public $incrementing = true;
    
    protected $fillable = [
        'inscripcion_id',
        'horario_id',
        'clases_asistidas',
        'clases_totales',
        'clases_restantes',
        'permisos_usados',
        'fecha_inicio',
        'fecha_fin',
        'estado'
    ];
    
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'clases_asistidas' => 'integer',
        'clases_totales' => 'integer',
        'clases_restantes' => 'integer',
        'permisos_usados' => 'integer'
    ];
    
    protected $attributes = [
        'clases_asistidas' => 0,
        'clases_totales' => 12,
        'clases_restantes' => 12,
        'permisos_usados' => 0,
        'estado' => 'activo'
    ];
    
    // Relaciones
    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }
    
    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }
    
    // Métodos de negocio
    public function registrarAsistencia(): bool
    {
        if ($this->clases_restantes <= 0 || $this->estado !== 'activo') {
            return false;
        }
        
        $this->clases_asistidas++;
        $this->clases_restantes--;
        return $this->save();
    }
    
    public function registrarFalta(): bool
    {
        if ($this->clases_restantes <= 0 || $this->estado !== 'activo') {
            return false;
        }
        
        $this->clases_restantes--;
        return $this->save();
    }
    
    public function usarPermiso(): bool
    {
        if ($this->permisos_usados >= 3 || $this->estado !== 'activo') {
            return false;
        }
        
        $this->permisos_usados++;
        return $this->save();
    }
    
    public function recuperarClase(): bool
    {
        if ($this->permisos_usados <= 0) {
            return false;
        }
        
        $this->permisos_usados--;
        $this->clases_restantes++;
        return $this->save();
    }
    
    public function renovarMes(): bool
    {
        $this->clases_asistidas = 0;
        $this->clases_restantes = $this->clases_totales;
        $this->permisos_usados = 0;
        $this->fecha_inicio = now()->startOfMonth();
        $this->fecha_fin = now()->endOfMonth();
        $this->estado = 'activo';
        
        return $this->save();
    }
    
    public function pausar(): bool
    {
        $this->estado = 'pausado';
        return $this->save();
    }
    
    public function reanudar(): bool
    {
        $this->estado = 'activo';
        return $this->save();
    }
    
    public function finalizar(): bool
    {
        $this->estado = 'finalizado';
        return $this->save();
    }
    
    // Accesores
    public function getPorcentajeAsistenciaAttribute(): float
    {
        if ($this->clases_totales == 0) {
            return 0;
        }
        
        $clasesTomadas = $this->clases_totales - $this->clases_restantes;
        return round(($clasesTomadas / $this->clases_totales) * 100, 2);
    }
    
    public function getTieneClasesRestantesAttribute(): bool
    {
        return $this->clases_restantes > 0;
    }
    
    public function getPuedeUsarPermisoAttribute(): bool
    {
        return $this->permisos_usados < 3;
    }
    
    public function getEstaVencidoAttribute(): bool
    {
        if (!$this->fecha_fin) {
            return false;
        }
        
        return Carbon::now()->greaterThan(Carbon::parse($this->fecha_fin));
    }
    
    public function getDiasParaVencerAttribute(): ?int
    {
        if (!$this->fecha_fin) {
            return null;
        }
        
        return Carbon::now()->diffInDays(Carbon::parse($this->fecha_fin), false);
    }
    
    public function getClasesTomadasAttribute(): int
    {
        return $this->clases_totales - $this->clases_restantes;
    }
    
    public function getRequiereRenovacionAttribute(): bool
    {
        return $this->esta_vencido || $this->clases_restantes <= 2;
    }
}