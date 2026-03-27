<?php
// app/Http/Controllers/TutorialController.php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TutorialController extends Controller
{
    // =========================================================================
    // 🔐 MÉTODOS AUXILIARES DE SEGURIDAD
    // =========================================================================

    /**
     * Obtener rol del usuario como string limpio
     * ✅ Usa el campo 'nombre' de tu tabla roles
     */
    private function obtenerRol($user): string
    {
        if (!$user)
            return '';

        // Si el rol ya es string directo
        if (is_string($user->role)) {
            return trim($user->role);
        }

        // ✅ Si el rol es objeto con campo 'nombre' (tu estructura de BD)
        if (is_object($user->role)) {
            return trim($user->role->nombre ?? $user->role->name ?? '');
        }

        // Si es array (caso edge)
        if (is_array($user->role)) {
            return trim($user->role['nombre'] ?? $user->role['name'] ?? '');
        }

        return '';
    }

    /**
     * ✅ Verificar si el usuario es administrador
     * SOLO estos dos roles exactos de tu BD pueden administrar:
     * - "Superadmin We collab"
     * - "Admin We collab"
     */
    private function esAdministrador($user): bool
    {
        $rolUsuario = $this->obtenerRol($user);

        // ✅ Comparación EXACTA con los valores de tu tabla roles
        $rolesAdmin = [
            'Superadmin We collab',  // ← Exactamente como en tu BD
            'Admin We collab'        // ← Exactamente como en tu BD
        ];

        return in_array($rolUsuario, $rolesAdmin, true); // true = comparación estricta
    }

    /**
     * Verificar si el usuario tiene acceso para VER un tutorial
     */
    private function tieneAcceso($tutorial, $user = null): bool
    {
        // Contenido público: todos lo ven
        if (empty($tutorial->alcance) || trim($tutorial->alcance) === '') {
            return true;
        }

        if (!$user)
            return false;

        $rolUsuario = $this->obtenerRol($user);
        if (empty($rolUsuario))
            return false;

        // Superadmin ve todo
        if ($rolUsuario === 'Superadmin We collab') {
            return true;
        }

        // ✅ Match exacto con valores de tu BD (case-sensitive)
        return $rolUsuario === $tutorial->alcance;
    }

    // =========================================================================
    // 📋 INDEX - LISTAR TUTORIALES (Todos los usuarios activos)
    // =========================================================================

    public function index(Request $request): JsonResponse
    {
        $query = Tutorial::query()->where('estado', 'activo');

        // Búsqueda global
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhere('tipo_material', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo de material
        if ($tipo = $request->input('tipo_material')) {
            $query->where('tipo_material', $tipo);
        }

        // Ordenamiento y paginación
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $perPage = $request->input('per_page', 10);

        $tutoriales = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

        return response()->json([
            'data' => $tutoriales->items(),
            'current_page' => $tutoriales->currentPage(),
            'last_page' => $tutoriales->lastPage(),
            'per_page' => $tutoriales->perPage(),
            'total' => $tutoriales->total(),
        ], 200);
    }

    // =========================================================================
    // ➕ STORE - CREAR TUTORIAL (✅ SOLO: Superadmin We collab, Admin We collab)
    // =========================================================================

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:100|unique:tutoriales,titulo',
                'descripcion' => 'nullable|string',
                'tipo_material' => 'required|in:video,manual,guia,post,triptico',
                'formato' => 'required|in:pdf,word,mp4',
                'alcance' => 'required|in:Superadmin We collab,Admin We collab,Suscriptor SLC,Cliente Admin,Cliente Premium,Usuario Público,Prospecto',
                'estado' => 'required|in:activo,inactivo',
                'url' => 'nullable|url|max:500',
                'observacion' => 'nullable|string',
                'subcategoria_id' => 'nullable|exists:subcategorias,id',
                'user_id' => 'nullable|exists:users,id',
            ], [
                'subcategoria_id.exists' => 'La subcategoría seleccionada no existe',
                'alcance.in' => 'El valor de alcance no es válido',
                'titulo.unique' => 'Ya existe un tutorial con este título',
            ]);

            // Convertir strings vacíos a null para foreign keys
            if (empty($validated['subcategoria_id'])) {
                $validated['subcategoria_id'] = null;
            }
            if (empty($validated['user_id'])) {
                $validated['user_id'] = null;
            }

            $tutorial = Tutorial::create($validated);

            return response()->json([
                'message' => 'Tutorial creado exitosamente',
                'data' => $tutorial
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error al crear tutorial: ' . $e->getMessage(), [
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ]);
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    // =========================================================================
    // 👁️ SHOW - MOSTRAR TUTORIAL (Validación de acceso por rol/alcance)
    // =========================================================================

    public function show(Tutorial $tutorial, Request $request): JsonResponse
    {
        $user = $request->user();

        if ($tutorial->estado !== 'activo') {
            return response()->json(['success' => false, 'message' => 'Tutorial no disponible'], 404);
        }

        if (!$this->tieneAcceso($tutorial, $user)) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para ver este contenido'], 403);
        }

        return response()->json(['success' => true, 'data' => $tutorial], 200);
    }

    // =========================================================================
    // ✏️ UPDATE - ACTUALIZAR TUTORIAL (✅ SOLO: Superadmin We collab, Admin We collab)
    // =========================================================================

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $tutorial = Tutorial::find($id);
            if (!$tutorial) {
                return response()->json(['message' => 'Tutorial no encontrado'], 404);
            }

            $validated = $request->validate([
                'titulo' => 'required|string|max:100|unique:tutoriales,titulo,' . $tutorial->id,
                'descripcion' => 'nullable|string',
                'tipo_material' => 'required|in:video,manual,guia,post,triptico',
                'formato' => 'required|in:pdf,word,mp4',
                'alcance' => 'required|in:Superadmin We collab,Admin We collab,Suscriptor SLC,Cliente Admin,Cliente Premium,Usuario Público,Prospecto',
                'estado' => 'required|in:activo,inactivo',
                'url' => 'nullable|url|max:500',
                'observacion' => 'nullable|string',
                'subcategoria_id' => 'nullable|exists:subcategorias,id',
            ], [
                'subcategoria_id.exists' => 'La subcategoría seleccionada no existe',
                'alcance.in' => 'El valor de alcance no es válido',
                'titulo.unique' => 'Ya existe un tutorial con este título',
            ]);

            if (empty($validated['subcategoria_id'])) {
                $validated['subcategoria_id'] = null;
            }

            $tutorial->update($validated);

            return response()->json([
                'message' => 'Tutorial actualizado exitosamente',
                'data' => $tutorial
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error al actualizar: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // =========================================================================
    // 🗑️ DESTROY - ELIMINAR TUTORIAL (✅ SOLO: Superadmin We collab, Admin We collab)
    // =========================================================================

    public function destroy(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $user = $request->user();

            if (!$this->esAdministrador($user)) {
                return response()->json(['message' => 'No tienes permisos'], 403);
            }

            try {
                $tutorial = Tutorial::find($id);
                if (!$tutorial) {
                    return response()->json(['message' => 'Tutorial no encontrado'], 404);
                }

                $tutorial->delete();
                Log::info('✅ Tutorial eliminado', ['id' => $id]);

                return response()->json(null, 204);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('❌ Error al eliminar: ' . $e->getMessage());
                return response()->json(['message' => 'Error al eliminar'], 500);
            }
        });
    }
}