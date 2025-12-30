<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Estudiante;
use App\Models\Modalidad;
use App\Models\Horario;
use App\Models\InscripcionHorario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class InscripcionController extends Controller
{
    public function index(Request $request)
    {
        $query = Inscripcion::with(['estudiante', 'modalidad', 'horarios.disciplina', 'horarios.entrenador'])
            ->latest();
        
        // Filtros
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->has('modalidad_id')) {
            $query->where('modalidad_id', $request->modalidad_id);
        }
        
        if ($request->has('estudiante_id')) {
            $query->where('estudiante_id', $request->estudiante_id);
        }
        
        return $query->get();
    }

   public function store(Request $request)
{
    DB::beginTransaction();
    
    try {
        $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'modalidad_id' => 'required|exists:modalidades,id',
            'horarios' => 'required|array|min:1',
            'horarios.*' => 'exists:horarios,id',
        ]);
        
        // Obtener modalidad
        $modalidad = Modalidad::findOrFail($request->modalidad_id);
        
        // Verificar que el estudiante no tenga inscripción activa
        $inscripcionExistente = Inscripcion::where('estudiante_id', $request->estudiante_id)
            ->where('estado', 'activo')
            ->exists();
            
        if ($inscripcionExistente) {
            return response()->json([
                'error' => 'El estudiante ya tiene una inscripción activa'
            ], 422);
        }
        
        // Calcular fechas basadas en duración_dias
        $fechaInicio = now();
        $fechaFin = $fechaInicio->copy()->addDays($modalidad->duracion_dias);
        
        // Crear inscripción
        $inscripcion = Inscripcion::create([
            'estudiante_id' => $request->estudiante_id,
            'modalidad_id' => $request->modalidad_id,
            'clases_totales' => $modalidad->clases_totales,
            'clases_restantes' => $modalidad->clases_totales,
            'permisos_usados' => 0,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => 'activo'
        ]);
        
        // Asociar horarios
        $this->asociarHorarios($inscripcion, $request->horarios, $modalidad);
        
        DB::commit();
        
        return response()->json([
            'message' => 'Inscripción creada exitosamente',
            'data' => $inscripcion->load(['estudiante', 'modalidad', 'horarios'])
        ], 201);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Error al crear la inscripción: ' . $e->getMessage()
        ], 500);
    }
}
    public function show($id)
    {
        $inscripcion = Inscripcion::with([
            'estudiante', 
            'modalidad', 
            'horarios.disciplina', 
            'horarios.entrenador',
            'horarios.sucursal',
            'inscripcionHorarios' => function($query) {
                $query->select('id', 'inscripcion_id', 'horario_id', 'clases_asistidas', 
                              'clases_restantes', 'permisos_usados', 'estado');
            }
        ])->findOrFail($id);
        
        // Calcular estadísticas
        $totalClasesAsistidas = $inscripcion->inscripcionHorarios->sum('clases_asistidas');
        $totalClasesRestantes = $inscripcion->inscripcionHorarios->sum('clases_restantes');
        $totalPermisosUsados = $inscripcion->inscripcionHorarios->sum('permisos_usados');
        
        $inscripcion->estadisticas = [
            'clases_asistidas' => $totalClasesAsistidas,
            'clases_restantes' => $totalClasesRestantes,
            'permisos_usados' => $totalPermisosUsados,
            'porcentaje_asistencia' => $inscripcion->clases_totales > 0 
                ? round((($inscripcion->clases_totales - $totalClasesRestantes) / $inscripcion->clases_totales) * 100, 2)
                : 0
        ];
        
        return $inscripcion;
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $inscripcion = Inscripcion::findOrFail($id);
            
            $request->validate([
                'estado' => 'sometimes|in:activo,inactivo,vencido,finalizado',
                'clases_restantes' => 'sometimes|integer|min:0',
                'permisos_usados' => 'sometimes|integer|min:0',
                'fecha_fin' => 'sometimes|date',
                'horarios' => 'sometimes|array',
                'horarios.*' => 'exists:horarios,id'
            ]);
            
            // Actualizar inscripción
            $inscripcion->update($request->only([
                'estado', 'clases_restantes', 'permisos_usados', 'fecha_fin'
            ]));
            
            // Si se envían horarios, actualizarlos
            if ($request->has('horarios')) {
                $this->actualizarHorarios($inscripcion, $request->horarios);
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Inscripción actualizada exitosamente',
                'data' => $inscripcion->load(['estudiante', 'modalidad', 'horarios'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al actualizar la inscripción: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $inscripcion = Inscripcion::findOrFail($id);
            
            // Verificar si hay asistencias registradas
            $tieneAsistencias = $inscripcion->inscripcionHorarios()
                ->where('clases_asistidas', '>', 0)
                ->exists();
                
            if ($tieneAsistencias) {
                return response()->json([
                    'error' => 'No se puede eliminar una inscripción con asistencias registradas'
                ], 422);
            }
            
            // Liberar cupos de horarios
            foreach ($inscripcion->horarios as $horario) {
                $horario->decrement('cupo_actual');
            }
            
            // Eliminar relaciones
            $inscripcion->horarios()->detach();
            
            // Eliminar inscripción
            $inscripcion->delete();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Inscripción eliminada exitosamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al eliminar la inscripción: ' . $e->getMessage()
            ], 500);
        }
    }

    // Métodos adicionales

    public function asociarHorario(Request $request, $inscripcionId)
    {
        $request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'clases_totales' => 'sometimes|integer|min:1',
            'clases_restantes' => 'sometimes|integer|min:0',
            'permisos_usados' => 'sometimes|integer|min:0'
        ]);
        
        $inscripcion = Inscripcion::findOrFail($inscripcionId);
        $horario = Horario::findOrFail($request->horario_id);
        
        // Verificar cupo
        if (!$horario->tiene_cupo) {
            return response()->json([
                'error' => 'El horario no tiene cupo disponible'
            ], 422);
        }
        
        // Verificar que no esté ya asociado
        if ($inscripcion->horarios()->where('horario_id', $request->horario_id)->exists()) {
            return response()->json([
                'error' => 'El horario ya está asociado a esta inscripción'
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            // Asociar horario
            $inscripcion->horarios()->attach($request->horario_id, [
                'clases_totales' => $request->clases_totales ?? 
                    floor($inscripcion->clases_totales / ($inscripcion->horarios()->count() + 1)),
                'clases_restantes' => $request->clases_restantes ?? 
                    floor($inscripcion->clases_totales / ($inscripcion->horarios()->count() + 1)),
                'permisos_usados' => $request->permisos_usados ?? 0,
                'fecha_inicio' => $inscripcion->fecha_inicio,
                'fecha_fin' => $inscripcion->fecha_fin,
                'estado' => 'activo'
            ]);
            
            // Incrementar cupo del horario
            $horario->increment('cupo_actual');
            
            // Recalcular distribución de clases
            $this->recalcularDistribucionClases($inscripcion);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Horario asociado exitosamente',
                'data' => $inscripcion->load('horarios')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al asociar horario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function desasociarHorario($inscripcionId, $horarioId)
    {
        $inscripcion = Inscripcion::findOrFail($inscripcionId);
        $horario = Horario::findOrFail($horarioId);
        
        DB::beginTransaction();
        
        try {
            // Obtener relación pivote
            $pivote = $inscripcion->inscripcionHorarios()
                ->where('horario_id', $horarioId)
                ->first();
            
            // Verificar si hay asistencias en este horario
            if ($pivote && $pivote->clases_asistidas > 0) {
                return response()->json([
                    'error' => 'No se puede desasociar un horario con asistencias registradas'
                ], 422);
            }
            
            // Desasociar
            $inscripcion->horarios()->detach($horarioId);
            
            // Decrementar cupo del horario
            $horario->decrement('cupo_actual');
            
            // Recalcular distribución de clases
            $this->recalcularDistribucionClases($inscripcion);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Horario desasociado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al desasociar horario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function renovar($id)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Actualizar fechas
            $fechaInicio = now();
            $fechaFin = $fechaInicio->copy()->addMonth();
            
            $inscripcion->update([
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'clases_restantes' => $inscripcion->clases_totales,
                'permisos_usados' => 0,
                'estado' => 'activo'
            ]);
            
            // Renovar cada horario asociado
            foreach ($inscripcion->inscripcionHorarios as $inscripcionHorario) {
                $inscripcionHorario->update([
                    'clases_asistidas' => 0,
                    'clases_restantes' => $inscripcionHorario->clases_totales,
                    'permisos_usados' => 0,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'estado' => 'activo'
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Inscripción renovada exitosamente',
                'data' => $inscripcion->load(['estudiante', 'modalidad', 'horarios'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al renovar la inscripción: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verificarVencimientos()
    {
        $inscripcionesVencidas = Inscripcion::where('estado', 'activo')
            ->where('fecha_fin', '<', now())
            ->get();
        
        foreach ($inscripcionesVencidas as $inscripcion) {
            $inscripcion->update(['estado' => 'vencido']);
        }
        
        return response()->json([
            'message' => 'Verificación completada',
            'vencidas' => $inscripcionesVencidas->count()
        ]);
    }

    // Métodos privados

    private function asociarHorarios($inscripcion, $horariosIds, $modalidad)
    {
        $totalHorarios = count($horariosIds);
        $clasesPorHorario = floor($modalidad->clases_mensuales / $totalHorarios);
        
        foreach ($horariosIds as $horarioId) {
            $horario = Horario::findOrFail($horarioId);
            
            // Verificar cupo
            if (!$horario->tiene_cupo) {
                throw new \Exception("El horario {$horario->nombre} no tiene cupo disponible");
            }
            
            // Asociar horario
            $inscripcion->horarios()->attach($horarioId, [
                'clases_totales' => $clasesPorHorario,
                'clases_restantes' => $clasesPorHorario,
                'permisos_usados' => 0,
                'fecha_inicio' => $inscripcion->fecha_inicio,
                'fecha_fin' => $inscripcion->fecha_fin,
                'estado' => 'activo'
            ]);
            
            // Incrementar cupo del horario
            $horario->increment('cupo_actual');
        }
    }

    private function actualizarHorarios($inscripcion, $horariosIds)
    {
        // Obtener horarios actuales
        $horariosActuales = $inscripcion->horarios()->pluck('horarios.id')->toArray();
        
        // Horarios a eliminar
        $horariosAEliminar = array_diff($horariosActuales, $horariosIds);
        
        // Horarios a agregar
        $horariosAAgregar = array_diff($horariosIds, $horariosActuales);
        
        // Eliminar horarios
        foreach ($horariosAEliminar as $horarioId) {
            $this->desasociarHorario($inscripcion->id, $horarioId);
        }
        
        // Agregar nuevos horarios
        $modalidad = $inscripcion->modalidad;
        foreach ($horariosAAgregar as $horarioId) {
            $this->asociarHorario($inscripcion->id, $horarioId, $modalidad);
        }
    }

    private function recalcularDistribucionClases($inscripcion)
    {
        $totalHorarios = $inscripcion->horarios()->count();
        
        if ($totalHorarios === 0) return;
        
        $clasesPorHorario = floor($inscripcion->clases_totales / $totalHorarios);
        
        foreach ($inscripcion->inscripcionHorarios as $inscripcionHorario) {
            // Mantener las clases asistidas, ajustar el resto
            $clasesAsistidas = $inscripcionHorario->clases_asistidas;
            $nuevasClasesTotales = $clasesPorHorario;
            $nuevasClasesRestantes = max(0, $nuevasClasesTotales - $clasesAsistidas);
            
            $inscripcionHorario->update([
                'clases_totales' => $nuevasClasesTotales,
                'clases_restantes' => $nuevasClasesRestantes
            ]);
        }
    }
}